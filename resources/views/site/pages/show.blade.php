@extends('layouts.site')

@php
    use App\Support\Seo\SitePageSeo;

    $rawSeo = SitePageSeo::normalize($page->seo);
    $seo = SitePageSeo::withAutoFill($rawSeo, $page);
    $siteName = SitePageSeo::siteName();
    $documentTitle = filled($rawSeo['meta_title'])
        ? $rawSeo['meta_title']
        : ($page->title.' – '.$siteName);
    $ogImage = SitePageSeo::absoluteUrl($seo['og_image']);
    $twitterImage = SitePageSeo::absoluteUrl($seo['twitter_image'] ?: $seo['og_image']);
    $canonical = SitePageSeo::absoluteUrl($seo['canonical_url'] ?: SitePageSeo::pageUrl($page));
    $ogUrl = SitePageSeo::absoluteUrl($seo['og_url'] ?: $canonical);
@endphp

@section('title', $documentTitle)
@section('meta_description', filled($rawSeo['meta_description'])
    ? $rawSeo['meta_description']
    : (($siteSettings->site_name ?? 'Tüsiszállás').' – vendégház, szoba és pihenés nyugodt környezetben. Online foglalás.'))

@push('head')
    @if (filled($seo['meta_keywords']))
        <meta name="keywords" content="{{ $seo['meta_keywords'] }}">
    @endif
    @if (filled($seo['robots']))
        <meta name="robots" content="{{ $seo['robots'] }}">
    @endif
    @if (filled($canonical))
        <link rel="canonical" href="{{ $canonical }}">
    @endif

    <meta property="og:type" content="{{ $seo['og_type'] ?: 'website' }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $seo['og_title'] ?: $documentTitle }}">
    @if (filled($seo['og_description'] ?: $seo['meta_description']))
        <meta property="og:description" content="{{ $seo['og_description'] ?: $seo['meta_description'] }}">
    @endif
    @if (filled($ogUrl))
        <meta property="og:url" content="{{ $ogUrl }}">
    @endif
    @if (filled($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <meta name="twitter:card" content="{{ $seo['twitter_card'] ?: 'summary_large_image' }}">
    <meta name="twitter:title" content="{{ $seo['twitter_title'] ?: ($seo['og_title'] ?: $documentTitle) }}">
    @if (filled($seo['twitter_description'] ?: $seo['og_description'] ?: $seo['meta_description']))
        <meta name="twitter:description" content="{{ $seo['twitter_description'] ?: ($seo['og_description'] ?: $seo['meta_description']) }}">
    @endif
    @if (filled($twitterImage))
        <meta name="twitter:image" content="{{ $twitterImage }}">
    @endif

    @if ($page->is_homepage)
        @php
            $schemaSettings = $siteSettings ?? \App\Models\SiteSetting::current();
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'LodgingBusiness',
                'name' => $siteName,
                'url' => $canonical ?: url('/'),
                'description' => $seo['meta_description'] ?: $documentTitle,
            ];
            if (filled($schemaSettings->phone)) {
                $schema['telephone'] = $schemaSettings->phone;
            }
            if (filled($schemaSettings->email)) {
                $schema['email'] = $schemaSettings->email;
            }
            if (filled($schemaSettings->address)) {
                $schema['address'] = [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $schemaSettings->address,
                    'addressLocality' => 'Tüsifalu',
                    'addressCountry' => 'HU',
                ];
            }
            if (filled($ogImage)) {
                $schema['image'] = $ogImage;
            }
        @endphp
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
    @endif

    @if ($cssUrl = $page->cssPublicUrl())
        <link rel="stylesheet" href="{{ $cssUrl }}">
    @elseif (filled($page->css))
        <style>{!! $page->css !!}</style>
    @endif
    <style>{!! \App\Support\GrapesJs\SiteDynamicBlockStyles::css() !!}</style>
@endpush

@section('content')
    @php
        $needsNavOffset = ! ($siteSettings ?? \App\Models\SiteSetting::current())->hasCustomHeader();
    @endphp
    <article @class(['site-builder-page', 'pt-24' => $needsNavOffset])>
        <div class="site-builder-content">
            {!! \App\Support\PhoneNormalizer::sanitizeHtml(
                app(\App\Services\SiteDynamicBlockRenderer::class)->hydrate(
                    \App\Support\GrapesJs\SiteRichAttr::repairFeaturesSections($page->html ?? '')
                )
            ) !!}
        </div>
    </article>
@endsection
