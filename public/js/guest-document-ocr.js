let workerPromise = null;

async function getWorker() {
    if (! workerPromise) {
        workerPromise = (async () => {
            const tesseractModule = await import('https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.esm.min.js');
            const createWorker =
                tesseractModule?.createWorker
                ?? tesseractModule?.default?.createWorker
                ?? tesseractModule?.default;

            if (typeof createWorker !== 'function') {
                throw new TypeError(
                    `createWorker is not a function; exports: ${Object.keys(tesseractModule ?? {}).join(', ')}`
                );
            }

            // MRZ / személyi: angol OCR-B karakterekhez eng is kell; hun a feliratokhoz.
            const worker = await createWorker('eng+hun');

            return worker;
        })();
    }

    return workerPromise;
}

function loadHtmlImage(src) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.decoding = 'async';
        image.onload = () => resolve(image);
        image.onerror = () => reject(new Error('Image element failed to load'));
        image.src = src;
    });
}

async function canvasToJpegFile(canvas, name = 'ocr.jpg', quality = 0.92) {
    const jpegBlob = await new Promise((resolve, reject) => {
        canvas.toBlob(
            (result) => (result ? resolve(result) : reject(new Error('canvas.toBlob failed'))),
            'image/jpeg',
            quality,
        );
    });

    return new File([jpegBlob], name, { type: 'image/jpeg' });
}

async function imageFromBlob(blob) {
    const objectUrl = URL.createObjectURL(blob);

    try {
        return await loadHtmlImage(objectUrl);
    } finally {
        URL.revokeObjectURL(objectUrl);
    }
}

async function blobToPreparedCanvases(blob, { mrzCrop = false } = {}) {
    const image = await imageFromBlob(blob);
    const maxSide = 2200;
    const scale = Math.min(1, maxSide / Math.max(image.naturalWidth, image.naturalHeight));
    const width = Math.max(1, Math.round(image.naturalWidth * scale));
    const height = Math.max(1, Math.round(image.naturalHeight * scale));

    const full = document.createElement('canvas');
    full.width = width;
    full.height = height;
    const fullCtx = full.getContext('2d');
    if (! fullCtx) {
        throw new Error('Canvas 2D context unavailable');
    }
    fullCtx.fillStyle = '#ffffff';
    fullCtx.fillRect(0, 0, width, height);
    fullCtx.drawImage(image, 0, 0, width, height);

    const result = {
        fullFile: await canvasToJpegFile(full, 'ocr-full.jpg'),
        mrzFile: null,
    };

    if (! mrzCrop) {
        return result;
    }

    // TD1 MRZ: az alsó ~30% sávban van. Nagyítjuk és kontrasztosítjuk.
    const cropTop = Math.floor(height * 0.68);
    const cropHeight = Math.max(40, height - cropTop);
    const mrz = document.createElement('canvas');
    const upscale = 2.4;
    mrz.width = Math.max(1, Math.round(width * upscale));
    mrz.height = Math.max(1, Math.round(cropHeight * upscale));
    const mrzCtx = mrz.getContext('2d');
    if (! mrzCtx) {
        throw new Error('MRZ canvas 2D context unavailable');
    }

    mrzCtx.imageSmoothingEnabled = false;
    mrzCtx.fillStyle = '#ffffff';
    mrzCtx.fillRect(0, 0, mrz.width, mrz.height);
    mrzCtx.drawImage(
        full,
        0,
        cropTop,
        width,
        cropHeight,
        0,
        0,
        mrz.width,
        mrz.height,
    );

    // Egyszerű kontraszt: szürkeárnyalat + küszöb a gépi sorokhoz.
    try {
        const imageData = mrzCtx.getImageData(0, 0, mrz.width, mrz.height);
        const data = imageData.data;
        for (let i = 0; i < data.length; i += 4) {
            const gray = (0.299 * data[i]) + (0.587 * data[i + 1]) + (0.114 * data[i + 2]);
            const v = gray < 140 ? 0 : 255;
            data[i] = v;
            data[i + 1] = v;
            data[i + 2] = v;
        }
        mrzCtx.putImageData(imageData, 0, 0);
    } catch (error) {
        console.warn('[guest-document-ocr] MRZ contrast failed', error);
    }

    result.mrzFile = await canvasToJpegFile(mrz, 'ocr-mrz.jpg', 0.95);

    return result;
}

async function loadSourceBlob({ dataUrl = null, url = null } = {}) {
    if (dataUrl) {
        const response = await fetch(dataUrl);
        const blob = await response.blob();

        if (! blob || blob.size < 100) {
            throw new Error('OCR dataUrl blob too small/empty');
        }

        return blob;
    }

    if (! url) {
        throw new Error('No OCR image source provided');
    }

    const response = await fetch(url, {
        credentials: 'same-origin',
        cache: 'no-store',
    });

    if (! response.ok) {
        throw new Error('OCR image fetch failed: ' + response.status);
    }

    const blob = await response.blob();

    if (! blob || blob.size < 100) {
        throw new Error('OCR image blob too small/empty: ' + (blob?.size ?? 0));
    }

    if ((blob.type || '').includes('text/html')) {
        throw new Error('OCR image URL returned HTML instead of an image');
    }

    return blob;
}

async function recognizeWithParams(worker, file, params = {}) {
    await worker.setParameters({
        preserve_interword_spaces: '1',
        tessedit_pageseg_mode: '6',
        tessedit_char_whitelist: '',
        ...params,
    });

    const result = await worker.recognize(file);

    return (result?.data?.text ?? '').trim();
}

async function runOcr(sources, field = null) {
    const worker = await getWorker();
    const blob = await loadSourceBlob(sources);
    const isBack = field === 'id_card_back_path';
    const prepared = await blobToPreparedCanvases(blob, { mrzCrop: isBack });

    if (! isBack) {
        return recognizeWithParams(worker, prepared.fullFile, {
            tessedit_pageseg_mode: '6',
        });
    }

    // Hátlap: külön MRZ-pass (whitelist), majd teljes kép.
    const parts = [];

    if (prepared.mrzFile) {
        const mrzText = await recognizeWithParams(worker, prepared.mrzFile, {
            tessedit_pageseg_mode: '6',
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789<',
        });

        if (mrzText) {
            parts.push(mrzText);
        }

        // Második próba: egyenkénti szövegsorok (PSM 7).
        const mrzTextLines = await recognizeWithParams(worker, prepared.mrzFile, {
            tessedit_pageseg_mode: '7',
            tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789<',
        });

        if (mrzTextLines && mrzTextLines !== mrzText) {
            parts.push(mrzTextLines);
        }
    }

    const fullText = await recognizeWithParams(worker, prepared.fullFile, {
        tessedit_pageseg_mode: '6',
        tessedit_char_whitelist: '',
    });

    if (fullText) {
        parts.push(fullText);
    }

    return parts.join('\n').trim();
}

function resolvePayload(payload) {
    if (Array.isArray(payload)) {
        return payload[0] ?? {};
    }

    if (payload && typeof payload === 'object' && payload.detail && typeof payload.detail === 'object') {
        return payload.detail;
    }

    return payload ?? {};
}

document.addEventListener('livewire:init', () => {
    Livewire.on('guest-document-ocr', async (payload) => {
        const detail = resolvePayload(payload);
        const url = detail?.url ?? null;
        const dataUrl = detail?.dataUrl ?? null;
        const field = detail?.field;
        const travelerUuid = detail?.travelerUuid;
        const componentId = detail?.componentId;

        if ((! url && ! dataUrl) || ! field || ! travelerUuid || ! componentId) {
            console.error('[guest-document-ocr] incomplete payload', detail);
            return;
        }

        const component = Livewire.find(componentId);

        if (! component) {
            console.error('[guest-document-ocr] Livewire component not found', componentId);
            return;
        }

        try {
            await component.call('markDocumentCheckPending', travelerUuid, field);
            const text = await runOcr({ dataUrl, url }, field);
            await component.call('applyDocumentOcrResult', travelerUuid, field, text);
        } catch (error) {
            console.error('[guest-document-ocr]', error);

            try {
                await component.call(
                    'reportDocumentOcrClientError',
                    travelerUuid,
                    field,
                    String(error?.message ?? error),
                );
            } catch (reportError) {
                console.error('[guest-document-ocr] report failed', reportError);
            }

            await component.call('applyDocumentOcrResult', travelerUuid, field, '');
        }
    });
});
