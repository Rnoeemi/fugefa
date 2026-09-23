@php
    $analyticsEnabled = (bool) config('seo.analytics.enabled');
    $ga4Id = trim((string) config('seo.analytics.ga4_id'));
    $gtmId = trim((string) config('seo.analytics.gtm_id'));
    $showConsent = $analyticsEnabled && ($ga4Id !== '' || $gtmId !== '');
    $privacyUrl = (string) config('seo.privacy_url', '/oldal/adatkezelesi-tajekoztato');
    $cookiePolicyUrl = (string) config('seo.cookie_policy_url', $privacyUrl);
@endphp

@if ($showConsent)
    <div
        id="cookie-consent"
        class="cookie-consent"
        role="region"
        aria-labelledby="cookie-consent-title"
        aria-describedby="cookie-consent-desc"
        aria-hidden="true"
        hidden
    >
        <div class="cookie-consent__inner">
            <div class="cookie-consent__copy">
                <h2 id="cookie-consent-title" class="cookie-consent__title">Süti beállítások</h2>
                <p id="cookie-consent-desc" class="cookie-consent__text">
                    A weboldal a működéshez szükséges sütiket használ.
                    Statisztikai sütiket (Google Analytics) csak az Ön hozzájárulása után töltünk be.
                    Részletek az
                    <a href="{{ $cookiePolicyUrl }}">adatkezelési / süti tájékoztatóban</a>.
                </p>
            </div>
            <div class="cookie-consent__actions">
                <button type="button" class="cookie-consent__btn cookie-consent__btn--reject" data-cookie-reject>
                    Elutasítom
                </button>
                <button type="button" class="cookie-consent__btn cookie-consent__btn--accept" data-cookie-accept>
                    Elfogadom
                </button>
            </div>
        </div>
    </div>

    <p class="cookie-consent-reopen-wrap">
        <button type="button" class="cookie-consent-reopen" data-cookie-settings>
            Süti beállítások
        </button>
    </p>

    <script>
        (function () {
            var STORAGE_KEY = 'cookie-consent';
            var VERSION_KEY = 'cookie-consent-version';
            var CONSENT_VERSION = '1';
            var banner = document.getElementById('cookie-consent');
            if (!banner) return;

            function getConsent() {
                try {
                    var version = localStorage.getItem(VERSION_KEY);
                    if (version !== CONSENT_VERSION) {
                        return null;
                    }
                    return localStorage.getItem(STORAGE_KEY);
                } catch (e) {
                    return null;
                }
            }

            function setConsent(value) {
                try {
                    localStorage.setItem(STORAGE_KEY, value);
                    localStorage.setItem(VERSION_KEY, CONSENT_VERSION);
                } catch (e) {}
            }

            function showBanner(visible) {
                banner.hidden = !visible;
                banner.setAttribute('aria-hidden', visible ? 'false' : 'true');
            }

            function grantConsent() {
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        analytics_storage: 'granted',
                        ad_storage: 'denied',
                        ad_user_data: 'denied',
                        ad_personalization: 'denied'
                    });
                }
                if (typeof window.__loadSiteAnalytics === 'function') {
                    window.__loadSiteAnalytics();
                }
            }

            function denyConsent() {
                if (typeof gtag === 'function') {
                    gtag('consent', 'update', {
                        analytics_storage: 'denied',
                        ad_storage: 'denied',
                        ad_user_data: 'denied',
                        ad_personalization: 'denied'
                    });
                }
            }

            function applyStored() {
                var value = getConsent();
                if (value === 'accepted') {
                    grantConsent();
                    showBanner(false);
                    return;
                }
                if (value === 'rejected') {
                    denyConsent();
                    showBanner(false);
                    return;
                }
                showBanner(true);
            }

            banner.querySelector('[data-cookie-accept]')?.addEventListener('click', function () {
                setConsent('accepted');
                grantConsent();
                showBanner(false);
            });

            banner.querySelector('[data-cookie-reject]')?.addEventListener('click', function () {
                setConsent('rejected');
                denyConsent();
                showBanner(false);
            });

            document.querySelectorAll('[data-cookie-settings]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    try {
                        localStorage.removeItem(STORAGE_KEY);
                    } catch (e) {}
                    showBanner(true);
                    banner.querySelector('[data-cookie-accept]')?.focus();
                });
            });

            applyStored();
        })();
    </script>
@endif
