<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SebastianBergmann\Type\FalseType;

class DocumentImageRecognitionService
{
    /**
     * OCR debug napló: storage/logs/ocr.log
     * Élesen állítsd false-ra (személyes adatok!).
     */
    public const OCR_DEBUG_LOG = false;

    public const TYPE_ID_CARD = 'id_card';

    public const TYPE_ADDRESS_CARD = 'address_card';

    public const TYPE_UNKNOWN = 'unknown';

    public const SIDE_FRONT = 'front';

    public const SIDE_BACK = 'back';

    public const SIDE_UNKNOWN = 'unknown';

    /**
     * @return array{
     *     type: string,
     *     side: string,
     *     confidence: float,
     *     label: string,
     *     message: string,
     *     ok_for: bool,
     *     matched_keywords: list<string>,
     *     extracted: array{
     *         full_name: ?string,
     *         id_number: ?string,
     *         birth_date: ?string,
     *         nationality: ?string,
     *         address: ?string
     *     }
     * }
     */
    public function analyze(mixed $upload, string $expectedField, ?string $ocrText = null): array
    {
        $absolutePath = $this->resolveAbsolutePath($upload);
        $rawOcr = (string) ($ocrText ?? '');
        $normalizedOcr = $this->normalizeText($rawOcr);
        $hasOcr = filled($normalizedOcr) && mb_strlen($normalizedOcr) >= 12;

        $visual = $absolutePath ? $this->analyzeVisual($absolutePath) : [
            'has_face_region' => false,
            'has_mrz_band' => false,
            'greenish' => false,
            'bluish' => false,
            'aspect_ok' => false,
            'skin_ratio' => 0.0,
            'mrz_band_ratio' => 0.0,
            'width' => null,
            'height' => null,
        ];

        $keywordHit = $hasOcr
            ? $this->classifyFromText($normalizedOcr, $rawOcr)
            : ['type' => self::TYPE_UNKNOWN, 'side' => self::SIDE_UNKNOWN, 'keywords' => [], 'scores' => []];

        [$type, $side] = $this->resolveTypeAndSide($keywordHit, $visual, $expectedField, $hasOcr);
        $confidence = $this->confidence($keywordHit, $visual, $type, $side, $hasOcr);
        $okFor = $this->matchesExpected($expectedField, $type, $side, $visual, $hasOcr);
        $label = $this->humanLabel($type, $side);
        $extraction = $hasOcr
            ? $this->extractFieldsDetailed($rawOcr, $normalizedOcr, $type, $side, $expectedField)
            : [
                'extracted' => [
                    'full_name' => null,
                    'id_number' => null,
                    'birth_date' => null,
                    'nationality' => null,
                    'address' => null,
                ],
                'details' => [],
            ];
        $extracted = $extraction['extracted'];

        $result = [
            'type' => $type,
            'side' => $side,
            'confidence' => $confidence,
            'label' => $label,
            'message' => $okFor
                ? $this->successMessage($expectedField)
                : $this->mismatchMessage($expectedField, $label),
            'ok_for' => $okFor,
            'matched_keywords' => $keywordHit['keywords'],
            'extracted' => $extracted,
        ];

        $this->logDocumentAnalysis(
            $expectedField,
            $rawOcr,
            $visual,
            $result,
            $extraction['details'] ?? [],
            is_string($upload) ? $upload : ($absolutePath ?: get_debug_type($upload)),
        );

        return $result;
    }

    public function resolveAbsolutePath(mixed $upload): ?string
    {
        if ($upload instanceof TemporaryUploadedFile) {
            return $upload->getRealPath() ?: null;
        }

        if (is_array($upload)) {
            return $this->resolveAbsolutePath(array_values($upload)[0] ?? null);
        }

        if (! is_string($upload) || blank($upload)) {
            return null;
        }

        if (is_file($upload)) {
            return $upload;
        }

        $disk = Storage::disk('public');

        return $disk->exists($upload) ? $disk->path($upload) : null;
    }

    public function publicUrl(mixed $upload): ?string
    {
        try {
            if ($upload instanceof TemporaryUploadedFile) {
                return $upload->temporaryUrl();
            }

            if (is_array($upload)) {
                return $this->publicUrl(array_values($upload)[0] ?? null);
            }

            if (! is_string($upload) || blank($upload)) {
                return null;
            }

            if (Storage::disk('public')->exists($upload)) {
                return Storage::disk('public')->url($upload);
            }
        } catch (\Throwable) {
            return null;
        }

        return null;
    }

    /**
     * Kliensoldali OCR-hez: a fájl tartalma data URL-ként (max ~3.5 MB).
     * Így elkerüljük a Livewire temporary URL / JFIF hibákat.
     */
    public function ocrDataUrl(mixed $upload, int $maxBytes = 3_500_000): ?string
    {
        $path = $this->resolveAbsolutePath($upload);

        if (! $path || ! is_file($path)) {
            return null;
        }

        $size = filesize($path);

        if ($size === false || $size <= 0 || $size > $maxBytes) {
            return null;
        }

        $binary = @file_get_contents($path);

        if ($binary === false || $binary === '') {
            return null;
        }

        $mime = @mime_content_type($path) ?: 'image/jpeg';

        // A böngésző / Tesseract számára a jfif-et jpeg-ként adjuk át.
        if (in_array(Str::lower($mime), ['image/jfif', 'image/pjpeg', 'image/jpg'], true)
            || Str::endsWith(Str::lower($path), ['.jfif', '.jpe'])
        ) {
            $mime = 'image/jpeg';
        }

        return 'data:'.$mime.';base64,'.base64_encode($binary);
    }

    /**
     * @return array{type: string, side: string, keywords: list<string>, scores: array{front: int, back: int, address: int}}
     */
    protected function classifyFromText(string $normalized, string $raw): array
    {
        $keywords = [];
        $front = 0;
        $back = 0;
        $address = 0;

        $frontHints = [
            'szemelyazonosito igazolvany' => 6,
            'szemelyazonosito' => 5,
            'identity card' => 5,
            'szuletesi ido' => 3,
            'szuletesi hely' => 3,
            'date of birth' => 2,
            'family name' => 2,
            'given names' => 2,
            'vezeteknev' => 2,
            'utonev' => 2,
        ];

        $backHints = [
            '<<<' => 10,
            // A `<<` önmagában túl gyakori zaj – csak erősebb MRZ jelekkel együtt érjen sokat.
            '<<' => 2,
            'idhun' => 8,
            'i<hun' => 8,
            // Az előlapon is van aláírás mező – önmagában NEM hátlap!
            'alairas' => 1,
            'signature' => 1,
            'can ' => 2,
            'machine readable' => 4,
        ];

        $addressHints = [
            'lakcimkartya' => 10,
            'lakcim kartya' => 10,
            'lakcimkar' => 6,
            'allando lakcim' => 8,
            'allando lakohely' => 8,
            'allando lak' => 5,
            'tartozkodasi hely' => 6,
            'tartozkodasi' => 4,
            'permanent address' => 6,
            'bejelentett' => 4,
            'bejelentes' => 3,
            'helyrajzi' => 4,
            'lakcim' => 4,
            'iranyitoszam' => 3,
            'postal code' => 2,
        ];

        $strongIdFrontHints = [
            'szemelyazonosito igazolvany',
            'szemelyazonosito',
            'identity card',
        ];

        foreach ($frontHints as $hint => $score) {
            if (str_contains($normalized, $hint)) {
                $front += $score;
                $keywords[] = $hint;
            }
        }

        foreach ($backHints as $hint => $score) {
            $haystack = $hint === '<<' || $hint === '<<<'
                ? Str::lower($raw)
                : $normalized;

            if (str_contains($haystack, $hint) || str_contains(Str::lower($raw), $hint)) {
                $back += $score;
                $keywords[] = trim($hint);
            }
        }

        foreach ($addressHints as $hint => $score) {
            if (str_contains($normalized, $hint)) {
                $address += $score;
                $keywords[] = $hint;
            }
        }

        // Lakcím jellegű tartalom (irányítószám + közterület), tipikus lakcímkártyán.
        if (preg_match('/\b\d{4}\b/', $normalized) && preg_match('/(utca|ut |ter |ter$|korut|koz |setany|lepcso|ajto)/', $normalized)) {
            $address += 5;
            $keywords[] = 'postal-street';
        }

        // Erős MRZ: sok '<' / IDHUN / hosszú MRZ-szerű sor.
        // Figyelem: az előlap OCR-je is produkálhat 1-2 '<' zajt – ne dőljünk be.
        $upper = Str::upper($raw);
        $chevronCount = substr_count($upper, '<');
        if ($chevronCount >= 12 || preg_match('/(?:ID|I<)HUN[A-Z0-9<]{6,}/', $upper)) {
            $back += 8;
            $keywords[] = 'mrz';
        }

        // OCR gyakran elrontja a '<' jeleket – hosszú, sűrű alfanumerikus sorok a hátlapon.
        $mrzLikeLines = 0;
        foreach (preg_split('/\R+/', $upper) ?: [] as $line) {
            $line = preg_replace('/\s+/', '', $line) ?? $line;
            if (strlen($line) >= 28 && preg_match('/^[A-Z0-9<]{28,}$/', $line) && substr_count($line, '<') >= 3) {
                $mrzLikeLines++;
            }
        }
        if ($mrzLikeLines >= 2) {
            $back += 6;
            $keywords[] = 'mrz-line';
        } elseif ($mrzLikeLines === 1) {
            $back += 2;
            $keywords[] = 'mrz-line-weak';
        }

        $hasStrongIdFront = false;
        foreach ($strongIdFrontHints as $hint) {
            if (str_contains($normalized, $hint)) {
                $hasStrongIdFront = true;
                break;
            }
        }

        if ($address >= 3 && $address >= $back && (! $hasStrongIdFront || $address >= $front)) {
            return [
                'type' => self::TYPE_ADDRESS_CARD,
                'side' => self::SIDE_FRONT,
                'keywords' => $keywords,
                'scores' => compact('front', 'back', 'address'),
            ];
        }

        if ($back >= 3 && $back >= $front && $back >= $address) {
            return [
                'type' => self::TYPE_ID_CARD,
                'side' => self::SIDE_BACK,
                'keywords' => $keywords,
                'scores' => compact('front', 'back', 'address'),
            ];
        }

        // Előlap csak erős ID-jelzéssel – a névmezők önmagukban nem elég (lakcímkártyán is vannak).
        if ($hasStrongIdFront && $front >= 4 && $front > $back && $front > $address) {
            return [
                'type' => self::TYPE_ID_CARD,
                'side' => self::SIDE_FRONT,
                'keywords' => $keywords,
                'scores' => compact('front', 'back', 'address'),
            ];
        }

        return [
            'type' => self::TYPE_UNKNOWN,
            'side' => self::SIDE_UNKNOWN,
            'keywords' => $keywords,
            'scores' => compact('front', 'back', 'address'),
        ];
    }

    /**
     * @param  array{type: string, side: string, keywords: list<string>, scores?: array{front: int, back: int, address: int}}  $keywordHit
     * @param  array{has_face_region: bool, has_mrz_band: bool, greenish: bool, bluish: bool, aspect_ok: bool}  $visual
     * @return array{0: string, 1: string}
     */
    protected function resolveTypeAndSide(array $keywordHit, array $visual, string $expectedField, bool $hasOcr): array
    {
        $scores = $keywordHit['scores'] ?? ['front' => 0, 'back' => 0, 'address' => 0];
        $frontScore = (int) ($scores['front'] ?? 0);
        $backScore = (int) ($scores['back'] ?? 0);
        $ocrFront = $frontScore >= 4
            || ($keywordHit['type'] === self::TYPE_ID_CARD && $keywordHit['side'] === self::SIDE_FRONT);
        $strongFace = $visual['has_face_region'] === true;
        $realMrz = $this->hasRealMrzEvidence($keywordHit, $visual);
        // Gyenge hátlap-jel (pl. csak „Signature” felirat) NEM elég.
        $ocrBack = $realMrz || $backScore >= 6;

        // Elvárt mező: hátlap – ne dőljünk be gyenge „előlap” OCR-nek.
        if ($expectedField === 'id_card_back_path') {
            if ($ocrBack || $visual['has_mrz_band']) {
                return [self::TYPE_ID_CARD, self::SIDE_BACK];
            }

            // Erős arckép + erős előlap-szöveg → tényleg előlap (elutasítandó ide).
            if ($strongFace && $ocrFront && $frontScore > $backScore + 2) {
                return [self::TYPE_ID_CARD, self::SIDE_FRONT];
            }

            // Erős előlap szöveg (SZEMÉLYAZONOSÍTÓ IGAZOLVÁNY) MRZ nélkül → előlap.
            if ($ocrFront && $frontScore > $backScore + 2 && ! $realMrz) {
                return [self::TYPE_ID_CARD, self::SIDE_FRONT];
            }

            // Hátlap mezőbe töltöttek, nincs erős előlap-jel → hátlapnak vesszük.
            if (! $strongFace || $backScore > 0 || $visual['has_mrz_band']) {
                return [self::TYPE_ID_CARD, self::SIDE_BACK];
            }

            // Gyenge arcjel a hátlapon (hologram/aláírás) – még mindig hátlap, ha nincs erős előlap-kulcsszó.
            if (! $ocrFront) {
                return [self::TYPE_ID_CARD, self::SIDE_BACK];
            }

            return [self::TYPE_ID_CARD, self::SIDE_FRONT];
        }

        // Elvárt mező: előlap
        if ($expectedField === 'id_card_front_path') {
            // Scannelt előlap: gyakran nincs skin-alapú „arc”, de van erős előlap-szöveg.
            if ($ocrFront && $frontScore >= $backScore && ! $realMrz) {
                return [self::TYPE_ID_CARD, self::SIDE_FRONT];
            }

            if ($keywordHit['type'] === self::TYPE_ID_CARD && $keywordHit['side'] === self::SIDE_FRONT) {
                return [self::TYPE_ID_CARD, self::SIDE_FRONT];
            }

            if ($strongFace || $ocrFront || $keywordHit['type'] === self::TYPE_UNKNOWN) {
                return [self::TYPE_ID_CARD, self::SIDE_FRONT];
            }

            // Csak valódi MRZ esetén mondjuk hátlapnak az előlap mezőben.
            if ($realMrz && $backScore > $frontScore) {
                return [self::TYPE_ID_CARD, self::SIDE_BACK];
            }

            return [self::TYPE_ID_CARD, self::SIDE_FRONT];
        }

        // Elvárt mező: lakcímkártya – ne dőljünk be névmezőknek / gyenge „arc” jelnek.
        if ($expectedField === 'address_card_front_path') {
            $hasStrongIdFrontText = collect($keywordHit['keywords'] ?? [])->contains(
                fn (string $keyword): bool => in_array($keyword, [
                    'szemelyazonosito igazolvany',
                    'szemelyazonosito',
                    'identity card',
                ], true)
            );

            if (($scores['address'] ?? 0) >= 2 || $keywordHit['type'] === self::TYPE_ADDRESS_CARD) {
                return [self::TYPE_ADDRESS_CARD, self::SIDE_FRONT];
            }

            // Csak egyértelmű személyi jelek esetén utasítsuk el.
            if ($hasStrongIdFrontText || ($ocrBack && $backScore >= 6)) {
                return [
                    self::TYPE_ID_CARD,
                    $ocrBack ? self::SIDE_BACK : self::SIDE_FRONT,
                ];
            }

            // Lakcímkártya mező: alapértelmezés lakcímkártya (nincs fotó tipikusan).
            return [self::TYPE_ADDRESS_CARD, self::SIDE_FRONT];
        }

        if ($keywordHit['type'] !== self::TYPE_UNKNOWN) {
            return [$keywordHit['type'], $keywordHit['side']];
        }

        if ($strongFace && ! $ocrBack) {
            return [self::TYPE_ID_CARD, self::SIDE_FRONT];
        }

        if ($ocrBack || $visual['has_mrz_band']) {
            return [self::TYPE_ID_CARD, self::SIDE_BACK];
        }

        if ($visual['greenish'] && ! $strongFace) {
            return [self::TYPE_ADDRESS_CARD, self::SIDE_FRONT];
        }

        return [self::TYPE_UNKNOWN, self::SIDE_UNKNOWN];
    }

    /**
     * @param  array{has_face_region: bool, has_mrz_band: bool, greenish: bool, bluish: bool, aspect_ok: bool}  $visual
     * @param  array{type: string, side: string, keywords: list<string>}  $keywordHit
     */
    protected function hasStrongMrz(array $visual, array $keywordHit): bool
    {
        return $this->hasRealMrzEvidence($keywordHit, $visual);
    }

    /**
     * Valódi MRZ / hátlap jel – az előlap „Signature” felirata NEM számít.
     *
     * @param  array{type?: string, side?: string, keywords?: list<string>}  $keywordHit
     * @param  array{has_mrz_band?: bool}  $visual
     */
    protected function hasRealMrzEvidence(array $keywordHit, array $visual = []): bool
    {
        $keywords = $keywordHit['keywords'] ?? [];

        return in_array('mrz', $keywords, true)
            || in_array('mrz-line', $keywords, true)
            || in_array('<<<', $keywords, true)
            || in_array('idhun', $keywords, true)
            || in_array('i<hun', $keywords, true)
            || (($visual['has_mrz_band'] ?? false) === true && in_array('<<', $keywords, true));
    }

    /**
     * @return array{
     *     has_face_region: bool,
     *     has_mrz_band: bool,
     *     greenish: bool,
     *     bluish: bool,
     *     aspect_ok: bool,
     *     skin_ratio: float,
     *     mrz_band_ratio: float,
     *     width: ?int,
     *     height: ?int
     * }
     */
    protected function analyzeVisual(string $absolutePath): array
    {
        $empty = [
            'has_face_region' => false,
            'has_mrz_band' => false,
            'greenish' => false,
            'bluish' => false,
            'aspect_ok' => false,
            'skin_ratio' => 0.0,
            'mrz_band_ratio' => 0.0,
            'width' => null,
            'height' => null,
        ];

        $info = @getimagesize($absolutePath);

        if ($info === false) {
            return $empty;
        }

        [$width, $height] = $info;
        $ratio = $width / max($height, 1);
        $aspectOk = $ratio >= 1.35 && $ratio <= 1.85;
        $image = $this->loadImage($absolutePath, $info[2] ?? null);

        if (! $image) {
            return array_merge($empty, [
                'aspect_ok' => $aspectOk,
                'width' => $width,
                'height' => $height,
            ]);
        }

        $sampleW = 96;
        $sampleH = 60;
        $sample = imagecreatetruecolor($sampleW, $sampleH);
        imagecopyresampled($sample, $image, 0, 0, 0, 0, $sampleW, $sampleH, $width, $height);
        imagedestroy($image);

        $skin = 0;
        $greenBias = 0;
        $blueBias = 0;
        $darkBottom = 0;
        $pixels = $sampleW * $sampleH;

        for ($y = 0; $y < $sampleH; $y++) {
            for ($x = 0; $x < $sampleW; $x++) {
                $rgb = imagecolorat($sample, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if ($this->looksLikeSkin($r, $g, $b) && $x < (int) ($sampleW * 0.48)) {
                    $skin++;
                }

                if ($g > $r + 18 && $g > $b + 12) {
                    $greenBias++;
                }

                if ($b > $r + 12 && $b >= $g) {
                    $blueBias++;
                }

                if ($y > (int) ($sampleH * 0.78)) {
                    $luma = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
                    if ($luma < 55) {
                        $darkBottom++;
                    }
                }
            }
        }

        imagedestroy($sample);
        $bottomPixels = $sampleW * max(1, $sampleH - (int) ($sampleH * 0.78));
        $skinRatio = $skin / max($pixels, 1);
        $mrzBandRatio = $darkBottom / max($bottomPixels, 1);

        return [
            // Magasabb küszöb: a hátlap hologramja ne számítson „arcnak”.
            'has_face_region' => $skinRatio > 0.055,
            // Alsó sötét sáv – MRZ gyanú.
            'has_mrz_band' => $mrzBandRatio > 0.32,
            'greenish' => ($greenBias / max($pixels, 1)) > 0.1,
            'bluish' => ($blueBias / max($pixels, 1)) > 0.08,
            'aspect_ok' => $aspectOk,
            'skin_ratio' => round($skinRatio, 4),
            'mrz_band_ratio' => round($mrzBandRatio, 4),
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @param  array{type: string, side: string, keywords: list<string>, scores?: array{front: int, back: int, address: int}}  $keywordHit
     * @param  array{has_face_region: bool, has_mrz_band: bool, greenish: bool, bluish: bool, aspect_ok: bool}  $visual
     */
    protected function confidence(array $keywordHit, array $visual, string $type, string $side, bool $hasOcr): float
    {
        $score = $hasOcr ? 0.25 : 0.15;

        if ($keywordHit['type'] !== self::TYPE_UNKNOWN) {
            $score += 0.4;
        }

        if ($keywordHit['side'] !== self::SIDE_UNKNOWN) {
            $score += 0.15;
        }

        if ($type === self::TYPE_ID_CARD && $side === self::SIDE_FRONT && $visual['has_face_region']) {
            $score += 0.15;
        }

        if ($type === self::TYPE_ID_CARD && $side === self::SIDE_BACK && $this->hasStrongMrz($visual, $keywordHit)) {
            $score += 0.2;
        }

        if ($type === self::TYPE_ADDRESS_CARD && (($keywordHit['scores']['address'] ?? 0) >= 3 || $visual['greenish'])) {
            $score += 0.15;
        }

        return min(0.99, round($score, 2));
    }

    /**
     * @param  array{has_face_region: bool, has_mrz_band: bool, greenish: bool, bluish: bool, aspect_ok: bool}  $visual
     */
    protected function matchesExpected(
        string $expectedField,
        string $type,
        string $side,
        array $visual,
        bool $hasOcr,
    ): bool {
        return match ($expectedField) {
            'id_card_front_path' => $type === self::TYPE_ID_CARD && $side === self::SIDE_FRONT,
            'id_card_back_path' => $type === self::TYPE_ID_CARD && $side === self::SIDE_BACK,
            'address_card_front_path' => $type === self::TYPE_ADDRESS_CARD && $side === self::SIDE_FRONT,
            default => false,
        };
    }

    /**
     * @return array{full_name: ?string, id_number: ?string, birth_date: ?string, nationality: ?string, address: ?string}
     */
    protected function extractFields(string $raw, string $normalized, string $type, string $side): array
    {
        return $this->extractFieldsDetailed($raw, $normalized, $type, $side)['extracted'];
    }

    /**
     * @return array{
     *     extracted: array{full_name: ?string, id_number: ?string, birth_date: ?string, nationality: ?string, address: ?string},
     *     details: array<string, mixed>
     * }
     */
    protected function extractFieldsDetailed(
        string $raw,
        string $normalized,
        string $type,
        string $side,
        ?string $expectedField = null,
    ): array {
        $mrz = $this->extractFromMrzDetailed($raw);
        $field = $expectedField ?? '';

        // Hátlap: csak MRZ igazolványszám / dátum – a nevet NEM használjuk (zajos OCR).
        // Előlap: a „Családi és utónév” alatti név.
        if ($field === 'id_card_back_path' || ($side === self::SIDE_BACK && $field !== 'id_card_front_path')) {
            $fullName = null;
            $idNumber = $mrz['id_number'] ?? $this->extractIdNumber($raw, $normalized);
            $birthDate = $mrz['birth_date'] ?? $this->extractBirthDate($raw);
            $nationality = $mrz['nationality']
                ?? (str_contains($normalized, 'magyar') || str_contains($normalized, 'hun') ? 'HU' : null);
            $address = null;
        } elseif ($side === self::SIDE_FRONT || $field === 'id_card_front_path') {
            $fullName = $this->cleanExtractedPersonName(
                $this->extractNameFromIdFront($raw, $normalized)
                    ?? $mrz['full_name']
                    ?? $this->extractName($raw, $normalized)
            );
            $idNumber = $mrz['id_number'] ?? $this->extractIdNumber($raw, $normalized);
            $birthDate = $mrz['birth_date'] ?? $this->extractBirthDate($raw);
            $nationality = $mrz['nationality']
                ?? (str_contains($normalized, 'magyar') || str_contains($normalized, 'hun') ? 'HU' : null);
            $address = null;
        } else {
            $fullName = $this->cleanExtractedPersonName(
                $this->extractNameFromIdFront($raw, $normalized)
                    ?? $mrz['full_name']
                    ?? $this->extractName($raw, $normalized)
            );
            $idNumber = $mrz['id_number'] ?? $this->extractIdNumber($raw, $normalized);
            $birthDate = $mrz['birth_date'] ?? $this->extractBirthDate($raw);
            $address = ($type === self::TYPE_ADDRESS_CARD || $field === 'address_card_front_path')
                ? $this->extractAddress($raw, $normalized)
                : null;
            $nationality = $mrz['nationality']
                ?? (str_contains($normalized, 'magyar') || str_contains($normalized, 'hun') ? 'HU' : null);
        }

        if ($field === 'address_card_front_path' || $type === self::TYPE_ADDRESS_CARD) {
            $address = $this->extractAddress($raw, $normalized);
            // Lakcímkártyáról nem töltünk személyi mezőket.
            $fullName = null;
            $idNumber = null;
            $birthDate = null;
            $nationality = null;
        }

        $extracted = [
            'full_name' => $fullName,
            'id_number' => $idNumber,
            'birth_date' => $birthDate,
            'nationality' => $nationality,
            'address' => $address,
        ];

        $details = [
            'front_name_source' => ($field === 'id_card_front_path' && $this->extractNameFromIdFront($raw, $normalized))
                ? 'csaladi_utonev_label'
                : null,
            'mrz' => $mrz,
        ];

        if ($field === 'address_card_front_path' || $type === self::TYPE_ADDRESS_CARD) {
            $details['address_card'] = [
                'address' => $address,
                'ocr_lines' => array_values(array_filter(array_map(
                    static fn (string $line): string => trim($line),
                    preg_split('/\R+/', $raw) ?: [],
                ), static fn (string $line): bool => $line !== '')),
            ];
        }

        return [
            'extracted' => $extracted,
            'details' => $details,
        ];
    }

    /**
     * Magyar személyi igazolvány hátlap MRZ – ICAO Doc 9303 TD1 (3 × 30 karakter).
     *
     * @return array{full_name: ?string, id_number: ?string, birth_date: ?string, nationality: ?string, expiry_date: ?string}
     */
    protected function extractFromMrz(string $raw): array
    {
        $detailed = $this->extractFromMrzDetailed($raw);

        return [
            'full_name' => $detailed['full_name'],
            'id_number' => $detailed['id_number'],
            'birth_date' => $detailed['birth_date'],
            'nationality' => $detailed['nationality'],
            'expiry_date' => $detailed['expiry_date'],
        ];
    }

    /**
     * @return array{
     *     full_name: ?string,
     *     id_number: ?string,
     *     birth_date: ?string,
     *     nationality: ?string,
     *     expiry_date: ?string,
     *     sex: ?string,
     *     mrz_lines: list<string>,
     *     line1: ?string,
     *     line2: ?string,
     *     line3: ?string,
     *     line1_parsed: array<string, mixed>,
     *     line2_parsed: array<string, mixed>,
     *     checks: array<string, mixed>
     * }
     */
    protected function extractFromMrzDetailed(string $raw): array
    {
        $lines = $this->mrzCandidateLines($raw);
        [$line1, $line2, $line3] = $this->pickMrzTriplet($lines);

        $line1Data = $line1 ? $this->parseTd1Line1($line1) : [];
        $line2Data = $line2 ? $this->parseTd1Line2($line2) : [];
        $fullName = $line3 ? $this->parseTd1Line3($line3) : null;

        $parsed = [
            'full_name' => $fullName,
            'id_number' => $line1Data['id_number'] ?? null,
            'birth_date' => $line2Data['birth_date'] ?? null,
            'expiry_date' => $line2Data['expiry_date'] ?? null,
            'nationality' => $line2Data['nationality'] ?? null,
            'sex' => $line2Data['sex'] ?? null,
        ];

        // Ha a hármas hiányos, soronkénti fallback.
        if (($parsed['id_number'] ?? null) === null
            || ($parsed['full_name'] ?? null) === null
            || ($parsed['birth_date'] ?? null) === null
        ) {
            foreach ($lines as $line) {
                $asLine1 = $this->parseTd1Line1($line);
                $asLine2 = $this->parseTd1Line2($line);
                $asLine3 = $this->parseTd1Line3($line);

                if (($parsed['id_number'] ?? null) === null && ($asLine1['id_number'] ?? null) !== null) {
                    $parsed['id_number'] = $asLine1['id_number'];
                    $line1Data = $asLine1;
                    $line1 ??= $line;
                }

                if (($parsed['full_name'] ?? null) === null && $asLine3 !== null) {
                    $parsed['full_name'] = $asLine3;
                    $line3 ??= $line;
                }

                if (($parsed['birth_date'] ?? null) === null && ($asLine2['birth_date'] ?? null) !== null) {
                    $parsed['birth_date'] = $asLine2['birth_date'];
                    $parsed['expiry_date'] = $asLine2['expiry_date'];
                    $parsed['nationality'] = $asLine2['nationality'];
                    $parsed['sex'] = $asLine2['sex'];
                    $line2Data = $asLine2;
                    $line2 ??= $line;
                }
            }
        }

        if (($parsed['nationality'] ?? null) === null && ($parsed['id_number'] ?? null) !== null) {
            $parsed['nationality'] = 'HU';
        }

        return [
            'full_name' => $parsed['full_name'] ?? null,
            'id_number' => $parsed['id_number'] ?? null,
            'birth_date' => $parsed['birth_date'] ?? null,
            'nationality' => $parsed['nationality'] ?? null,
            'expiry_date' => $parsed['expiry_date'] ?? null,
            'sex' => $parsed['sex'] ?? null,
            'mrz_lines' => $lines,
            'line1' => $line1,
            'line2' => $line2,
            'line3' => $line3,
            'line1_parsed' => $line1Data,
            'line2_parsed' => $line2Data,
            'checks' => [
                'document_number_check_ok' => (bool) ($line1Data['check_ok'] ?? false),
                'birth_expiry_checks_ok' => (bool) ($line2Data['checks_ok'] ?? false),
            ],
        ];
    }
    /**
     * @return list<string>
     */
    protected function mrzCandidateLines(string $raw): array
    {
        $lines = [];
        $seen = [];

        $add = function (string $line) use (&$lines, &$seen): void {
            $line = $this->normalizeMrzLine($line);

            if ($line === '' || strlen($line) < 18) {
                return;
            }

            if (! (
                substr_count($line, '<') >= 2
                || preg_match('/^(?:[I1L][<D]|ID)HUN/', $line)
                || preg_match('/^\d{6}.[MF<]\d{6}/', $line)
                || preg_match('/^[A-Z]{2,}<+[A-Z]/', $line)
            )) {
                return;
            }

            $padded = $this->padMrzLine($line);

            if (isset($seen[$padded])) {
                return;
            }

            $seen[$padded] = true;
            $lines[] = $padded;
        };

        foreach (preg_split('/\R+/', Str::upper($raw)) ?: [] as $line) {
            $add($line);

            // OCR néha egy sorba ragasztja a 3 MRZ sort.
            $compact = $this->normalizeMrzLine($line);
            if (strlen($compact) >= 54) {
                foreach ($this->splitGluedMrz($compact) as $part) {
                    $add($part);
                }
            }
        }

        // Teljes szövegből is keressünk MRZ-szerű blokkokat (ha a sortörés elromlott).
        $all = $this->normalizeMrzLine($raw);
        if (strlen($all) >= 54) {
            foreach ($this->splitGluedMrz($all) as $part) {
                $add($part);
            }
        }

        // I<HUN... / IDHUN... minták a nyers szövegben.
        if (preg_match_all('/(?:I<HUN|IDHUN)[A-Z0-9<]{8,40}/', $all, $m1)) {
            foreach ($m1[0] as $hit) {
                $add($hit);
            }
        }

        if (preg_match_all('/\d{6}\d[MF<]\d{6}\dHUN[A-Z0-9<]{0,20}/', $all, $m2)) {
            foreach ($m2[0] as $hit) {
                $add($hit);
            }
        }

        if (preg_match_all('/[A-Z]{2,}<+[A-Z][A-Z<]{4,}/', $all, $m3)) {
            foreach ($m3[0] as $hit) {
                $add($hit);
            }
        }

        return $lines;
    }

    /**
     * @return list<string>
     */
    protected function splitGluedMrz(string $compact): array
    {
        $parts = [];

        // TD1: 3×30 karakter egymás után.
        if (preg_match('/((?:I<HUN|IDHUN)[A-Z0-9<]{20,35})(\d{6}.[MF<]\d{6}.[A-Z<]{3}[A-Z0-9<]{0,20})([A-Z]+<<[A-Z<]+)/', $compact, $m)) {
            $parts[] = $m[1];
            $parts[] = $m[2];
            $parts[] = $m[3];
        }

        // Csúszó 30 karakteres ablakok HUN / << körül.
        $length = strlen($compact);
        for ($i = 0; $i < $length - 24; $i++) {
            $window = substr($compact, $i, 30);
            if (str_starts_with($window, 'I<HUN')
                || str_starts_with($window, 'IDHUN')
                || preg_match('/^\d{6}.[MF<]\d{6}/', $window)
                || str_contains($window, '<<')
            ) {
                $parts[] = $window;
            }
        }

        return $parts;
    }

    protected function normalizeMrzLine(string $line): string
    {
        $line = Str::upper($line);
        // Gyakori OCR félreolvasások a `<` és MRZ karaktereknél.
        $line = strtr($line, [
            ' ' => '',
            '‹' => '<',
            '›' => '<',
            '«' => '<',
            '»' => '<',
            '〈' => '<',
            '〉' => '<',
            '〈' => '<',
            '〉' => '<',
            'Ｋ' => 'K',
            '＜' => '<',
        ]);
        $line = preg_replace('/[^A-Z0-9<]/', '', $line) ?? $line;

        // Gyakori OCR a dokumentumkódnál: 1D / L< / etc.
        if (preg_match('/^[1L]DHUN/', $line)) {
            $line = 'ID'.substr($line, 2);
        } elseif (preg_match('/^[1L]<HUN/', $line)) {
            $line = 'I<'.substr($line, 2);
        }

        // « helyett gyakran K/C a filler elején: HUN317928KEK0 -> HUN317928KE<0
        $line = preg_replace('/(HUN\d{6}[A-Z]{2})[KC](\d)/', '$1<$2', $line) ?? $line;

        return $line;
    }

    /**
     * TD1 sorok 30 karakteresek – OCR-nél vágjuk / kitöltjük.
     */
    protected function padMrzLine(string $line): string
    {
        if (strlen($line) > 30) {
            // Ha túl hosszú, a legvalószínűbb a 30 karakteres TD1 blokk eleje.
            $line = substr($line, 0, 30);
        }

        return str_pad($line, 30, '<');
    }

    /**
     * @param  list<string>  $lines
     * @return array{0: ?string, 1: ?string, 2: ?string}
     */
    protected function pickMrzTriplet(array $lines): array
    {
        $best = [null, null, null];
        $bestScore = -1;

        foreach ($lines as $index => $line) {
            if (! $this->looksLikeMrzLine1($line)) {
                continue;
            }

            $line1 = $line;
            $line2 = $lines[$index + 1] ?? null;
            $line3 = $lines[$index + 2] ?? null;
            $score = 0;

            $l1 = $this->parseTd1Line1($line1);
            if (($l1['id_number'] ?? null) !== null) {
                $score += 3;
            }
            if (($l1['check_ok'] ?? false) === true) {
                $score += 2;
            }

            if ($line2) {
                $l2 = $this->parseTd1Line2($line2);
                if (($l2['birth_date'] ?? null) !== null) {
                    $score += 3;
                }
                if (($l2['checks_ok'] ?? false) === true) {
                    $score += 2;
                }
            }

            if ($line3 && $this->parseTd1Line3($line3)) {
                $score += 3;
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $best = [$line1, $line2, $line3];
            }
        }

        if ($best[0] !== null) {
            return $best;
        }

        // Név sor alapján visszafelé.
        foreach ($lines as $index => $line) {
            if ($this->parseTd1Line3($line) === null) {
                continue;
            }

            return [
                $lines[$index - 2] ?? null,
                $lines[$index - 1] ?? null,
                $line,
            ];
        }

        return [null, null, null];
    }

    protected function looksLikeMrzLine1(string $line): bool
    {
        // TD1: pozíció 1-2 = ID vagy I<, 3-5 = kiállító ország.
        return (bool) preg_match('/^(?:ID|I<|[I1L]D|[I1L]<)HUN/', $line);
    }

    /**
     * 1. sor (30 karakter):
     * 1-2 típus (ID / I<), 3-5 ország (HUN), 6-14 okmányszám, 15 ellenőrző, 16-30 opcionális.
     *
     * @return array{id_number: ?string, document_number_raw: ?string, issuing_country: ?string, check_ok: bool}
     */
    protected function parseTd1Line1(string $line): array
    {
        $line = $this->padMrzLine($line);

        if (! preg_match('/^(?:ID|I<)HUN/', $line) && ! preg_match('/^[I1L][<D]HUN/', $line)) {
            // Regex fallback rövidebb / zajos sorokra.
            if (preg_match('/(?:ID|I<)HUN([0-9A-Z<]{9})(\d)/', $line, $m)) {
                $rawNumber = $m[1];
                $check = $m[2];
                $idNumber = $this->normalizeHungarianDocumentNumber($rawNumber);

                return [
                    'id_number' => $idNumber,
                    'document_number_raw' => $rawNumber,
                    'issuing_country' => 'HUN',
                    'check_ok' => $this->mrzCheckDigit($rawNumber) === $check,
                ];
            }

            return [
                'id_number' => null,
                'document_number_raw' => null,
                'issuing_country' => null,
                'check_ok' => false,
            ];
        }

        // 0-index: 5..13 = pozíció 6-14, 14 = pozíció 15
        $rawNumber = substr($line, 5, 9);
        $check = substr($line, 14, 1);
        $idNumber = $this->normalizeHungarianDocumentNumber($rawNumber);

        return [
            'id_number' => $idNumber,
            'document_number_raw' => $rawNumber,
            'issuing_country' => substr($line, 2, 3),
            'check_ok' => ctype_digit($check) && $this->mrzCheckDigit($rawNumber) === $check,
        ];
    }

    /**
     * Magyar okmányszám a 9 karakteres MRZ mezőből: 6 szám + 2 betű (+ kitöltő <).
     */
    protected function normalizeHungarianDocumentNumber(string $rawNumber): ?string
    {
        $rawNumber = rtrim($rawNumber, '<');

        if (preg_match('/^([0-9]{6}[A-Z]{2})/', $rawNumber, $m)) {
            return $m[1];
        }

        // OCR: O↔0 a számjegyekben.
        $fixed = $this->ocrFixMrzDocumentNumber($rawNumber);
        if ($fixed !== null) {
            return $fixed;
        }

        return strlen($rawNumber) >= 6 ? $rawNumber : null;
    }

    protected function ocrFixMrzDocumentNumber(string $rawNumber): ?string
    {
        $rawNumber = rtrim($rawNumber, '<');
        if (strlen($rawNumber) < 8) {
            return null;
        }

        $digits = substr($rawNumber, 0, 6);
        $letters = substr($rawNumber, 6, 2);

        $digits = strtr($digits, ['O' => '0', 'D' => '0', 'I' => '1', 'L' => '1', 'S' => '5', 'B' => '8']);
        $letters = strtr($letters, ['0' => 'O', '1' => 'I', '5' => 'S', '8' => 'B']);

        if (preg_match('/^[0-9]{6}[A-Z]{2}$/', $digits.$letters)) {
            return $digits.$letters;
        }

        return null;
    }

    /**
     * 2. sor (30 karakter):
     * 1-6 születés, 7 ellenőrző, 8 nem, 9-14 lejárat, 15 ellenőrző,
     * 16-18 állampolgárság, 19-29 opcionális, 30 összegzett ellenőrző.
     *
     * @return array{birth_date: ?string, expiry_date: ?string, nationality: ?string, sex: ?string, checks_ok: bool}
     */
    protected function parseTd1Line2(string $line): array
    {
        $line = $this->padMrzLine($line);

        $birthRaw = substr($line, 0, 6);
        $birthCheck = substr($line, 6, 1);
        $sex = substr($line, 7, 1);
        $expiryRaw = substr($line, 8, 6);
        $expiryCheck = substr($line, 14, 1);
        $nationality = substr($line, 15, 3);

        // Ha a pozíciós minta nem néz ki dátumnak, regex fallback.
        if (! preg_match('/^\d{6}$/', $birthRaw) || ! preg_match('/^\d{6}$/', $expiryRaw)) {
            if (preg_match('/^(\d{6})(\d)([MF<])(\d{6})(\d)([A-Z<]{3})/', $line, $m)) {
                $birthRaw = $m[1];
                $birthCheck = $m[2];
                $sex = $m[3];
                $expiryRaw = $m[4];
                $expiryCheck = $m[5];
                $nationality = $m[6];
            } else {
                return [
                    'birth_date' => null,
                    'expiry_date' => null,
                    'nationality' => null,
                    'sex' => null,
                    'checks_ok' => false,
                ];
            }
        }

        $birthOk = ctype_digit($birthCheck) && $this->mrzCheckDigit($birthRaw) === $birthCheck;
        $expiryOk = ctype_digit($expiryCheck) && $this->mrzCheckDigit($expiryRaw) === $expiryCheck;

        return [
            'birth_date' => $this->parseMrzDate($birthRaw, isBirth: true),
            'expiry_date' => $this->parseMrzDate($expiryRaw, isBirth: false),
            'nationality' => $nationality === 'HUN' ? 'HU' : (preg_match('/^[A-Z]{3}$/', $nationality) ? $nationality : null),
            'sex' => in_array($sex, ['M', 'F'], true) ? $sex : null,
            'checks_ok' => $birthOk && $expiryOk,
        ];
    }

    /**
     * YYMMDD → Y-m-d.
     * Születés: 00..(aktuális év) → 20xx, egyébként 19xx.
     * Lejárat: 20xx (magyar okmányoknál).
     */
    protected function parseMrzDate(string $yymmdd, bool $isBirth): ?string
    {
        if (! preg_match('/^\d{6}$/', $yymmdd)) {
            return null;
        }

        $yy = (int) substr($yymmdd, 0, 2);
        $mm = (int) substr($yymmdd, 2, 2);
        $dd = (int) substr($yymmdd, 4, 2);

        if ($isBirth) {
            $pivot = (int) date('y');
            $year = $yy <= $pivot ? 2000 + $yy : 1900 + $yy;
        } else {
            $year = 2000 + $yy;
        }

        if (! checkdate($mm, $dd, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $mm, $dd);
    }

    /**
     * 3. sor (30 karakter): vezetéknév << keresztnév, `<` = szóköz / kitöltő.
     */
    protected function parseTd1Line3(string $line): ?string
    {
        $line = $this->padMrzLine($line);

        // 1. / 2. MRZ sor ne legyen név.
        if (preg_match('/^(?:ID|I<)/', $line) || preg_match('/^\d{6}/', $line) || preg_match('/^\d/', $line)) {
            return null;
        }

        if (! str_contains($line, '<<')) {
            return null;
        }

        // Számjegy a névsorban = zaj / rossz sor.
        if (preg_match('/\d/', $line)) {
            return null;
        }

        $line = rtrim($line, '<');
        [$surnameRaw, $givenRaw] = array_pad(explode('<<', $line, 2), 2, '');

        $surname = trim(str_replace('<', ' ', $surnameRaw));
        $given = trim(str_replace('<', ' ', rtrim($givenRaw, '<')));
        $full = trim(preg_replace('/\s+/', ' ', $surname.' '.$given) ?? '');

        if ($full === '' || ! preg_match('/[A-Z]{2,}/', $full)) {
            return null;
        }

        if (mb_strlen($surname) < 2 || mb_strlen($given) < 2) {
            return null;
        }

        return $this->cleanExtractedPersonName(Str::title(Str::lower($full)));
    }

    /**
     * OCR zaj: „A TAJTI ZOLTÁN”, leading/trailing egyszeres betűk eltávolítása.
     */
    protected function cleanExtractedPersonName(?string $name): ?string
    {
        if ($name === null || blank($name)) {
            return null;
        }

        $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);

        // „A Tajti Zoltán”, „I Kovacs Janos” – egybetűs prefix (OCR szemét a fotó/címke mellől).
        $name = preg_replace('/^[A-ZÁÉÍÓÖŐÚÜŰa-záéíóöőúüű]\s+(?=[A-ZÁÉÍÓÖŐÚÜŰa-záéíóöőúüű]{2,})/u', '', $name) ?? $name;
        // Trailing egybetűs token.
        $name = preg_replace('/\s+[A-ZÁÉÍÓÖŐÚÜŰa-záéíóöőúüű]$/u', '', $name) ?? $name;

        $name = trim($name);

        return mb_strlen($name) >= 5 ? $name : null;
    }

    /**
     * ICAO Doc 9303 ellenőrző számjegy: súlyok 7-3-1, karakterérték 0-9 / A-Z=10-35 / `<=0`.
     */
    protected function mrzCheckDigit(string $data): string
    {
        $weights = [7, 3, 1];
        $sum = 0;

        $length = strlen($data);
        for ($i = 0; $i < $length; $i++) {
            $char = $data[$i];
            if ($char >= '0' && $char <= '9') {
                $value = (int) $char;
            } elseif ($char >= 'A' && $char <= 'Z') {
                $value = ord($char) - 55; // A=10
            } else {
                $value = 0; // <
            }

            $sum += $value * $weights[$i % 3];
        }

        return (string) ($sum % 10);
    }

    /**
     * Előlap: „Családi és utónév / Family name and Given name” alatt nagybetűs név.
     */
    protected function extractNameFromIdFront(string $raw, string $normalized): ?string
    {
        $lines = preg_split('/\R+/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $norm = $this->normalizeText($line);

            $isNameLabel = (str_contains($norm, 'csaladi') && str_contains($norm, 'utonev'))
                || (str_contains($norm, 'family name') && str_contains($norm, 'given'))
                || (str_contains($norm, 'vezeteknev') && str_contains($norm, 'utonev'))
                || str_contains($norm, 'csaladi es utonev')
                || str_contains($norm, 'family name and given');

            if (! $isNameLabel) {
                continue;
            }

            // Ugyanazon a soron, címke után egyértelmű elválasztóval (nem az angol címkefordítás!).
            if (preg_match('/(?:csaladi\s+es\s+utonev|vezeteknev\s+es\s+utonev)\s*(?:\/[^\n:]*)?\s*[:.\-]\s*([A-ZÁÉÍÓÖŐÚÜŰ][A-ZÁÉÍÓÖŐÚÜŰ\s\-]{4,})\s*$/iu', $line, $m)
                || preg_match('/:\s*([A-ZÁÉÍÓÖŐÚÜŰ][A-ZÁÉÍÓÖŐÚÜŰ\s\-]{4,})\s*$/u', $line, $m)
            ) {
                $candidate = trim($m[1]);
                if ($this->looksLikeUppercasePersonName($candidate)) {
                    return $this->cleanExtractedPersonName(
                        Str::title(Str::lower(preg_replace('/\s+/', ' ', $candidate) ?? $candidate))
                    );
                }
            }

            for ($offset = 1; $offset <= 4; $offset++) {
                $candidate = trim((string) ($lines[$index + $offset] ?? ''));

                if ($candidate === '') {
                    continue;
                }

                $candidateNorm = $this->normalizeText($candidate);
                if (str_contains($candidateNorm, 'family name')
                    || str_contains($candidateNorm, 'given name')
                    || str_contains($candidateNorm, 'csaladi')
                    || str_contains($candidateNorm, 'utonev')
                    || str_contains($candidateNorm, 'szuletesi')
                    || str_contains($candidateNorm, 'nemzetiseg')
                    || str_contains($candidateNorm, 'nationality')
                ) {
                    continue;
                }

                // „A TAJTI ZOLTÁN” jellegű OCR zaj.
                $candidate = preg_replace('/^[A-ZÁÉÍÓÖŐÚÜŰ]\s+/u', '', $candidate) ?? $candidate;
                $candidate = trim($candidate);

                if ($this->looksLikeUppercasePersonName($candidate)) {
                    return $this->cleanExtractedPersonName(
                        Str::title(Str::lower(preg_replace('/\s+/', ' ', $candidate) ?? $candidate))
                    );
                }
            }
        }

        return null;
    }

    protected function looksLikeUppercasePersonName(string $line): bool
    {
        $clean = trim(preg_replace('/\s+/', ' ', $line) ?? $line);
        $norm = $this->normalizeText($clean);

        if ($clean === '' || mb_strlen($clean) < 5) {
            return false;
        }

        if (! preg_match('/^[A-ZÁÉÍÓÖŐÚÜŰ][A-ZÁÉÍÓÖŐÚÜŰ\s\-]{4,}$/u', Str::upper($clean))) {
            return false;
        }

        $blocked = ['magyarorszag', 'hungary', 'igazolvany', 'identity', 'lakcim', 'szuletesi', 'family', 'given', 'csaladi', 'utonev', 'nemzetiseg', 'nationality'];

        foreach ($blocked as $word) {
            if (str_contains($norm, $word)) {
                return false;
            }
        }

        $spaces = substr_count($clean, ' ');

        return $spaces >= 1 && $spaces <= 4;
    }

    /**
     * Összeveti az OCR-ből kiolvasott / szövegben megtalált adatokat a megadott űrlapmezőkkel.
     * Csak egyértelmű ELTÉRÉS esetén bukik el – ha nem olvasható ki, nem utasít el.
     *
     * @param  array{full_name?: ?string, id_number?: ?string, birth_date?: ?string, nationality?: ?string, address?: ?string}  $extracted
     * @param  array{full_name_on_document?: ?string, name?: ?string, id_number?: ?string, address_on_card?: ?string}  $provided
     * @param  array{compare_name?: bool, compare_id?: bool, compare_address?: bool, soft?: bool}  $options
     * @return array{ok: bool, name_matches: ?bool, id_matches: ?bool, address_matches: ?bool, messages: list<string>, warnings: list<string>, needs_manual_validation: bool}
     */
    public function compareToProvided(array $extracted, array $provided, ?string $ocrText = null, array $options = []): array
    {
        $compareName = $options['compare_name'] ?? true;
        $compareId = $options['compare_id'] ?? true;
        $compareAddress = $options['compare_address'] ?? false;
        $soft = $options['soft'] ?? false;

        $messages = [];
        $warnings = [];
        $nameMatches = null;
        $idMatches = null;
        $addressMatches = null;
        $nameReason = 'not_checked';
        $idReason = 'not_checked';
        $ocrNormalized = $this->normalizeText((string) $ocrText);

        $providedNameRaw = (string) ($provided['full_name_on_document'] ?? $provided['name'] ?? '');
        $providedName = $this->normalizePersonName($providedNameRaw);
        $extractedName = $this->normalizePersonName((string) ($extracted['full_name'] ?? ''));

        if ($compareName && filled($providedName)) {
            if (filled($extractedName)) {
                $nameMatches = $this->personNamesMatch($providedName, $extractedName);
                $nameReason = $nameMatches
                    ? 'extracted_name_matches_provided'
                    : 'extracted_name_differs_from_provided';
            } elseif (filled($ocrNormalized)) {
                $nameMatches = $this->ocrContainsName($ocrNormalized, $providedName);
                $nameReason = $nameMatches
                    ? 'provided_name_found_in_raw_ocr'
                    : 'provided_name_not_found_in_raw_ocr';
            } else {
                $nameReason = 'no_extracted_name_and_no_ocr_text';
            }

            if ($nameMatches === true) {
                // ok
            } elseif ($nameMatches === false) {
                $messages[] = 'A megadott név ('.$providedNameRaw.') nem egyezik az igazolványon olvasható adatokkal'
                    .(filled($extracted['full_name'] ?? null) ? ' ('.$extracted['full_name'].')' : '').'.';
            } else {
                $warnings[] = 'A név összevetése nem volt egyértelműen elvégezhető az OCR alapján.';
            }
        } else {
            $nameReason = $compareName ? 'no_provided_name' : 'name_compare_skipped';
        }

        $providedIdRaw = (string) ($provided['id_number'] ?? '');
        $providedId = $this->normalizeIdNumber($providedIdRaw);
        $extractedId = $this->normalizeIdNumber((string) ($extracted['id_number'] ?? ''));

        if ($compareId && filled($providedId)) {
            if (filled($extractedId)) {
                $idMatches = $providedId === $extractedId;
                $idReason = $idMatches
                    ? 'extracted_id_matches_provided'
                    : 'extracted_id_differs_from_provided';
            } elseif (filled($ocrText)) {
                $idMatches = $this->ocrContainsIdNumber((string) $ocrText, $providedId);
                $idReason = $idMatches
                    ? 'provided_id_found_in_raw_ocr'
                    : 'provided_id_not_found_in_raw_ocr';
            } else {
                $idReason = 'no_extracted_id_and_no_ocr_text';
            }

            if ($idMatches === true) {
                // ok
            } elseif ($idMatches === false) {
                $messages[] = 'A megadott igazolványszám ('.$providedIdRaw.') nem egyezik az igazolványon olvasható adatokkal'
                    .(filled($extracted['id_number'] ?? null) ? ' ('.$extracted['id_number'].')' : '').'.';
            } else {
                $warnings[] = 'Az igazolványszám összevetése nem volt egyértelműen elvégezhető az OCR alapján.';
            }
        } else {
            $idReason = $compareId ? 'no_provided_id' : 'id_compare_skipped';
        }

        if ($compareAddress) {
            $providedAddressRaw = (string) ($provided['address_on_card'] ?? '');
            $providedAddress = $this->normalizeText($providedAddressRaw);
            $extractedAddress = $this->normalizeText((string) ($extracted['address'] ?? ''));

            if (filled($providedAddress) && filled($extractedAddress)) {
                $addressMatches = $providedAddress === $extractedAddress
                    || str_contains($providedAddress, $extractedAddress)
                    || str_contains($extractedAddress, $providedAddress)
                    || $this->addressesLooselyMatch($providedAddress, $extractedAddress);
            } elseif (filled($providedAddress) && filled($ocrNormalized)) {
                $parts = array_values(array_filter(
                    preg_split('/\s+/', $providedAddress) ?: [],
                    fn (string $part): bool => mb_strlen($part) >= 3
                ));
                $hits = 0;
                foreach ($parts as $part) {
                    if (str_contains($ocrNormalized, $part)) {
                        $hits++;
                    }
                }
                $addressMatches = $parts !== [] && ($hits / count($parts)) >= 0.5;
            }
        }

        $needsManual = ($nameMatches === false) || ($idMatches === false) || ($addressMatches === false);

        $result = [
            'ok' => $soft ? true : ($messages === []),
            'name_matches' => $nameMatches,
            'id_matches' => $idMatches,
            'address_matches' => $addressMatches,
            'messages' => $soft ? [] : $messages,
            'warnings' => $warnings,
            'needs_manual_validation' => $needsManual,
        ];

        $this->logOcrCompare([
            'provided_raw' => [
                'full_name_on_document' => $provided['full_name_on_document'] ?? null,
                'name' => $provided['name'] ?? null,
                'id_number' => $provided['id_number'] ?? null,
                'address_on_card' => $provided['address_on_card'] ?? null,
            ],
            'provided_normalized' => [
                'name' => $providedName !== '' ? $providedName : null,
                'id_number' => $providedId !== '' ? $providedId : null,
            ],
            'extracted_raw' => $extracted,
            'extracted_normalized' => [
                'name' => $extractedName !== '' ? $extractedName : null,
                'id_number' => $extractedId !== '' ? $extractedId : null,
            ],
            'ocr_raw_length' => mb_strlen((string) $ocrText),
            'ocr_raw' => (string) $ocrText,
            'name_matches' => $nameMatches,
            'name_reason' => $nameReason,
            'id_matches' => $idMatches,
            'id_reason' => $idReason,
            'address_matches' => $addressMatches,
            'ok' => $result['ok'],
            'messages' => $result['messages'],
            'warnings' => $warnings,
            'needs_manual_validation' => $needsManual,
            'options' => $options,
        ]);

        return $result;
    }

    protected function addressesLooselyMatch(string $left, string $right): bool
    {
        $leftParts = array_values(array_filter(
            preg_split('/[\s,]+/', $left) ?: [],
            fn (string $part): bool => mb_strlen($part) >= 3
        ));

        if ($leftParts === []) {
            return false;
        }

        $hits = 0;
        foreach ($leftParts as $part) {
            if (str_contains($right, $part)) {
                $hits++;
            }
        }

        return ($hits / count($leftParts)) >= 0.5;
    }

    /**
     * @param  array<string, mixed>  $result
     * @param  array<string, mixed>  $details
     * @param  array<string, mixed>  $visual
     */
    protected function logDocumentAnalysis(
        string $expectedField,
        string $rawOcr,
        array $visual,
        array $result,
        array $details,
        string $uploadPath,
    ): void {
        if (! self::OCR_DEBUG_LOG) {
            return;
        }

        $lines = [];
        $lines[] = str_repeat('=', 72);
        $lines[] = '['.now()->format('Y-m-d H:i:s').'] '.$expectedField;
        $lines[] = 'upload: '.$uploadPath;
        $lines[] = 'detected: '.($result['label'] ?? '?').' | ok_for='.(($result['ok_for'] ?? false) ? 'YES' : 'NO')
            .' | confidence='.($result['confidence'] ?? '?');
        $lines[] = 'message: '.($result['message'] ?? '');

        if ($expectedField === 'id_card_front_path') {
            $lines[] = '';
            $lines[] = '--- ARC FELISMERÉS ---';
            $lines[] = 'has_face_region: '.(($visual['has_face_region'] ?? false) ? 'YES' : 'NO');
            $lines[] = 'skin_ratio: '.($visual['skin_ratio'] ?? 0).' (küszöb > 0.055)';
            $lines[] = 'image: '.($visual['width'] ?? '?').'x'.($visual['height'] ?? '?')
                .' | aspect_ok='.(($visual['aspect_ok'] ?? false) ? 'YES' : 'NO');
            $lines[] = '';
            $lines[] = '--- TELJES RAW OCR SZÖVEG ---';
            $lines[] = $rawOcr !== '' ? $rawOcr : '(ÜRES – az OCR nem adott vissza szöveget)';
            $lines[] = '';
            $lines[] = '--- KIOLVASOTT MEZŐK ---';
            foreach (($result['extracted'] ?? []) as $key => $value) {
                $lines[] = $key.': '.(filled($value) ? $value : '(null)');
            }
        } elseif ($expectedField === 'id_card_back_path') {
            $mrz = $details['mrz'] ?? [];
            $lines[] = '';
            $lines[] = '--- MRZ NYERS SOROK (jelöltek) ---';
            $mrzLines = $mrz['mrz_lines'] ?? [];
            if ($mrzLines === []) {
                $lines[] = '(nincs MRZ-jelölt sor az OCR-ben)';
            } else {
                foreach ($mrzLines as $i => $mrzLine) {
                    $lines[] = 'candidate['.$i.']: '.$mrzLine;
                }
            }
            $lines[] = '';
            $lines[] = '--- TD1 HÁRMAS ---';
            $lines[] = 'line1: '.($mrz['line1'] ?? '(null)');
            $lines[] = 'line2: '.($mrz['line2'] ?? '(null)');
            $lines[] = 'line3: '.($mrz['line3'] ?? '(null)');
            $lines[] = '';
            $lines[] = '--- MRZ-BŐL KIOLVASOTT ADATOK ---';
            $lines[] = 'okmanyszam: '.($mrz['id_number'] ?? '(null)');
            $lines[] = 'nev: '.($mrz['full_name'] ?? '(null)');
            $lines[] = 'szuletesi_datum: '.($mrz['birth_date'] ?? '(null)');
            $lines[] = 'lejarati_datum: '.($mrz['expiry_date'] ?? '(null)');
            $lines[] = 'nem: '.($mrz['sex'] ?? '(null)');
            $lines[] = 'allampolgarsag: '.($mrz['nationality'] ?? '(null)');
            $lines[] = 'document_number_check_ok: '.(($mrz['checks']['document_number_check_ok'] ?? false) ? 'YES' : 'NO');
            $lines[] = 'birth_expiry_checks_ok: '.(($mrz['checks']['birth_expiry_checks_ok'] ?? false) ? 'YES' : 'NO');
            $lines[] = '';
            $lines[] = '--- TELJES RAW OCR SZÖVEG ---';
            $lines[] = $rawOcr !== '' ? $rawOcr : '(ÜRES – az OCR nem adott vissza szöveget)';
        } elseif ($expectedField === 'address_card_front_path') {
            $card = $details['address_card'] ?? [];
            $lines[] = '';
            $lines[] = '--- LAKCÍMKÁRTYA KIOLVASOTT ADATOK ---';
            $lines[] = 'nev: '.($card['full_name'] ?? $result['extracted']['full_name'] ?? '(null)');
            $lines[] = 'cim: '.($card['address'] ?? $result['extracted']['address'] ?? '(null)');
            $lines[] = 'igazolvanyszam: '.($card['id_number'] ?? $result['extracted']['id_number'] ?? '(null)');
            $lines[] = 'szuletesi_datum: '.($card['birth_date'] ?? $result['extracted']['birth_date'] ?? '(null)');
            $lines[] = 'allampolgarsag: '.($card['nationality'] ?? $result['extracted']['nationality'] ?? '(null)');
            $lines[] = '';
            $lines[] = '--- OCR SOROK ---';
            $ocrLines = $card['ocr_lines'] ?? [];
            if ($ocrLines === []) {
                $lines[] = $rawOcr !== '' ? $rawOcr : '(ÜRES – az OCR nem adott vissza szöveget)';
            } else {
                foreach ($ocrLines as $i => $ocrLine) {
                    $lines[] = '['.$i.'] '.$ocrLine;
                }
            }
            $lines[] = '';
            $lines[] = '--- TELJES RAW OCR SZÖVEG ---';
            $lines[] = $rawOcr !== '' ? $rawOcr : '(ÜRES – az OCR nem adott vissza szöveget)';
        } else {
            $lines[] = '';
            $lines[] = '--- RAW OCR ---';
            $lines[] = $rawOcr !== '' ? $rawOcr : '(üres)';
            $lines[] = '--- EXTRACTED ---';
            $lines[] = json_encode($result['extracted'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '';
        }

        $lines[] = str_repeat('=', 72);
        $lines[] = '';

        $this->writeOcrLog(implode(PHP_EOL, $lines));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function logOcrCompare(array $context): void
    {
        if (! self::OCR_DEBUG_LOG) {
            return;
        }

        $yn = static function (mixed $value): string {
            if ($value === true) {
                return 'YES';
            }
            if ($value === false) {
                return 'NO';
            }

            return 'NULL (nem egyértelmű)';
        };

        $lines = [];
        $lines[] = str_repeat('-', 72);
        $lines[] = '['.now()->format('Y-m-d H:i:s').'] COMPARE';
        $lines[] = 'provided_name: '.($context['provided_raw']['full_name_on_document'] ?? $context['provided_raw']['name'] ?? '(null)');
        $lines[] = 'provided_name_normalized: '.($context['provided_normalized']['name'] ?? '(null)');
        $lines[] = 'extracted_name: '.($context['extracted_raw']['full_name'] ?? '(null)');
        $lines[] = 'extracted_name_normalized: '.($context['extracted_normalized']['name'] ?? '(null)');
        $lines[] = 'name_matches: '.$yn($context['name_matches'] ?? null).' | reason='.($context['name_reason'] ?? '');
        $lines[] = 'provided_id: '.($context['provided_raw']['id_number'] ?? '(null)');
        $lines[] = 'provided_id_normalized: '.($context['provided_normalized']['id_number'] ?? '(null)');
        $lines[] = 'extracted_id: '.($context['extracted_raw']['id_number'] ?? '(null)');
        $lines[] = 'extracted_id_normalized: '.($context['extracted_normalized']['id_number'] ?? '(null)');
        $lines[] = 'id_matches: '.$yn($context['id_matches'] ?? null).' | reason='.($context['id_reason'] ?? '');
        $lines[] = 'ok: '.(($context['ok'] ?? false) ? 'YES' : 'NO');
        if (($context['messages'] ?? []) !== []) {
            $lines[] = 'messages: '.implode(' | ', $context['messages']);
        }
        if (($context['warnings'] ?? []) !== []) {
            $lines[] = 'warnings: '.implode(' | ', $context['warnings']);
        }
        $lines[] = 'ocr_raw_length: '.($context['ocr_raw_length'] ?? 0);
        $lines[] = str_repeat('-', 72);
        $lines[] = '';

        $this->writeOcrLog(implode(PHP_EOL, $lines));
    }

    /**
     * @param  array<string, mixed>  $context
     */
    protected function logOcr(string $event, array $context): void
    {
        if (! self::OCR_DEBUG_LOG) {
            return;
        }

        $this->writeOcrLog(
            '['.now()->format('Y-m-d H:i:s').'] [OCR] '.$event.' '
            .(json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '')
            .PHP_EOL
        );
    }

    protected function writeOcrLog(string $contents): void
    {
        try {
            $path = storage_path('logs/ocr.log');
            $dir = dirname($path);
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            file_put_contents($path, $contents, FILE_APPEND | LOCK_EX);
        } catch (\Throwable) {
            // A naplózás soha ne törje el a feltöltést.
        }
    }

    protected function personNamesMatch(string $left, string $right): bool
    {
        return $left === $right
            || str_contains($left, $right)
            || str_contains($right, $left)
            || $this->namesLooselyMatch($left, $right);
    }

    protected function ocrContainsName(string $ocrNormalized, string $providedName): bool
    {
        $parts = array_values(array_filter(
            explode(' ', $providedName),
            fn (string $part): bool => mb_strlen($part) >= 2
        ));

        if ($parts === []) {
            return false;
        }

        $hits = 0;
        foreach ($parts as $part) {
            if (str_contains($ocrNormalized, $part)) {
                $hits++;
            }
        }

        // Legalább a vezetéknév + 1 utónév, vagy az összes rész legalább 2/3-a.
        return $hits >= min(count($parts), 2) || ($hits / count($parts)) >= 0.66;
    }

    protected function ocrContainsIdNumber(string $ocrText, string $providedId): bool
    {
        $compactOcr = Str::upper(preg_replace('/[^A-Z0-9]/', '', $ocrText) ?? $ocrText);
        $providedId = Str::upper(preg_replace('/[^A-Z0-9]/', '', $providedId) ?? $providedId);

        if ($providedId === '' || strlen($providedId) < 6) {
            return false;
        }

        if (str_contains($compactOcr, $providedId)) {
            return true;
        }

        // OCR gyakran szóközöket/pontokat tesz a számjegyek közé – fuzzy keresés.
        $pattern = '/'.implode('\s*', str_split($providedId)).'/i';

        return (bool) preg_match($pattern, Str::upper($ocrText));
    }

    protected function normalizePersonName(string $value): string
    {
        $value = $this->normalizeText($value);
        $value = preg_replace('/[^a-z\s\-]/', '', $value) ?? $value;

        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }

    protected function normalizeIdNumber(string $value): string
    {
        return Str::upper(preg_replace('/\s+/', '', $value) ?? $value);
    }

    protected function namesLooselyMatch(string $left, string $right): bool
    {
        $leftParts = array_values(array_filter(explode(' ', $left)));
        $rightParts = array_values(array_filter(explode(' ', $right)));

        if ($leftParts === [] || $rightParts === []) {
            return false;
        }

        sort($leftParts);
        sort($rightParts);

        return $leftParts === $rightParts;
    }

    protected function extractIdNumber(string $raw, string $normalized): ?string
    {
        $upper = Str::upper($raw);

        // Tipikus HU személyi: 6 szám + 2 betű, vagy 2 betű + 6 szám.
        if (preg_match('/\b([A-Z]{2}\s?\d{6}|\d{6}\s?[A-Z]{2})\b/', $upper, $m)) {
            return preg_replace('/\s+/', '', $m[1]) ?? $m[1];
        }

        if (preg_match('/\b([A-Z]{2}\d{6})\b/', $upper, $m)) {
            return $m[1];
        }

        // OCR zajjal: 1 2 3 4 5 6 A B
        if (preg_match('/(\d(?:[\s.\-]?\d){5})\s*([A-Z]{2})\b/', $upper, $m)) {
            $digits = preg_replace('/\D/', '', $m[1]) ?? '';
            if (strlen($digits) === 6) {
                return $digits.$m[2];
            }
        }

        return null;
    }

    protected function extractName(string $raw, string $normalized): ?string
    {
        $fromFront = $this->extractNameFromIdFront($raw, $normalized);
        if (filled($fromFront)) {
            return $fromFront;
        }

        $lines = preg_split('/\R+/', $raw) ?: [];
        $candidates = [];

        foreach ($lines as $line) {
            $clean = trim(preg_replace('/\s+/', ' ', $line) ?? $line);

            if ($clean === '' || mb_strlen($clean) < 5) {
                continue;
            }

            if (preg_match('/(vezeteknev|family name|surname|csaladi nev)\s*[:.]?\s*(.+)$/iu', $clean, $m)) {
                $candidates[] = trim($m[2]);
            }

            if (preg_match('/(utonev|given names?|forenames?|keresztnev)\s*[:.]?\s*(.+)$/iu', $clean, $m)) {
                $last = $candidates[array_key_last($candidates)] ?? '';
                $candidates[] = trim($last.' '.$m[2]);
            }

            if ($this->looksLikeUppercasePersonName($clean)) {
                $candidates[] = Str::title(Str::lower($clean));
            }
        }

        $candidates = array_values(array_filter(array_map(
            fn (string $value): string => trim(preg_replace('/\s+/', ' ', $value) ?? $value),
            $candidates,
        ), fn (string $value): bool => mb_strlen($value) >= 5));

        return $candidates[0] ?? null;
    }

    protected function extractBirthDate(string $raw): ?string
    {
        if (preg_match('/\b(19|20)\d{2}[.\/-](0?[1-9]|1[0-2])[.\/-](0?[1-9]|[12]\d|3[01])\b/', $raw, $m)) {
            try {
                return \Illuminate\Support\Carbon::parse(str_replace(['.', '/'], '-', $m[0]))->toDateString();
            } catch (\Throwable) {
                // continue
            }
        }

        if (preg_match('/\b(0?[1-9]|[12]\d|3[01])[.\/-](0?[1-9]|1[0-2])[.\/-]((?:19|20)\d{2})\b/', $raw, $m)) {
            try {
                return \Illuminate\Support\Carbon::createFromFormat('d-m-Y', sprintf('%02d-%02d-%s', (int) $m[1], (int) $m[2], $m[3]))
                    ->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    protected function extractAddress(string $raw, string $normalized): ?string
    {
        $lines = preg_split('/\R+/', $raw) ?: [];

        // Elsődleges: „Lakóhely:” / „Lakohely:” utáni tartalom.
        for ($i = 0; $i < count($lines); $i++) {
            $clean = trim(preg_replace('/\s+/', ' ', $lines[$i]) ?? $lines[$i]);
            $norm = $this->normalizeText($clean);

            // normalizeText: Lakóhely → lakohely
            if (! str_contains($norm, 'lakohely')) {
                continue;
            }

            $after = null;
            if (preg_match('/lakohely\s*[:.]?\s*(.+)$/iu', $norm, $m) && filled(trim($m[1] ?? ''))) {
                // A cím a normalizált sorból jön; az eredeti sort használjuk, ha van tartalom a `:` után.
                if (preg_match('/:\s*(.+)$/u', $clean, $rawMatch) && filled(trim($rawMatch[1]))) {
                    $after = trim($rawMatch[1]);
                } else {
                    $after = trim($m[1]);
                }
            }

            $parts = [];
            if (filled($after)
                && ! preg_match('/^(bejelentesi|bejelentesi|kiallito|kiallito)/iu', $this->normalizeText($after))
            ) {
                $parts[] = $after;
            }

            for ($offset = 1; $offset <= 3; $offset++) {
                $next = trim((string) ($lines[$i + $offset] ?? ''));
                if ($next === '') {
                    continue;
                }

                $nextNorm = $this->normalizeText($next);
                if (str_contains($nextNorm, 'bejelentes')
                    || str_contains($nextNorm, 'kiallito')
                    || str_contains($nextNorm, 'hatosag')
                    || str_contains($nextNorm, 'igazol')
                ) {
                    break;
                }

                $parts[] = $next;

                if (preg_match('/\b\d{4}\b/', $next) || count($parts) >= 2) {
                    break;
                }
            }

            $address = trim(preg_replace('/\s+,/', ',', implode(', ', $parts)) ?? '');
            $address = trim($address, " \t\n\r\0\x0B,");

            if ($address !== '' && mb_strlen($address) >= 5) {
                return $address;
            }
        }

        // Fallback: állandó lakcím / irányítószámos sor
        $buffer = [];
        $capture = false;

        foreach ($lines as $line) {
            $clean = trim(preg_replace('/\s+/', ' ', $line) ?? $line);
            $norm = $this->normalizeText($clean);

            if ($clean === '') {
                continue;
            }

            if (str_contains($norm, 'allando lakcim')
                || str_contains($norm, 'allando lakohely')
                || str_contains($norm, 'tartozkodasi hely')
                || str_contains($norm, 'permanent address')
            ) {
                $capture = true;

                if (preg_match('/(?:lakcim|lakohely|address)\s*[:.]?\s*(.+)$/iu', $clean, $m) && filled(trim($m[1]))) {
                    $buffer[] = trim($m[1]);
                }

                continue;
            }

            if ($capture) {
                if (str_contains($norm, 'bejelentes') || str_contains($norm, 'kiallito')) {
                    break;
                }

                if (preg_match('/\b\d{4}\b/', $clean) || preg_match('/(utca|út|ter|tér|korut|körút|köz)/iu', $clean)) {
                    $buffer[] = $clean;
                }

                if (count($buffer) >= 2) {
                    break;
                }
            }
        }

        $address = trim(implode(', ', $buffer), " \t\n\r\0\x0B,");

        return $address !== '' ? $address : null;
    }

    protected function humanLabel(string $type, string $side): string
    {
        if ($type === self::TYPE_ADDRESS_CARD) {
            return 'Lakcímkártya';
        }

        if ($type === self::TYPE_ID_CARD && $side === self::SIDE_BACK) {
            return 'Személyi igazolvány (hátlap)';
        }

        if ($type === self::TYPE_ID_CARD && $side === self::SIDE_FRONT) {
            return 'Személyi igazolvány (előlap)';
        }

        return 'Ismeretlen dokumentum';
    }

    protected function successMessage(string $expectedField): string
    {
        return match ($expectedField) {
            'id_card_front_path' => 'Személyi igazolvány előlap felismerve',
            'id_card_back_path' => 'Személyi igazolvány hátlap felismerve',
            'address_card_front_path' => 'Lakcímkártya előlap felismerve',
            default => 'Okmány felismerve',
        };
    }

    protected function mismatchMessage(string $expectedField, string $detectedLabel): string
    {
        $expected = match ($expectedField) {
            'id_card_front_path' => 'személyi igazolvány előlapot',
            'id_card_back_path' => 'személyi igazolvány hátlapot',
            'address_card_front_path' => 'lakcímkártya előlapot',
            default => 'megfelelő okmányt',
        };

        return "A feltöltött kép ({$detectedLabel}) nem megfelelő ide. Kérjük, {$expected} töltsön fel.";
    }

    protected function normalizeText(?string $text): string
    {
        $text = Str::lower(Str::ascii((string) $text));
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        return trim($text);
    }

    protected function looksLikeSkin(int $r, int $g, int $b): bool
    {
        return $r > 95 && $g > 40 && $b > 20
            && max($r, $g, $b) - min($r, $g, $b) > 15
            && abs($r - $g) > 15
            && $r > $g
            && $r > $b;
    }

    /**
     * @return \GdImage|false
     */
    protected function loadImage(string $path, ?int $type)
    {
        return match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG => @imagecreatefrompng($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($path),
            default => false,
        };
    }
}
