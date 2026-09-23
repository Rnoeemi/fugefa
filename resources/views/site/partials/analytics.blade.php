@php
    $analyticsEnabled = (bool) config('seo.analytics.enabled');
    $ga4Id = trim((string) config('seo.analytics.ga4_id'));
    $gtmId = trim((string) config('seo.analytics.gtm_id'));
    $hasAnalytics = $analyticsEnabled && ($ga4Id !== '' || $gtmId !== '');
@endphp

@if ($hasAnalytics)
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied',
            wait_for_update: 500
        });
        gtag('js', new Date());
        window.__siteAnalytics = {
            ga4Id: @json($ga4Id !== '' ? $ga4Id : null),
            gtmId: @json($gtmId !== '' ? $gtmId : null),
            loaded: false
        };
        window.__loadSiteAnalytics = function () {
            if (window.__siteAnalytics.loaded) {
                return;
            }
            window.__siteAnalytics.loaded = true;

            @if ($gtmId !== '')
                (function (w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
                    var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s),
                        dl = l !== 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer', @json($gtmId));
            @elseif ($ga4Id !== '')
                var s = document.createElement('script');
                s.async = true;
                s.src = 'https://www.googletagmanager.com/gtag/js?id=' + @json($ga4Id);
                document.head.appendChild(s);
                gtag('config', @json($ga4Id), { anonymize_ip: true });
            @endif
        };
    </script>
@endif
