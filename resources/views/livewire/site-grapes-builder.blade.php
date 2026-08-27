<div class="tsb-shell" wire:ignore data-grapes-root>
    <div class="tsb-toast" data-tsb-toast hidden></div>

    <div class="tsb-media-modal" data-tsb-media-modal hidden>
        <div class="tsb-media-modal__backdrop" data-tsb-media-close></div>
        <div class="tsb-media-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-media-title">
            <header class="tsb-media-modal__header">
                <h2 id="tsb-media-title" class="tsb-media-modal__title">Média kiválasztása</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-media-close aria-label="Bezárás">×</button>
            </header>

            <div class="tsb-media-modal__tabs" role="tablist">
                <button type="button" class="tsb-media-modal__tab is-active" data-tsb-media-tab="library" role="tab" aria-selected="true">Médiatár</button>
                <button type="button" class="tsb-media-modal__tab" data-tsb-media-tab="external" role="tab" aria-selected="false">Külső URL</button>
            </div>

            <div class="tsb-media-modal__body">
                <div class="tsb-media-modal__panel is-active" data-tsb-media-panel="library">
                    <div class="tsb-media-modal__toolbar">
                        <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-media-upload-btn>Feltöltés</button>
                        <input type="file" data-tsb-media-upload accept="image/*,video/mp4,video/webm,video/ogg,.svg,.jpg,.jpeg,.png,.webp,.gif,.avif" hidden>
                        <button type="button" class="tsb-btn" data-tsb-media-refresh>Frissítés</button>
                        <span class="tsb-media-modal__status" data-tsb-media-status></span>
                    </div>
                    <div class="tsb-media-grid" data-tsb-media-grid></div>
                    <p class="tsb-media-modal__empty" data-tsb-media-empty hidden>Még nincs feltöltött fájl ebben a mappában.</p>
                </div>

                <div class="tsb-media-modal__panel" data-tsb-media-panel="external">
                    <label class="tsb-media-external">
                        <span class="tsb-media-external__label">Külső kép, videó vagy embed URL</span>
                        <input type="text" class="tsb-media-external__input" data-tsb-media-external placeholder="https://… vagy /images/site/…">
                    </label>
                    <p class="tsb-media-modal__hint">YouTube / Google Maps embed, CDN kép, vagy bármilyen közvetlen fájl-URL.</p>
                    <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-media-apply-external>Alkalmaz</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tsb-rich-modal" data-tsb-rich-modal hidden>
        <div class="tsb-rich-modal__backdrop" data-tsb-rich-close></div>
        <div class="tsb-rich-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-rich-title">
            <header class="tsb-rich-modal__header">
                <h2 id="tsb-rich-title" class="tsb-rich-modal__title" data-tsb-rich-heading>Szöveg szerkesztése</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-rich-close aria-label="Bezárás">×</button>
            </header>

            <div class="tsb-media-modal__tabs" role="tablist">
                <button type="button" class="tsb-media-modal__tab is-active" data-tsb-rich-tab="visual" role="tab" aria-selected="true">Szerkesztő</button>
                <button type="button" class="tsb-media-modal__tab" data-tsb-rich-tab="source" role="tab" aria-selected="false">HTML forrás</button>
            </div>

            <div class="tsb-rich-modal__body">
                <div class="tsb-rich-modal__panel is-active" data-tsb-rich-panel="visual">
                    <div class="tsb-rich-toolbar" data-tsb-rich-toolbar>
                        <button type="button" class="tsb-btn" data-cmd="bold" title="Félkövér"><b>B</b></button>
                        <button type="button" class="tsb-btn" data-cmd="italic" title="Dőlt"><i>I</i></button>
                        <button type="button" class="tsb-btn" data-cmd="underline" title="Aláhúzott"><u>U</u></button>
                        <button type="button" class="tsb-btn" data-cmd="insertUnorderedList" title="Listás">• Lista</button>
                        <button type="button" class="tsb-btn" data-cmd="insertOrderedList" title="Számozott">1. Lista</button>
                        <button type="button" class="tsb-btn" data-cmd="createLink" title="Link">Link</button>
                        <button type="button" class="tsb-btn" data-cmd="removeFormat" title="Formázás törlése">Törlés</button>
                    </div>
                    <div class="tsb-rich-editor" data-tsb-rich-editor contenteditable="true" role="textbox" aria-multiline="true"></div>
                </div>
                <div class="tsb-rich-modal__panel" data-tsb-rich-panel="source">
                    <textarea class="tsb-rich-source" data-tsb-rich-source spellcheck="false" placeholder="<p>HTML…</p>"></textarea>
                    <p class="tsb-media-modal__hint">A HTML a canvasen és a publikus oldalon is megjelenik (pl. &lt;strong&gt;, &lt;br&gt;, &lt;a&gt;).</p>
                </div>
            </div>

            <footer class="tsb-rich-modal__footer">
                <button type="button" class="tsb-btn" data-tsb-rich-close>Mégse</button>
                <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-rich-apply>Alkalmaz</button>
            </footer>
        </div>
    </div>

    <div class="tsb-link-modal" data-tsb-link-modal hidden>
        <div class="tsb-link-modal__backdrop" data-tsb-link-close></div>
        <div class="tsb-link-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-link-title">
            <header class="tsb-rich-modal__header">
                <h2 id="tsb-link-title" class="tsb-rich-modal__title" data-tsb-link-heading>Link kiválasztása</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-link-close aria-label="Bezárás">×</button>
            </header>

            <div class="tsb-link-modal__body">
                <div class="tsb-link-modal__search">
                    <input type="search" class="tsb-link-modal__search-input" data-tsb-link-search placeholder="Keresés a publikus oldalak között…" autocomplete="off">
                </div>

                <div class="tsb-link-list" data-tsb-link-list></div>

                <div class="tsb-link-external">
                    <label class="tsb-media-external__label" for="tsb-link-external-input">Külső / egyedi URL</label>
                    <input id="tsb-link-external-input" type="text" class="tsb-media-external__input" data-tsb-link-external placeholder="https://… vagy /egyedi-utvonal">
                </div>

                <label class="tsb-link-blank">
                    <input type="checkbox" data-tsb-link-blank>
                    <span>Új lapon nyíljon meg</span>
                </label>
            </div>

            <footer class="tsb-rich-modal__footer">
                <button type="button" class="tsb-btn" data-tsb-link-close>Mégse</button>
                <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-link-apply>Alkalmaz</button>
            </footer>
        </div>
    </div>

    <div class="tsb-items-modal" data-tsb-items-modal hidden>
        <div class="tsb-items-modal__backdrop" data-tsb-items-close></div>
        <div class="tsb-items-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-items-title">
            <header class="tsb-rich-modal__header">
                <h2 id="tsb-items-title" class="tsb-rich-modal__title" data-tsb-items-heading>Tartalmak szerkesztése</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-items-close aria-label="Bezárás">×</button>
            </header>
            <div class="tsb-items-modal__body">
                <p class="tsb-items-modal__reorder-hint" data-tsb-items-reorder-hint hidden>Húzza az elemeket, vagy a ↑ ↓ gombokkal változtassa a sorrendet.</p>
                <div class="tsb-items-list" data-tsb-items-list></div>
                <button type="button" class="tsb-btn" data-tsb-items-add>+ Új elem</button>
            </div>
            <footer class="tsb-rich-modal__footer">
                <button type="button" class="tsb-btn" data-tsb-items-close>Mégse</button>
                <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-items-apply>Alkalmaz</button>
            </footer>
        </div>
    </div>

    <div class="tsb-layout-modal" data-tsb-layout-modal hidden>
        <div class="tsb-layout-modal__backdrop" data-tsb-layout-close></div>
        <div class="tsb-layout-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-layout-title">
            <header class="tsb-rich-modal__header">
                <h2 id="tsb-layout-title" class="tsb-rich-modal__title" data-tsb-layout-heading>Elrendezés választása</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-layout-close aria-label="Bezárás">×</button>
            </header>
            <div class="tsb-layout-modal__body">
                <p class="tsb-media-modal__hint">Válassz egy elrendezést. A tartalom megmarad, csak a pozíció változik.</p>
                <div class="tsb-layout-grid" data-tsb-layout-grid role="listbox" aria-label="Elrendezések"></div>
            </div>
            <footer class="tsb-rich-modal__footer">
                <button type="button" class="tsb-btn" data-tsb-layout-close>Mégse</button>
                <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-layout-apply>Alkalmaz</button>
            </footer>
        </div>
    </div>

    <div class="tsb-seo-modal" data-tsb-seo-modal hidden>
        <div class="tsb-seo-modal__backdrop" data-tsb-seo-close></div>
        <div class="tsb-seo-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="tsb-seo-title">
            <header class="tsb-rich-modal__header">
                <h2 id="tsb-seo-title" class="tsb-rich-modal__title">SEO beállítások</h2>
                <button type="button" class="tsb-media-modal__x" data-tsb-seo-close aria-label="Bezárás">×</button>
            </header>

            <div class="tsb-seo-modal__body">
                <p class="tsb-seo-modal__hint">Az ismétlődő mezők (Open Graph, Twitter) automatikusan követik a meta értékeket, amíg külön nem módosítod őket.</p>

                <section class="tsb-seo-section">
                    <h3 class="tsb-seo-section__title">Alap meta</h3>
                    <label class="tsb-seo-field">
                        <span>Meta title</span>
                        <input type="text" data-seo-field="meta_title" maxlength="70" placeholder="Oldalcím a keresőben">
                    </label>
                    <label class="tsb-seo-field">
                        <span>Meta description</span>
                        <textarea data-seo-field="meta_description" rows="3" maxlength="320" placeholder="Rövid leírás a kereső találatokhoz"></textarea>
                    </label>
                    <label class="tsb-seo-field">
                        <span>Meta keywords</span>
                        <input type="text" data-seo-field="meta_keywords" placeholder="kulcsszó1, kulcsszó2">
                    </label>
                    <label class="tsb-seo-field">
                        <span>Canonical URL</span>
                        <input type="text" data-seo-field="canonical_url" placeholder="https://…">
                    </label>
                    <label class="tsb-seo-field">
                        <span>Robots</span>
                        <select data-seo-field="robots">
                            <option value="index,follow">index, follow</option>
                            <option value="noindex,follow">noindex, follow</option>
                            <option value="index,nofollow">index, nofollow</option>
                            <option value="noindex,nofollow">noindex, nofollow</option>
                        </select>
                    </label>
                </section>

                <section class="tsb-seo-section">
                    <h3 class="tsb-seo-section__title">Open Graph</h3>
                    <label class="tsb-seo-field">
                        <span>OG title</span>
                        <input type="text" data-seo-field="og_title" placeholder="Automatikusan: meta title">
                    </label>
                    <label class="tsb-seo-field">
                        <span>OG description</span>
                        <textarea data-seo-field="og_description" rows="2" placeholder="Automatikusan: meta description"></textarea>
                    </label>
                    <div class="tsb-seo-field">
                        <span>OG image</span>
                        <div class="tsb-seo-media">
                            <input type="text" data-seo-field="og_image" placeholder="/images/site/… vagy https://…">
                            <button type="button" class="tsb-btn" data-seo-media="og_image">Tallózás</button>
                        </div>
                    </div>
                    <label class="tsb-seo-field">
                        <span>OG type</span>
                        <select data-seo-field="og_type">
                            <option value="website">website</option>
                            <option value="article">article</option>
                        </select>
                    </label>
                    <label class="tsb-seo-field">
                        <span>OG URL</span>
                        <input type="text" data-seo-field="og_url" placeholder="Automatikusan: canonical">
                    </label>
                </section>

                <section class="tsb-seo-section">
                    <h3 class="tsb-seo-section__title">Twitter Card</h3>
                    <label class="tsb-seo-field">
                        <span>Twitter card</span>
                        <select data-seo-field="twitter_card">
                            <option value="summary_large_image">summary_large_image</option>
                            <option value="summary">summary</option>
                        </select>
                    </label>
                    <label class="tsb-seo-field">
                        <span>Twitter title</span>
                        <input type="text" data-seo-field="twitter_title" placeholder="Automatikusan: OG / meta title">
                    </label>
                    <label class="tsb-seo-field">
                        <span>Twitter description</span>
                        <textarea data-seo-field="twitter_description" rows="2" placeholder="Automatikusan: OG / meta description"></textarea>
                    </label>
                    <div class="tsb-seo-field">
                        <span>Twitter image</span>
                        <div class="tsb-seo-media">
                            <input type="text" data-seo-field="twitter_image" placeholder="Automatikusan: OG image">
                            <button type="button" class="tsb-btn" data-seo-media="twitter_image">Tallózás</button>
                        </div>
                    </div>
                </section>
            </div>

            <footer class="tsb-rich-modal__footer">
                <button type="button" class="tsb-btn" data-tsb-seo-close>Mégse</button>
                <button type="button" class="tsb-btn tsb-btn--primary" data-tsb-seo-save>Mentés</button>
            </footer>
        </div>
    </div>

    <header class="tsb-topbar">
        <div class="tsb-topbar__left">
            <a href="{{ $backUrl }}" class="tsb-back" title="Vissza" aria-label="Vissza">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
            <span class="tsb-logo">Tüsiszállás</span>
            <span class="tsb-sep"></span>
            <span class="tsb-page" title="{{ $title }}">{{ $title }}</span>
            <span class="tsb-status" data-grapes-status>Betöltés…</span>
        </div>

        <div class="tsb-topbar__center">
            <div class="tsb-devices" data-tsb-devices role="group" aria-label="Eszköz előnézet">
                <button type="button" class="tsb-device is-active" data-device="Desktop">Asztal</button>
                <button type="button" class="tsb-device" data-device="Tablet">Tablet</button>
                <button type="button" class="tsb-device" data-device="Mobile">Mobil</button>
            </div>
        </div>

        <div class="tsb-topbar__right">
            @if ($previewUrl)
                <button type="button" class="tsb-btn" data-grapes-preview title="Mentés után új lapon nyílik">Előnézet</button>
            @endif
            @if (! empty($enableSeo))
                <button type="button" class="tsb-btn" data-grapes-seo title="Oldal SEO beállításai">SEO</button>
            @endif
            <button type="button" class="tsb-btn tsb-btn--primary" data-grapes-save title="Ctrl/Cmd + S">Mentés</button>
        </div>
    </header>

    <div class="tsb-loading" data-tsb-loading role="status" aria-live="polite">
        <div class="tsb-loading__spinner" aria-hidden="true"></div>
        <p>Szerkesztő betöltése…</p>
    </div>

    <div class="tsb-workspace" data-tsb-workspace hidden>
        <aside class="tsb-sidebar tsb-sidebar--left">
            <div class="tsb-sidebar__tabs" data-tsb-tabs="left">
                <button type="button" class="tsb-sidebar__tab is-active" data-tab="blocks">Elemek</button>
                <button type="button" class="tsb-sidebar__tab" data-tab="layers">Rétegek</button>
            </div>
            <div class="tsb-sidebar__panels">
                <div class="tsb-sidebar__panel is-active" data-panel="blocks">
                    <div class="tsb-blocks-search">
                        <input type="search" class="tsb-blocks-search__input" data-tsb-block-search placeholder="Blokk keresése…" autocomplete="off">
                    </div>
                    <div id="tsb-blocks" class="tsb-blocks-mount"></div>
                </div>
                <div class="tsb-sidebar__panel" data-panel="layers">
                    <div id="tsb-layers" class="tsb-mount"></div>
                </div>
            </div>
        </aside>

        <main class="tsb-canvas-area">
            <div id="tsb-editor" class="tsb-editor" data-grapes-editor></div>
        </main>

        <div class="tsb-right-wrap">
            <button type="button" class="tsb-sidebar-toggle" data-tsb-right-toggle title="Panel elrejtése" aria-expanded="true">›</button>
            <aside class="tsb-sidebar tsb-sidebar--right">
                <div class="tsb-sidebar__tabs" data-tsb-tabs="right">
                    <button type="button" class="tsb-sidebar__tab is-active" data-tab="styles">Stílus</button>
                    <button type="button" class="tsb-sidebar__tab" data-tab="traits">Tulajdonságok</button>
                </div>
                <div class="tsb-sidebar__panels">
                    <div class="tsb-sidebar__panel is-active" data-panel="styles">
                        <div id="tsb-styles" class="tsb-mount"></div>
                    </div>
                    <div class="tsb-sidebar__panel" data-panel="traits">
                        <div id="tsb-traits" class="tsb-mount"></div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

@script
<script>
    const config = {
        ...@js($payload),
        previewUrl: @js($previewUrl),
    };

    const root = document.querySelector('[data-grapes-root]');
    const statusEl = root?.querySelector('[data-grapes-status]');
    const editorEl = root?.querySelector('[data-grapes-editor]');
    const saveBtn = root?.querySelector('[data-grapes-save]');
    const previewBtn = root?.querySelector('[data-grapes-preview]');
    const loadingEl = root?.querySelector('[data-tsb-loading]');
    const workspaceEl = root?.querySelector('[data-tsb-workspace]');
    const toastEl = root?.querySelector('[data-tsb-toast]');
    const mediaModalEl = root?.querySelector('[data-tsb-media-modal]');

    const setStatus = (text) => {
        if (statusEl) statusEl.textContent = text;
    };

    const showToast = (message) => {
        if (! toastEl) return;
        toastEl.textContent = message;
        toastEl.hidden = false;
        clearTimeout(showToast._timer);
        showToast._timer = setTimeout(() => { toastEl.hidden = true; }, 3200);
    };

    const createMediaPicker = () => {
        if (! mediaModalEl) {
            return {
                open() {},
            };
        }

        const gridEl = mediaModalEl.querySelector('[data-tsb-media-grid]');
        const emptyEl = mediaModalEl.querySelector('[data-tsb-media-empty]');
        const statusElMedia = mediaModalEl.querySelector('[data-tsb-media-status]');
        const externalInput = mediaModalEl.querySelector('[data-tsb-media-external]');
        const uploadInput = mediaModalEl.querySelector('[data-tsb-media-upload]');
        let onPick = null;
        let loading = false;

        const setMediaStatus = (text) => {
            if (statusElMedia) statusElMedia.textContent = text || '';
        };

        const setTab = (tab) => {
            mediaModalEl.querySelectorAll('[data-tsb-media-tab]').forEach((btn) => {
                const active = btn.dataset.tsbMediaTab === tab;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            mediaModalEl.querySelectorAll('[data-tsb-media-panel]').forEach((panel) => {
                panel.classList.toggle('is-active', panel.dataset.tsbMediaPanel === tab);
            });
        };

        const close = () => {
            mediaModalEl.hidden = true;
            onPick = null;
            document.removeEventListener('keydown', onKeydown);
        };

        const onKeydown = (event) => {
            if (event.key === 'Escape') {
                close();
            }
        };

        const pick = (url) => {
            const value = String(url || '').trim();
            if (! value || typeof onPick !== 'function') return;
            onPick(value);
            close();
        };

        const renderItems = (items) => {
            if (! gridEl) return;
            gridEl.innerHTML = '';
            const list = Array.isArray(items) ? items : [];
            if (emptyEl) emptyEl.hidden = list.length > 0;

            list.forEach((item) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tsb-media-card';
                btn.title = item.name || item.url;

                if (item.type === 'image') {
                    const img = document.createElement('img');
                    img.src = item.url;
                    img.alt = item.name || '';
                    img.loading = 'lazy';
                    btn.appendChild(img);
                } else {
                    const badge = document.createElement('span');
                    badge.className = 'tsb-media-card__badge';
                    badge.textContent = 'VIDEO';
                    btn.appendChild(badge);
                }

                const caption = document.createElement('span');
                caption.className = 'tsb-media-card__name';
                caption.textContent = item.name || item.url;
                btn.appendChild(caption);

                btn.addEventListener('click', () => pick(item.url));
                gridEl.appendChild(btn);
            });
        };

        const loadLibrary = async () => {
            if (! config.mediaListUrl || loading) return;
            loading = true;
            setMediaStatus('Betöltés…');
            try {
                const res = await fetch(config.mediaListUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                if (! res.ok) throw new Error('list failed');
                const data = await res.json();
                renderItems(data.items || []);
                setMediaStatus(`${(data.items || []).length} fájl`);
            } catch (e) {
                console.error(e);
                setMediaStatus('Nem sikerült betölteni');
                showToast('Médiatár betöltési hiba');
            } finally {
                loading = false;
            }
        };

        const uploadFile = async (file) => {
            if (! file || ! config.mediaUploadUrl) return;
            setMediaStatus('Feltöltés…');
            const body = new FormData();
            body.append('file', file);
            try {
                const res = await fetch(config.mediaUploadUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                    body,
                });
                const data = await res.json().catch(() => ({}));
                if (! res.ok) {
                    const message = data.message
                        || (data.errors?.file ? [].concat(data.errors.file)[0] : null)
                        || `Feltöltési hiba (${res.status})`;
                    throw new Error(message);
                }
                showToast('Feltöltve');
                await loadLibrary();
                if (data.item?.url) {
                    pick(data.item.url);
                }
            } catch (e) {
                console.error(e);
                setMediaStatus('');
                showToast(e.message || 'Feltöltési hiba');
            }
        };

        mediaModalEl.querySelectorAll('[data-tsb-media-close]').forEach((el) => {
            el.addEventListener('click', close);
        });

        mediaModalEl.querySelectorAll('[data-tsb-media-tab]').forEach((btn) => {
            btn.addEventListener('click', () => setTab(btn.dataset.tsbMediaTab));
        });

        mediaModalEl.querySelector('[data-tsb-media-refresh]')?.addEventListener('click', () => loadLibrary());

        mediaModalEl.querySelector('[data-tsb-media-upload-btn]')?.addEventListener('click', (event) => {
            event.preventDefault();
            uploadInput?.click();
        });

        mediaModalEl.querySelector('[data-tsb-media-apply-external]')?.addEventListener('click', () => {
            pick(externalInput?.value || '');
        });

        externalInput?.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                pick(externalInput.value);
            }
        });

        uploadInput?.addEventListener('change', () => {
            const file = uploadInput.files?.[0];
            uploadInput.value = '';
            if (file) uploadFile(file);
        });

        return {
            open({ currentUrl = '', onSelect } = {}) {
                onPick = onSelect;
                if (externalInput) externalInput.value = currentUrl || '';
                setTab('library');
                mediaModalEl.hidden = false;
                document.addEventListener('keydown', onKeydown);
                loadLibrary();
            },
        };
    };

    const mediaPicker = createMediaPicker();

    const uploadFileToMediaLibrary = async (file) => {
        if (! file || ! config.mediaUploadUrl) {
            throw new Error('Nincs feltöltési végpont');
        }
        const body = new FormData();
        body.append('file', file);
        const res = await fetch(config.mediaUploadUrl, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            credentials: 'same-origin',
            body,
        });
        const data = await res.json().catch(() => ({}));
        if (! res.ok) {
            const message = data.message
                || (data.errors?.file ? [].concat(data.errors.file)[0] : null)
                || `Feltöltési hiba (${res.status})`;
            throw new Error(message);
        }
        const url = data.item?.url || '';
        if (! url) throw new Error('A feltöltés nem adott vissza URL-t');
        return url;
    };

    const DATA_URI_RE = /data:(?:image|video|audio)\/[a-z0-9.+-]+;base64,[a-z0-9+/=\s]+/gi;

    const uploadDataUriToMediaLibrary = async (dataUri) => {
        const res = await fetch(dataUri);
        const blob = await res.blob();
        const subtype = (blob.type.split('/')[1] || 'png').split(';')[0].replace('jpeg', 'jpg');
        const file = new File([blob], `embed-${Date.now()}.${subtype || 'png'}`, {
            type: blob.type || 'image/png',
        });
        return uploadFileToMediaLibrary(file);
    };

    const replaceDataUrisInString = async (input, cache) => {
        const value = String(input ?? '');
        if (! value.includes('data:')) {
            return { value, changed: false };
        }
        const matches = value.match(DATA_URI_RE) || [];
        if (! matches.length) {
            return { value, changed: false };
        }
        let next = value;
        let changed = false;
        for (const uri of [...new Set(matches)]) {
            if (! cache.has(uri)) {
                cache.set(uri, uploadDataUriToMediaLibrary(uri).catch((error) => {
                    console.error(error);
                    return null;
                }));
            }
            const url = await cache.get(uri);
            if (url) {
                next = next.split(uri).join(url);
                changed = true;
            }
        }
        return { value: next, changed };
    };

    /**
     * A Grapes stíluskezelő fájlválasztója base64-et ágyazhat be.
     * Mentés előtt feltöltjük a médiatárba, különben a Livewire payload túl nagy lesz.
     */
    const materializeEmbeddedMediaBeforeSave = async () => {
        const cache = new Map();
        let changed = false;

        const walk = async (components) => {
            const list = typeof components?.each === 'function'
                ? (() => { const out = []; components.each((c) => out.push(c)); return out; })()
                : (Array.isArray(components) ? components : []);

            for (const cmp of list) {
                const attrs = { ...(cmp.getAttributes?.() || {}) };
                let attrsChanged = false;
                for (const key of Object.keys(attrs)) {
                    const raw = attrs[key];
                    if (typeof raw !== 'string' || ! raw.includes('data:')) continue;
                    const result = await replaceDataUrisInString(raw, cache);
                    if (result.changed) {
                        attrs[key] = result.value;
                        attrsChanged = true;
                        changed = true;
                    }
                }
                if (attrsChanged) {
                    cmp.addAttributes?.(attrs);
                }

                const src = cmp.get?.('src');
                if (typeof src === 'string' && src.startsWith('data:')) {
                    if (! cache.has(src)) {
                        cache.set(src, uploadDataUriToMediaLibrary(src).catch((error) => {
                            console.error(error);
                            return null;
                        }));
                    }
                    const url = await cache.get(src);
                    if (url) {
                        cmp.set?.('src', url);
                        cmp.addAttributes?.({ src: url });
                        changed = true;
                    }
                }

                const style = { ...(cmp.getStyle?.() || {}) };
                let styleChanged = false;
                for (const key of Object.keys(style)) {
                    const raw = style[key];
                    if (typeof raw !== 'string' || ! raw.includes('data:')) continue;
                    const result = await replaceDataUrisInString(raw, cache);
                    if (result.changed) {
                        style[key] = result.value;
                        styleChanged = true;
                        changed = true;
                    }
                }
                if (styleChanged) {
                    cmp.setStyle?.(style);
                }

                await walk(cmp.components?.());
            }
        };

        await walk(editor.getWrapper?.()?.components?.());

        // CssComposer szabályok (háttérkép gyakran ide kerül)
        try {
            const rules = editor.CssComposer?.getAll?.();
            if (rules && typeof rules.each === 'function') {
                const ruleList = [];
                rules.each((rule) => ruleList.push(rule));
                for (const rule of ruleList) {
                    const style = { ...(rule.getStyle?.() || {}) };
                    let styleChanged = false;
                    for (const key of Object.keys(style)) {
                        const raw = style[key];
                        if (typeof raw !== 'string' || ! raw.includes('data:')) continue;
                        const result = await replaceDataUrisInString(raw, cache);
                        if (result.changed) {
                            style[key] = result.value;
                            styleChanged = true;
                            changed = true;
                        }
                    }
                    if (styleChanged) {
                        rule.setStyle?.(style);
                    }
                }
            }
        } catch (e) {
            console.error(e);
        }

        return changed;
    };

    const slimProjectDataForSync = (projectData) => {
        try {
            const clone = JSON.parse(JSON.stringify(projectData || {}));
            if (Array.isArray(clone.assets)) {
                clone.assets = clone.assets.filter((asset) => ! String(asset?.src || '').startsWith('data:'));
            }
            const walk = (node) => {
                if (! node || typeof node !== 'object') return;
                if (Array.isArray(node)) {
                    node.forEach(walk);
                    return;
                }
                Object.keys(node).forEach((key) => {
                    const val = node[key];
                    if (typeof val === 'string' && val.includes('data:')) {
                        node[key] = val.replace(DATA_URI_RE, '');
                    } else if (val && typeof val === 'object') {
                        walk(val);
                    }
                });
            };
            walk(clone);
            return clone;
        } catch (e) {
            console.error(e);
            return projectData || {};
        }
    };

    const createSeoEditor = () => {
        const modal = root?.querySelector('[data-tsb-seo-modal]');
        const seoBtn = root?.querySelector('[data-grapes-seo]');
        if (! modal || ! seoBtn || ! config.enableSeo) {
            return;
        }

        const fields = {};
        modal.querySelectorAll('[data-seo-field]').forEach((el) => {
            fields[el.dataset.seoField] = el;
        });

        const emptySeo = () => ({
            meta_title: '',
            meta_description: '',
            meta_keywords: '',
            canonical_url: '',
            robots: 'index,follow',
            og_title: '',
            og_description: '',
            og_image: '',
            og_type: 'website',
            og_url: '',
            twitter_card: 'summary_large_image',
            twitter_title: '',
            twitter_description: '',
            twitter_image: '',
        });

        let seoState = { ...emptySeo(), ...(config.seo || {}) };
        const lastSynced = {};
        const mirrors = [
            { source: 'meta_title', targets: ['og_title', 'twitter_title'] },
            { source: 'meta_description', targets: ['og_description', 'twitter_description'] },
            { source: 'canonical_url', targets: ['og_url'] },
            { source: 'og_title', targets: ['twitter_title'] },
            { source: 'og_description', targets: ['twitter_description'] },
            { source: 'og_image', targets: ['twitter_image'] },
        ];

        const readForm = () => {
            const data = emptySeo();
            Object.keys(data).forEach((key) => {
                if (fields[key]) {
                    data[key] = String(fields[key].value || '').trim();
                }
            });
            return data;
        };

        const writeForm = (data) => {
            Object.keys(fields).forEach((key) => {
                fields[key].value = data[key] ?? '';
                lastSynced[key] = data[key] ?? '';
            });
        };

        const applyMirrors = (sourceKey) => {
            const sourceVal = String(fields[sourceKey]?.value || '');
            mirrors
                .filter((m) => m.source === sourceKey)
                .forEach((m) => {
                    m.targets.forEach((targetKey) => {
                        const target = fields[targetKey];
                        if (! target) return;
                        const current = String(target.value || '');
                        const prev = lastSynced[sourceKey] ?? '';
                        if (current === '' || current === prev) {
                            target.value = sourceVal;
                            lastSynced[targetKey] = sourceVal;
                        }
                    });
                });
            lastSynced[sourceKey] = sourceVal;
        };

        const close = () => {
            modal.hidden = true;
            document.removeEventListener('keydown', onKeydown);
        };

        const onKeydown = (event) => {
            if (event.key === 'Escape') {
                close();
            }
        };

        const open = () => {
            const data = { ...emptySeo(), ...seoState };
            if (! data.meta_title) {
                data.meta_title = config.seoPageTitle || '';
            }
            if (! data.canonical_url) {
                data.canonical_url = config.seoPageUrl || '';
            }
            if (! data.og_title) data.og_title = data.meta_title;
            if (! data.og_description) data.og_description = data.meta_description;
            if (! data.og_url) data.og_url = data.canonical_url;
            if (! data.twitter_title) data.twitter_title = data.og_title || data.meta_title;
            if (! data.twitter_description) {
                data.twitter_description = data.og_description || data.meta_description;
            }
            if (! data.twitter_image) data.twitter_image = data.og_image;
            writeForm(data);
            modal.hidden = false;
            document.addEventListener('keydown', onKeydown);
            fields.meta_title?.focus();
        };

        const save = async () => {
            const payload = readForm();
            try {
                const saved = await $wire.saveSeo(payload);
                seoState = { ...emptySeo(), ...(saved || payload) };
                config.seo = seoState;
                showToast('SEO mentve');
                close();
            } catch (e) {
                console.error(e);
                showToast('SEO mentési hiba');
            }
        };

        Object.keys(fields).forEach((key) => {
            fields[key].addEventListener('input', () => applyMirrors(key));
            fields[key].addEventListener('change', () => applyMirrors(key));
        });

        modal.querySelectorAll('[data-seo-media]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.seoMedia;
                const input = fields[key];
                if (! input) return;
                mediaPicker.open({
                    currentUrl: input.value || '',
                    onSelect: (url) => {
                        input.value = url;
                        applyMirrors(key);
                    },
                });
            });
        });

        modal.querySelectorAll('[data-tsb-seo-close]').forEach((el) => {
            el.addEventListener('click', close);
        });
        modal.querySelector('[data-tsb-seo-save]')?.addEventListener('click', save);
        seoBtn.addEventListener('click', open);
    };

    createSeoEditor();

    const createRichEditor = () => {
        const modal = root?.querySelector('[data-tsb-rich-modal]');
        if (! modal) {
            return { open() {} };
        }

        const headingEl = modal.querySelector('[data-tsb-rich-heading]');
        const visualEl = modal.querySelector('[data-tsb-rich-editor]');
        const sourceEl = modal.querySelector('[data-tsb-rich-source]');
        let onApply = null;
        let syncingTabs = false;

        const sanitizeRichHtml = (html) => {
            try {
                const doc = new DOMParser().parseFromString(`<div id="tsb-rich-root">${String(html ?? '')}</div>`, 'text/html');
                const wrap = doc.getElementById('tsb-rich-root');
                if (! wrap) return String(html ?? '');
                wrap.querySelectorAll('script, object, embed').forEach((el) => el.remove());
                wrap.querySelectorAll('*').forEach((el) => {
                    [...el.attributes].forEach((attr) => {
                        if (/^on/i.test(attr.name)) {
                            el.removeAttribute(attr.name);
                        }
                    });
                });
                return wrap.innerHTML;
            } catch (e) {
                return String(html ?? '');
            }
        };

        const setTab = (tab) => {
            if (syncingTabs) return;
            syncingTabs = true;
            try {
                if (tab === 'source' && visualEl && sourceEl) {
                    sourceEl.value = visualEl.innerHTML;
                }
                if (tab === 'visual' && visualEl && sourceEl) {
                    visualEl.innerHTML = sourceEl.value;
                }
                modal.querySelectorAll('[data-tsb-rich-tab]').forEach((btn) => {
                    const active = btn.dataset.tsbRichTab === tab;
                    btn.classList.toggle('is-active', active);
                    btn.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                modal.querySelectorAll('[data-tsb-rich-panel]').forEach((panel) => {
                    panel.classList.toggle('is-active', panel.dataset.tsbRichPanel === tab);
                });
            } finally {
                syncingTabs = false;
            }
        };

        const close = () => {
            modal.hidden = true;
            onApply = null;
            document.removeEventListener('keydown', onKeydown);
        };

        const onKeydown = (event) => {
            if (event.key === 'Escape') close();
        };

        const apply = () => {
            const activeSource = modal.querySelector('[data-tsb-rich-panel="source"].is-active');
            let html = activeSource ? (sourceEl?.value || '') : (visualEl?.innerHTML || '');
            html = sanitizeRichHtml(html);
            if (typeof onApply === 'function') {
                onApply(html);
            }
            close();
        };

        modal.querySelectorAll('[data-tsb-rich-close]').forEach((el) => {
            el.addEventListener('click', close);
        });
        modal.querySelectorAll('[data-tsb-rich-tab]').forEach((btn) => {
            btn.addEventListener('click', () => setTab(btn.dataset.tsbRichTab));
        });
        modal.querySelector('[data-tsb-rich-apply]')?.addEventListener('click', apply);

        modal.querySelector('[data-tsb-rich-toolbar]')?.addEventListener('click', (event) => {
            const btn = event.target.closest('[data-cmd]');
            if (! btn) return;
            event.preventDefault();
            const cmd = btn.dataset.cmd;
            visualEl?.focus();
            if (cmd === 'createLink') {
                const selection = window.getSelection?.();
                const range = selection && selection.rangeCount ? selection.getRangeAt(0).cloneRange() : null;
                let currentUrl = 'https://';
                if (selection?.anchorNode) {
                    const anchor = selection.anchorNode.nodeType === 1
                        ? selection.anchorNode.closest?.('a')
                        : selection.anchorNode.parentElement?.closest?.('a');
                    if (anchor?.getAttribute?.('href')) {
                        currentUrl = anchor.getAttribute('href');
                    }
                }
                linkPicker.open({
                    title: 'Link beszúrása',
                    url: currentUrl === 'https://' ? '' : currentUrl,
                    newTab: false,
                    onSave: ({ url, newTab }) => {
                        visualEl?.focus();
                        if (range && selection) {
                            selection.removeAllRanges();
                            selection.addRange(range);
                        }
                        document.execCommand('createLink', false, url);
                        if (newTab) {
                            const sel = window.getSelection?.();
                            const a = sel?.anchorNode?.nodeType === 1
                                ? sel.anchorNode.closest?.('a')
                                : sel?.anchorNode?.parentElement?.closest?.('a');
                            if (a) {
                                a.setAttribute('target', '_blank');
                                a.setAttribute('rel', 'noopener noreferrer');
                            }
                        }
                    },
                });
                return;
            }
            document.execCommand(cmd, false, null);
        });

        return {
            open({ title = 'Szöveg szerkesztése', html = '', onSave } = {}) {
                onApply = onSave;
                if (headingEl) headingEl.textContent = title;
                const safe = sanitizeRichHtml(html);
                if (visualEl) visualEl.innerHTML = safe;
                if (sourceEl) sourceEl.value = safe;
                setTab('visual');
                modal.hidden = false;
                document.addEventListener('keydown', onKeydown);
                setTimeout(() => visualEl?.focus(), 50);
            },
        };
    };

    const richEditor = createRichEditor();

    const createLinkPicker = () => {
        const modal = root?.querySelector('[data-tsb-link-modal]');
        if (! modal) {
            return { open() {} };
        }

        const listEl = modal.querySelector('[data-tsb-link-list]');
        const searchEl = modal.querySelector('[data-tsb-link-search]');
        const externalEl = modal.querySelector('[data-tsb-link-external]');
        const blankEl = modal.querySelector('[data-tsb-link-blank]');
        const headingEl = modal.querySelector('[data-tsb-link-heading]');
        const links = Array.isArray(config.publicLinks) ? config.publicLinks : [];
        let onApply = null;
        let selectedUrl = '';

        const close = () => {
            modal.hidden = true;
            onApply = null;
            document.removeEventListener('keydown', onKeydown);
        };

        const onKeydown = (event) => {
            if (event.key === 'Escape') close();
        };

        const renderList = (query = '') => {
            if (! listEl) return;
            listEl.innerHTML = '';
            const q = String(query || '').trim().toLowerCase();
            const filtered = links.filter((item) => {
                if (! q) return true;
                return String(item.label || '').toLowerCase().includes(q)
                    || String(item.url || '').toLowerCase().includes(q)
                    || String(item.group || '').toLowerCase().includes(q);
            });

            if (! filtered.length) {
                const empty = document.createElement('p');
                empty.className = 'tsb-media-modal__empty';
                empty.textContent = 'Nincs találat.';
                listEl.appendChild(empty);
                return;
            }

            let lastGroup = null;
            filtered.forEach((item) => {
                if (item.group && item.group !== lastGroup) {
                    lastGroup = item.group;
                    const group = document.createElement('div');
                    group.className = 'tsb-link-list__group';
                    group.textContent = item.group;
                    listEl.appendChild(group);
                }

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tsb-link-list__item' + (item.url === selectedUrl ? ' is-selected' : '');
                btn.innerHTML = `<span class="tsb-link-list__label"></span><span class="tsb-link-list__url"></span>`;
                btn.querySelector('.tsb-link-list__label').textContent = item.label || item.url;
                btn.querySelector('.tsb-link-list__url').textContent = item.url;
                btn.addEventListener('click', () => {
                    selectedUrl = item.url;
                    if (externalEl) externalEl.value = item.url;
                    renderList(searchEl?.value || '');
                });
                listEl.appendChild(btn);
            });
        };

        const apply = () => {
            const url = String(externalEl?.value || selectedUrl || '').trim();
            if (! url) {
                showToast('Adj meg vagy válassz egy linket');
                return;
            }
            if (typeof onApply === 'function') {
                onApply({
                    url,
                    newTab: !! blankEl?.checked,
                });
            }
            close();
        };

        modal.querySelectorAll('[data-tsb-link-close]').forEach((el) => {
            el.addEventListener('click', close);
        });
        modal.querySelector('[data-tsb-link-apply]')?.addEventListener('click', apply);
        searchEl?.addEventListener('input', () => renderList(searchEl.value));
        externalEl?.addEventListener('input', () => {
            selectedUrl = externalEl.value.trim();
            renderList(searchEl?.value || '');
        });

        return {
            open({ title = 'Link kiválasztása', url = '', newTab = false, onSave } = {}) {
                onApply = onSave;
                selectedUrl = String(url || '');
                if (headingEl) headingEl.textContent = title;
                if (externalEl) externalEl.value = selectedUrl;
                if (blankEl) blankEl.checked = !! newTab;
                if (searchEl) searchEl.value = '';
                renderList('');
                modal.hidden = false;
                document.addEventListener('keydown', onKeydown);
                setTimeout(() => searchEl?.focus(), 40);
            },
        };
    };

    const linkPicker = createLinkPicker();

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

    const parseItemsJson = (raw) => {
        if (Array.isArray(raw)) return raw;
        if (! raw || typeof raw !== 'string') return [];
        let text = raw.trim();
        if (text.includes('&quot;') || text.includes('&#39;') || text.includes('&amp;')) {
            const ta = document.createElement('textarea');
            ta.innerHTML = text;
            text = ta.value;
        }
        try {
            const parsed = JSON.parse(text);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    };

    /** Régi Képsáv: data-image1…4 → items lista */
    const galleryItemsFromAttrs = (attrs) => {
        let items = parseItemsJson(attrs['data-items'] || '[]');
        if (items.length > 0) return items;
        const legacy = [];
        for (let i = 1; i <= 4; i += 1) {
            const url = String(attrs[`data-image${i}`] || '').trim();
            if (url) legacy.push({ image: url, alt: '' });
        }
        return legacy;
    };

    const contentIconSvg = (iconId, className = 'ts-icon') => {
        const icons = Array.isArray(config.contentIcons) ? config.contentIcons : [];
        const found = icons.find((icon) => icon.id === iconId);
        if (! found?.svg) return '';
        return String(found.svg).replace(/class="[^"]*"/, `class="${className}"`);
    };

    const contentItemVisual = (item, className) => {
        const image = String(item?.image || '').trim();
        if (image) {
            return `<span class="${className} ${className}--media" aria-hidden="true"><img src="${escapeHtml(image)}" alt=""></span>`;
        }
        return contentIconSvg(item?.icon || '', className);
    };

    const isVideoMediaUrl = (url) => /\.(mp4|webm|ogg)(\?|$)/i.test(String(url || ''));

    const baGalleryZoomIcon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="M16 16l4 4"/></svg>';

    const renderBaGallerySlide = (item) => {
        const before = String(item?.image || item?.image_before || item?.media_url || '').trim();
        const after = String(item?.image_after || '').trim();
        const alt = String(item?.alt || '');

        if (! before && ! after) {
            return '';
        }

        if (before && after) {
            const label = alt ? `Nagyítás: ${escapeHtml(alt)}` : 'Nagyítás';
            return (
                `<figure class="ts-ba-gallery__slide ts-ba-gallery__slide--compare" data-ts-ba-slide data-lightbox-type="compare" data-lightbox-before="${escapeHtml(before)}" data-lightbox-after="${escapeHtml(after)}" data-lightbox-alt="${escapeHtml(alt)}">`
                + `<div class="ts-ba-gallery__compare" style="--split:50%">`
                + `<div class="ts-ba-gallery__layer ts-ba-gallery__layer--after">`
                + `<img src="${escapeHtml(after)}" alt="${escapeHtml(alt)}" loading="lazy">`
                + `<span class="ts-ba-gallery__tag ts-ba-gallery__tag--after">Utána</span>`
                + `</div>`
                + `<div class="ts-ba-gallery__layer ts-ba-gallery__layer--before">`
                + `<img src="${escapeHtml(before)}" alt="${escapeHtml(alt)}" loading="lazy">`
                + `<span class="ts-ba-gallery__tag ts-ba-gallery__tag--before">Előtte</span>`
                + `</div>`
                + `<div class="ts-ba-gallery__handle" aria-hidden="true"><span class="ts-ba-gallery__knob">‹ ›</span></div>`
                + `<input type="range" class="ts-ba-gallery__range" min="0" max="100" value="50" aria-label="Előtte és utána összehasonlítás">`
                + `</div>`
                + `<button type="button" class="ts-ba-gallery__zoom ts-ba-gallery__zoom--icon" data-ts-ba-zoom aria-label="${label}">${baGalleryZoomIcon}</button>`
                + `</figure>`
            );
        }

        const url = before || after;
        const label = alt ? `Kép nagyítása: ${escapeHtml(alt)}` : 'Kép nagyítása';
        return (
            `<figure class="ts-ba-gallery__slide" data-ts-ba-slide data-lightbox-type="image" data-lightbox-src="${escapeHtml(url)}" data-lightbox-alt="${escapeHtml(alt)}">`
            + `<button type="button" class="ts-ba-gallery__zoom ts-ba-gallery__zoom--fill" data-ts-ba-zoom aria-label="${label}">`
            + `<img class="ts-ba-gallery__img" src="${escapeHtml(url)}" alt="${escapeHtml(alt)}" loading="lazy">`
            + `</button>`
            + `<button type="button" class="ts-ba-gallery__zoom ts-ba-gallery__zoom--icon" data-ts-ba-zoom aria-label="${label}">${baGalleryZoomIcon}</button>`
            + `</figure>`
        );
    };

    const renderItemsHtml = (kind, items, options = {}) => {
        const list = Array.isArray(items) ? items : [];
        if (kind === 'testimonials') {
            const cards = list.map((item) => (
                `<blockquote class="ts-quote__card"><p>„${escapeHtml(item.quote || '')}”</p><cite>${escapeHtml(item.cite || '')}</cite></blockquote>`
            )).join('');
            const layout = String(options.layout || 'marquee');
            if (layout === 'single') {
                return list.slice(0, 1).map((item) => (
                    `<blockquote class="ts-quote__card"><p>„${escapeHtml(item.quote || '')}”</p><cite>${escapeHtml(item.cite || '')}</cite></blockquote>`
                )).join('');
            }
            if (layout === 'grid') {
                return cards;
            }
            // Dupla lista a folyamatos marquee-hez
            return cards + cards;
        }
        if (kind === 'faq') {
            return list.map((item, index) => (
                `<details class="ts-faq__item"${index === 0 ? ' open' : ''}>`
                + `<summary>${escapeHtml(item.q || '')}</summary>`
                + `<p>${escapeHtml(item.a || '')}</p>`
                + `</details>`
            )).join('');
        }
        if (kind === 'features') {
            return list.map((item) => (
                `<article class="ts-feature">`
                + contentItemVisual(item, 'ts-feature__icon')
                + `<h3 class="ts-feature__title">${escapeHtml(item.title || '')}</h3>`
                + `<p class="ts-feature__text">${escapeHtml(item.text || '')}</p>`
                + `</article>`
            )).join('');
        }
        if (kind === 'icon-list') {
            return list.map((item) => (
                `<article class="ts-icon-item">`
                + contentItemVisual(item, 'ts-icon-item__icon')
                + `<h3 class="ts-icon-item__title">${escapeHtml(item.title || '')}</h3>`
                + `<p class="ts-icon-item__text">${escapeHtml(item.text || '')}</p>`
                + `</article>`
            )).join('');
        }
        if (kind === 'hero-slides') {
            return list.map((item) => {
                const mediaUrl = item.media_url || '';
                const mediaType = isVideoMediaUrl(mediaUrl) ? 'video' : 'image';
                const safeUrl = escapeHtml(mediaUrl);
                const eyebrow = String(item.eyebrow || '').trim();
                const title = String(item.title || '').trim();
                const lead = String(item.lead || '').trim();
                const primaryLabel = String(item.primary_label || '').trim();
                const secondaryLabel = String(item.secondary_label || '').trim();
                let actions = '';
                if (primaryLabel) {
                    actions += `<a class="ts-btn ts-btn--primary" href="${escapeHtml(item.primary_href || '#')}">${escapeHtml(primaryLabel)}</a>`;
                }
                if (secondaryLabel) {
                    actions += `<a class="ts-btn ts-btn--inverse" href="${escapeHtml(item.secondary_href || '#')}">${escapeHtml(secondaryLabel)}</a>`;
                }
                return (
                    `<article class="ts-hero-slider__slide" data-ts-slide data-media-type="${mediaType}">`
                    + `<div class="ts-hero-slider__media" aria-hidden="true">`
                    + `<img class="ts-hero-slider__img" src="${safeUrl}" alt="">`
                    + `<video class="ts-hero-slider__video" autoplay muted loop playsinline><source src="${safeUrl}" type="video/mp4"></video>`
                    + `<div class="ts-hero-slider__overlay" data-ts-hero-overlay></div>`
                    + `</div>`
                    + `<div class="ts-hero-slider__inner">`
                    + (eyebrow ? `<p class="ts-hero-slider__eyebrow">${escapeHtml(eyebrow)}</p>` : '')
                    + `<h1 class="ts-hero-slider__title">${escapeHtml(title)}</h1>`
                    + (lead ? `<p class="ts-hero-slider__lead">${escapeHtml(lead)}</p>` : '')
                    + (actions ? `<div class="ts-hero-slider__actions">${actions}</div>` : '')
                    + `</div>`
                    + `</article>`
                );
            }).join('');
        }
        if (kind === 'gallery') {
            return list.map((item) => {
                const url = String(item.image || item.media_url || '').trim();
                const alt = String(item.alt || '');
                if (! url) {
                    return `<figure class="ts-gallery__item ts-gallery__item--empty" aria-hidden="true"></figure>`;
                }
                const label = alt ? `Kép megnyitása: ${escapeHtml(alt)}` : 'Kép megnyitása';
                return (
                    `<figure class="ts-gallery__item">`
                    + `<button type="button" class="ts-gallery__trigger" data-ts-gallery-src="${escapeHtml(url)}" aria-label="${label}">`
                    + `<img src="${escapeHtml(url)}" alt="${escapeHtml(alt)}" loading="lazy">`
                    + `</button></figure>`
                );
            }).join('');
        }
        if (kind === 'ba-gallery') {
            const filled = list.filter((item) => {
                const before = String(item?.image || item?.image_before || item?.media_url || '').trim();
                const after = String(item?.image_after || '').trim();
                return Boolean(before || after);
            });
            if (! filled.length) {
                return '<figure class="ts-ba-gallery__slide ts-ba-gallery__slide--empty" data-ts-ba-slide aria-hidden="true"></figure>';
            }
            return filled.map((item) => renderBaGallerySlide(item)).join('');
        }
        return '';
    };

    const syncItemsContainers = (model, attrs) => {
        const layout = attrs['data-layout'] || '';
        const applyToEl = (container) => {
            const key = container.getAttribute('data-ts-items') || 'items';
            const kind = container.getAttribute('data-ts-items-kind') || key;
            const attrName = `data-${String(key).replaceAll('_', '-')}`;
            const items = kind === 'gallery'
                ? galleryItemsFromAttrs(attrs)
                : parseItemsJson(attrs[attrName] || attrs['data-items'] || '[]');
            const html = renderItemsHtml(kind, items, { layout });
            container.innerHTML = html;
        };

        const el = model.getEl?.() || model.view?.el;
        if (el) {
            el.querySelectorAll('[data-ts-items]').forEach(applyToEl);
        }

        if (typeof model.find === 'function') {
            try {
                model.find('[data-ts-items]').forEach((cmp) => {
                    const key = cmp.getAttributes?.()?.['data-ts-items']
                        || cmp.getEl?.()?.getAttribute?.('data-ts-items')
                        || 'items';
                    const kind = cmp.getAttributes?.()?.['data-ts-items-kind']
                        || cmp.getEl?.()?.getAttribute?.('data-ts-items-kind')
                        || key;
                    const attrName = `data-${String(key).replaceAll('_', '-')}`;
                    const items = kind === 'gallery'
                        ? galleryItemsFromAttrs(attrs)
                        : parseItemsJson(attrs[attrName] || attrs['data-items'] || '[]');
                    cmp.components?.(renderItemsHtml(kind, items, { layout }));
                });
            } catch (e) {
                // ignore
            }
        }

        if (el?.classList?.contains('ts-hero-slider')) {
            // Szekció-szintű surface overlay ne maradjon
            Array.from(el.children || []).forEach((child) => {
                if (! (child instanceof HTMLElement)) return;
                const isSurface = child.hasAttribute('data-ts-bg-overlay')
                    || child.classList.contains('ts-surface-overlay');
                const isSlideOverlay = child.hasAttribute('data-ts-hero-overlay')
                    || child.classList.contains('ts-hero-slider__overlay');
                if (isSurface && ! isSlideOverlay) {
                    child.remove();
                }
                // Legacy üres stage
                if (child.hasAttribute('data-ts-slider-stage') || child.classList.contains('ts-hero-slider__stage')) {
                    child.remove();
                }
            });
            const overlay = attrs['data-overlay'];
            if (overlay != null && overlay !== '') {
                el.style.setProperty('--ts-hero-overlay-opacity', String(overlay));
            }
            const overlayColor = attrs['data-overlay-color'];
            if (overlayColor) {
                const solid = String(overlayColor).replace(
                    /rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*[\d.]+\s*)?\)/i,
                    'rgb($1, $2, $3)'
                );
                el.style.setProperty('--ts-overlay-color', solid);
            } else {
                el.style.removeProperty('--ts-overlay-color');
            }
            el.querySelectorAll('.ts-hero-slider__overlay, [data-ts-hero-overlay]').forEach((node) => {
                node.style.opacity = '1';
                node.style.removeProperty('background');
                node.style.removeProperty('background-color');
                node.style.removeProperty('background-image');
            });
            setTimeout(() => window.TsHeroSlider?.initOne?.(el), 0);
        }
        if (el?.classList?.contains('ts-gallery')) {
            setTimeout(() => window.TsGallery?.initOne?.(el), 0);
        }
        if (el?.classList?.contains('ts-ba-gallery')) {
            setTimeout(() => window.TsBaGallery?.initOne?.(el), 0);
        }
    };

    const createItemsEditor = () => {
        const modal = root?.querySelector('[data-tsb-items-modal]');
        if (! modal) {
            return { open() {} };
        }

        const listEl = modal.querySelector('[data-tsb-items-list]');
        const headingEl = modal.querySelector('[data-tsb-items-heading]');
        let fields = [];
        let items = [];
        let onApply = null;

        const close = () => {
            modal.hidden = true;
            onApply = null;
            document.removeEventListener('keydown', onKeydown);
        };

        const onKeydown = (event) => {
            if (event.key === 'Escape') close();
        };

        const readItemsFromDom = () => {
            if (! listEl) return [];
            return [...listEl.querySelectorAll('[data-tsb-item]')].map((row) => {
                const item = {};
                fields.forEach((field) => {
                    const input = row.querySelector(`[data-field="${field.key}"]`);
                    item[field.key] = input ? input.value : '';
                    if (field.type === 'icon') {
                        const imageInput = row.querySelector('[data-field="image"]');
                        item.image = imageInput ? imageInput.value : '';
                    }
                });
                return item;
            });
        };

        const moveItem = (fromIndex, toIndex) => {
            items = readItemsFromDom();
            if (fromIndex === toIndex || fromIndex < 0 || toIndex < 0 || fromIndex >= items.length || toIndex >= items.length) {
                return;
            }
            const [moved] = items.splice(fromIndex, 1);
            items.splice(toIndex, 0, moved);
            render();
        };

        const bindRowReorder = (row, index, total) => {
            const upBtn = row.querySelector('[data-tsb-item-up]');
            const downBtn = row.querySelector('[data-tsb-item-down]');
            if (upBtn) upBtn.disabled = index <= 0;
            if (downBtn) downBtn.disabled = index >= total - 1;

            upBtn?.addEventListener('click', (event) => {
                event.preventDefault();
                moveItem(index, index - 1);
            });
            downBtn?.addEventListener('click', (event) => {
                event.preventDefault();
                moveItem(index, index + 1);
            });

            const handle = row.querySelector('[data-tsb-item-drag]');
            handle?.addEventListener('dragstart', (event) => {
                event.dataTransfer?.setData('text/plain', String(index));
                if (event.dataTransfer) event.dataTransfer.effectAllowed = 'move';
                row.classList.add('is-dragging');
            });
            handle?.addEventListener('dragend', () => {
                row.classList.remove('is-dragging');
                listEl?.querySelectorAll('.tsb-items-row.is-drop-target').forEach((el) => {
                    el.classList.remove('is-drop-target');
                });
            });

            row.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
                row.classList.add('is-drop-target');
            });
            row.addEventListener('dragleave', (event) => {
                if (event.relatedTarget instanceof Node && row.contains(event.relatedTarget)) return;
                row.classList.remove('is-drop-target');
            });
            row.addEventListener('drop', (event) => {
                event.preventDefault();
                row.classList.remove('is-drop-target');
                const from = parseInt(event.dataTransfer?.getData('text/plain') || '', 10);
                if (Number.isNaN(from) || from === index) return;
                moveItem(from, index);
            });
        };

        const renderIconPicker = (field, item = {}) => {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-items-field tsb-items-field--icon';
            const span = document.createElement('span');
            span.textContent = field.label || 'Ikon vagy kép';
            wrap.appendChild(span);

            const hint = document.createElement('p');
            hint.className = 'tsb-items-field__hint';
            hint.textContent = 'Válasszon SVG ikont, vagy töltsön fel saját képet. Ha van kép, az felülírja az ikont.';
            wrap.appendChild(hint);

            const imageRow = document.createElement('div');
            imageRow.className = 'tsb-items-media-row';
            const imageInput = document.createElement('input');
            imageInput.type = 'text';
            imageInput.className = 'tsb-media-external__input';
            imageInput.dataset.field = 'image';
            imageInput.value = item.image || '';
            imageInput.placeholder = 'Saját kép URL…';
            const pickBtn = document.createElement('button');
            pickBtn.type = 'button';
            pickBtn.className = 'tsb-btn';
            pickBtn.textContent = 'Kép';
            const clearBtn = document.createElement('button');
            clearBtn.type = 'button';
            clearBtn.className = 'tsb-btn';
            clearBtn.textContent = 'Kép törlése';
            const syncImageUi = () => {
                const hasImage = !! String(imageInput.value || '').trim();
                wrap.classList.toggle('has-image', hasImage);
                clearBtn.hidden = ! hasImage;
            };
            pickBtn.addEventListener('click', () => {
                mediaPicker.open({
                    currentUrl: imageInput.value || '',
                    onSelect: (url) => {
                        imageInput.value = url || '';
                        if (url) {
                            hidden.value = 'none';
                            grid.querySelectorAll('.tsb-icon-picker__btn').forEach((el) => el.classList.remove('is-active'));
                            const noneBtn = grid.querySelector('[data-icon-id="none"]');
                            noneBtn?.classList.add('is-active');
                        }
                        syncImageUi();
                    },
                });
            });
            clearBtn.addEventListener('click', () => {
                imageInput.value = '';
                syncImageUi();
            });
            imageInput.addEventListener('input', syncImageUi);
            imageRow.appendChild(imageInput);
            imageRow.appendChild(pickBtn);
            imageRow.appendChild(clearBtn);
            wrap.appendChild(imageRow);

            const iconLabel = document.createElement('span');
            iconLabel.className = 'tsb-items-field__sub';
            iconLabel.textContent = 'Vagy SVG ikon';
            wrap.appendChild(iconLabel);

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.dataset.field = field.key;
            hidden.value = item[field.key] || 'none';
            wrap.appendChild(hidden);

            const grid = document.createElement('div');
            grid.className = 'tsb-icon-picker';
            const icons = Array.isArray(config.contentIcons) ? config.contentIcons : [];
            icons.forEach((icon) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tsb-icon-picker__btn' + (hidden.value === icon.id ? ' is-active' : '');
                btn.dataset.iconId = icon.id;
                btn.title = icon.name || icon.id;
                btn.setAttribute('aria-label', icon.name || icon.id);
                if (icon.id === 'none' || ! icon.svg) {
                    btn.textContent = '—';
                } else {
                    btn.innerHTML = icon.svg;
                }
                btn.addEventListener('click', () => {
                    hidden.value = icon.id;
                    imageInput.value = '';
                    syncImageUi();
                    grid.querySelectorAll('.tsb-icon-picker__btn').forEach((el) => el.classList.remove('is-active'));
                    btn.classList.add('is-active');
                });
                grid.appendChild(btn);
            });
            wrap.appendChild(grid);
            syncImageUi();
            return wrap;
        };

        const renderMediaField = (field, value) => {
            const label = document.createElement('label');
            label.className = 'tsb-items-field tsb-items-field--media';
            const span = document.createElement('span');
            span.textContent = field.label || field.key;
            label.appendChild(span);

            const row = document.createElement('div');
            row.className = 'tsb-items-media-row';
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'tsb-media-external__input';
            input.dataset.field = field.key;
            input.value = value || '';
            input.placeholder = '/images/…';
            const pickBtn = document.createElement('button');
            pickBtn.type = 'button';
            pickBtn.className = 'tsb-btn';
            pickBtn.textContent = 'Média';
            pickBtn.addEventListener('click', () => {
                mediaPicker.open({
                    currentUrl: input.value || '',
                    onSelect: (url) => {
                        input.value = url || '';
                    },
                });
            });
            row.appendChild(input);
            row.appendChild(pickBtn);
            label.appendChild(row);
            return label;
        };

        const render = () => {
            if (! listEl) return;
            listEl.innerHTML = '';
            const reorderHint = modal.querySelector('[data-tsb-items-reorder-hint]');
            if (reorderHint) reorderHint.hidden = items.length <= 1;

            items.forEach((item, index) => {
                const row = document.createElement('div');
                row.className = 'tsb-items-row';
                row.dataset.tsbItem = '1';

                const head = document.createElement('div');
                head.className = 'tsb-items-row__head';

                const meta = document.createElement('div');
                meta.className = 'tsb-items-row__meta';
                const dragBtn = document.createElement('button');
                dragBtn.type = 'button';
                dragBtn.className = 'tsb-items-row__drag';
                dragBtn.dataset.tsbItemDrag = '1';
                dragBtn.draggable = true;
                dragBtn.setAttribute('aria-label', 'Húzza a sorrend módosításához');
                dragBtn.title = 'Húzza a sorrend módosításához';
                dragBtn.textContent = '⋮⋮';
                const label = document.createElement('strong');
                label.textContent = `#${index + 1}`;
                meta.appendChild(dragBtn);
                meta.appendChild(label);
                head.appendChild(meta);

                const actions = document.createElement('div');
                actions.className = 'tsb-items-row__actions';

                const upBtn = document.createElement('button');
                upBtn.type = 'button';
                upBtn.className = 'tsb-items-row__move';
                upBtn.dataset.tsbItemUp = '1';
                upBtn.setAttribute('aria-label', 'Feljebb');
                upBtn.title = 'Feljebb';
                upBtn.textContent = '↑';

                const downBtn = document.createElement('button');
                downBtn.type = 'button';
                downBtn.className = 'tsb-items-row__move';
                downBtn.dataset.tsbItemDown = '1';
                downBtn.setAttribute('aria-label', 'Lejjebb');
                downBtn.title = 'Lejjebb';
                downBtn.textContent = '↓';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'tsb-btn';
                removeBtn.textContent = 'Törlés';
                removeBtn.addEventListener('click', () => {
                    items = readItemsFromDom().filter((_, i) => i !== index);
                    render();
                });

                actions.appendChild(upBtn);
                actions.appendChild(downBtn);
                actions.appendChild(removeBtn);
                head.appendChild(actions);
                row.appendChild(head);

                fields.forEach((field) => {
                    if (field.type === 'icon') {
                        row.appendChild(renderIconPicker(field, item));
                        return;
                    }
                    if (field.type === 'media') {
                        row.appendChild(renderMediaField(field, item[field.key] || ''));
                        return;
                    }
                    const fieldLabel = document.createElement('label');
                    fieldLabel.className = 'tsb-items-field';
                    const span = document.createElement('span');
                    span.textContent = field.label || field.key;
                    fieldLabel.appendChild(span);
                    let input;
                    if (field.type === 'textarea') {
                        input = document.createElement('textarea');
                        input.rows = 3;
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                    }
                    input.className = field.type === 'textarea' ? 'tsb-rich-source' : 'tsb-media-external__input';
                    input.dataset.field = field.key;
                    input.value = item[field.key] || '';
                    fieldLabel.appendChild(input);
                    row.appendChild(fieldLabel);
                });

                bindRowReorder(row, index, items.length);
                listEl.appendChild(row);
            });
        };

        modal.querySelectorAll('[data-tsb-items-close]').forEach((el) => {
            el.addEventListener('click', close);
        });
        modal.querySelector('[data-tsb-items-add]')?.addEventListener('click', () => {
            items = readItemsFromDom();
            const blank = {};
            fields.forEach((field) => { blank[field.key] = ''; });
            if (fields.some((field) => field.type === 'icon')) {
                blank.image = '';
            }
            items.push(blank);
            render();
        });
        modal.querySelector('[data-tsb-items-apply]')?.addEventListener('click', () => {
            items = readItemsFromDom();
            if (typeof onApply === 'function') {
                onApply(items);
            }
            close();
        });

        return {
            open({ title = 'Tartalmak szerkesztése', itemFields = [], value = [], onSave, addLabel = '+ Új elem' } = {}) {
                onApply = onSave;
                fields = Array.isArray(itemFields) && itemFields.length
                    ? itemFields
                    : [{ key: 'text', label: 'Szöveg', type: 'text' }];
                items = parseItemsJson(value);
                if (! items.length) {
                    const blank = {};
                    fields.forEach((field) => { blank[field.key] = ''; });
                    if (fields.some((field) => field.type === 'icon')) {
                        blank.image = '';
                    }
                    items = [blank];
                }
                if (headingEl) headingEl.textContent = title;
                const addBtn = modal.querySelector('[data-tsb-items-add]');
                if (addBtn) addBtn.textContent = addLabel;
                render();
                modal.hidden = false;
                document.addEventListener('keydown', onKeydown);
            },
        };
    };

    const itemsEditor = createItemsEditor();

    const layoutWireframes = window.TSB_LAYOUT_WIRES || {};

    const wireframeForLayout = (id, wireKey) => {
        const key = String(wireKey || id || '');
        if (layoutWireframes[key]) return layoutWireframes[key];
        if (layoutWireframes[id]) return layoutWireframes[id];
        return layoutWireframes['hero-bottom'] || layoutWireframes.bottom || '';
    };

    const createLayoutPicker = () => {
        const modal = root?.querySelector('[data-tsb-layout-modal]');
        if (! modal) {
            return { open() {} };
        }

        const gridEl = modal.querySelector('[data-tsb-layout-grid]');
        const headingEl = modal.querySelector('[data-tsb-layout-heading]');
        let pending = null;
        let selectedId = 'bottom';

        const close = () => {
            modal.hidden = true;
            pending = null;
        };

        const render = (options, currentId) => {
            selectedId = currentId || options?.[0]?.id || 'bottom';
            gridEl.innerHTML = '';

            (options || []).forEach((opt) => {
                const id = String(opt.id ?? opt.value ?? '');
                const name = opt.name ?? opt.label ?? id;
                const hint = opt.hint || '';
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'tsb-layout-card' + (id === selectedId ? ' is-selected' : '');
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', id === selectedId ? 'true' : 'false');
                btn.dataset.layoutId = id;
                btn.innerHTML = `
                    <div class="tsb-layout-card__wire">${wireframeForLayout(id, opt.wire)}</div>
                    <div class="tsb-layout-card__meta">
                        <strong></strong>
                        <span></span>
                    </div>`;
                btn.querySelector('strong').textContent = name;
                const hintEl = btn.querySelector('span');
                if (hint) {
                    hintEl.textContent = hint;
                } else {
                    hintEl.remove();
                }
                btn.addEventListener('click', () => {
                    selectedId = id;
                    gridEl.querySelectorAll('.tsb-layout-card').forEach((card) => {
                        const on = card.dataset.layoutId === selectedId;
                        card.classList.toggle('is-selected', on);
                        card.setAttribute('aria-selected', on ? 'true' : 'false');
                    });
                });
                gridEl.appendChild(btn);
            });
        };

        modal.querySelectorAll('[data-tsb-layout-close]').forEach((el) => {
            el.addEventListener('click', close);
        });
        modal.querySelector('[data-tsb-layout-apply]')?.addEventListener('click', () => {
            pending?.onSelect?.(selectedId);
            close();
        });

        return {
            open({ title, options, value, onSelect }) {
                pending = { onSelect };
                if (headingEl) headingEl.textContent = title || 'Elrendezés választása';
                render(options || [], value || 'bottom');
                modal.hidden = false;
            },
        };
    };

    const layoutPicker = createLayoutPicker();

    const hrefTargetAttrName = (hrefKeyOrAttr) => {
        const key = String(hrefKeyOrAttr || '')
            .replace(/^data-/, '')
            .replaceAll('-', '_');
        const targetKey = key.endsWith('_href')
            ? key.replace(/_href$/, '_target')
            : `${key}_target`;
        return `data-${targetKey.replaceAll('_', '-')}`;
    };

    const isBlankSocialUrl = (url) => {
        const value = String(url ?? '').trim();
        return value === '' || value === '#';
    };

    const resolveBoundHref = (rawValue, schemeHint = '', paramKey = '') => {
        const value = String(rawValue || '').trim();
        if (! value) return '';
        if (/^(https?:|mailto:|tel:|sms:|\/\/|#|\/)/i.test(value)) {
            return value;
        }
        let scheme = String(schemeHint || '').trim().toLowerCase();
        if (! scheme && paramKey) {
            if (paramKey === 'topbar_email' || paramKey === 'email') scheme = 'mailto';
            else if (paramKey === 'topbar_phone' || paramKey === 'phone') scheme = 'tel';
        }
        if (! scheme && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) scheme = 'mailto';
        if (! scheme && /^[\d\s+().-]+$/.test(value) && value.replace(/\D/g, '').length >= 6) scheme = 'tel';
        if (scheme === 'tel' || scheme === 'mailto') {
            const normalized = scheme === 'tel' ? value.replace(/\s+/g, '') : value;
            return `${scheme}:${normalized}`;
        }
        return value;
    };

    const bindTabs = (side) => {
        const tabs = root.querySelectorAll(`[data-tsb-tabs="${side}"] .tsb-sidebar__tab`);
        const panels = side === 'left'
            ? root.querySelectorAll('.tsb-sidebar--left [data-panel]')
            : root.querySelectorAll('.tsb-sidebar--right [data-panel]');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((t) => t.classList.toggle('is-active', t === tab));
                panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.panel === tab.dataset.tab));
                if (side === 'right' && tab.dataset.tab === 'traits') {
                    setTimeout(() => {
                        if (typeof injectTraitsGuide === 'function') {
                            injectTraitsGuide();
                        }
                        if (typeof bindTraitCategoryAccordion === 'function') {
                            bindTraitCategoryAccordion();
                        }
                    }, 20);
                }
            });
        });
    };

    const activateRightTab = (tabName) => {
        const tabs = root.querySelectorAll('[data-tsb-tabs="right"] .tsb-sidebar__tab');
        const panels = root.querySelectorAll('.tsb-sidebar--right [data-panel]');
        tabs.forEach((tab) => tab.classList.toggle('is-active', tab.dataset.tab === tabName));
        panels.forEach((panel) => panel.classList.toggle('is-active', panel.dataset.panel === tabName));
        if (workspaceEl?.classList.contains('is-right-collapsed')) {
            workspaceEl.classList.remove('is-right-collapsed');
            const toggle = root.querySelector('[data-tsb-right-toggle]');
            if (toggle) {
                toggle.setAttribute('aria-expanded', 'true');
                toggle.title = 'Panel elrejtése';
                toggle.textContent = '›';
            }
        }
    };

    bindTabs('left');
    bindTabs('right');

    root.querySelector('[data-tsb-right-toggle]')?.addEventListener('click', (event) => {
        const collapsed = workspaceEl.classList.toggle('is-right-collapsed');
        event.currentTarget.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        event.currentTarget.title = collapsed ? 'Panel megjelenítése' : 'Panel elrejtése';
        event.currentTarget.textContent = collapsed ? '‹' : '›';
    });

    if (! editorEl || typeof grapesjs === 'undefined') {
        setStatus('GrapesJS nem töltődött be');
        if (loadingEl) loadingEl.hidden = true;
        return;
    }

    let dirty = false;
    let syncing = false;
    let ready = false;

    const markDirty = () => {
        if (! ready) return;
        dirty = true;
        setStatus('Előnézet – nincs mentve');
    };

    window.addEventListener('beforeunload', (event) => {
        if (! dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });

    const globalColors = Array.isArray(config.globalColors) ? config.globalColors : [];
    const colorPalette = globalColors
        .map((item) => item?.value)
        .filter((value) => typeof value === 'string' && value.length > 0);
    const fontFamilies = Array.isArray(config.fontFamilies) ? config.fontFamilies : [];
    const fontFamilyProperty = fontFamilies.length
        ? { extend: 'font-family', options: fontFamilies }
        : 'font-family';

    const editor = grapesjs.init({
        container: editorEl,
        height: '100%',
        width: 'auto',
        fromElement: false,
        storageManager: false,
        noticeOnUnload: false,
        showOffsets: true,
        panels: { defaults: [] },
        blockManager: { appendTo: '#tsb-blocks' },
        layerManager: { appendTo: '#tsb-layers' },
        styleManager: {
            appendTo: '#tsb-styles',
            // Clear gomb: eltávolítja a felülírást → visszaáll a téma / osztály alapértelmezése
            clearProperties: true,
            // Általános (display/float/position) elrejtve – laikus szerkesztőnek zavaró.
            // Az eltolás (top/right/bottom/left) a Méret szektorban van.
            sectors: [
                {
                    id: 'dimension',
                    name: 'Méret',
                    open: false,
                    properties: [
                        'width',
                        'height',
                        'max-width',
                        'min-height',
                        'margin',
                        'padding',
                        {
                            id: 'eltolas',
                            type: 'composite',
                            property: 'offset',
                            label: 'Eltolás',
                            // Csak a rész-tulajdonságok íródjanak (top/right/…), ne egy offset shorthand
                            detached: true,
                            properties: [
                                {
                                    type: 'number',
                                    units: ['px', '%', 'em', 'rem', 'vh', 'vw'],
                                    default: 'auto',
                                    property: 'top',
                                },
                                {
                                    type: 'number',
                                    units: ['px', '%', 'em', 'rem', 'vh', 'vw'],
                                    default: 'auto',
                                    property: 'right',
                                },
                                {
                                    type: 'number',
                                    units: ['px', '%', 'em', 'rem', 'vh', 'vw'],
                                    default: 'auto',
                                    property: 'bottom',
                                },
                                {
                                    type: 'number',
                                    units: ['px', '%', 'em', 'rem', 'vh', 'vw'],
                                    default: 'auto',
                                    property: 'left',
                                },
                            ],
                        },
                    ],
                },
                {
                    id: 'typography',
                    name: 'Tipográfia',
                    open: false,
                    properties: [
                        fontFamilyProperty,
                        'font-size',
                        'font-weight',
                        'letter-spacing',
                        'color',
                        'line-height',
                        'text-align',
                        'text-shadow',
                    ],
                },
                {
                    id: 'decorations',
                    name: 'Díszítés',
                    open: false,
                    properties: [
                        'background-color',
                        'border-radius',
                        'border',
                        'box-shadow',
                        'background',
                    ],
                },
                {
                    id: 'flex',
                    name: 'Flex',
                    open: false,
                    properties: [
                        'flex-direction',
                        'flex-wrap',
                        'justify-content',
                        'align-items',
                        'align-content',
                        'order',
                        'flex-basis',
                        'flex-grow',
                        'flex-shrink',
                        'align-self',
                    ],
                },
                {
                    id: 'extra',
                    name: 'Extra',
                    open: false,
                    properties: [
                        'opacity',
                        'transition',
                        'transform',
                    ],
                },
            ],
        },
        traitManager: { appendTo: '#tsb-traits' },
        i18n: {
            locale: 'hu',
            detectLocale: false,
            messages: {
                hu: {
                    styleManager: {
                        empty: 'Jelöljön ki egy elemet a stílusok szerkesztéséhez',
                        layer: 'Réteg',
                        fileButton: 'Képek',
                        sectors: {
                            dimension: 'Méret',
                            typography: 'Tipográfia',
                            decorations: 'Díszítés',
                            flex: 'Flex',
                            extra: 'Extra',
                        },
                        properties: {
                            eltolas: 'Eltolás',
                            top: 'Fent',
                            right: 'Jobb',
                            left: 'Bal',
                            bottom: 'Lent',
                            width: 'Szélesség',
                            height: 'Magasság',
                            'max-width': 'Max. szélesség',
                            'min-height': 'Min. magasság',
                            margin: 'Margó',
                            'margin-top-sub': 'Fent',
                            'margin-right-sub': 'Jobb',
                            'margin-bottom-sub': 'Lent',
                            'margin-left-sub': 'Bal',
                            padding: 'Belső margó',
                            'padding-top-sub': 'Fent',
                            'padding-right-sub': 'Jobb',
                            'padding-bottom-sub': 'Lent',
                            'padding-left-sub': 'Bal',
                            'font-family': 'Betűtípus',
                            'font-size': 'Betűméret',
                            'font-weight': 'Betűvastagság',
                            'letter-spacing': 'Betűköz',
                            color: 'Szövegszín',
                            'line-height': 'Sormagasság',
                            'text-align': 'Igazítás',
                            'text-decoration': 'Szövegdísz',
                            'text-shadow': 'Szövegárnyék',
                            'text-shadow-h': 'X',
                            'text-shadow-v': 'Y',
                            'text-shadow-blur': 'Életlenség',
                            'text-shadow-color': 'Szín',
                            'background-color': 'Háttérszín',
                            'border-radius': 'Saroklekerekítés',
                            'border-top-left-radius-sub': 'Bal fent',
                            'border-top-right-radius-sub': 'Jobb fent',
                            'border-bottom-left-radius-sub': 'Bal lent',
                            'border-bottom-right-radius-sub': 'Jobb lent',
                            border: 'Keret',
                            'border-width-sub': 'Vastagság',
                            'border-style-sub': 'Stílus',
                            'border-color-sub': 'Szín',
                            'box-shadow': 'Árnyék',
                            'box-shadow-h': 'X',
                            'box-shadow-v': 'Y',
                            'box-shadow-blur': 'Életlenség',
                            'box-shadow-spread': 'Terjedés',
                            'box-shadow-color': 'Szín',
                            'box-shadow-type': 'Típus',
                            background: 'Háttér',
                            'background-image-sub': 'Kép',
                            'background-repeat-sub': 'Ismétlés',
                            'background-position-sub': 'Pozíció',
                            'background-attachment-sub': 'Rögzítés',
                            'background-size-sub': 'Méret',
                            opacity: 'Átlátszóság',
                            transition: 'Átmenet',
                            'transition-property-sub': 'Tulajdonság',
                            'transition-duration-sub': 'Időtartam',
                            'transition-timing-function-sub': 'Időzítés',
                            transform: 'Transzformáció',
                            'transform-rotate-x': 'Forgatás X',
                            'transform-rotate-y': 'Forgatás Y',
                            'transform-rotate-z': 'Forgatás Z',
                            'transform-scale-x': 'Nagyítás X',
                            'transform-scale-y': 'Nagyítás Y',
                            'transform-scale-z': 'Nagyítás Z',
                            'flex-direction': 'Irány',
                            'flex-wrap': 'Tördelés',
                            'justify-content': 'Főtengely igazítás',
                            'align-items': 'Kereszttengely igazítás',
                            'align-content': 'Sorok igazítása',
                            order: 'Sorrend',
                            'flex-basis': 'Alapméret',
                            'flex-grow': 'Növekedés',
                            'flex-shrink': 'Zsugorodás',
                            'align-self': 'Saját igazítás',
                        },
                    },
                },
            },
        },
        colorPicker: {
            // A Style Manager a jobb oldalsávban van; ha a picker az editor konténerbe
            // kerül, overflow / stacking miatt nem látszik. Mindig a body-ra.
            appendTo: 'body',
            palette: colorPalette.length ? [colorPalette] : [
                ['#0f2920', '#1f3d32', '#1c1917', '#8d6b3e', '#f7f4ef', '#ffffff', '#000000'],
            ],
            showPalette: true,
            showPaletteOnly: false,
            showAlpha: true,
            preferredFormat: 'hex',
            allowEmpty: true,
            cancelText: 'Mégse',
            chooseText: 'Ok',
            clearText: 'Ürítés',
        },
        // Stíluskezelő „Képek” gombja: fájlt a médiatárba tölti (URL), ne base64-et ágyazzon be.
        assetManager: {
            upload: true,
            autoAdd: 1,
            uploadName: 'file',
            uploadFile: async (event) => {
                const files = event?.dataTransfer?.files || event?.target?.files || [];
                if (! files.length || ! config.mediaUploadUrl) return;

                for (const file of Array.from(files)) {
                    try {
                        const url = await uploadFileToMediaLibrary(file);
                        if (url) {
                            editor.AssetManager.add({ src: url, type: file.type?.startsWith('video/') ? 'video' : 'image', name: file.name });
                            showToast('Feltöltve');
                        }
                    } catch (e) {
                        console.error(e);
                        showToast(e.message || 'Képfeltöltési hiba');
                    }
                }
            },
        },
        canvas: {
            styles: config.canvasStyles || [],
        },
        deviceManager: {
            devices: [
                // width: canvas szélesség; widthMedia üres → Desktop szabályok media query NÉLKÜL
                { id: 'Desktop', name: 'Asztal', width: '100%', widthMedia: '' },
                { id: 'Tablet', name: 'Tablet', width: '768px', widthMedia: '992px' },
                { id: 'Mobile', name: 'Mobil', width: '375px', widthMedia: '480px' },
            ],
        },
        selectorManager: {
            componentFirst: true,
        },
    });

    if (config.dynamicCss) {
        editor.addStyle(config.dynamicCss);
    }
    if (config.visibilityCss) {
        editor.addStyle(config.visibilityCss);
    }
    if (config.layoutCss) {
        editor.addStyle(config.layoutCss);
    }
    const headerCanvasReadableCss = `
header.site-nav[data-site-nav],
header.site-nav[data-site-nav] .ts-nav-topbar {
  background: #fff !important;
  color: #3d3d3d !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
  border-bottom: 1px solid #ececec !important;
}
header.site-nav[data-site-nav] .ts-nav-links a,
header.site-nav[data-site-nav] .ts-nav-links .nav-link,
header.site-nav[data-site-nav] .ts-nav-brand,
header.site-nav[data-site-nav] .ts-nav-brand-text,
header.site-nav[data-site-nav] .ts-nav-topbar__item,
header.site-nav[data-site-nav] .ts-menu-placeholder,
header.site-nav[data-site-nav] .ts-nav-toggle {
  color: inherit !important;
}
header.site-nav[data-site-nav] .ts-menu-placeholder {
  border: 1px dashed color-mix(in srgb, currentColor 50%, transparent) !important;
}
header.site-nav[data-site-nav] .ts-nav-topbar__contact {
  opacity: 0.88;
}
header.site-nav[data-site-nav] .ts-nav-social--needs-url {
  outline: 2px dashed #e67e22 !important;
  outline-offset: 2px;
  cursor: help;
}
`;
    editor.addStyle(`
.ts-surface-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background: var(--ts-overlay-color, var(--color-primary));
}
`);

    const appendThemeStylesToCanvas = () => {
        const doc = editor.Canvas?.getDocument?.();
        if (! doc?.head) return;

        [config.themeCssUrl, config.customCssUrl].filter(Boolean).forEach((href) => {
            let link = Array.from(doc.querySelectorAll('link[data-ts-theme-css]'))
                .find((node) => node.getAttribute('data-ts-theme-css') === href);
            if (! link) {
                link = doc.createElement('link');
                link.rel = 'stylesheet';
                link.href = href;
                link.setAttribute('data-ts-theme-css', href);
            }
            doc.head.appendChild(link);
        });

        // Grapes CSS a téma linkek UTÁN: így a Style Manager háttérszínje
        // felülírja a téma szekció-hátterét. A gombok !important miatt maradnak.
        Array.from(doc.head.querySelectorAll('style')).forEach((styleEl) => {
            doc.head.appendChild(styleEl);
        });
    };

    editor.on('load', appendThemeStylesToCanvas);
    editor.on('canvas:frame:load', appendThemeStylesToCanvas);
    editor.on('component:add', () => setTimeout(appendThemeStylesToCanvas, 0));

    // Kép cseréje: dupla katt a saját médiatárral (ne a Grapes beépített, feltöltés nélküli panellel)
    editor.on('component:dblclick', (component) => {
        if (! component) return;
        const tag = String(component.get?.('tagName') || '').toLowerCase();
        const type = String(component.get?.('type') || '');
        const attrs = component.getAttributes?.() || {};
        const isImage = tag === 'img' || type === 'image' || type === 'ts-bg-img' || type === 'ts-image'
            || attrs['data-gjs-type'] === 'ts-image'
            || component.getEl?.()?.closest?.('[data-gjs-type="ts-image"]');

        if (! isImage) return;

        let target = component;
        if (type !== 'ts-image' && attrs['data-gjs-type'] !== 'ts-image') {
            let parent = typeof component.parent === 'function' ? component.parent() : null;
            while (parent) {
                const pAttrs = parent.getAttributes?.() || {};
                if (pAttrs['data-gjs-type'] === 'ts-image' || parent.get?.('type') === 'ts-image') {
                    target = parent;
                    break;
                }
                parent = typeof parent.parent === 'function' ? parent.parent() : null;
            }
        }

        const current = target.getAttributes?.()?.['data-media-url']
            || target.getAttributes?.()?.src
            || target.get?.('src')
            || attrs.src
            || '';

        mediaPicker.open({
            currentUrl: current,
            onSelect: (url) => {
                if (! url) return;
                if (target.getAttributes?.()?.['data-gjs-type'] === 'ts-image' || target.get?.('type') === 'ts-image') {
                    target.addAttributes({ 'data-media-url': url });
                    scheduleBlockSync(target);
                    return;
                }
                setComponentSrc(target, url);
                if (typeof target.addAttributes === 'function') {
                    target.addAttributes({ src: url });
                }
            },
        });
    });

    const decorateStyleClearButtons = () => {
        document.querySelectorAll('#tsb-styles .gjs-sm-clear').forEach((btn) => {
            if (btn.dataset.tsbClearDecorated === '1') return;
            btn.dataset.tsbClearDecorated = '1';
            btn.setAttribute('title', 'Vissza a téma alapértelmezettjére (csak ez a mező)');
            btn.setAttribute('aria-label', 'Alapértelmezett érték visszaállítása (téma)');
            btn.classList.add('tsb-sm-clear');
        });
    };

    /** @type {string|null} */
    let lastColorPropId = null;

    const propertyIdFromEl = (propEl) => {
        if (! propEl) return null;
        const cls = Array.from(propEl.classList).find((name) => name.startsWith('gjs-sm-property__'));
        return cls ? cls.replace(/^gjs-sm-property__/, '') : null;
    };

    const findColorPropertyEl = (propId = null) => {
        const nodes = Array.from(document.querySelectorAll('#tsb-styles .gjs-sm-property'));
        if (propId) {
            const exact = nodes.find((el) => el.classList.contains(`gjs-sm-property__${propId}`));
            if (exact) return exact;
        }
        const active = document.querySelector('.sp-replacer.sp-active')?.closest('.gjs-sm-property');
        if (active) return active;
        const focus = document.querySelector('#tsb-styles .gjs-sm-property:focus-within');
        if (focus?.querySelector('.sp-replacer, .gjs-field-color, .gjs-field-colorp')) return focus;
        return null;
    };

    const rememberColorPropertyFromEvent = (event) => {
        const propEl = event.target?.closest?.('#tsb-styles .gjs-sm-property');
        if (! propEl) return;
        if (! propEl.querySelector('.sp-replacer, .gjs-field-color, .gjs-field-colorp, .gjs-sm-field.gjs-sm-color')) return;
        const id = propertyIdFromEl(propEl);
        if (id) lastColorPropId = id;
    };

    document.addEventListener('pointerdown', rememberColorPropertyFromEvent, true);
    document.addEventListener('focusin', rememberColorPropertyFromEvent, true);

    const eachStyleSector = (callback) => {
        const sectors = editor.StyleManager?.getSectors?.();
        if (! sectors) return;
        if (typeof sectors.each === 'function') {
            sectors.each(callback);
            return;
        }
        if (typeof sectors.forEach === 'function') {
            sectors.forEach(callback);
            return;
        }
        if (Array.isArray(sectors)) {
            sectors.forEach(callback);
        }
    };

    const clearColorProperty = (propId) => {
        if (! propId) return false;
        let cleared = false;
        const propEl = findColorPropertyEl(propId);

        // 1) Grapes beépített clear gomb (ugyanarra a mezőre)
        const clearBtn = propEl?.querySelector('.gjs-sm-clear');
        if (clearBtn) {
            clearBtn.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true, view: window }));
            cleared = true;
        }

        // 2) StyleManager property.clear()
        try {
            eachStyleSector((sector) => {
                const prop = sector?.getProperty?.(propId);
                if (prop && typeof prop.clear === 'function') {
                    prop.clear();
                    cleared = true;
                }
            });
        } catch (e) {
            // ignore
        }

        // 3) Komponens / kijelölt target stílusának törlése
        const targets = [];
        const selected = editor.getSelected?.();
        if (selected) targets.push(selected);
        try {
            const wrapper = editor.getWrapper?.();
            const smTargets = editor.StyleManager?.getSelected?.()
                || editor.StyleManager?.getTargets?.()?.()
                || null;
            if (Array.isArray(smTargets)) targets.push(...smTargets);
            else if (smTargets) targets.push(smTargets);
            if (wrapper && ! targets.length) targets.push(wrapper);
        } catch (e) {
            // ignore
        }

        targets.forEach((target) => {
            if (! target) return;
            try {
                if (typeof target.removeStyle === 'function') {
                    target.removeStyle(propId);
                    cleared = true;
                }
            } catch (e) {
                // ignore
            }
            try {
                const style = { ...(target.getStyle?.() || {}) };
                if (Object.prototype.hasOwnProperty.call(style, propId)) {
                    delete style[propId];
                    target.setStyle?.(style);
                    cleared = true;
                }
            } catch (e) {
                // ignore
            }
            try {
                target.addStyle?.({ [propId]: '' });
                target.removeStyle?.(propId);
            } catch (e) {
                // ignore
            }
        });

        // 4) UI mező ürítése a panelen
        if (propEl) {
            propEl.querySelectorAll('.gjs-field-colorp-c, .gjs-field-color-picker, .gjs-field-colorp').forEach((node) => {
                if (node instanceof HTMLElement) {
                    node.style.backgroundColor = '';
                    node.style.backgroundImage = '';
                }
            });
            propEl.querySelectorAll('input').forEach((input) => {
                if (! (input instanceof HTMLInputElement)) return;
                if (input.type === 'hidden') return;
                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        }

        return cleared;
    };

    const closeSpectrumPopups = () => {
        try {
            const $ = window.jQuery || window.$;
            if ($?.fn?.spectrum) {
                $('.sp-replacer').spectrum('hide');
            }
        } catch (e) {
            // ignore
        }
        document.querySelectorAll('.sp-container').forEach((node) => {
            node.classList.add('sp-hidden');
            // Ne legyen tartós inline display:none – a Spectrum show() ezt nem mindig törli
            if (node.style.display === 'none') {
                node.style.removeProperty('display');
            }
        });
        document.querySelectorAll('.sp-replacer.sp-active').forEach((node) => {
            node.classList.remove('sp-active');
        });
    };

    const ensureSpectrumVisible = () => {
        document.querySelectorAll('.sp-container').forEach((node) => {
            if (node.classList.contains('sp-hidden')) return;
            // Bent ragadt display:none feloldása
            if (node.style.display === 'none') {
                node.style.removeProperty('display');
            }
            // Body-n legyen, ne az editor overflow-ja mögött
            if (node.parentElement !== document.body) {
                document.body.appendChild(node);
            }
        });
        injectSpectrumThemeReset();
    };

    const resetActiveColorToThemeDefault = () => {
        const propEl = findColorPropertyEl(lastColorPropId);
        const propId = lastColorPropId || propertyIdFromEl(propEl) || propertyIdFromEl(findColorPropertyEl());
        if (! propId) {
            console.warn('[tsb] Nincs aktív színmező a visszaállításhoz');
            return false;
        }
        lastColorPropId = propId;
        const ok = clearColorProperty(propId);
        closeSpectrumPopups();
        decorateStyleClearButtons();
        return ok;
    };

    const injectSpectrumThemeReset = () => {
        document.querySelectorAll('.sp-container').forEach((container) => {
            if (container.classList.contains('sp-hidden')) return;
            if (container.style.display === 'none') return;

            const currentPropEl = findColorPropertyEl(lastColorPropId);
            const currentPropId = lastColorPropId || propertyIdFromEl(currentPropEl);
            if (currentPropId) lastColorPropId = currentPropId;

            let row = container.querySelector('.tsb-sp-theme-reset');
            if (! row) {
                const paletteHost = container.querySelector('.sp-palette-container')
                    || container.querySelector('.sp-picker-container')
                    || container;
                row = document.createElement('div');
                row.className = 'tsb-sp-theme-reset';
                row.innerHTML = ''
                    + '<div class="tsb-sp-theme-reset__label">Globális / téma</div>'
                    + '<button type="button" class="tsb-sp-theme-reset__btn">'
                    + '<span class="tsb-sp-theme-reset__icon" aria-hidden="true"></span>'
                    + '<span>Alapértelmezett</span>'
                    + '</button>';
                const btn = row.querySelector('button');
                btn.setAttribute('title', 'Csak ezt a színt állítja vissza a megjelenés (téma) alapértelmezésére');
                btn.addEventListener('pointerdown', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    const id = lastColorPropId
                        || propertyIdFromEl(findColorPropertyEl())
                        || btn.dataset.propId
                        || '';
                    if (id) {
                        lastColorPropId = id;
                        btn.dataset.propId = id;
                    }
                }, true);
                btn.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    if (btn.dataset.propId) lastColorPropId = btn.dataset.propId;
                    resetActiveColorToThemeDefault();
                });

                const palette = container.querySelector('.sp-palette-container');
                if (palette?.parentNode) {
                    palette.parentNode.insertBefore(row, palette);
                } else {
                    paletteHost.insertBefore(row, paletteHost.firstChild);
                }
            }

            const btn = row.querySelector('button');
            if (btn && currentPropId) {
                btn.dataset.propId = currentPropId;
            }
        });
    };

    // Ne figyeljük az attribute változásokat: a setAttribute / class / style
    // módosítás végtelen MutationObserver-ciklust okozott, és a szerkesztő
    // soha nem ért el a „ready” állapotig (örök „Szerkesztő betöltése…”).
    let spectrumResetTimer = 0;
    const scheduleSpectrumUiRefresh = () => {
        if (spectrumResetTimer) return;
        spectrumResetTimer = window.setTimeout(() => {
            spectrumResetTimer = 0;
            ensureSpectrumVisible();
            decorateStyleClearButtons();
        }, 50);
    };
    const spectrumResetObserver = new MutationObserver(scheduleSpectrumUiRefresh);
    spectrumResetObserver.observe(document.body, {
        childList: true,
        subtree: true,
    });

    // Színmező megnyitásakor azonnal a body-ra + láthatóvá
    document.addEventListener('pointerdown', (event) => {
        const target = event.target;
        if (! (target instanceof Element)) return;
        if (! target.closest('.sp-replacer, .gjs-field-colorp, .gjs-field-color, .gjs-sm-field.gjs-sm-color')) {
            return;
        }
        setTimeout(ensureSpectrumVisible, 0);
        setTimeout(ensureSpectrumVisible, 80);
    }, true);

    editor.on('component:selected', () => setTimeout(decorateStyleClearButtons, 40));
    editor.on('style:target', () => setTimeout(decorateStyleClearButtons, 40));
    editor.on('style:custom', () => setTimeout(decorateStyleClearButtons, 40));
    editor.on('load', () => {
        setTimeout(decorateStyleClearButtons, 80);
        setTimeout(injectSpectrumThemeReset, 80);
        setTimeout(() => {
            const doc = editor.Canvas?.getDocument?.();
            if (doc) window.TsHeroSlider?.init?.(doc);
            if (doc) window.TsGallery?.init?.(doc);
            if (doc) window.TsBaGallery?.init?.(doc);
            if (doc) window.TsReveal?.init?.(doc, { forceVisible: true });
        }, 120);
    });

    editor.on('canvas:frame:load', () => {
        setTimeout(() => {
            const doc = editor.Canvas?.getDocument?.();
            if (doc) window.TsHeroSlider?.init?.(doc);
            if (doc) window.TsGallery?.init?.(doc);
            if (doc) window.TsBaGallery?.init?.(doc);
            if (doc) window.TsReveal?.init?.(doc, { forceVisible: true });
        }, 80);
    });

    // Ne a beépített "image" típust használjuk a háttérképekre:
    // annál a getAttributes() elnyeli a data-ts-* markereket, és a src külön property.
    editor.DomComponents.addType('ts-bg-img', {
        isComponent: (el) => el?.tagName === 'IMG' && (
            el.hasAttribute('data-ts-bg-image') || el.hasAttribute('data-ts-hero-image')
        ),
        model: {
            defaults: {
                tagName: 'img',
                void: true,
                droppable: false,
                selectable: false,
                hoverable: false,
                highlightable: false,
                editable: false,
                traits: [],
            },
        },
    });

    /** @type {(component: object) => void} */
    let scheduleBlockSync = () => {};

    editor.TraitManager.addType('media', {
        eventCapture: ['input', 'change'],
        createInput({ trait }) {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-media-trait';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'tsb-media-trait__input';
            input.placeholder = 'URL…';
            input.value = trait.getValue?.() ?? '';

            const browse = document.createElement('button');
            browse.type = 'button';
            browse.className = 'tsb-btn tsb-media-trait__browse';
            browse.textContent = 'Tallózás';

            const applyValue = (url) => {
                input.value = url;
                const component = trait.component || trait.target;
                const name = trait.get('name');
                if (component && name) {
                    component.addAttributes({ [name]: url });
                    scheduleBlockSync(component);
                }
                input.dispatchEvent(new Event('change', { bubbles: true }));
            };

            browse.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                mediaPicker.open({
                    currentUrl: input.value,
                    onSelect: applyValue,
                });
            });

            wrap.appendChild(input);
            wrap.appendChild(browse);
            return wrap;
        },
        onEvent({ elInput, component, trait }) {
            const input = elInput.querySelector('.tsb-media-trait__input');
            if (! input) return;
            const name = trait.get('name');
            component.addAttributes({ [name]: input.value });
            scheduleBlockSync(component);
        },
        onUpdate({ elInput, component, trait }) {
            const input = elInput.querySelector('.tsb-media-trait__input');
            if (! input) return;
            const name = trait.get('name');
            const attrs = component.getAttributes() || {};
            input.value = attrs[name] ?? '';
        },
    });

    editor.TraitManager.addType('rich', {
        createInput({ trait }) {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-rich-trait';

            const preview = document.createElement('div');
            preview.className = 'tsb-rich-trait__preview';
            preview.innerHTML = trait.getValue?.() || '<span class="tsb-rich-trait__empty">Üres</span>';

            const editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'tsb-btn tsb-rich-trait__edit';
            editBtn.textContent = 'Szerkesztés';

            const applyValue = (html) => {
                preview.innerHTML = html || '<span class="tsb-rich-trait__empty">Üres</span>';
                preview.dataset.value = html || '';
                const component = trait.component || trait.target;
                const name = trait.get('name');
                if (component && name) {
                    component.addAttributes({ [name]: encodeRichAttr(html || '') });
                }
                preview.dispatchEvent(new Event('change', { bubbles: true }));
            };

            editBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const attrs = (trait.component || trait.target)?.getAttributes?.() || {};
                const current = preview.dataset.value
                    || decodeRichAttr(attrs[trait.get('name')] || '')
                    || '';
                richEditor.open({
                    title: trait.get('label') || 'Szöveg szerkesztése',
                    html: current,
                    onSave: applyValue,
                });
            });

            preview.dataset.value = decodeRichAttr(trait.getValue?.() || '');
            if (preview.dataset.value) {
                preview.innerHTML = preview.dataset.value;
            }
            wrap.appendChild(preview);
            wrap.appendChild(editBtn);
            return wrap;
        },
        onEvent({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-rich-trait__preview');
            if (! preview) return;
            const name = trait.get('name');
            const html = preview.dataset.value || '';
            component.addAttributes({ [name]: encodeRichAttr(html) });
        },
        onUpdate({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-rich-trait__preview');
            if (! preview) return;
            const name = trait.get('name');
            const attrs = component.getAttributes() || {};
            const value = decodeRichAttr(attrs[name] ?? '');
            preview.dataset.value = value;
            preview.innerHTML = value || '<span class="tsb-rich-trait__empty">Üres</span>';
        },
    });

    editor.TraitManager.addType('link', {
        createInput({ trait }) {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-link-trait';

            const preview = document.createElement('div');
            preview.className = 'tsb-link-trait__preview';

            const editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'tsb-btn tsb-link-trait__edit';
            editBtn.textContent = 'Tallózás';

            const renderPreview = (url, newTab) => {
                preview.dataset.url = url || '';
                preview.dataset.newTab = newTab ? '1' : '0';
                const urlLabel = url || 'Nincs link';
                preview.innerHTML = '';
                const main = document.createElement('div');
                main.className = 'tsb-link-trait__url';
                main.textContent = urlLabel;
                preview.appendChild(main);
                if (url && newTab) {
                    const badge = document.createElement('span');
                    badge.className = 'tsb-link-trait__badge';
                    badge.textContent = 'Új lap';
                    preview.appendChild(badge);
                }
            };

            const applyValue = ({ url, newTab }) => {
                const component = trait.component || trait.target;
                const name = trait.get('name');
                const targetAttr = hrefTargetAttrName(name);
                renderPreview(url, newTab);
                if (component && name) {
                    component.addAttributes({
                        [name]: url || '',
                        [targetAttr]: newTab ? '_blank' : '',
                    });
                }
                preview.dispatchEvent(new Event('change', { bubbles: true }));
            };

            editBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                const component = trait.component || trait.target;
                const name = trait.get('name');
                const attrs = component?.getAttributes?.() || {};
                const targetAttr = hrefTargetAttrName(name);
                linkPicker.open({
                    title: trait.get('label') || 'Link kiválasztása',
                    url: preview.dataset.url || attrs[name] || '',
                    newTab: (preview.dataset.newTab === '1') || attrs[targetAttr] === '_blank',
                    onSave: applyValue,
                });
            });

            const component = trait.component || trait.target;
            const name = trait.get('name');
            const attrs = component?.getAttributes?.() || {};
            renderPreview(attrs[name] || trait.getValue?.() || '', attrs[hrefTargetAttrName(name)] === '_blank');

            wrap.appendChild(preview);
            wrap.appendChild(editBtn);
            return wrap;
        },
        onEvent({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-link-trait__preview');
            if (! preview) return;
            const name = trait.get('name');
            const targetAttr = hrefTargetAttrName(name);
            component.addAttributes({
                [name]: preview.dataset.url || '',
                [targetAttr]: preview.dataset.newTab === '1' ? '_blank' : '',
            });
        },
        onUpdate({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-link-trait__preview');
            if (! preview) return;
            const name = trait.get('name');
            const attrs = component.getAttributes() || {};
            const targetAttr = hrefTargetAttrName(name);
            const url = attrs[name] ?? '';
            const newTab = attrs[targetAttr] === '_blank';
            preview.dataset.url = url;
            preview.dataset.newTab = newTab ? '1' : '0';
            preview.innerHTML = '';
            const main = document.createElement('div');
            main.className = 'tsb-link-trait__url';
            main.textContent = url || 'Nincs link';
            preview.appendChild(main);
            if (url && newTab) {
                const badge = document.createElement('span');
                badge.className = 'tsb-link-trait__badge';
                badge.textContent = 'Új lap';
                preview.appendChild(badge);
            }
        },
    });

    editor.TraitManager.addType('layout', {
        createInput({ trait }) {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-layout-trait';

            const preview = document.createElement('div');
            preview.className = 'tsb-layout-trait__preview';

            const editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'tsb-btn';
            editBtn.textContent = 'Választás';

            const options = trait.get('layoutOptions') || [];

            const labelFor = (id) => {
                const opt = options.find((o) => String(o.id ?? o.value ?? '') === String(id));
                return (opt?.name ?? opt?.label ?? id) || '—';
            };

            const renderPreview = (id) => {
                const value = id || options[0]?.id || 'bottom';
                preview.dataset.value = value;
                preview.textContent = labelFor(value);
            };

            const applyValue = (id) => {
                const component = trait.component || trait.target;
                const name = trait.get('name');
                renderPreview(id);
                if (component && name) {
                    component.addAttributes({ [name]: id });
                }
                preview.dispatchEvent(new Event('change', { bubbles: true }));
            };

            editBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                layoutPicker.open({
                    title: trait.get('label') || 'Elrendezés választása',
                    options,
                    value: preview.dataset.value || 'bottom',
                    onSelect: applyValue,
                });
            });

            renderPreview(trait.getValue?.() || 'bottom');
            wrap.appendChild(preview);
            wrap.appendChild(editBtn);
            return wrap;
        },
        onEvent({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-layout-trait__preview');
            if (! preview) return;
            component.addAttributes({ [trait.get('name')]: preview.dataset.value || 'bottom' });
        },
        onUpdate({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-layout-trait__preview');
            if (! preview) return;
            const attrs = component.getAttributes() || {};
            const value = attrs[trait.get('name')] || 'bottom';
            const options = trait.get('layoutOptions') || [];
            const opt = options.find((o) => String(o.id ?? o.value ?? '') === String(value));
            preview.dataset.value = value;
            preview.textContent = (opt?.name ?? opt?.label ?? value) || '—';
        },
    });

    editor.TraitManager.addType('items', {
        createInput({ trait }) {
            const wrap = document.createElement('div');
            wrap.className = 'tsb-items-trait';

            const itemLabel = String(trait.get('itemLabel') || 'elem').trim() || 'elem';
            const editLabel = String(trait.get('editLabel') || 'Tartalmak szerkesztése').trim()
                || 'Tartalmak szerkesztése';
            const addLabel = String(trait.get('addLabel') || `+ Új ${itemLabel}`).trim()
                || `+ Új ${itemLabel}`;
            const hintText = String(trait.get('itemsHint') || '').trim()
                || 'Itt szerkesztheti a szövegeket, és új elemeket is hozzáadhat.';

            const hint = document.createElement('p');
            hint.className = 'tsb-items-trait__hint';
            hint.textContent = hintText;

            const preview = document.createElement('div');
            preview.className = 'tsb-items-trait__preview';

            const editBtn = document.createElement('button');
            editBtn.type = 'button';
            editBtn.className = 'tsb-btn tsb-btn--primary';
            editBtn.textContent = editLabel;

            const fields = trait.get('itemFields') || [];

            const countLabel = (count) => {
                if (! count) return `Nincs ${itemLabel} még`;
                return `${count} ${itemLabel}`;
            };

            const renderPreview = (raw) => {
                const items = parseItemsJson(raw);
                preview.dataset.value = JSON.stringify(items);
                preview.textContent = countLabel(items.length);
            };

            const applyValue = (items) => {
                const component = trait.component || trait.target;
                const name = trait.get('name');
                const json = JSON.stringify(items || []);
                renderPreview(json);
                if (component && name) {
                    component.addAttributes({ [name]: json });
                }
                preview.dispatchEvent(new Event('change', { bubbles: true }));
            };

            editBtn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                itemsEditor.open({
                    title: editLabel,
                    itemFields: fields,
                    value: preview.dataset.value || '[]',
                    addLabel,
                    onSave: applyValue,
                });
            });

            renderPreview(trait.getValue?.() || '[]');
            wrap.appendChild(hint);
            wrap.appendChild(preview);
            wrap.appendChild(editBtn);
            return wrap;
        },
        onEvent({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-items-trait__preview');
            if (! preview) return;
            component.addAttributes({ [trait.get('name')]: preview.dataset.value || '[]' });
        },
        onUpdate({ elInput, component, trait }) {
            const preview = elInput.querySelector('.tsb-items-trait__preview');
            if (! preview) return;
            const attrs = component.getAttributes() || {};
            const name = trait.get('name');
            let items = parseItemsJson(attrs[name] ?? '[]');
            // Régi Képsáv image1–4 → items a szerkesztőben
            if (items.length === 0 && (component.get('type') === 'ts-gallery' || attrs['data-gjs-type'] === 'ts-gallery')) {
                items = galleryItemsFromAttrs(attrs);
            }
            const itemLabel = String(trait.get('itemLabel') || 'elem').trim() || 'elem';
            preview.dataset.value = JSON.stringify(items);
            preview.textContent = items.length
                ? `${items.length} ${itemLabel}`
                : `Nincs ${itemLabel} még`;
        },
    });

    const syncCanvasDeviceClass = () => {
        const canvasArea = root.querySelector('.tsb-canvas-area');
        if (! canvasArea) return;
        const device = String(editor.getDevice?.() || 'Desktop');
        canvasArea.classList.toggle('is-device-desktop', device === 'Desktop');
        canvasArea.classList.toggle('is-device-tablet', device === 'Tablet');
        canvasArea.classList.toggle('is-device-mobile', device === 'Mobile');
    };

    root.querySelectorAll('[data-tsb-devices] .tsb-device').forEach((btn) => {
        btn.addEventListener('click', () => {
            root.querySelectorAll('[data-tsb-devices] .tsb-device').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            editor.setDevice(btn.dataset.device);
            syncCanvasDeviceClass();
        });
    });
    editor.on('change:device', syncCanvasDeviceClass);
    editor.on('device:select', syncCanvasDeviceClass);
    editor.on('load', syncCanvasDeviceClass);
    syncCanvasDeviceClass();

    const refreshDynamicComponent = async (model) => {
        if (model._tsRefreshing) return;
        const type = model.getAttributes()['data-ts-dynamic'];
        if (! type) return;

        model._tsRefreshing = true;
        const attrs = model.getAttributes();
        const payload = {
            title: attrs['data-title'] || '',
            limit: attrs['data-limit'] || '12',
            type: attrs['data-type'] || 'all',
            slug: attrs['data-slug'] || '',
            text: attrs['data-text'] || '',
            button: attrs['data-button'] || '',
            embed_url: attrs['data-embed-url'] || '',
            check_in: attrs['data-check-in'] || '',
            check_out: attrs['data-check-out'] || '',
            guests: attrs['data-guests'] || '2',
            show_title: attrs['data-show-title'] ?? '1',
            show_text: attrs['data-show-text'] ?? '1',
            show_button: attrs['data-show-button'] ?? '1',
        };

        try {
            const html = await $wire.renderDynamicPreview(type, payload);
            model.components(html || '<p style="padding:1rem;opacity:.7;">Nincs tartalom</p>');
            lockBlockChildren(model);
        } catch (e) {
            console.error(e);
            model.components('<p style="padding:1rem;color:#b91c1c;">Nem sikerült betölteni az élő adatokat.</p>');
        } finally {
            model._tsRefreshing = false;
            applyRevealChildrenToModel(model);
        }
    };

    const isLinkParam = (param) => {
        if (! param) return false;
        if (param.type === 'link') return true;
        if (param.type && param.type !== 'text' && param.type !== 'textarea') return false;
        const key = String(param.key || '');
        return /_href$/i.test(key) || /^(href|url)$/i.test(key);
    };

    const isRichContentParam = (param) => {
        if (! param) return false;
        if (param.type === 'rich') return true;
        if (param.type === 'media' || param.type === 'select' || param.type === 'number' || param.type === 'link' || param.type === 'items' || param.type === 'checkbox' || param.type === 'boolean') return false;
        if (isLinkParam(param)) return false;
        if (param.type && param.type !== 'text' && param.type !== 'textarea') return false;
        const key = String(param.key || '');
        if (/^show_/i.test(key)) return false;
        if (/^(phone|email|address)$/i.test(key)) return false;
        if (/(_href|_url|href|url)$/i.test(key) || /overlay|slug|limit|guests|^type$|check_in|check_out|extra_class|css_class/i.test(key)) {
            return false;
        }
        // Szöveges tartalom (felirat, cím, szöveg, gomb, stb.)
        if (param.type === 'textarea') return true;
        return /(^|_)(title|lead|body|text|eyebrow|label|quote|question|answer|desc|content|heading|button)(_|$)/i.test(key)
            || /^(title|lead|body|text|eyebrow|button|name)$/i.test(key)
            || /_label$/i.test(key);
    };

    const inferTraitGroup = (param) => {
        if (! param) return { id: 'general', label: 'Általános' };
        if (param.group) {
            return {
                id: String(param.group),
                label: String(param.groupLabel || param.group),
            };
        }

        const key = String(param.key || '');

        if (key === 'layout' || key === 'section_width' || param.type === 'layout') {
            return { id: 'layout', label: 'Elrendezés' };
        }
        if (
            param.type === 'media'
            || /^(media_|overlay)/.test(key)
            || key === 'overlay'
            || key === 'overlay_color'
            || key === 'embed_url'
        ) {
            return { id: 'background', label: 'Háttér és overlay' };
        }

        const statMatch = key.match(/^stat(\d+)_(value|label)$/i);
        if (statMatch) {
            return { id: `stat-${statMatch[1]}`, label: `${statMatch[1]}. adat` };
        }

        const colMatch = key.match(/^col(\d+)$/i);
        if (colMatch) {
            return { id: `col-${colMatch[1]}`, label: `${colMatch[1]}. oszlop` };
        }

        if (/^primary_/.test(key)) {
            return { id: 'btn-primary', label: 'Elsődleges gomb' };
        }
        if (/^secondary_/.test(key)) {
            return { id: 'btn-secondary', label: 'Másodlagos gomb' };
        }
        if (/^button_/.test(key) || key === 'show_button') {
            return { id: 'btn-main', label: 'Gomb' };
        }
        if (key === 'phone' || key === 'email' || key === 'show_phone' || key === 'show_email') {
            return { id: 'contact-details', label: 'Elérhetőségek' };
        }
        if (/^(logo\d+|image\d+)$/i.test(key)) {
            return { id: 'media-items', label: 'Képek' };
        }
        if (key === 'items' || param.type === 'items') {
            return { id: 'items', label: 'Lista elemek' };
        }
        if (key === 'extra_class') {
            return { id: 'advanced', label: 'Haladó' };
        }
        if (key === 'reveal_children') {
            return { id: 'animation', label: 'Animáció' };
        }
        if (/^(title|lead|text|body|eyebrow|name|quote)$/i.test(key) || /(title|lead|text|body|eyebrow)$/i.test(key)) {
            return { id: 'content', label: 'Szöveges tartalom' };
        }

        return { id: 'general', label: 'Általános' };
    };

    const TRAIT_OPEN_PRIORITY = ['content', 'btn-primary', 'btn-main', 'layout', 'background'];

    const traitFromParam = (param, { openGroupId = null } = {}) => {
        const attr = param.attr || `data-${param.key}`;
        let type = 'text';
        if (param.type === 'number') type = 'number';
        else if (param.type === 'select') type = 'select';
        else if (param.type === 'media') type = 'media';
        else if (param.type === 'layout') type = 'layout';
        else if (param.type === 'items') type = 'items';
        else if (param.type === 'color') type = 'color';
        else if (param.type === 'checkbox' || param.type === 'boolean') type = 'checkbox';
        else if (param.type === 'link' || isLinkParam(param)) type = 'link';
        else if (param.type === 'rich' || isRichContentParam(param)) type = 'rich';

        const inferred = inferTraitGroup(param);
        const groupId = inferred.id;
        const groupLabel = inferred.label;

        const trait = {
            type,
            name: attr,
            label: param.label || param.key,
            paramKey: param.key || '',
            // GrapesJS 0.21+ natív trait kategóriák (lenyíló csoportok)
            category: {
                id: groupId,
                label: groupLabel,
                open: openGroupId ? groupId === openGroupId : groupId === 'content',
            },
        };

        if (type === 'checkbox') {
            trait.valueTrue = '1';
            trait.valueFalse = '0';
        }

        if (param.type === 'layout') {
            trait.layoutOptions = Array.isArray(param.options) ? param.options : [];
        }

        if (param.type === 'items') {
            trait.itemFields = Array.isArray(param.itemFields) ? param.itemFields : [];
            trait.itemLabel = param.itemLabel || 'elem';
            trait.editLabel = param.editLabel || 'Tartalmak szerkesztése';
            trait.addLabel = param.addLabel || (`+ Új ${param.itemLabel || 'elem'}`);
            trait.itemsHint = param.hint || '';
        }

        if (param.type === 'number') {
            if (param.min != null) trait.min = param.min;
            if (param.max != null) trait.max = param.max;
            if (param.step != null) trait.step = param.step;
        }

        if (param.type === 'select') {
            trait.options = (param.options || []).map((opt) => ({
                id: opt.id ?? opt.value ?? '',
                name: opt.name ?? opt.label ?? String(opt.id ?? ''),
            }));
        }

        return trait;
    };

    const traitsFromParams = (params) => {
        const list = params || [];
        const groupIds = list.map((param) => inferTraitGroup(param).id);
        const openGroupId = TRAIT_OPEN_PRIORITY.find((id) => groupIds.includes(id)) || groupIds[0] || null;
        return list.map((param) => traitFromParam(param, { openGroupId }));
    };

    const changeEventsFromParams = (params, { excludeSectionWidth = false } = {}) => (params || [])
        .filter((param) => ! excludeSectionWidth || param.key !== 'section_width')
        .map((param) => `change:attributes:${param.attr || `data-${param.key}`}`)
        .join(' ');

    const sanitizeExtraClasses = (raw) => String(raw ?? '')
        .split(/\s+/)
        .map((token) => token.trim())
        .filter((token) => /^[A-Za-z_][A-Za-z0-9_-]*$/.test(token));

    const applyExtraClassesToModel = (model) => {
        if (! model) return;
        const el = model.getEl?.() || model.view?.el;
        if (! el) return;

        const attrs = model.getAttributes?.() || {};
        const prevExtra = sanitizeExtraClasses(model._tsAppliedExtraClasses || '');
        const nextExtra = sanitizeExtraClasses(attrs['data-extra-class'] || '');

        prevExtra.forEach((name) => {
            el.classList.remove(name);
            if (typeof model.removeClass === 'function') {
                model.removeClass(name);
            }
        });
        nextExtra.forEach((name) => {
            el.classList.add(name);
            if (typeof model.addClass === 'function') {
                model.addClass(name);
            }
        });

        const extraClassValue = nextExtra.join(' ');
        model._tsAppliedExtraClasses = extraClassValue;
        if (extraClassValue) {
            el.setAttribute('data-extra-class', extraClassValue);
            model.addAttributes?.({ 'data-extra-class': extraClassValue });
        } else {
            el.removeAttribute('data-extra-class');
            if (attrs['data-extra-class']) {
                model.removeAttributes?.(['data-extra-class']);
            }
        }
    };

    const applyRevealChildrenToModel = (model, { forceVisible = true } = {}) => {
        if (! model) return;
        const el = model.getEl?.() || model.view?.el;
        if (! el) return;

        const enabled = String(model.getAttributes?.()?.['data-reveal-children'] ?? '0') === '1';
        if (! enabled) {
            el.removeAttribute('data-reveal-children');
            el.classList.remove('is-ts-revealed');
            window.TsReveal?.prepareSection?.(el, { forceVisible: false });
            if (model.getAttributes?.()['data-reveal-children']) {
                model.removeAttributes?.(['data-reveal-children']);
            }
            return;
        }

        el.setAttribute('data-reveal-children', '1');
        model.addAttributes?.({ 'data-reveal-children': '1' });
        window.TsReveal?.prepareSection?.(el, { forceVisible });
    };

    const applyFooterSiteContact = (model, block) => {
        if (! model) return;
        const type = block?.id || block?.gjsType || model.getAttributes?.()?.['data-gjs-type'];
        if (type !== 'ts-footer-full' && ! model.getEl?.()?.classList?.contains('ts-footer')) return;

        const el = model.getEl?.() || model.view?.el;
        if (! el) return;

        const useSite = String(model.getAttributes?.()?.['data-use-site-contact'] ?? el.getAttribute('data-use-site-contact') ?? '1') !== '0';
        if (! useSite) return;

        const contact = config.siteContact || {};
        const setField = (field, value, scheme) => {
            const li = el.querySelector(`[data-ts-site-field="${field}"]`);
            if (! li) return;
            const filled = !! String(value || '').trim();
            li.hidden = ! filled;
            if (field === 'address') {
                const textNode = li.matches('[data-ts-text="address"]') ? li : li.querySelector('[data-ts-text="address"]');
                if (textNode) textNode.textContent = value || '';
                else if (filled) li.textContent = value;
                return;
            }
            const link = li.querySelector('a');
            if (link) {
                link.textContent = value || '';
                link.setAttribute('href', filled ? resolveBoundHref(value, scheme, field) : '#');
            }
        };

        setField('address', contact.address || '', '');
        setField('phone', contact.phone || '', 'tel');
        setField('email', contact.email || '', 'mailto');
    };

    if (config.enableDynamicBlocks) {
        (config.dynamicBlocks || []).forEach((block) => {
            const attrs = {
                'data-ts-dynamic': block.dynamicKey,
                'data-gjs-type': block.gjsType || block.id,
            };

            (block.params || []).forEach((param) => {
                const attr = param.attr || `data-${param.key}`;
                attrs[attr] = param.default ?? '';
            });

            const refreshParams = (block.params || []).filter((param) => param.key !== 'extra_class' && param.key !== 'section_width');
            const events = changeEventsFromParams(refreshParams, { excludeSectionWidth: true });

            editor.DomComponents.addType(block.gjsType || block.id, {
                isComponent: (el) => el?.getAttribute?.('data-ts-dynamic') === block.dynamicKey,
                model: {
                    defaults: {
                        tagName: 'section',
                        droppable: false,
                        attributes: {
                            class: block.className || 'ts-dyn-block',
                            ...attrs,
                        },
                        traits: traitsFromParams(block.params || []),
                    },
                    init() {
                        if (events) {
                            this.on(events, () => refreshDynamicComponent(this));
                        }
                        this.on('change:attributes:data-extra-class', () => applyExtraClassesToModel(this));
                        this.on('change:attributes:data-reveal-children', () => applyRevealChildrenToModel(this));
                    },
                },
                view: {
                    init() {
                        refreshDynamicComponent(this.model);
                    },
                    onRender() {
                        applyExtraClassesToModel(this.model);
                        applyRevealChildrenToModel(this.model);
                    },
                },
            });
        });
    }

    const eachComponent = (components, callback) => {
        if (! components) return;
        if (typeof components.forEach === 'function') {
            components.forEach(callback);
            return;
        }
        if (typeof components.each === 'function') {
            components.each(callback);
            return;
        }
        (components.models || []).forEach(callback);
    };

    const hasDataAttr = (attrs, name) => Object.prototype.hasOwnProperty.call(attrs || {}, name);

    const componentHasMarker = (cmp, name) => {
        const attrs = cmp.getAttributes?.() || {};
        if (hasDataAttr(attrs, name)) return true;
        const el = cmp.getEl?.();
        return !!(el && typeof el.hasAttribute === 'function' && el.hasAttribute(name));
    };

    const setComponentSrc = (cmp, url) => {
        if (! cmp) return;
        const value = String(url ?? '');
        const tag = String(cmp.get?.('tagName') || '').toLowerCase();
        const type = String(cmp.get?.('type') || '');

        // Beépített image típus: kötelező a model.src (addAttributes önmagában nem elég a getHtml-hez)
        if (tag === 'img' || type === 'image' || type === 'ts-bg-img') {
            cmp.set?.('src', value);
        }

        cmp.addAttributes?.({ src: value });
    };

    const syncMediaUrlToDescendants = (model, mediaUrl) => {
        if (! model) return;

        // 1) GrapesJS find – a legmegbízhatóbb a már betöltött image komponensekre
        if (typeof model.find === 'function') {
            try {
                model.find('img').forEach((cmp) => setComponentSrc(cmp, mediaUrl));
                model.find('source').forEach((cmp) => setComponentSrc(cmp, mediaUrl));
            } catch (e) {
                // ignore selector errors
            }
        }

        // 2) Manuális walk – ha a find nem talál / más típusnév
        const walk = (components) => {
            eachComponent(components, (cmp) => {
                const tag = String(cmp.get?.('tagName') || '').toLowerCase();
                const type = String(cmp.get?.('type') || '');

                if (tag === 'img' || type === 'image' || type === 'ts-bg-img') {
                    setComponentSrc(cmp, mediaUrl);
                }
                if (tag === 'source') {
                    setComponentSrc(cmp, mediaUrl);
                }

                walk(cmp.components?.());
            });
        };

        walk(model.components?.());
    };

    const encodeRichAttr = (html) => {
        const value = String(html ?? '');
        if (! value) return '';
        if (value.startsWith('html:')) return value;
        return 'html:' + encodeURIComponent(value);
    };

    const decodeRichAttr = (value) => {
        const raw = String(value ?? '');
        if (! raw) return '';
        if (raw.startsWith('html:')) {
            try {
                return decodeURIComponent(raw.slice(5));
            } catch (e) {
                return raw.slice(5);
            }
        }
        if (raw.includes('&lt;') || raw.includes('&gt;') || raw.includes('&quot;')) {
            const ta = document.createElement('textarea');
            ta.innerHTML = raw;
            return ta.value;
        }
        return raw;
    };

    const isRichBlockParam = (param) => param && (param.type === 'rich' || param.type === 'textarea' || isRichContentParam(param));

    const paramValueFromAttrs = (attrs, param) => {
        const attr = param.attr || `data-${String(param.key || '').replaceAll('_', '-')}`;
        const raw = attrs[attr] ?? param.default ?? '';
        return isRichBlockParam(param) ? decodeRichAttr(raw) : raw;
    };

    const setComponentText = (cmp, value) => {
        // HTML tartalom: GrapesJS komponensfává parseoljuk (<strong>, <br>, <a>, stb.)
        cmp.components?.(String(value ?? ''));
    };

    const normalizeBtnStyle = (value, fallback = 'primary') => {
        const variant = String(value ?? '').toLowerCase().trim();
        if (variant === 'ghost') return 'secondary';
        if (['primary', 'secondary', 'inverse'].includes(variant)) return variant;
        return fallback || 'primary';
    };

    const isButtonStyleParam = (param) => {
        if (! param) return false;
        const key = String(param.key || '');
        return param.type === 'select'
            && (key === 'button_style' || key.endsWith('_style'))
            && /style$/i.test(key);
    };

    const buttonStyleMarkerKeys = (styleKey) => {
        const key = String(styleKey || '');
        if (key === 'button_style') {
            return ['button_label', 'button_href'];
        }
        if (key.endsWith('_style')) {
            const base = key.slice(0, -'_style'.length);
            return [`${base}_label`, `${base}_href`];
        }
        return [];
    };

    const applyBtnStyleClass = (target, style) => {
        const variant = normalizeBtnStyle(style);
        const modifiers = ['ts-btn--primary', 'ts-btn--secondary', 'ts-btn--inverse', 'ts-btn--ghost'];
        if (target?.removeClass && target?.addClass) {
            target.removeClass(modifiers);
            target.addClass(['ts-btn', `ts-btn--${variant}`]);
            return;
        }
        if (target?.classList) {
            modifiers.forEach((name) => target.classList.remove(name));
            target.classList.add('ts-btn', `ts-btn--${variant}`);
        }
    };

    const syncButtonStyles = (model, block, attrs, el) => {
        const styleParams = (block.params || []).filter(isButtonStyleParam);
        if (! styleParams.length) return;

        const rootUpdates = {};
        styleParams.forEach((param) => {
            const attr = param.attr || `data-${String(param.key || '').replaceAll('_', '-')}`;
            const fallback = param.default || 'primary';
            if (attrs[attr] == null || attrs[attr] === '') {
                rootUpdates[attr] = fallback;
            }
        });
        if (Object.keys(rootUpdates).length) {
            model.addAttributes?.(rootUpdates);
            Object.assign(attrs, rootUpdates);
        }

        const applyToCmp = (cmp, styleKey, style) => {
            applyBtnStyleClass(cmp, style);
            const current = cmp.getAttributes?.() || {};
            if (current['data-ts-btn'] !== styleKey) {
                cmp.addAttributes?.({ 'data-ts-btn': styleKey });
            }
        };

        const walk = (components) => {
            eachComponent(components, (cmp) => {
                const elAttrs = cmp.getAttributes?.() || {};
                const tag = String(cmp.get?.('tagName') || '').toLowerCase();
                const classes = String(cmp.getClasses?.()?.join?.(' ') || cmp.get('classes') || '');

                styleParams.forEach((param) => {
                    const attr = param.attr || `data-${String(param.key || '').replaceAll('_', '-')}`;
                    const style = normalizeBtnStyle(attrs[attr] ?? param.default, param.default || 'primary');
                    const markers = buttonStyleMarkerKeys(param.key);
                    const matched = elAttrs['data-ts-btn'] === param.key
                        || markers.includes(elAttrs['data-ts-text'])
                        || markers.includes(elAttrs['data-ts-href']);
                    const looksLikeBtn = tag === 'a' || /\bts-btn\b/.test(classes) || elAttrs['data-ts-btn'];

                    if (matched && looksLikeBtn) {
                        applyToCmp(cmp, param.key, style);
                    }
                });

                walk(cmp.components?.());
            });
        };

        walk(model.components?.());

        if (! el) return;
        styleParams.forEach((param) => {
            const attr = param.attr || `data-${String(param.key || '').replaceAll('_', '-')}`;
            const style = normalizeBtnStyle(attrs[attr] ?? param.default, param.default || 'primary');
            const nodes = new Set([
                ...el.querySelectorAll(`[data-ts-btn="${param.key}"]`),
            ]);
            buttonStyleMarkerKeys(param.key).forEach((markerKey) => {
                el.querySelectorAll(`[data-ts-text="${markerKey}"], [data-ts-href="${markerKey}"]`).forEach((node) => {
                    nodes.add(node);
                });
            });
            nodes.forEach((node) => {
                if (! (node instanceof Element)) return;
                if (! (node.matches('a, button, .ts-btn') || node.classList.contains('ts-btn'))) return;
                node.setAttribute('data-ts-btn', param.key);
                applyBtnStyleClass(node, style);
            });
        });
    };

    const buildHeroOverlayBackground = (layout, tone) => {
        const color = String(tone || '').trim() || 'var(--color-primary)';
        // Osztott: asztalon nincs overlay (CSS rejti); mobilra a klasszikus alsó gradiens kell.
        if (layout === 'split') {
            return `linear-gradient(180deg, color-mix(in srgb, ${color} 28%, transparent) 0%, color-mix(in srgb, ${color} 78%, transparent) 70%, color-mix(in srgb, ${color} 94%, transparent) 100%)`;
        }
        if (layout === 'center') {
            return `linear-gradient(180deg, color-mix(in srgb, ${color} 45%, transparent) 0%, color-mix(in srgb, ${color} 72%, transparent) 55%, color-mix(in srgb, ${color} 88%, transparent) 100%)`;
        }
        return `linear-gradient(180deg, color-mix(in srgb, ${color} 28%, transparent) 0%, color-mix(in srgb, ${color} 78%, transparent) 70%, color-mix(in srgb, ${color} 94%, transparent) 100%)`;
    };

    const applyOverlayNodeStyles = (node, { opacity, color, layout = 'bottom', isHero = false } = {}) => {
        if (! node) return;
        const opacityValue = (opacity != null && opacity !== '') ? String(opacity) : '0.7';
        const colorValue = String(color || '').trim() || 'var(--color-primary)';
        node.style.pointerEvents = 'none';
        if (isHero) {
            node.style.background = buildHeroOverlayBackground(layout, colorValue);
            if (layout === 'split') {
                // Asztali opacity-t a CSS (!important) 0-ra kényszeríti; mobilra CSS változó.
                node.style.removeProperty('opacity');
                const section = node.closest?.('.ts-hero') || node.parentElement;
                if (section?.style) {
                    section.style.setProperty('--ts-hero-overlay-opacity', opacityValue);
                }
                return;
            }
            node.style.opacity = opacityValue;
            return;
        }
        node.style.opacity = opacityValue;
        node.style.background = colorValue;
        node.style.backgroundColor = colorValue;
        node.style.backgroundImage = 'none';
    };

    const blockHasOverlayParam = (block) => (block?.params || []).some((param) => param.key === 'overlay');

    /**
     * Style Manageres háttérképes szekciókhoz: overlay réteg létrehozása, ha még nincs.
     * A szekció saját background-image-e mögött/fölött az overlay gyerek érvényesül.
     */
    const ensureSurfaceOverlayChrome = (model, el, block) => {
        if (! model || ! el || ! blockHasOverlayParam(block)) return;
        // Hero slider: overlay slide-onként a médián van – szekció-szintű surface overlay tilos
        if (
            block?.id === 'ts-hero-slider'
            || block?.gjsType === 'ts-hero-slider'
            || el.classList?.contains('ts-hero-slider')
        ) {
            const removeSurface = (components) => {
                const list = [];
                eachComponent(components, (cmp) => {
                    const attrs = cmp.getAttributes?.() || {};
                    const cls = String(attrs.class || '');
                    if (
                        ('data-ts-bg-overlay' in attrs || cls.includes('ts-surface-overlay'))
                        && ! ('data-ts-hero-overlay' in attrs)
                        && ! cls.includes('ts-hero-slider__overlay')
                    ) {
                        // Csak közvetlen szekció-gyerek surface overlay
                        if (cmp.parent?.() === model) {
                            list.push(cmp);
                        }
                    }
                });
                list.forEach((cmp) => cmp.remove?.());
            };
            removeSurface(model.components?.());
            Array.from(el.children || []).forEach((child) => {
                if (! (child instanceof HTMLElement)) return;
                const isSurface = child.hasAttribute('data-ts-bg-overlay')
                    || child.classList.contains('ts-surface-overlay');
                const isSlideOverlay = child.hasAttribute('data-ts-hero-overlay')
                    || child.classList.contains('ts-hero-slider__overlay');
                if (isSurface && ! isSlideOverlay) {
                    child.remove();
                }
            });
            return;
        }

        let overlayCmp = null;
        const findOverlay = (components) => {
            eachComponent(components, (cmp) => {
                if (overlayCmp) return;
                if (componentHasMarker(cmp, 'data-ts-bg-overlay') || componentHasMarker(cmp, 'data-ts-hero-overlay')) {
                    overlayCmp = cmp;
                    return;
                }
                findOverlay(cmp.components?.());
            });
        };
        findOverlay(model.components?.());

        if (! overlayCmp) {
            const collection = model.components?.();
            if (collection && typeof collection.add === 'function') {
                const added = collection.add({
                    tagName: 'div',
                    attributes: {
                        class: 'ts-surface-overlay',
                        'data-ts-bg-overlay': '',
                        'data-gjs-name': 'Overlay',
                    },
                    style: {
                        position: 'absolute',
                        top: '0',
                        right: '0',
                        bottom: '0',
                        left: '0',
                        'z-index': '1',
                        'pointer-events': 'none',
                        opacity: '0',
                        background: 'var(--color-primary)',
                    },
                    selectable: false,
                    hoverable: false,
                    highlightable: false,
                    draggable: false,
                    droppable: false,
                    copyable: false,
                    removable: false,
                    layerable: true,
                }, { at: 0 });
                overlayCmp = Array.isArray(added) ? added[0] : added;
            } else {
                const node = document.createElement('div');
                node.className = 'ts-surface-overlay';
                node.setAttribute('data-ts-bg-overlay', '');
                el.insertBefore(node, el.firstChild);
            }
        }

        const rootStyle = model.getStyle?.() || {};
        if (! rootStyle.position || rootStyle.position === 'static') {
            model.addStyle?.({
                position: 'relative',
                isolation: 'isolate',
                overflow: rootStyle.overflow || 'hidden',
            });
        }

        Array.from(el.children || []).forEach((child) => {
            if (! (child instanceof HTMLElement)) return;
            if (
                child.hasAttribute('data-ts-bg-overlay')
                || child.hasAttribute('data-ts-hero-overlay')
                || child.hasAttribute('data-ts-bg-style')
                || child.classList.contains('ts-surface-overlay')
            ) {
                return;
            }
            const computed = window.getComputedStyle(child);
            if (computed.position === 'static') {
                child.style.position = 'relative';
            }
            if (! child.style.zIndex) {
                child.style.zIndex = '2';
            }
        });
    };

    const ensureContactMarkup = (model, el, attrs = {}) => {
        if (! el?.classList?.contains('ts-contact')) return;
        if (el.querySelector('[data-ts-text="title"]') && el.querySelector('[data-ts-show="address"]')) {
            return;
        }

        const readFallback = (selector) => el.querySelector(selector)?.textContent?.trim() || '';
        let title = attrs['data-title'] || readFallback('h2') || 'Kapcsolat';
        let address = attrs['data-address'] || '';
        let phone = attrs['data-phone'] || '';
        let email = attrs['data-email'] || '';

        if (! address || ! phone || ! email) {
            el.querySelectorAll('p').forEach((p) => {
                const text = (p.textContent || '').trim();
                const link = p.querySelector('a')?.getAttribute('href') || '';
                if (! address && /^Cím:/i.test(text)) {
                    address = text.replace(/^Cím:\s*/i, '').trim();
                }
                if (! phone && (/^Telefon:/i.test(text) || link.startsWith('tel:'))) {
                    phone = (link.replace(/^tel:/i, '') || text.replace(/^Telefon:\s*/i, '')).trim();
                }
                if (! email && (/^Email:/i.test(text) || link.startsWith('mailto:'))) {
                    email = (link.replace(/^mailto:/i, '') || text.replace(/^Email:\s*/i, '')).trim();
                }
            });
        }

        const inner = `
  <div class="ts-contact__inner">
    <h2 class="ts-contact__title" data-ts-text="title">${escapeHtml(title)}</h2>
    <p class="ts-contact__row ts-contact__row--address" data-ts-show="address">
      <span class="ts-contact__label">Cím:</span>
      <span class="ts-contact__value" data-ts-text="address">${escapeHtml(address)}</span>
    </p>
    <p class="ts-contact__row ts-contact__row--phone" data-ts-show="phone">
      <span class="ts-contact__label">Telefon:</span>
      <a class="ts-contact__value" data-ts-text="phone" data-ts-href="phone" data-ts-href-scheme="tel" href="${escapeHtml(resolveBoundHref(phone, 'tel'))}">${escapeHtml(phone)}</a>
    </p>
    <p class="ts-contact__row ts-contact__row--email" data-ts-show="email">
      <span class="ts-contact__label">Email:</span>
      <a class="ts-contact__value" data-ts-text="email" data-ts-href="email" data-ts-href-scheme="mailto" href="${escapeHtml(resolveBoundHref(email, 'mailto'))}">${escapeHtml(email)}</a>
    </p>
  </div>`;

        const styleCmps = [];
        model?.components?.()?.each?.((cmp) => {
            if (String(cmp.get?.('tagName') || '').toLowerCase() === 'style') {
                styleCmps.push(cmp);
            }
        });

        try {
            model.components?.(inner);
            styleCmps.forEach((cmp) => model.append?.(cmp));
        } catch (e) {
            el.innerHTML = inner;
        }

        model?.addAttributes?.({
            'data-gjs-type': 'ts-contact',
            'data-title': title,
            'data-address': address,
            'data-phone': phone,
            'data-email': email,
        });
    };

    const featureItemFromHtml = (html) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = decodeRichAttr(html || '');
        const title = tmp.querySelector('h3')?.textContent?.trim() || '';
        let text = tmp.querySelector('p')?.textContent?.trim() || '';
        if (! text) {
            text = (tmp.textContent || '').trim();
            if (title && text.startsWith(title)) {
                text = text.slice(title.length).trim();
            }
        }
        return { title, text };
    };

    const ensureFeaturesMarkup = (model, el, attrs = {}) => {
        if (! el?.classList?.contains('ts-features')) return;

        const hasItemsContainer = !! el.querySelector('[data-ts-items="items"], [data-ts-items-kind="features"]');
        const hasLegacyCols = attrs['data-col1'] != null
            || attrs['data-col2'] != null
            || attrs['data-col3'] != null
            || !! el.querySelector('[data-ts-text="col1"], [data-ts-text="col2"], [data-ts-text="col3"]');

        if (hasItemsContainer && ! hasLegacyCols) {
            if (! attrs['data-columns']) {
                model?.addAttributes?.({ 'data-columns': '3' });
            }
            return;
        }

        const items = [];
        const rawItems = parseItemsJson(attrs['data-items'] || '[]');
        if (rawItems.length) {
            rawItems.forEach((item) => {
                if (! item || typeof item !== 'object') return;
                items.push({
                    title: String(item.title || ''),
                    text: String(item.text || ''),
                });
            });
        } else {
            ['data-col1', 'data-col2', 'data-col3'].forEach((attr) => {
                if (attrs[attr] == null) return;
                const parsed = featureItemFromHtml(attrs[attr]);
                if (parsed.title || parsed.text) items.push(parsed);
            });
            if (! items.length) {
                el.querySelectorAll('.ts-features__grid > .ts-feature, .ts-features__grid > article').forEach((node) => {
                    const parsed = featureItemFromHtml(node.innerHTML || '');
                    if (parsed.title || parsed.text) items.push(parsed);
                });
            }
        }

        if (! items.length) {
            items.push(
                { title: 'Csendes környezet', text: 'Természetközeli pihenés, távol a város zajától.' },
                { title: 'Online foglalás', text: 'Nézze meg a szabad időpontokat, és foglaljon pár kattintással.' },
                { title: 'Vendégközpontú', text: 'Rugalmas fogadás, személyes odafigyelés minden tartózkodásnál.' },
            );
        }

        const title = attrs['data-title'] || el.querySelector('.ts-features__title, h2')?.textContent?.trim() || 'Miért nálunk?';
        const columns = attrs['data-columns'] || '3';
        const itemsJson = JSON.stringify(items);

        const styleCmps = [];
        model?.components?.()?.each?.((cmp) => {
            if (String(cmp.get?.('tagName') || '').toLowerCase() === 'style') {
                styleCmps.push(cmp);
            }
        });

        const inner = `
  <div class="ts-features__inner">
    <h2 class="ts-features__title" data-ts-text="title">${escapeHtml(decodeRichAttr(title))}</h2>
    <div class="ts-features__grid" data-ts-items="items" data-ts-items-kind="features"></div>
  </div>`;

        model?.components?.(inner);
        styleCmps.forEach((cmp) => model?.append?.(cmp));

        model?.addAttributes?.({
            'data-gjs-type': 'ts-features',
            'data-title': decodeRichAttr(title),
            'data-columns': columns,
            'data-items': itemsJson,
        });
        model?.removeAttributes?.(['data-col1', 'data-col2', 'data-col3']);
    };

    /** Régi Képsáv (data-image1…4 + background-image div-ek) → items + data-ts-items rács */
    const ensureGalleryMarkup = (model, el, attrs = {}) => {
        if (! el?.classList?.contains('ts-gallery')) return;

        const hasItemsContainer = !! el.querySelector('[data-ts-items="items"], [data-ts-items-kind="gallery"]');
        const hasStage = !! el.querySelector('.ts-gallery__stage');
        const hasLegacyDom = !! el.querySelector('[data-ts-src-from]');
        const hasLegacyAttrs = attrs['data-image1'] != null
            || attrs['data-image2'] != null
            || attrs['data-image3'] != null
            || attrs['data-image4'] != null;

        if (hasItemsContainer && hasStage && ! hasLegacyDom) {
            if (hasLegacyAttrs) {
                const items = galleryItemsFromAttrs(attrs);
                model?.addAttributes?.({ 'data-items': JSON.stringify(items) });
                model?.removeAttributes?.(['data-image1', 'data-image2', 'data-image3', 'data-image4']);
            }
            let layout = attrs['data-layout'] || 'grid';
            if (layout === 'featured') layout = 'grid';
            if (layout === 'mosaic') layout = 'masonry';
            if (layout !== (attrs['data-layout'] || '')) {
                model?.addAttributes?.({ 'data-layout': layout });
            }
            return;
        }

        let items = galleryItemsFromAttrs(attrs);
        if (! items.length) {
            el.querySelectorAll('.ts-gallery__item, [data-ts-src-from]').forEach((node) => {
                const fromAttr = node.getAttribute?.('data-ts-src-from');
                let url = '';
                if (fromAttr) {
                    url = String(attrs[`data-${fromAttr}`] || '').trim();
                }
                if (! url) {
                    const bg = node.style?.backgroundImage || '';
                    const match = bg.match(/url\(["']?([^"')]+)["']?\)/i);
                    url = match ? match[1] : '';
                }
                if (! url) {
                    url = node.querySelector?.('img')?.getAttribute?.('src') || '';
                }
                if (url) items.push({ image: url, alt: '' });
            });
        }

        const title = attrs['data-title'] || el.querySelector('.ts-gallery__title, h2')?.textContent?.trim() || 'Hangulatképek';
        let layout = attrs['data-layout'] || 'grid';
        if (layout === 'featured') layout = 'grid';
        if (layout === 'mosaic') layout = 'masonry';
        const itemsJson = JSON.stringify(items);

        const styleCmps = [];
        model?.components?.()?.each?.((cmp) => {
            if (String(cmp.get?.('tagName') || '').toLowerCase() === 'style') {
                styleCmps.push(cmp);
            }
        });

        const titleHtml = escapeHtml(decodeRichAttr(title));
        const inner = `
  <div class="ts-gallery__inner">
    <h2 class="ts-gallery__title" data-ts-text="title">${titleHtml}</h2>
    <div class="ts-gallery__stage">
      <button type="button" class="ts-gallery__nav ts-gallery__nav--prev" data-ts-gallery-prev aria-label="Előző kép" hidden>‹</button>
      <div class="ts-gallery__grid" data-ts-items="items" data-ts-items-kind="gallery"></div>
      <button type="button" class="ts-gallery__nav ts-gallery__nav--next" data-ts-gallery-next aria-label="Következő kép" hidden>›</button>
    </div>
  </div>`;

        model?.components?.(inner);
        styleCmps.forEach((cmp) => model?.append?.(cmp));

        model?.addAttributes?.({
            'data-gjs-type': 'ts-gallery',
            'data-title': decodeRichAttr(title),
            'data-items': itemsJson,
            'data-layout': layout,
        });
        model?.removeAttributes?.(['data-image1', 'data-image2', 'data-image3', 'data-image4']);
    };

    /** Régi Kép blokk (bare img) → linkelhető wrapper */
    const ensureImageMarkup = (model, el, attrs = {}) => {
        const isImage = el?.classList?.contains('ts-image')
            || el?.getAttribute?.('data-gjs-type') === 'ts-image'
            || model?.get?.('type') === 'ts-image';
        if (! isImage) return;
        if (el.querySelector('a[data-ts-href="href"]')) return;

        const img = el.querySelector('img');
        if (! img) return;

        const mediaUrl = attrs['data-media-url'] || img.getAttribute('src') || '';
        const alt = attrs['data-alt'] || img.getAttribute('alt') || '';
        const href = attrs['data-href'] || '';

        const styleCmps = [];
        model?.components?.()?.each?.((cmp) => {
            if (String(cmp.get?.('tagName') || '').toLowerCase() === 'style') {
                styleCmps.push(cmp);
            }
        });

        const inner = `<a class="ts-image__link" data-ts-href="href" href="${escapeHtml(href)}"><img data-ts-bg-image src="${escapeHtml(mediaUrl)}" alt="${escapeHtml(alt)}" loading="lazy"></a>`;

        try {
            model.components?.(inner);
            styleCmps.forEach((cmp) => model.append?.(cmp));
        } catch (e) {
            const link = document.createElement('a');
            link.className = 'ts-image__link';
            link.setAttribute('data-ts-href', 'href');
            if (href) link.setAttribute('href', href);
            img.parentNode?.insertBefore(link, img);
            link.appendChild(img);
        }

        model?.addAttributes?.({
            'data-gjs-type': 'ts-image',
            'data-media-url': mediaUrl,
            'data-alt': alt,
            'data-href': href,
        });
        el.classList?.add?.('ts-image');
    };

    const syncInteractiveBlock = (model, block) => {
        if (! model || model._tsSyncing) return;
        model._tsSyncing = true;

        try {
        const attrs = model.getAttributes?.() || {};
        const mediaType = attrs['data-media-type'] || 'image';
        const mediaUrl = attrs['data-media-url'] || attrs['data-background-url'] || '';
        const overlay = attrs['data-overlay'];
        const overlayColor = String(attrs['data-overlay-color'] || '').trim();
        const rootUpdates = {};
        const elEarly = model.getEl?.() || model.view?.el;
        if (elEarly) {
            ensureSurfaceOverlayChrome(model, elEarly, block);
            if (block?.id === 'ts-contact' || block?.gjsType === 'ts-contact') {
                ensureContactMarkup(model, elEarly, attrs);
            }
            if (block?.id === 'ts-features' || block?.gjsType === 'ts-features') {
                ensureFeaturesMarkup(model, elEarly, attrs);
            }
            if (block?.id === 'ts-gallery' || block?.gjsType === 'ts-gallery') {
                ensureGalleryMarkup(model, elEarly, attrs);
            }
            if (block?.id === 'ts-image' || block?.gjsType === 'ts-image') {
                ensureImageMarkup(model, elEarly, attrs);
            }
        }

        // Migráció után friss attrs (pl. features col → items)
        Object.assign(attrs, model.getAttributes?.() || {});

        if (attrs['data-media-type'] != null) {
            rootUpdates['data-media-type'] = mediaType;
        }
        if (attrs['data-media-url'] != null || attrs['data-background-url'] != null) {
            rootUpdates['data-media-url'] = mediaUrl;
        }
        if (overlay != null && overlay !== '') {
            rootUpdates['data-overlay'] = overlay;
        }
        if (attrs['data-overlay-color'] != null) {
            rootUpdates['data-overlay-color'] = overlayColor;
        }
        if (Object.keys(rootUpdates).length) {
            const changed = Object.entries(rootUpdates).some(([key, value]) => attrs[key] !== value);
            if (changed) {
                model.addAttributes?.(rootUpdates);
            }
        }

        const walk = (components) => {
            eachComponent(components, (cmp) => {
                const elAttrs = cmp.getAttributes?.() || {};
                const tag = String(cmp.get?.('tagName') || '').toLowerCase();

                (block.params || []).forEach((param) => {
                    const key = param.key;
                    const attr = param.attr || `data-${String(key || '').replaceAll('_', '-')}`;
                    const value = paramValueFromAttrs(attrs, param);

                    if (elAttrs['data-ts-text'] === key) {
                        setComponentText(cmp, value);
                    }
                    if (elAttrs['data-ts-href'] === key) {
                        const targetAttr = hrefTargetAttrName(key);
                        const target = attrs[targetAttr] || '';
                        const elNode = cmp.getEl?.();
                        const scheme = elAttrs['data-ts-href-scheme']
                            || elNode?.getAttribute?.('data-ts-href-scheme')
                            || '';
                        const isSocialUrl = /^social_(facebook|instagram|tiktok|linkedin)_url$/.test(key);
                        const rawUrl = String(value || '').trim();
                        let href = resolveBoundHref(rawUrl, scheme, key);
                        if (isSocialUrl && isBlankSocialUrl(rawUrl)) {
                            href = '#';
                        }
                        const linkAttrs = { href };
                        if (target === '_blank') {
                            linkAttrs.target = '_blank';
                            linkAttrs.rel = 'noopener noreferrer';
                        }
                        cmp.addAttributes?.(linkAttrs);
                        if (target !== '_blank') {
                            cmp.removeAttributes?.(['target', 'rel']);
                        }
                    }
                });

                if (componentHasMarker(cmp, 'data-ts-src-from')) {
                    const key = elAttrs['data-ts-src-from'] || cmp.getEl?.()?.getAttribute?.('data-ts-src-from');
                    const attrName = `data-${String(key || '').replaceAll('_', '-')}`;
                    const value = attrs[attrName] || '';
                    if (tag === 'img' || cmp.get?.('type') === 'image' || cmp.get?.('type') === 'ts-bg-img') {
                        setComponentSrc(cmp, value);
                    } else {
                        cmp.addStyle?.({ 'background-image': value ? `url("${value}")` : 'none' });
                    }
                } else if (componentHasMarker(cmp, 'data-ts-bg-style')) {
                    cmp.addStyle?.({ 'background-image': mediaUrl ? `url("${mediaUrl}")` : 'none' });
                }

                if (componentHasMarker(cmp, 'data-ts-bg-overlay') || componentHasMarker(cmp, 'data-ts-hero-overlay')) {
                    const isHeroOverlay = componentHasMarker(cmp, 'data-ts-hero-overlay');
                    const layout = attrs['data-layout'] || 'bottom';
                    const tone = overlayColor || 'var(--color-primary)';
                    const opacityValue = (overlay != null && overlay !== '') ? String(overlay) : '0.7';
                    const elNode = cmp.getEl?.();
                    if (elNode) {
                        applyOverlayNodeStyles(elNode, {
                            opacity: opacityValue,
                            color: tone,
                            layout,
                            isHero: isHeroOverlay,
                        });
                    } else if (isHeroOverlay && layout === 'split') {
                        cmp.addStyle?.({
                            'pointer-events': 'none',
                            background: buildHeroOverlayBackground(layout, tone),
                        });
                    } else if (isHeroOverlay) {
                        cmp.addStyle?.({
                            opacity: opacityValue,
                            'pointer-events': 'none',
                            background: buildHeroOverlayBackground(layout, tone),
                        });
                    } else {
                        cmp.addStyle?.({
                            opacity: opacityValue,
                            'pointer-events': 'none',
                            background: tone,
                            'background-color': tone,
                            'background-image': 'none',
                        });
                    }
                    if (isHeroOverlay && layout === 'split') {
                        model.getEl?.()?.style?.setProperty?.('--ts-hero-overlay-opacity', opacityValue);
                    }
                }

                if (tag === 'iframe' && attrs['data-embed-url'] != null) {
                    setComponentSrc(cmp, attrs['data-embed-url'] || '');
                }

                walk(cmp.components?.());
            });
        };

        walk(model.components?.());

        // media_url → minden img/source a blokkban (marker nélkül is; a GJS image típus elrejti a data-ts-* attr-okat)
        if (attrs['data-media-url'] != null || attrs['data-background-url'] != null) {
            syncMediaUrlToDescendants(model, mediaUrl);
        }

        syncItemsContainers(model, attrs);

        // Canvas azonnali visszajelzés (élő DOM)
        const el = model.getEl?.() || model.view?.el;
        syncButtonStyles(model, block, attrs, el || null);
        if (! el) return;

        if (attrs['data-image-side'] != null) {
            el.setAttribute('data-image-side', attrs['data-image-side'] || 'right');
        }

        if (attrs['data-columns'] != null || block?.id === 'ts-features' || block?.gjsType === 'ts-features'
            || block?.id === 'ts-icon-list' || block?.gjsType === 'ts-icon-list') {
            const columns = attrs['data-columns'] || '3';
            el.setAttribute('data-columns', columns);
            if (attrs['data-columns'] == null) {
                model.addAttributes({ 'data-columns': columns });
            }
        }

        const layoutParam = (block.params || []).find((param) => param.type === 'layout' || param.key === 'layout');
        if (layoutParam) {
            const layoutAttr = layoutParam.attr || 'data-layout';
            let layoutValue = attrs[layoutAttr] || layoutParam.default || 'bottom';
            // Régi split: data-image-side → layout
            if ((block?.id === 'ts-split' || block?.gjsType === 'ts-split') && ! attrs[layoutAttr] && attrs['data-image-side']) {
                layoutValue = attrs['data-image-side'] === 'left' ? 'image-left' : 'image-right';
            }
            el.setAttribute(layoutAttr, layoutValue);
            if (attrs[layoutAttr] == null || attrs[layoutAttr] !== layoutValue) {
                model.addAttributes({ [layoutAttr]: layoutValue });
            }
            const opacityValue = (overlay != null && overlay !== '') ? String(overlay) : '0.55';
            if (layoutValue === 'split') {
                if (block?.id === 'ts-banner' || block?.gjsType === 'ts-banner') {
                    el.style.setProperty('--ts-banner-overlay-opacity', opacityValue);
                }
                if (block?.id === 'ts-bg-section' || block?.gjsType === 'ts-bg-section') {
                    el.style.setProperty('--ts-bg-section-overlay-opacity', opacityValue);
                }
            }
        }

        const sectionWidthParam = (block.params || []).find((param) => param.key === 'section_width');
        if (sectionWidthParam) {
            const sectionWidthAttr = sectionWidthParam.attr || 'data-section-width';
            const sectionWidthValue = attrs[sectionWidthAttr] || sectionWidthParam.default || 'content';
            el.setAttribute(sectionWidthAttr, sectionWidthValue);
            if (attrs[sectionWidthAttr] == null) {
                model.addAttributes({ [sectionWidthAttr]: sectionWidthValue });
            }
        }

        // Egyedi CSS class-ok a root szekción (alap ts-* classok megmaradnak)
        applyExtraClassesToModel(model);
        applyRevealChildrenToModel(model);
        applyFooterSiteContact(model, block);

        const logoUrl = String((model.getAttributes?.() || attrs)['data-logo-url'] || '').trim();
        const hasLogo = logoUrl !== '';
        const brandLabel = String((model.getAttributes?.() || attrs)['data-brand'] || '').trim();
        el.classList.toggle('has-logo', hasLogo);
        if (hasLogo) {
            model.addClass?.('has-logo');
        } else {
            model.removeClass?.('has-logo');
        }
        el.querySelectorAll('[data-ts-logo], .ts-nav-logo, img[data-ts-src-from="logo_url"]').forEach((img) => {
            if (hasLogo) {
                img.setAttribute('src', logoUrl);
                if (brandLabel) {
                    img.setAttribute('alt', brandLabel);
                }
            } else {
                img.removeAttribute('src');
            }
        });
        try {
            model.find?.('.ts-nav-logo')?.forEach((cmp) => setComponentSrc(cmp, hasLogo ? logoUrl : ''));
            model.find?.('[data-ts-src-from=logo_url]')?.forEach((cmp) => setComponentSrc(cmp, hasLogo ? logoUrl : ''));
        } catch (e) {
            // ignore selector errors in older grapes builds
        }

        const logoHeight = String(attrs['data-logo-height'] || '40').replace(/[^\d.]/g, '') || '40';
        const logoMaxWidth = String(attrs['data-logo-max-width'] || '180').replace(/[^\d.]/g, '') || '180';
        el.style.setProperty('--ts-logo-height', `${logoHeight}px`);
        el.style.setProperty('--ts-logo-max-width', `${logoMaxWidth}px`);
        el.setAttribute('data-logo-height', logoHeight);
        el.setAttribute('data-logo-max-width', logoMaxWidth);
        if (attrs['data-logo-height'] == null || attrs['data-logo-max-width'] == null) {
            model.addAttributes({
                'data-logo-height': logoHeight,
                'data-logo-max-width': logoMaxWidth,
            });
        }

        const menuBreakpoint = attrs['data-menu-breakpoint'] || 'phone';
        el.setAttribute('data-menu-breakpoint', menuBreakpoint);
        if (attrs['data-menu-breakpoint'] == null) {
            model.addAttributes({ 'data-menu-breakpoint': menuBreakpoint });
        }

        const blockId = String(block?.id || block?.gjsType || '');
        if (blockId === 'ts-header-bar') {
            const HEADER_PRESETS = {
                standard: { brand_align: 'left', desktop_menu: 'visible' },
                'brand-center': { brand_align: 'center', desktop_menu: 'visible' },
                minimal: { brand_align: 'left', desktop_menu: 'hidden' },
            };

            let layout = String(attrs['data-layout'] || 'standard');
            if (layout === 'with-cta' || layout === 'with-topbar' || layout === 'brand-left' || layout === 'menu-left') {
                layout = 'standard';
            }

            let brandAlign = attrs['data-brand-align'] || 'left';
            let desktopMenu = attrs['data-desktop-menu'] || 'visible';
            const prevLayout = model._tsLastHeaderLayout;

            if (HEADER_PRESETS[layout] && layout !== prevLayout) {
                brandAlign = HEADER_PRESETS[layout].brand_align;
                desktopMenu = HEADER_PRESETS[layout].desktop_menu;
            } else {
                const inferred = brandAlign === 'center' && desktopMenu !== 'hidden'
                    ? 'brand-center'
                    : (brandAlign === 'left' && desktopMenu === 'hidden'
                        ? 'minimal'
                        : (brandAlign === 'left' && desktopMenu !== 'hidden' ? 'standard' : layout));
                if (HEADER_PRESETS[inferred]) {
                    layout = inferred;
                }
            }

            model._tsLastHeaderLayout = layout;

            el.setAttribute('data-layout', layout);
            el.setAttribute('data-brand-align', brandAlign);
            el.setAttribute('data-desktop-menu', desktopMenu);
            model.addAttributes({
                'data-layout': layout,
                'data-brand-align': brandAlign,
                'data-desktop-menu': desktopMenu,
            });

            const menuAlign = attrs['data-menu-align'] || 'right';
            el.setAttribute('data-menu-align', menuAlign);
            if (attrs['data-menu-align'] == null) {
                model.addAttributes({ 'data-menu-align': menuAlign });
            }

            const mobileStyle = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen']
                .find((s) => String(attrs['data-mobile-menu-style'] || '').startsWith(s)) || 'dropdown';
            el.setAttribute('data-mobile-menu-style', mobileStyle);
            if (String(attrs['data-mobile-menu-style'] || '') !== mobileStyle) {
                model.addAttributes({ 'data-mobile-menu-style': mobileStyle });
            }

            el.querySelector('[data-nav-panel]')?.removeAttribute('hidden');
        }

        (block.params || []).forEach((param) => {
            const value = paramValueFromAttrs(attrs, param);
            el.querySelectorAll(`[data-ts-text="${param.key}"]`).forEach((node) => {
                node.innerHTML = value;
            });
            el.querySelectorAll(`[data-ts-href="${param.key}"]`).forEach((node) => {
                const scheme = node.getAttribute('data-ts-href-scheme') || '';
                const isSocialUrl = /^social_(facebook|instagram|tiktok|linkedin)_url$/.test(param.key);
                const rawUrl = String(value || '').trim();
                let href = resolveBoundHref(rawUrl, scheme, param.key);
                if (isSocialUrl && isBlankSocialUrl(rawUrl)) {
                    href = '#';
                }
                node.setAttribute('href', href);
                if (isSocialUrl) {
                    const missing = isBlankSocialUrl(rawUrl);
                    node.classList.toggle('ts-nav-social--needs-url', missing);
                    if (missing) {
                        node.setAttribute('title', 'Add meg a közösségi oldal URL-jét a Tulajdonságokban');
                    } else {
                        node.removeAttribute('title');
                    }
                }
                const targetAttr = hrefTargetAttrName(param.key);
                const target = attrs[targetAttr] || '';
                if (target === '_blank') {
                    node.setAttribute('target', '_blank');
                    node.setAttribute('rel', 'noopener noreferrer');
                } else {
                    node.removeAttribute('target');
                    node.removeAttribute('rel');
                }
            });
        });

        if (block?.id === 'ts-header-bar' || block?.gjsType === 'ts-header-bar') {
            ['facebook', 'instagram', 'tiktok', 'linkedin'].forEach((platform) => {
                const showAttr = `data-show-social-${platform}`;
                const urlAttr = `data-social-${platform}-url`;
                if (attrs[showAttr] != null) {
                    el.setAttribute(showAttr, String(attrs[showAttr]));
                }
                if (attrs[urlAttr] != null) {
                    el.setAttribute(urlAttr, String(attrs[urlAttr]));
                }
            });
        }

        if (attrs['data-media-type'] != null) {
            el.setAttribute('data-media-type', mediaType);
        }
        if (attrs['data-media-url'] != null || attrs['data-background-url'] != null || mediaUrl !== '') {
            el.setAttribute('data-media-url', mediaUrl);
            el.querySelectorAll('[data-ts-bg-image], [data-ts-hero-image]').forEach((img) => {
                img.setAttribute('src', mediaUrl);
            });
            el.querySelectorAll('[data-ts-bg-source], [data-ts-hero-source]').forEach((source) => {
                source.setAttribute('src', mediaUrl);
            });
            el.querySelectorAll('[data-ts-bg-video], [data-ts-hero-video]').forEach((video) => {
                video.load?.();
            });
            el.querySelectorAll('[data-ts-bg-style]:not([data-ts-src-from])').forEach((node) => {
                node.style.backgroundImage = mediaUrl ? `url("${mediaUrl}")` : 'none';
            });
        }

        if (attrs['data-embed-url'] != null) {
            el.querySelectorAll('iframe').forEach((frame) => {
                frame.setAttribute('src', attrs['data-embed-url'] || '');
            });
        }

        el.querySelectorAll('[data-ts-src-from]').forEach((node) => {
            const key = node.getAttribute('data-ts-src-from');
            if (! key) return;
            const attrName = `data-${key.replaceAll('_', '-')}`;
            const value = attrs[attrName] || '';
            if (node.tagName === 'IMG') {
                node.setAttribute('src', value);
            } else {
                node.style.backgroundImage = value ? `url("${value}")` : 'none';
            }
        });
        if (overlay != null && overlay !== '') {
            el.setAttribute('data-overlay', overlay);
        }

        const tone = overlayColor || '';
        const layout = attrs['data-layout'] || 'bottom';
        if (tone) {
            el.setAttribute('data-overlay-color', tone);
            el.style.setProperty('--ts-overlay-color', tone);
        } else {
            el.removeAttribute('data-overlay-color');
            el.style.removeProperty('--ts-overlay-color');
        }

        el.querySelectorAll('[data-ts-bg-overlay], [data-ts-hero-overlay]').forEach((node) => {
            applyOverlayNodeStyles(node, {
                opacity: overlay,
                color: tone,
                layout,
                isHero: node.hasAttribute('data-ts-hero-overlay'),
            });
        });

        if (shouldLockBlockChildren(block)) {
            lockBlockChildren(model);
        }
        } finally {
            model._tsSyncing = false;
        }
    };

    const shouldLockBlockChildren = (block) => ! block?.container;

    const lockBlockChildren = (model) => {
        if (! model) return;

        const walk = (components) => {
            eachComponent(components, (cmp) => {
                cmp.set?.({
                    selectable: false,
                    hoverable: false,
                    highlightable: false,
                    editable: false,
                    draggable: false,
                });
                walk(cmp.components?.());
            });
        };

        walk(model.components?.());
    };

    const resolveTraitableModel = (model) => {
        if (! model) return null;
        const attrs = model.getAttributes?.() || {};
        let type = attrs['data-gjs-type'];
        if (type && traitableByType[type]) {
            return { model, block: traitableByType[type] };
        }

        const tag = String(model.get?.('tagName') || '').toLowerCase();
        const cls = String(attrs.class || '');
        if (tag === 'header' || /\b(site-nav|ts-header-bar|ts-header-simple)\b/.test(cls)) {
            const inferred = 'ts-header-bar';
            if (traitableByType[inferred]) {
                if (! type || type === 'ts-header-simple') {
                    model.addAttributes({ 'data-gjs-type': inferred });
                }
                return { model, block: traitableByType[inferred] };
            }
        }

        if (tag === 'footer' || /\b(ts-footer|ts-footer-min)\b/.test(cls)) {
            const inferred = /\bts-footer-min\b/.test(cls) ? 'ts-footer-minimal' : 'ts-footer-full';
            if (traitableByType[inferred]) {
                if (! type) {
                    model.addAttributes({ 'data-gjs-type': inferred });
                }
                return { model, block: traitableByType[inferred] };
            }
        }

        const classInfer = [
            ['ts-contact', 'ts-contact'],
            ['ts-map', 'ts-map'],
        ];
        for (const [className, inferred] of classInfer) {
            if (new RegExp(`\\b${className}\\b`).test(cls) && traitableByType[inferred]) {
                if (! type) {
                    model.addAttributes({ 'data-gjs-type': inferred });
                }
                return { model, block: traitableByType[inferred] };
            }
        }

        return null;
    };

    const findBlockRoot = (model) => {
        let current = model;
        while (current) {
            if (resolveTraitableModel(current)) {
                return current;
            }
            current = typeof current.parent === 'function' ? current.parent() : null;
        }
        return null;
    };

    const findChromeRoot = (wrapper) => {
        let found = null;
        const visit = (components) => {
            eachComponent(components, (cmp) => {
                if (found) return;
                if (resolveTraitableModel(cmp)) {
                    found = cmp;
                    return;
                }
                visit(cmp.components?.());
            });
        };
        visit(wrapper?.components?.());
        return found;
    };

    // Ha van data-gjs-type, mindig töltsük be a JSON params traitjeit (régi tartalomra is)
    const traitableByType = {};
    [...(config.interactiveBlocks || []), ...(config.dynamicBlocks || [])].forEach((block) => {
        traitableByType[block.gjsType || block.id] = block;
    });

    scheduleBlockSync = (component) => {
        if (! component) return;
        window.setTimeout(() => {
            const resolved = resolveTraitableModel(component);
            if (resolved) {
                syncInteractiveBlock(resolved.model, resolved.block);
            }
        }, 0);
    };

    const flushAllInteractiveBlocks = () => {
        const wrapper = editor.getWrapper?.();
        if (! wrapper) return;

        const visit = (components) => {
            eachComponent(components, (cmp) => {
                const resolved = resolveTraitableModel(cmp);
                if (resolved?.block?.params?.length) {
                    syncInteractiveBlock(cmp, resolved.block);
                }
                visit(cmp.components?.());
            });
        };

        visit(wrapper.components?.());
    };

    const traitsMount = () => document.querySelector('#tsb-traits');
    const stylesMount = () => document.querySelector('#tsb-styles');

    const blockHasMediaTrait = (block) => (block?.params || []).some((param) => (
        param.type === 'media' || param.key === 'media_url' || param.key === 'media_type'
    ));

    const blockHasOverlayTrait = (block) => (block?.params || []).some((param) => (
        param.key === 'overlay' || param.key === 'overlay_color'
    ));

    const syncStyleManagerForBlock = (block) => {
        const stylesEl = stylesMount();
        if (! stylesEl) return;

        const mediaInTraits = blockHasMediaTrait(block);
        stylesEl.classList.toggle('tsb-styles--media-in-traits', mediaInTraits);
        stylesEl.classList.toggle('tsb-styles--overlay-in-traits', blockHasOverlayTrait(block));

        let guide = stylesEl.querySelector('[data-tsb-style-guide]');
        if (! guide) {
            guide = document.createElement('div');
            guide.className = 'tsb-panel-guide';
            guide.setAttribute('data-tsb-style-guide', '');
            stylesEl.prepend(guide);
        }

        if (mediaInTraits) {
            guide.innerHTML = '<strong>Stílus fül:</strong> tipográfia, térköz, szövegszín. '
                + '<strong>Háttérkép / overlay</strong> → Tulajdonságok fül.';
            guide.hidden = false;
        } else if (blockHasOverlayTrait(block)) {
            guide.innerHTML = '<strong>Háttérkép</strong> itt (Stílus → Háttér). '
                + '<strong>Overlay erősség/szín</strong> → Tulajdonságok fül.';
            guide.hidden = false;
        } else {
            guide.hidden = true;
            guide.textContent = '';
        }
    };

    const injectTraitsGuide = () => {
        const mount = traitsMount();
        if (! mount) return;
        let guide = mount.querySelector('[data-tsb-trait-guide]');
        if (! guide) {
            guide = document.createElement('div');
            guide.className = 'tsb-panel-guide';
            guide.setAttribute('data-tsb-trait-guide', '');
            mount.insertBefore(guide, mount.firstChild);
        }
        guide.innerHTML = '<strong>Tulajdonságok:</strong> nyisd ki a csoportot (szöveg, gombok, háttér). '
            + 'Betű / margó / szín → <strong>Stílus</strong> fül.';
        guide.hidden = false;
    };

    const bindTraitCategoryAccordion = () => {
        const categories = editor.TraitManager?.getCategories?.() || [];
        const each = (cb) => {
            if (typeof categories?.each === 'function') {
                categories.each(cb);
            } else {
                (Array.isArray(categories) ? categories : []).forEach(cb);
            }
        };

        each((category) => {
            if (category._tsbTraitAccBound) return;
            category._tsbTraitAccBound = true;
            category.on('change:open', (model, open) => {
                if (! open) return;
                each((other) => {
                    if (other !== model) {
                        other.set('open', false);
                    }
                });
            });
        });
    };

    const ensureVisibilityDefaults = (model, block) => {
        if (! model || ! block?.params?.length) return;
        const updates = {};
        const attrs = model.getAttributes?.() || {};
        (block.params || []).forEach((param) => {
            if (param.type !== 'checkbox' && param.type !== 'boolean') return;
            if (! /^show_/i.test(String(param.key || ''))) return;
            const attr = param.attr || `data-${String(param.key).replaceAll('_', '-')}`;
            if (attrs[attr] == null || attrs[attr] === '') {
                updates[attr] = param.default != null ? String(param.default) : '1';
            }
        });
        if (Object.keys(updates).length) {
            model.addAttributes(updates);
        }
    };

    const applyBlockTraits = (model, block) => {
        if (! model || ! block?.params?.length) return;
        if (shouldLockBlockChildren(block)) {
            lockBlockChildren(model);
        }
        ensureVisibilityDefaults(model, block);
        model.set('traits', traitsFromParams(block.params));
        try {
            editor.TraitManager?.select?.(model);
        } catch (e) {
            // ignore
        }
        syncInteractiveBlock(model, block);
        activateRightTab('traits');
        injectTraitsGuide();
        bindTraitCategoryAccordion();
        syncStyleManagerForBlock(block);
        setTimeout(() => {
            injectTraitsGuide();
            bindTraitCategoryAccordion();
            syncStyleManagerForBlock(block);
        }, 40);
    };

    editor.on('trait:select', () => {
        injectTraitsGuide();
        bindTraitCategoryAccordion();
    });
    editor.on('load', () => setTimeout(() => {
        injectTraitsGuide();
        bindTraitCategoryAccordion();
    }, 200));

    (config.interactiveBlocks || []).forEach((block) => {
        const type = block.gjsType || block.id;
        const events = changeEventsFromParams(block.params);
        const defaultAttrs = { 'data-gjs-type': type };

        (block.params || []).forEach((param) => {
            const attr = param.attr || `data-${String(param.key || '').replaceAll('_', '-')}`;
            defaultAttrs[attr] = param.default ?? '';
        });

        editor.DomComponents.addType(type, {
            isComponent: (el) => el?.getAttribute?.('data-gjs-type') === type
                || (type === 'ts-header-bar' && el?.classList?.contains?.('site-nav') && ! el?.classList?.contains?.('ts-header-simple'))
                || (type === 'ts-header-simple' && el?.classList?.contains?.('ts-header-simple'))
                || (type === 'ts-contact' && el?.classList?.contains?.('ts-contact'))
                || (type === 'ts-map' && el?.classList?.contains?.('ts-map'))
                || (type === 'ts-gallery' && el?.classList?.contains?.('ts-gallery'))
                || (type === 'ts-ba-gallery' && el?.classList?.contains?.('ts-ba-gallery'))
                || (type === 'ts-image' && el?.classList?.contains?.('ts-image')),
            model: {
                defaults: {
                    traits: traitsFromParams(block.params || []),
                    attributes: defaultAttrs,
                    selectable: true,
                    hoverable: true,
                    highlightable: true,
                    droppable: block.container ? true : false,
                },
                init() {
                    if (events) {
                        this.on(events, () => syncInteractiveBlock(this, block));
                    }
                    this.on('change:attributes:data-reveal-children', () => applyRevealChildrenToModel(this));
                },
            },
            view: {
                onRender() {
                    syncInteractiveBlock(this.model, block);
                    if (shouldLockBlockChildren(block)) {
                        lockBlockChildren(this.model);
                    }
                },
            },
        });
    });

    // Style Manager „Háttérszín” → overlay színtónus (képes/videós szekciókon)
    const findOverlayBlockRoot = (model) => {
        let current = model;
        while (current) {
            const attrs = current.getAttributes?.() || {};
            const gjsType = attrs['data-gjs-type'];
            const block = traitableByType[gjsType];
            if (block?.params?.some((param) => param.key === 'overlay' || param.key === 'overlay_color')) {
                return { model: current, block };
            }
            current = typeof current.parent === 'function' ? current.parent() : null;
        }
        return null;
    };

    const normalizeCssColor = (value) => {
        const raw = String(value || '').trim();
        if (! raw || raw === 'transparent' || raw === 'rgba(0, 0, 0, 0)' || raw === 'inherit' || raw === 'initial') {
            return '';
        }
        if (raw.includes('url(') || raw.includes('gradient(')) {
            return '';
        }
        return raw;
    };

    let bridgingOverlayFromStyle = false;
    const bridgeBackgroundColorToOverlay = (model) => {
        if (bridgingOverlayFromStyle || ! model) return;
        const resolved = findOverlayBlockRoot(model);
        if (! resolved) return;

        const style = resolved.model.getStyle?.() || {};
        const color = normalizeCssColor(style['background-color'])
            || normalizeCssColor(style.background);
        if (! color) return;

        const current = String(resolved.model.getAttributes?.()?.['data-overlay-color'] || '').trim();
        if (current === color) return;

        bridgingOverlayFromStyle = true;
        try {
            resolved.model.addAttributes({ 'data-overlay-color': color });
            syncInteractiveBlock(resolved.model, resolved.block);
        } finally {
            bridgingOverlayFromStyle = false;
        }
    };

    editor.on('style:property:update', (property) => {
        const name = property?.getName?.() || property?.get?.('property') || '';
        if (name === 'background-color' || name === 'background') {
            const selected = editor.getSelected?.();
            if (selected) bridgeBackgroundColorToOverlay(selected);
        }
        if (name === 'padding' || String(name).startsWith('padding-')) {
            syncHeroSliderPaddingVars(editor.getSelected?.());
        }
    });
    editor.on('component:styleUpdate', (component, prop) => {
        const propName = String(prop || '');
        if (! prop || propName === 'background-color' || propName === 'background' || propName.includes('background')) {
            bridgeBackgroundColorToOverlay(component || editor.getSelected?.());
        }
        if (! prop || propName === 'padding' || propName.startsWith('padding-') || propName.startsWith('--ts-hero-pad-')) {
            syncHeroSliderPaddingVars(component || editor.getSelected?.());
        }
    });

    const parsePaddingShorthand = (raw) => {
        const parts = String(raw || '').trim().split(/\s+/).filter(Boolean);
        if (parts.length === 1) return { top: parts[0], right: parts[0], bottom: parts[0], left: parts[0] };
        if (parts.length === 2) return { top: parts[0], right: parts[1], bottom: parts[0], left: parts[1] };
        if (parts.length === 3) return { top: parts[0], right: parts[1], bottom: parts[2], left: parts[1] };
        if (parts.length >= 4) return { top: parts[0], right: parts[1], bottom: parts[2], left: parts[3] };
        return { top: '', right: '', bottom: '', left: '' };
    };

    /**
     * Hero slider: a Stíluskezelő „Belső margó” a szekción áll – a kép full-bleed marad,
     * a térköz a szöveges (__inner) rétegre megy CSS változókkal.
     */
    const syncHeroSliderPaddingVars = (model) => {
        if (! model || model._tsSyncingHeroPad) return;
        const el = model.getEl?.() || model.view?.el;
        const attrs = model.getAttributes?.() || {};
        const isHero = attrs['data-gjs-type'] === 'ts-hero-slider'
            || el?.classList?.contains('ts-hero-slider');
        if (! isHero) return;

        const style = model.getStyle?.() || {};
        let top = String(style['padding-top'] || '').trim();
        let right = String(style['padding-right'] || '').trim();
        let bottom = String(style['padding-bottom'] || '').trim();
        let left = String(style['padding-left'] || '').trim();
        const shorthand = String(style.padding || '').trim();
        if (shorthand && ! top && ! right && ! bottom && ! left) {
            ({ top, right, bottom, left } = parsePaddingShorthand(shorthand));
        }

        const current = {
            top: String(style['--ts-hero-pad-top'] || '').trim(),
            right: String(style['--ts-hero-pad-right'] || '').trim(),
            bottom: String(style['--ts-hero-pad-bottom'] || '').trim(),
            left: String(style['--ts-hero-pad-left'] || '').trim(),
        };
        if (current.top === top && current.right === right && current.bottom === bottom && current.left === left) {
            // DOM sync still (canvas iframe)
            if (el) {
                const applyDom = (name, val) => {
                    if (val) el.style.setProperty(name, val);
                    else el.style.removeProperty(name);
                };
                applyDom('--ts-hero-pad-top', top);
                applyDom('--ts-hero-pad-right', right);
                applyDom('--ts-hero-pad-bottom', bottom);
                applyDom('--ts-hero-pad-left', left);
            }
            return;
        }

        model._tsSyncingHeroPad = true;
        try {
            const next = {
                '--ts-hero-pad-top': top || '',
                '--ts-hero-pad-right': right || '',
                '--ts-hero-pad-bottom': bottom || '',
                '--ts-hero-pad-left': left || '',
            };
            model.addStyle?.(next);
            if (el) {
                Object.entries(next).forEach(([name, val]) => {
                    if (val) el.style.setProperty(name, val);
                    else el.style.removeProperty(name);
                });
            }
        } finally {
            model._tsSyncingHeroPad = false;
        }
    };

    const syncAllHeroSliderPaddingVars = () => {
        const wrapper = editor.getWrapper?.();
        if (! wrapper) return;
        const walk = (components) => {
            eachComponent(components, (cmp) => {
                syncHeroSliderPaddingVars(cmp);
                walk(cmp.components?.());
            });
        };
        walk(wrapper.components?.());
    };

    editor.on('load', () => setTimeout(syncAllHeroSliderPaddingVars, 200));
    editor.on('canvas:frame:load', () => setTimeout(syncAllHeroSliderPaddingVars, 120));

    let selectingBlockRoot = false;

    editor.on('component:selected', (model) => {
        if (! model || selectingBlockRoot) return;

        const wrapper = editor.getWrapper?.();
        const isWrapper = model === wrapper || model.get?.('type') === 'wrapper';

        // Fejléc / lábléc szerkesztő: Body helyett a chrome gyökeret válaszd
        if (isWrapper && (config.target === 'header' || config.target === 'footer')) {
            if (config.target === 'footer') {
                let childCount = 0;
                eachComponent(wrapper.components?.(), () => { childCount += 1; });
                // Több gyökér (lábléc + extra szekciók): a wrapperen hagyjuk a fókuszt, hogy új blokk beférjen
                if (childCount > 1) {
                    return;
                }
            }
            const chrome = findChromeRoot(wrapper);
            if (chrome) {
                selectingBlockRoot = true;
                try {
                    editor.select(chrome);
                } finally {
                    selectingBlockRoot = false;
                }
                const resolvedChrome = resolveTraitableModel(chrome);
                if (resolvedChrome) {
                    applyBlockTraits(resolvedChrome.model, resolvedChrome.block);
                }
                return;
            }
        }

        const root = findBlockRoot(model);
        if (root && root !== model) {
            selectingBlockRoot = true;
            try {
                editor.select(root);
            } finally {
                selectingBlockRoot = false;
            }
            const resolvedRoot = resolveTraitableModel(root);
            if (resolvedRoot) {
                applyBlockTraits(resolvedRoot.model, resolvedRoot.block);
            }
            return;
        }

        const resolved = resolveTraitableModel(model);
        if (! resolved?.block?.params?.length) {
            syncHeroSliderPaddingVars(model);
            return;
        }

        applyBlockTraits(resolved.model, resolved.block);
        syncHeroSliderPaddingVars(resolved.model);
    });

    (config.blocks || []).forEach((block) => {
        editor.BlockManager.add(block.id, {
            label: block.label,
            category: {
                id: block.categoryKey || block.category,
                label: block.category,
                open: true,
            },
            content: block.content,
            media: block.media || undefined,
            attributes: {
                'data-keywords': (block.keywords || `${block.label} ${block.category || ''}`).toLowerCase(),
            },
        });
    });

    // Egy nyitott kategória egyszerre
    const bindCategoryAccordion = () => {
        const categories = editor.BlockManager.getCategories();
        const each = (cb) => {
            if (typeof categories?.each === 'function') {
                categories.each(cb);
            } else {
                (categories || []).forEach(cb);
            }
        };

        each((category) => {
            if (category._tsbAccordionBound) return;
            category._tsbAccordionBound = true;
            category.on('change:open', (model, open) => {
                if (! open) return;
                each((other) => {
                    if (other !== model) {
                        other.set('open', false);
                    }
                });
            });
        });
    };

    bindCategoryAccordion();
    editor.on('block:add', bindCategoryAccordion);

    const searchInput = root.querySelector('[data-tsb-block-search]');
    searchInput?.addEventListener('input', () => {
        const query = searchInput.value.trim().toLowerCase();
        root.querySelectorAll('#tsb-blocks .gjs-block-category').forEach((category) => {
            let visibleCount = 0;
            category.querySelectorAll('.gjs-block').forEach((block) => {
                const label = block.textContent?.toLowerCase() ?? '';
                const keywords = block.getAttribute('data-keywords')?.toLowerCase() ?? '';
                const match = ! query || label.includes(query) || keywords.includes(query);
                block.style.display = match ? '' : 'none';
                if (match) visibleCount += 1;
            });
            category.classList.toggle('gjs-hidden', visibleCount === 0);
        });
    });

    const menuPlaceholderHtml = config.menuPlaceholderHtml
        || '<span class="ts-menu-placeholder" data-ts-menu-primary data-gjs-name="Menü" data-gjs-editable="false" data-gjs-removable="false" data-gjs-copyable="false" data-gjs-draggable="false">Menü</span>';
    const menuMobilePlaceholderHtml = config.menuMobilePlaceholderHtml
        || '<span class="ts-menu-placeholder" data-ts-menu-primary-mobile data-gjs-name="Mobil menü" data-gjs-editable="false" data-gjs-removable="false" data-gjs-copyable="false" data-gjs-draggable="false">Mobil menü</span>';

    const ensureHeaderMenuPlaceholder = () => {
        if (config.target !== 'header') return;

        const wrapper = editor.getWrapper();
        if (! wrapper) return;

        const navs = [
            ...wrapper.find('nav.ts-nav-links'),
            ...wrapper.find('nav[data-ts-menu-slot=primary]'),
            ...wrapper.find('.ts-header-simple nav.ts-nav-links'),
        ];

        const seen = new Set();
        navs.forEach((nav) => {
            if (! nav || seen.has(nav)) return;
            seen.add(nav);

            nav.addAttributes({ 'data-ts-menu-slot': 'primary' });
            nav.components(menuPlaceholderHtml);
            nav.find('[data-ts-menu-primary]').forEach((el) => {
                el.set({
                    editable: false,
                    removable: false,
                    copyable: false,
                    draggable: false,
                    highlightable: true,
                    name: 'Menü',
                });
            });
        });

        wrapper.find('[data-ts-menu-slot=primary-mobile]').forEach((slot) => {
            slot.components(menuMobilePlaceholderHtml);
            slot.find('[data-ts-menu-primary-mobile]').forEach((el) => {
                el.set({
                    editable: false,
                    removable: false,
                    copyable: false,
                    draggable: false,
                    highlightable: true,
                    name: 'Mobil menü',
                });
            });
        });
    };

    const fixInvalidDesktopMediaRules = () => {
        try {
            const rules = editor.Css?.getAll?.() || editor.CssComposer?.getAll?.();
            if (! rules) return;
            const list = typeof rules.forEach === 'function' ? rules : (typeof rules.each === 'function' ? null : rules);
            const visit = (rule) => {
                const media = String(rule.get?.('mediaText') || rule.get?.('media') || '');
                if (! /max-width\s*:\s*100%/i.test(media)) return;
                rule.set?.({ mediaText: '', atRuleType: '' });
            };
            if (list && typeof list.forEach === 'function') {
                list.forEach(visit);
            } else if (typeof rules.each === 'function') {
                rules.each(visit);
            } else if (Array.isArray(rules)) {
                rules.forEach(visit);
            }
        } catch (e) {
            // ignore
        }
    };

    if (config.projectData) {
        editor.loadProjectData(config.projectData);
        fixInvalidDesktopMediaRules();
    } else {
        editor.setComponents(config.html || `<section style="padding:48px;font-family:var(--font-sans),sans-serif;"><h1>Új tartalom</h1><p>${config.emptyHint || ''}</p></section>`);
        if (config.css) {
            editor.setStyle(config.css);
        }
        if (config.target === 'header') {
            editor.addStyle(headerCanvasReadableCss);
        }
    }
    fixInvalidDesktopMediaRules();

    if (config.target === 'footer') {
        const wrap = editor.getWrapper?.();
        wrap?.set?.({ droppable: true });
        editor.on('load', () => {
            editor.getWrapper?.()?.set?.({ droppable: true });
        });
    }

    ensureHeaderMenuPlaceholder();
    flushAllInteractiveBlocks();

    const bootSelectChrome = () => {
        if (config.target !== 'header' && config.target !== 'footer') return;
        const chrome = findChromeRoot(editor.getWrapper?.());
        if (! chrome) return;
        selectingBlockRoot = true;
        try {
            editor.select(chrome);
        } finally {
            selectingBlockRoot = false;
        }
        const resolved = resolveTraitableModel(chrome);
        if (resolved) {
            applyBlockTraits(resolved.model, resolved.block);
        }
    };
    setTimeout(bootSelectChrome, 80);
    editor.on('load', () => setTimeout(bootSelectChrome, 40));

    const initHeaderNavPreview = () => {
        if (config.target !== 'header') return;
        const doc = editor.Canvas?.getFrameEl?.()?.contentDocument;
        if (! doc) return;

        const navRoot = doc.querySelector('[data-site-nav]');
        if (! navRoot || navRoot.dataset.tsNavBound === '1') return;
        navRoot.dataset.tsNavBound = '1';

        const mobileToggle = navRoot.querySelector('[data-nav-toggle]');
        const mobilePanel = navRoot.querySelector('[data-nav-panel]');
        const mobileOverlay = navRoot.querySelector('[data-nav-overlay]');
        const panelClose = navRoot.querySelector('[data-nav-close]');
        if (! mobileToggle || ! mobilePanel) return;

        mobilePanel.removeAttribute('hidden');
        const validMobileStyles = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen'];
        const rawMenuStyle = navRoot.getAttribute('data-mobile-menu-style') || 'dropdown';
        const menuStyle = validMobileStyles.find((s) => rawMenuStyle.startsWith(s)) || 'dropdown';
        navRoot.setAttribute('data-mobile-menu-style', menuStyle);
        const usesOverlay = menuStyle !== 'dropdown';

        const setOpen = (open) => {
            navRoot.classList.toggle('is-menu-open', open);
            mobilePanel.classList.toggle('is-open', open);
            mobilePanel.removeAttribute('hidden');
            mobilePanel.setAttribute('aria-hidden', open ? 'false' : 'true');
            mobileToggle.classList.toggle('is-open', open);
            mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            doc.body.classList.toggle('ts-nav-open', open && usesOverlay);
            if (mobileOverlay) {
                mobileOverlay.classList.toggle('is-open', open && usesOverlay);
                mobileOverlay.hidden = !(open && usesOverlay);
            }
        };

        setOpen(false);
        mobileToggle.addEventListener('click', () => setOpen(!navRoot.classList.contains('is-menu-open')));
        panelClose?.addEventListener('click', () => setOpen(false));
        mobileOverlay?.addEventListener('click', () => setOpen(false));
    };

    editor.on('load', () => setTimeout(initHeaderNavPreview, 120));
    editor.on('component:selected', () => setTimeout(initHeaderNavPreview, 0));

    editor.on('block:drag:stop', () => {
        if (config.target === 'header') {
            ensureHeaderMenuPlaceholder();
        }
    });

    const finalizeExportedHtml = (html) => {
        if (! html || typeof DOMParser === 'undefined') return html;

        try {
            const doc = new DOMParser().parseFromString(`<div id="tsb-export-root">${html}</div>`, 'text/html');
            const root = doc.getElementById('tsb-export-root');
            if (! root) return html;

            if (config.target === 'header') {
                const nav = root.querySelector('nav.ts-nav-links, nav[data-ts-menu-slot="primary"], header nav.ts-nav-links');
                if (nav) {
                    nav.setAttribute('data-ts-menu-slot', 'primary');
                    nav.innerHTML = menuPlaceholderHtml;
                }
                const mobileSlot = root.querySelector('[data-ts-menu-slot="primary-mobile"]');
                if (mobileSlot) {
                    mobileSlot.innerHTML = menuMobilePlaceholderHtml;
                }
                const header = root.querySelector('header[data-site-nav], header.site-nav, header.ts-header-simple');
                if (header) {
                    const logoUrl = header.getAttribute('data-logo-url') || '';
                    header.classList.toggle('has-logo', logoUrl.trim() !== '');
                    header.querySelectorAll('[data-ts-logo]').forEach((img) => {
                        if (logoUrl.trim()) {
                            img.setAttribute('src', logoUrl);
                            img.setAttribute('alt', header.getAttribute('data-brand') || '');
                        }
                    });
                    const logoHeight = String(header.getAttribute('data-logo-height') || '40').replace(/[^\d.]/g, '') || '40';
                    const logoMaxWidth = String(header.getAttribute('data-logo-max-width') || '180').replace(/[^\d.]/g, '') || '180';
                    header.style.setProperty('--ts-logo-height', `${logoHeight}px`);
                    header.style.setProperty('--ts-logo-max-width', `${logoMaxWidth}px`);
                    header.setAttribute('data-logo-height', logoHeight);
                    header.setAttribute('data-logo-max-width', logoMaxWidth);

                    const mobileStyle = ['dropdown', 'drawer-left', 'drawer-right', 'fullscreen']
                        .find((s) => String(header.getAttribute('data-mobile-menu-style') || '').startsWith(s)) || 'dropdown';
                    header.setAttribute('data-mobile-menu-style', mobileStyle);

                    header.querySelector('[data-nav-panel]')?.removeAttribute('hidden');
                }
            }

            root.querySelectorAll('[data-media-url], [data-background-url], [data-overlay], [data-overlay-color]').forEach((section) => {
                const mediaUrl = section.getAttribute('data-media-url')
                    || section.getAttribute('data-background-url')
                    || '';
                const overlay = section.getAttribute('data-overlay');
                const overlayColor = String(section.getAttribute('data-overlay-color') || '').trim();

                if (mediaUrl) {
                    section.querySelectorAll('[data-ts-bg-image], [data-ts-hero-image]').forEach((img) => {
                        img.setAttribute('src', mediaUrl);
                    });
                    section.querySelectorAll('[data-ts-bg-source], [data-ts-hero-source]').forEach((source) => {
                        source.setAttribute('src', mediaUrl);
                    });
                    section.querySelectorAll('[data-ts-bg-style]:not([data-ts-src-from])').forEach((node) => {
                        node.style.backgroundImage = `url("${mediaUrl}")`;
                    });
                } else {
                    section.querySelectorAll('[data-ts-bg-style]:not([data-ts-src-from])').forEach((node) => {
                        node.style.backgroundImage = 'none';
                    });
                }

                if ((overlay != null && overlay !== '') || overlayColor) {
                    section.querySelectorAll('[data-ts-bg-overlay], [data-ts-hero-overlay]').forEach((node) => {
                        applyOverlayNodeStyles(node, {
                            opacity: overlay || section.getAttribute('data-overlay') || '0.7',
                            color: overlayColor,
                            layout: section.getAttribute('data-layout') || 'bottom',
                            isHero: node.hasAttribute('data-ts-hero-overlay'),
                        });
                    });
                }

                if (overlayColor) {
                    section.style.setProperty('--ts-overlay-color', overlayColor);
                }

                // Surface overlay rétegezés: tartalom a overlay fölött maradjon a mentett HTML-ben is
                if (
                    ! section.classList.contains('ts-hero-slider')
                    && (section.querySelector(':scope > [data-ts-bg-overlay], :scope > .ts-surface-overlay'))
                ) {
                    if (! section.style.position || section.style.position === 'static') {
                        section.style.position = 'relative';
                    }
                    Array.from(section.children).forEach((child) => {
                        if (! (child instanceof HTMLElement)) return;
                        if (
                            child.hasAttribute('data-ts-bg-overlay')
                            || child.classList.contains('ts-surface-overlay')
                        ) {
                            if (! child.style.position) child.style.position = 'absolute';
                            if (! child.style.inset) {
                                child.style.top = '0';
                                child.style.right = '0';
                                child.style.bottom = '0';
                                child.style.left = '0';
                            }
                            child.style.zIndex = '1';
                            child.style.pointerEvents = 'none';
                            return;
                        }
                        if (child.tagName === 'STYLE') return;
                        if (! child.style.position || child.style.position === 'static') {
                            child.style.position = 'relative';
                        }
                        child.style.zIndex = '2';
                    });
                }
            });

            root.querySelectorAll('.ts-hero__overlay, .ts-banner__overlay, .ts-bg-section__overlay, .ts-stats__overlay').forEach((node) => {
                if (node.hasAttribute('data-ts-bg-overlay') || node.hasAttribute('data-ts-hero-overlay')) {
                    return;
                }
                const section = node.closest('[data-gjs-type], .ts-hero, .ts-banner, .ts-bg-section, .ts-stats');
                if (! section) return;
                applyOverlayNodeStyles(node, {
                    opacity: section.getAttribute('data-overlay') || '0.7',
                    color: section.getAttribute('data-overlay-color') || '',
                    layout: section.getAttribute('data-layout') || 'bottom',
                    isHero: node.classList.contains('ts-hero__overlay'),
                });
            });

            root.querySelectorAll('[data-extra-class]').forEach((section) => {
                const tokens = String(section.getAttribute('data-extra-class') || '')
                    .split(/\s+/)
                    .map((token) => token.trim())
                    .filter((token) => /^[A-Za-z_][A-Za-z0-9_-]*$/.test(token));
                tokens.forEach((name) => section.classList.add(name));
                if (tokens.length) {
                    section.setAttribute('data-extra-class', tokens.join(' '));
                }
            });

            root.querySelectorAll('[data-ts-src-from]').forEach((node) => {
                const key = node.getAttribute('data-ts-src-from');
                if (! key) return;
                const section = node.closest('[data-gjs-type]');
                if (! section) return;
                const attrName = `data-${key.replaceAll('_', '-')}`;
                const value = section.getAttribute(attrName) || '';
                if (node.tagName === 'IMG') {
                    node.setAttribute('src', value);
                } else if (value) {
                    node.style.backgroundImage = `url("${value}")`;
                } else {
                    node.style.backgroundImage = 'none';
                }
            });

            root.querySelectorAll('[data-embed-url]').forEach((section) => {
                const embed = section.getAttribute('data-embed-url') || '';
                section.querySelectorAll('iframe').forEach((frame) => {
                    frame.setAttribute('src', embed);
                });
            });

            root.querySelectorAll('[data-ts-href]').forEach((node) => {
                const key = node.getAttribute('data-ts-href');
                if (! key) return;
                const section = node.closest('[data-gjs-type]');
                if (! section) return;
                const hrefAttr = `data-${key.replaceAll('_', '-')}`;
                const href = section.getAttribute(hrefAttr);
                if (href != null) {
                    node.setAttribute('href', href);
                }
                const targetAttr = hrefTargetAttrName(key);
                const target = section.getAttribute(targetAttr) || '';
                if (target === '_blank') {
                    node.setAttribute('target', '_blank');
                    node.setAttribute('rel', 'noopener noreferrer');
                } else {
                    node.removeAttribute('target');
                    node.removeAttribute('rel');
                }
            });

            root.querySelectorAll('[data-ts-items]').forEach((container) => {
                const section = container.closest('[data-gjs-type]');
                if (! section) return;
                const key = container.getAttribute('data-ts-items') || 'items';
                const kind = container.getAttribute('data-ts-items-kind') || key;
                const attrName = `data-${String(key).replaceAll('_', '-')}`;
                const sectionAttrs = {};
                Array.from(section.attributes || []).forEach((a) => {
                    sectionAttrs[a.name] = a.value;
                });
                const items = kind === 'gallery'
                    ? galleryItemsFromAttrs(sectionAttrs)
                    : parseItemsJson(section.getAttribute(attrName) || '[]');
                const layout = section.getAttribute('data-layout') || '';
                container.innerHTML = renderItemsHtml(kind, items, { layout });
            });

            root.querySelectorAll('.is-ts-revealed').forEach((el) => el.classList.remove('is-ts-revealed'));
            root.querySelectorAll('.ts-reveal-item').forEach((el) => {
                el.classList.remove('ts-reveal-item');
                el.style.removeProperty('--ts-reveal-i');
            });
            root.querySelectorAll('[data-reveal-children="0"]').forEach((el) => {
                el.removeAttribute('data-reveal-children');
            });

            return root.innerHTML;
        } catch (e) {
            console.error(e);
            return html;
        }
    };

    const syncNow = async ({ openPreview = false } = {}) => {
        if (syncing) return false;

        syncing = true;
        if (saveBtn) saveBtn.disabled = true;
        setStatus('Mentés…');

        try {
            flushAllInteractiveBlocks();
            fixInvalidDesktopMediaRules();
            setStatus('Média előkészítése…');
            const materialized = await materializeEmbeddedMediaBeforeSave();
            if (materialized) {
                showToast('Beágyazott kép(ek) feltöltve a médiatárba');
            }
            let html = finalizeExportedHtml(editor.getHtml());
            let css = editor.getCss() || '';
            const uriCache = new Map();
            html = (await replaceDataUrisInString(html, uriCache)).value;
            css = (await replaceDataUrisInString(css, uriCache)).value;
            const projectData = slimProjectDataForSync(editor.getProjectData());
            await $wire.syncFromEditor(html, css, projectData);
            dirty = false;
            setStatus('Mentve ' + new Date().toLocaleTimeString('hu-HU'));
            showToast('Mentve');

            if (openPreview && config.previewUrl) {
                const url = config.previewUrl + (config.previewUrl.includes('?') ? '&' : '?') + '_t=' + Date.now();
                window.open(url, '_blank', 'noopener');
            }

            return true;
        } catch (e) {
            console.error(e);
            setStatus('Mentési hiba');
            showToast('Mentési hiba');
            return false;
        } finally {
            syncing = false;
            if (saveBtn) saveBtn.disabled = false;
        }
    };

    editor.on('update', () => markDirty());

    saveBtn?.addEventListener('click', () => syncNow());
    previewBtn?.addEventListener('click', () => syncNow({ openPreview: true }));

    window.addEventListener('keydown', (event) => {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') {
            event.preventDefault();
            syncNow();
        }
    });

    setTimeout(() => {
        ready = true;
        if (loadingEl) loadingEl.hidden = true;
        if (workspaceEl) workspaceEl.hidden = false;
        setStatus(dirty ? 'Előnézet – nincs mentve' : 'Előnézet – nincs mentetlen változás');
    }, 500);
</script>
@endscript
