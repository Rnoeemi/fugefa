<?php

namespace App\Support\GrapesJs;

class SiteDynamicBlockStyles
{
    public static function css(): string
    {
        return <<<'CSS'
.ts-dyn-block{font-family:var(--font-sans,Karla,sans-serif);color:var(--color-text)}
.ts-dyn-cards,.ts-dyn-list,.ts-dyn-featured,.ts-dyn-booking-cta,.ts-dyn-search,.ts-dyn-details,.ts-dyn-gallery,.ts-dyn-contact-form,.ts-dyn-map,.ts-dyn-appointment{padding:3.5rem 1.5rem}
.ts-dyn-cards__inner,.ts-dyn-list__inner,.ts-dyn-featured__inner,.ts-dyn-booking-cta__inner,.ts-dyn-search__inner,.ts-dyn-details__inner,.ts-dyn-gallery__inner,.ts-dyn-contact-form__inner,.ts-dyn-map__inner,.ts-dyn-appointment__inner{max-width:72rem;margin:0 auto}
.ts-dyn-appointment__title{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.4rem);margin:0 0 .5rem}
.ts-dyn-appointment__text{margin:0 0 1.5rem;opacity:.75;line-height:1.6}
.ts-dyn-appointment__text p{margin:0 0 .75rem}
.ts-dyn-appointment__text p:last-child{margin-bottom:0}
.ts-dyn-appointment__text strong{font-weight:600}
.ts-dyn-appointment__grid{display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fit,minmax(240px,1fr))}
.ts-dyn-appointment__card{background:#fff;border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);padding:1.25rem}
.ts-dyn-appointment__role{margin:0;font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;opacity:.55;font-weight:600}
.ts-dyn-appointment__card h3{margin:.35rem 0 .5rem;font-family:var(--font-display,Literata,serif);font-size:1.35rem}
.ts-dyn-appointment__card p{margin:0;line-height:1.55;opacity:.8;font-size:.95rem}
.ts-dyn-appointment__meta{margin-top:.75rem!important;font-size:.85rem!important}
.ts-dyn-appointment__cta{display:inline-block;margin-top:1rem;text-decoration:none;background:var(--btn-bg,var(--color-accent));color:var(--btn-fg,#fff);padding:.7rem 1rem;font-size:var(--btn-font-size,.75rem);letter-spacing:var(--tracking-btn,.1em);text-transform:var(--btn-text-transform,uppercase);font-weight:var(--btn-font-weight,600);border-radius:var(--radius-control,.55rem);box-shadow:var(--btn-shadow,none)}
.ts-dyn-cards__title,.ts-dyn-list__title{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.5rem);margin:0 0 1.75rem}
.ts-dyn-cards__grid{display:grid;gap:1.25rem;grid-template-columns:repeat(auto-fit,minmax(260px,1fr))}
.ts-dyn-card{background:#fff;border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);overflow:hidden}
.ts-dyn-card__media{display:block}
.ts-dyn-card__media img{display:block;width:100%;aspect-ratio:4/3;object-fit:cover}
.ts-dyn-card__body{padding:1.25rem}
.ts-dyn-card__type,.ts-dyn-list__type,.ts-dyn-featured__type{margin:0;font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;opacity:.55;font-weight:600}
.ts-dyn-card__name{margin:.35rem 0 .5rem;font-family:var(--font-display,Literata,serif);font-size:1.35rem}
.ts-dyn-card__name a,.ts-dyn-list__row h3 a,.ts-dyn-featured__body h2{color:inherit;text-decoration:none}
.ts-dyn-card__desc,.ts-dyn-featured__body p{margin:0;line-height:1.55;opacity:.8;font-size:.95rem}
.ts-dyn-card__price{margin:1rem 0 0;padding-top:.9rem;border-top:1px solid color-mix(in srgb,var(--color-text) 10%,transparent);display:flex;flex-wrap:wrap;align-items:baseline;column-gap:.4rem;row-gap:.15rem;line-height:1.2}
.ts-dyn-card__price-amount{font-family:var(--font-display,Literata,serif);font-size:clamp(1.35rem,2.2vw,1.65rem);font-weight:700;letter-spacing:-.01em;color:var(--color-primary,var(--color-text))!important}
.ts-dyn-card__price-unit{font-size:.9rem;font-weight:500;opacity:.65}
.ts-dyn-card__price-note{flex-basis:100%;font-size:.8rem;opacity:.55;margin-top:.15rem}
.ts-dyn-list__price,.ts-dyn-featured__price{margin:.75rem 0 0;font-size:.9rem}
.ts-dyn-card__actions{display:flex;flex-wrap:nowrap;align-items:stretch;gap:.75rem;margin-top:1rem}
.ts-dyn-card__actions a{flex:1 1 0;min-width:0;display:inline-flex;align-items:center;justify-content:center;text-align:center;text-decoration:none;box-sizing:border-box;font-size:var(--btn-font-size,.75rem);letter-spacing:var(--tracking-btn,.1em);text-transform:var(--btn-text-transform,uppercase);font-weight:var(--btn-font-weight,600);padding:var(--btn-pad-y,.7rem) var(--btn-pad-x,1rem);border-radius:var(--radius-control,.55rem);border:var(--border-width,1px) solid transparent}
.ts-dyn-featured__actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1rem}
.ts-dyn-featured__actions a,.ts-dyn-list__cta,.ts-dyn-booking-cta__inner a{text-decoration:none;font-size:var(--btn-font-size,.75rem);letter-spacing:var(--tracking-btn,.1em);text-transform:var(--btn-text-transform,uppercase);font-weight:var(--btn-font-weight,600);color:var(--link-color,var(--color-accent));border-radius:var(--radius-control,.55rem)}
.ts-dyn-card__cta,.ts-dyn-featured__actions a.is-primary,.ts-dyn-list__cta,.ts-dyn-booking-cta__inner a{background:var(--btn-bg,var(--color-accent));color:var(--btn-fg,#fff)!important;box-shadow:var(--btn-shadow,none)}
.ts-dyn-card__cta--outline{background:transparent!important;color:var(--btn-secondary-fg,var(--btn-ghost-fg,var(--color-text)))!important;border-color:var(--btn-secondary-border,var(--btn-ghost-border,color-mix(in srgb,var(--color-text) 22%,transparent)))!important;box-shadow:none!important}
.ts-dyn-list__rows{display:grid;gap:.75rem}
.ts-dyn-list__row{display:flex;justify-content:space-between;gap:1rem;align-items:center;padding:1rem 1.15rem;border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);background:#fff}
.ts-dyn-list__row h3{margin:.2rem 0;font-family:var(--font-display,Literata,serif);font-size:1.2rem}
.ts-dyn-featured{background:var(--color-light)}
.ts-dyn-featured__eyebrow{margin:0 0 1rem;letter-spacing:.16em;text-transform:uppercase;font-size:.75rem;opacity:.55}
.ts-dyn-featured__grid{display:grid;gap:1.5rem;grid-template-columns:minmax(0,1.1fr) minmax(0,1fr);align-items:center}
.ts-dyn-featured__media img{width:100%;aspect-ratio:4/3;object-fit:cover;display:block}
.ts-dyn-featured__body h2{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.4rem);margin:.35rem 0 .75rem}
.ts-dyn-booking-cta{background:var(--color-primary-mid);color:#fff}
.ts-dyn-booking-cta__inner{display:flex;flex-wrap:wrap;justify-content:space-between;gap:1.25rem;align-items:center}
.ts-dyn-booking-cta__inner h2{font-family:var(--font-display,Literata,serif);margin:0 0 .35rem;font-size:clamp(1.5rem,3vw,2rem)}
.ts-dyn-booking-cta__inner p{margin:0;opacity:.8}
.ts-dyn-empty{margin:0;opacity:.7}
.ts-dyn-search{background:var(--color-light)}
.ts-dyn-search__title{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.4rem);margin:0 0 .5rem}
.ts-dyn-search__text{margin:0 0 1.5rem;opacity:.75;line-height:1.6}
.ts-dyn-search__form{display:grid;gap:.75rem;grid-template-columns:minmax(0,2.4fr) minmax(0,1.15fr) minmax(0,1.15fr) minmax(3.25rem,4.25rem) auto;align-items:end;background:#fff;border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);padding:1rem 1.15rem}
.ts-dyn-search__field label{display:block;font-size:.65rem;letter-spacing:.1em;text-transform:uppercase;opacity:.55;margin-bottom:.3rem;font-weight:600}
.ts-dyn-search__field input,.ts-dyn-search__field select,.ts-dyn-contact-form__form input:not([type="checkbox"]):not([type="hidden"]):not([type="radio"]),.ts-dyn-contact-form__form textarea{width:100%;padding:.55rem .65rem;border:1px solid color-mix(in srgb, var(--color-text) 15%, transparent);background:#fff;font-size:.8125rem;line-height:1.35;border-radius:var(--radius-control,.55rem)}
.ts-dyn-search__field--accommodation select{min-width:0;text-overflow:ellipsis}
.ts-dyn-search__field--guests input{text-align:center;padding-left:.35rem;padding-right:.35rem;-moz-appearance:textfield}
.ts-dyn-search__field--guests input::-webkit-outer-spin-button,.ts-dyn-search__field--guests input::-webkit-inner-spin-button{-webkit-appearance:none;margin:0}
.ts-dyn-contact-form__form input:not([type="checkbox"]):not([type="hidden"]):not([type="radio"]),.ts-dyn-contact-form__form textarea{font:inherit;padding:.7rem .8rem}
.ts-dyn-search__actions button,.ts-dyn-contact-form__form button{appearance:none;border:0;background:var(--btn-bg,var(--color-accent));color:var(--btn-fg,#fff);padding:var(--btn-pad-y,.85rem) var(--btn-pad-x,1.2rem);font-size:var(--btn-font-size,.75rem);letter-spacing:var(--tracking-btn,.12em);text-transform:var(--btn-text-transform,uppercase);font-weight:var(--btn-font-weight,600);cursor:pointer;border-radius:var(--radius-control,.55rem);box-shadow:var(--btn-shadow,none)}
.ts-dyn-details__eyebrow,.ts-dyn-details__type{margin:0;font-size:.7rem;letter-spacing:.14em;text-transform:uppercase;opacity:.55;font-weight:600}
.ts-dyn-details__grid{display:grid;gap:1.5rem;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr)}
.ts-dyn-details__grid h2{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.4rem);margin:.35rem 0 1rem}
.ts-dyn-details__body{white-space:pre-line;line-height:1.65;opacity:.85}
.ts-dyn-details aside{background:var(--color-light);border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);padding:1.25rem}
.ts-dyn-details aside h3{font-family:var(--font-display,Literata,serif);font-size:1.35rem;margin:0 0 .75rem}
.ts-dyn-details aside h3+ul+h3{margin-top:1.5rem}
.ts-dyn-details ul{list-style:none;margin:0;padding:0}
.ts-dyn-details li{padding:.55rem 0;border-bottom:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);font-size:.95rem}
.ts-dyn-details__actions{display:flex;flex-wrap:wrap;gap:.75rem;margin-top:1.25rem}
.ts-dyn-details__actions a{text-decoration:none;font-size:var(--btn-font-size,.75rem);letter-spacing:var(--tracking-btn,.1em);text-transform:var(--btn-text-transform,uppercase);font-weight:var(--btn-font-weight,600);color:var(--link-color,var(--color-accent));border-radius:var(--radius-control,.55rem)}
.ts-dyn-details__actions a.is-primary{background:var(--btn-bg,var(--color-accent));color:var(--btn-fg,#fff);padding:.7rem 1rem;box-shadow:var(--btn-shadow,none)}
.ts-dyn-gallery__title{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.25rem);margin:0 0 1.25rem}
.ts-dyn-gallery__grid{display:grid;gap:.75rem;grid-template-columns:repeat(4,1fr)}
.ts-dyn-gallery__grid figure{margin:0;aspect-ratio:1;overflow:hidden;background:var(--color-muted)}
.ts-dyn-gallery__grid img{width:100%;height:100%;object-fit:cover;display:block}
.ts-dyn-contact-form__inner{display:grid;gap:2rem;grid-template-columns:minmax(0,1fr) minmax(0,1.1fr)}
.ts-dyn-contact-form__info h2{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.25rem);margin:0 0 .75rem}
.ts-dyn-contact-form__info p{margin:0 0 1rem;line-height:1.6;opacity:.8}
.ts-dyn-contact-form__info ul{list-style:none;margin:0;padding:0}
.ts-dyn-contact-form__info li{margin:0 0 .55rem;line-height:1.5}
.ts-dyn-contact-form__info a{color:var(--link-color,var(--color-accent))}
.ts-dyn-contact-form__form{display:grid;gap:1.15rem;background:#fff;border:1px solid color-mix(in srgb, var(--color-text) 8%, transparent);padding:1.25rem}
.ts-dyn-contact-form__form > input[type="hidden"]{display:none!important;position:absolute;width:0;height:0;margin:0;padding:0;border:0;overflow:hidden}
.ts-dyn-contact-form__form > div{display:grid;gap:.35rem}
.ts-dyn-contact-form__form label{display:block;font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;opacity:.55;margin-bottom:0;font-weight:600}
.ts-dyn-contact-form__status{padding:.75rem .9rem;border:1px solid color-mix(in srgb, #059669 35%, transparent);background:color-mix(in srgb, #059669 10%, #fff);color:#065f46;font-size:.9rem;line-height:1.45;border-radius:var(--radius-control,.55rem)}
.ts-dyn-contact-form__errors{padding:.75rem .9rem;border:1px solid color-mix(in srgb, #dc2626 35%, transparent);background:color-mix(in srgb, #dc2626 8%, #fff);color:#991b1b;font-size:.9rem;line-height:1.45;border-radius:var(--radius-control,.55rem)}
.ts-dyn-contact-form__errors ul{list-style:disc;margin:0;padding-left:1.1rem}
.ts-dyn-contact-form__errors li{margin:0 0 .25rem}
.ts-dyn-contact-form__consent{margin:.35rem 0 .15rem}
.ts-dyn-contact-form__consent-label{display:flex!important;align-items:flex-start;gap:.65rem;margin:0!important;font-size:.9rem!important;letter-spacing:0!important;text-transform:none!important;opacity:1!important;font-weight:500!important;line-height:1.45;cursor:pointer;color:color-mix(in srgb, var(--color-text) 88%, transparent)}
.ts-dyn-contact-form__consent-input{appearance:auto;width:1.05rem!important;height:1.05rem!important;min-width:1.05rem;max-width:1.05rem;margin:.15rem 0 0!important;padding:0!important;border:1px solid color-mix(in srgb, var(--color-text) 35%, transparent)!important;border-radius:.2rem!important;background:#fff!important;flex:0 0 auto;accent-color:var(--color-accent,var(--color-primary-mid));box-shadow:none!important}
.ts-dyn-contact-form__consent-text{display:block}
.ts-dyn-contact-form__consent-text a{color:var(--link-color,var(--color-accent));text-decoration:underline;text-underline-offset:.12em}
.ts-dyn-contact-form__form > button[type="submit"]{justify-self:start;margin-top:.25rem}
.ts-dyn-map__inner{display:grid;gap:1.5rem;grid-template-columns:minmax(0,.9fr) minmax(0,1.3fr)}
.ts-dyn-map__copy h2{font-family:var(--font-display,Literata,serif);font-size:clamp(1.75rem,3vw,2.25rem);margin:0 0 .75rem}
.ts-dyn-map__copy p{margin:0 0 .5rem;line-height:1.6}
.ts-dyn-map__note{opacity:.7}
.ts-dyn-map__copy a{color:var(--link-color,var(--color-accent))}
.ts-dyn-map__frame{min-height:280px;background:var(--color-muted);overflow:hidden}
.ts-dyn-map__frame iframe{width:100%;height:100%;min-height:280px;border:0;display:block}
@media (max-width:768px){.ts-dyn-featured__grid,.ts-dyn-details__grid,.ts-dyn-contact-form__inner,.ts-dyn-map__inner,.ts-dyn-search__form{grid-template-columns:1fr}.ts-dyn-list__row{flex-direction:column;align-items:flex-start}.ts-dyn-gallery__grid{grid-template-columns:repeat(2,1fr)}}

/* —— Surface overlay (Style Manager háttér + szín): tartalom felett ne takarjon —— */
section:not(.ts-hero-slider)[data-overlay],
section:not(.ts-hero-slider):has(> .ts-surface-overlay),
section:not(.ts-hero-slider):has(> [data-ts-bg-overlay]:not(.ts-hero-slider__overlay)){
  position:relative;
  isolation:isolate;
}
section:not(.ts-hero-slider) > .ts-surface-overlay,
section:not(.ts-hero-slider) > [data-ts-bg-overlay]:not(.ts-hero-slider__overlay):not(.ts-hero__overlay){
  position:absolute;
  inset:0;
  z-index:1;
  pointer-events:none;
  background:var(--ts-overlay-color,var(--color-primary));
}
section:not(.ts-hero-slider) > .ts-surface-overlay ~ :not(style):not(.ts-hero__media):not(.ts-banner__media):not(.ts-bg-section__media),
section:not(.ts-hero-slider) > [data-ts-bg-overlay]:not(.ts-hero-slider__overlay) ~ :not(style):not(.ts-hero__media):not(.ts-banner__media):not(.ts-bg-section__media){
  position:relative;
  z-index:2;
}

/* Hero: a kép mindig háttér (Grapes gyakran position:relative-et ír a médiára) */
.ts-hero > .ts-hero__media{
  position:absolute!important;
  inset:0!important;
  z-index:0!important;
  width:auto!important;
  height:auto!important;
  pointer-events:none;
  overflow:hidden!important;
}
.ts-hero{
  overflow:hidden;
}
.ts-hero[data-media-type="image"] .ts-hero__img{
  transform-origin:60% 40%;
  will-change:transform;
  animation:ts-hero-kenburns 28s ease-in-out infinite alternate;
}
@media (max-width:1023px){
  .ts-hero[data-media-type="image"] .ts-hero__img{animation-duration:24s}
}
@media (max-width:767px){
  .ts-hero[data-media-type="image"] .ts-hero__img{animation-duration:20s}
}
@media (prefers-reduced-motion:reduce){
  .ts-hero[data-media-type="image"] .ts-hero__img{animation:none;will-change:auto}
}
@keyframes ts-hero-kenburns{
  from{transform:scale(1.06) translate(0,0)}
  to{transform:scale(1.16) translate(-2.2%,-1.4%)}
}
.ts-hero[data-layout="split"] > .ts-hero__media{
  inset:auto!important;
  top:0!important;
  right:0!important;
  bottom:0!important;
  left:50%!important;
  width:50%!important;
}
@media (max-width:800px){
  .ts-hero[data-layout="split"] > .ts-hero__media{
    inset:0!important;
    width:auto!important;
    left:0!important;
  }
}

/* —— Hero slider (front always-on) —— */
.ts-hero-slider{
  position:relative;
  overflow:hidden;
  color:#fff!important;
  min-height:72vh;
}
.ts-hero-slider > .ts-surface-overlay,
.ts-hero-slider > [data-ts-bg-overlay]:not(.ts-hero-slider__overlay){
  display:none!important;
  opacity:0!important;
  visibility:hidden!important;
  pointer-events:none!important;
  z-index:-1!important;
}
/* Legacy üres stage – ne foglaljon helyet / ne takarjon */
.ts-hero-slider__stage:empty{display:none!important}
.ts-hero-slider__track{position:relative;z-index:0;width:100%;min-height:72vh}
.ts-hero-slider__slide{
  position:relative;
  display:grid!important;
  grid-template-columns:minmax(0,1fr);
  grid-template-rows:minmax(72vh,auto);
  align-items:end;
  min-height:72vh;
  width:100%;
  padding:0!important;
  box-sizing:border-box;
  color:#fff!important;
}
.ts-hero-slider__media{
  grid-area:1/1;
  position:relative!important;
  inset:auto!important;
  width:100%;
  height:100%;
  min-height:72vh;
  z-index:0!important;
  overflow:hidden;
  pointer-events:none;
}
.ts-hero-slider__img,
.ts-hero-slider__video{
  position:absolute!important;
  inset:0!important;
  width:100%!important;
  height:100%!important;
  object-fit:cover!important;
  display:block;
}
.ts-hero-slider__video{display:none}
.ts-hero-slider__slide[data-media-type="video"] .ts-hero-slider__img{display:none}
.ts-hero-slider__slide[data-media-type="video"] .ts-hero-slider__video{display:block}
.ts-hero-slider__overlay{
  position:absolute!important;
  inset:0!important;
  z-index:1!important;
  pointer-events:none;
  opacity:1!important;
  background:
    linear-gradient(
      180deg,
      color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) calc(var(--ts-hero-overlay-opacity, .85) * 32%), transparent) 0%,
      color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) calc(var(--ts-hero-overlay-opacity, .85) * 78%), transparent) 55%,
      color-mix(in srgb, var(--ts-overlay-color, var(--color-primary)) calc(var(--ts-hero-overlay-opacity, .85) * 92%), transparent) 100%
    )!important;
}
.ts-hero-slider__inner{
  grid-area:1/1;
  position:relative!important;
  z-index:2!important;
  width:100%;
  max-width:var(--content-max,72rem);
  margin:0 auto;
  padding-top:var(--ts-hero-pad-top, max(var(--section-y), 4.5rem))!important;
  padding-right:var(--ts-hero-pad-right, max(var(--section-x), 1.5rem))!important;
  padding-bottom:var(--ts-hero-pad-bottom, max(var(--section-y), 4.5rem))!important;
  padding-left:var(--ts-hero-pad-left, max(var(--section-x), 1.5rem))!important;
  box-sizing:border-box;
  color:#fff!important;
  opacity:1!important;
  pointer-events:auto;
  transform:none!important;
  filter:none!important;
}
.ts-hero-slider__eyebrow,
.ts-hero-slider__title,
.ts-hero-slider__lead{
  color:#fff!important;
  opacity:1!important;
}
.ts-hero-slider__actions{
  position:relative;
  z-index:3;
  display:flex;
  flex-wrap:wrap;
  gap:.75rem;
  margin-top:1.75rem;
  opacity:1!important;
}
.ts-hero-slider .ts-btn{
  opacity:1!important;
  filter:none!important;
}
.ts-hero-slider .ts-btn--primary{
  background:var(--btn-bg,var(--btn-primary-bg,var(--color-accent)))!important;
  color:var(--btn-fg,var(--btn-primary-fg,#fff))!important;
}
.ts-hero-slider .ts-btn--inverse{
  background:var(--btn-inverse-bg,#fff)!important;
  color:var(--btn-inverse-fg,#2a1f1a)!important;
  border:var(--border-width,1px) solid var(--btn-inverse-border,transparent)!important;
}
.ts-hero-slider .ts-btn--inverse:hover{
  background:var(--btn-inverse-hover-bg,#fbf5ec)!important;
  color:var(--btn-inverse-fg,#2a1f1a)!important;
}

/* Fade */
.ts-hero-slider[data-transition="fade"] .ts-hero-slider__track{display:block}
.ts-hero-slider[data-transition="fade"] .ts-hero-slider__slide{
  position:absolute;
  inset:0;
  width:100%;
  opacity:0;
  visibility:hidden;
  transition:opacity .55s ease,visibility .55s ease;
  pointer-events:none;
}
.ts-hero-slider[data-transition="fade"] .ts-hero-slider__slide.is-active,
.ts-hero-slider[data-transition="fade"]:not([data-ts-slider-ready]) .ts-hero-slider__slide:first-child{
  position:relative;
  opacity:1;
  visibility:visible;
  pointer-events:auto;
}

/* Slide – scroll (nincs will-change/transform) */
.ts-hero-slider[data-transition="slide"] .ts-hero-slider__track{
  display:flex!important;
  flex-wrap:nowrap;
  width:100%;
  overflow-x:auto;
  overflow-y:hidden;
  scroll-snap-type:x mandatory;
  scroll-behavior:smooth;
  -webkit-overflow-scrolling:touch;
  scrollbar-width:none;
  transform:none!important;
  will-change:auto!important;
  transition:none!important;
  filter:none!important;
  opacity:1!important;
}
.ts-hero-slider[data-transition="slide"] .ts-hero-slider__track::-webkit-scrollbar{display:none}
.ts-hero-slider[data-transition="slide"] .ts-hero-slider__slide{
  flex:0 0 100%;
  width:100%;
  min-width:100%;
  max-width:100%;
  scroll-snap-align:start;
  scroll-snap-stop:always;
  opacity:1!important;
  visibility:visible;
  transform:none!important;
  filter:none!important;
  will-change:auto!important;
}
.ts-hero-slider[data-layout="center"] .ts-hero-slider__slide{align-items:center}
.ts-hero-slider[data-layout="center"] .ts-hero-slider__inner{
  display:flex;
  flex-direction:column;
  align-items:center;
  text-align:center;
  justify-content:center;
  padding-top:var(--ts-hero-pad-top, 6rem)!important;
  padding-bottom:var(--ts-hero-pad-bottom, 6rem)!important;
}
.ts-hero-slider[data-layout="center"] .ts-hero-slider__actions{justify-content:center}
.ts-hero-slider[data-layout="center"] .ts-hero-slider__lead{margin-left:auto;margin-right:auto}

.ts-hero-slider__dots{position:absolute;z-index:5;left:50%;bottom:1.35rem;transform:translateX(-50%);display:flex;gap:.55rem;align-items:center;justify-content:center}
.ts-hero-slider[data-show-dots="0"] .ts-hero-slider__dots,
.ts-hero-slider__dots[hidden]{display:none!important}
.ts-hero-slider__dot{width:.65rem;height:.65rem;padding:0;border:0;border-radius:999px;background:color-mix(in srgb,#fff 45%,transparent);cursor:pointer;transition:transform .2s ease,background .2s ease}
.ts-hero-slider__dot:hover{background:color-mix(in srgb,#fff 75%,transparent)}
.ts-hero-slider__dot[aria-current="true"]{background:#fff;transform:scale(1.2)}
.ts-hero-slider__dot:focus-visible{outline:2px solid #fff;outline-offset:2px}

.ts-ba-gallery{padding:var(--section-y,3.5rem) max(var(--section-x),1.5rem);background:var(--color-surface);color:var(--color-text);font-family:var(--font-sans,Karla,sans-serif);box-sizing:border-box}
.ts-ba-gallery__inner{max-width:min(52rem,var(--content-max,72rem));margin:0 auto;width:100%;box-sizing:border-box}
.ts-ba-gallery__work{display:block;margin:0;padding:0;background:transparent}
.ts-ba-gallery__cover{position:relative;display:block;width:100%;padding:0;border:0;background:transparent!important;cursor:zoom-in}
.ts-ba-gallery__cover:focus-visible{outline:2px solid var(--color-accent,var(--color-primary));outline-offset:2px}
.ts-ba-gallery__cover img,.ts-ba-gallery__photo{display:block;width:100%!important;height:auto!important;max-width:100%!important;aspect-ratio:auto!important;object-fit:contain!important;object-position:center;background:transparent}
.ts-ba-gallery__copy{margin:1.1rem 0 0;color:color-mix(in srgb,var(--color-text) 72%,transparent);font-size:.95rem;line-height:1.55;text-align:left}
.ts-ba-gallery__copy:empty{display:none}
.ts-ba-gallery__copy p{margin:0 0 .15rem}
.ts-ba-gallery__lead{margin-bottom:.55rem!important;color:color-mix(in srgb,var(--color-text) 78%,transparent)}
.ts-ba-gallery__meta-label{font-weight:600;color:var(--color-text)}
.ts-ba-gallery__architect-link{color:inherit;text-decoration:underline;text-underline-offset:.12em}
.ts-ba-gallery__architect-link:hover{opacity:.82}
.ts-ba-gallery__compare{--split:50%;position:relative;overflow:hidden;aspect-ratio:16/10;user-select:none;touch-action:none;cursor:ew-resize}
.ts-ba-gallery__compare.is-dragging{cursor:grabbing}
.ts-ba-gallery__layer{position:absolute;inset:0;overflow:hidden;pointer-events:none}
.ts-ba-gallery__layer img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}
.ts-ba-gallery__layer--after{z-index:1}
.ts-ba-gallery__layer--before{z-index:2;-webkit-clip-path:inset(0 calc(100% - var(--split)) 0 0);clip-path:inset(0 calc(100% - var(--split)) 0 0)}
.ts-ba-gallery__handle{position:absolute;top:0;bottom:0;left:var(--split);z-index:4;width:2px;background:#fff;transform:translateX(-50%);pointer-events:none;box-shadow:0 0 0 1px rgb(0 0 0 / 18%)}
.ts-ba-gallery__knob{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:2.65rem;height:2.65rem;border-radius:999px;display:flex;align-items:center;justify-content:center;background:#fff;color:var(--color-primary);font-size:1.05rem;font-weight:700;letter-spacing:.04em;line-height:1;box-shadow:0 8px 22px rgb(0 0 0 / 22%)}
.ts-ba-gallery__range{position:absolute;inset:0;z-index:5;width:100%;height:100%;margin:0;opacity:0;cursor:ew-resize;appearance:none;-webkit-appearance:none;background:transparent;pointer-events:none}
.ts-ba-gallery__range::-webkit-slider-runnable-track{height:100%;background:transparent}
.ts-ba-gallery__range::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:2.65rem;height:100%;background:transparent;border:0}
.ts-ba-gallery__range::-moz-range-track{height:100%;background:transparent;border:0}
.ts-ba-gallery__range::-moz-range-thumb{width:2.65rem;height:100%;background:transparent;border:0;border-radius:0}
.ts-ba-gallery__tag{position:absolute;top:.85rem;padding:.35rem .7rem;border-radius:var(--radius-control,999px);background:color-mix(in srgb,var(--color-primary) 82%,#000);color:#fff;font-size:.65rem;font-weight:var(--label-weight,600);letter-spacing:var(--tracking-label,.14em);text-transform:uppercase;pointer-events:none}
.ts-ba-gallery__tag--before{left:.85rem}
.ts-ba-gallery__tag--after{right:.85rem}
.ts-ba-gallery-lightbox{position:fixed;inset:0;z-index:10050;display:flex;align-items:center;justify-content:center;padding:max(1rem,3vw);background:rgba(0,0,0,.88);box-sizing:border-box}
.ts-ba-gallery-lightbox[hidden]{display:none!important}
body.ts-ba-gallery-lightbox-open{overflow:hidden}
.ts-ba-gallery-lightbox__stage{margin:0;width:min(96vw,70rem,calc(92vh * 16 / 10))}
.ts-ba-gallery-lightbox__frame,.ts-ba-gallery-lightbox__stage .ts-ba-gallery__compare{width:100%;aspect-ratio:16/10;max-height:92vh;overflow:hidden;background:#000}
.ts-ba-gallery-lightbox__img{display:block;width:100%;height:100%;object-fit:contain}
.ts-ba-gallery-lightbox__close,.ts-ba-gallery-lightbox__prev,.ts-ba-gallery-lightbox__next{position:absolute;border:0;background:rgba(255,255,255,.14);color:#fff;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
.ts-ba-gallery-lightbox__close{top:1rem;right:1rem;width:2.75rem;height:2.75rem;border-radius:999px;font-size:1.6rem;line-height:1}
.ts-ba-gallery-lightbox__prev,.ts-ba-gallery-lightbox__next{top:50%;transform:translateY(-50%);width:2.75rem;height:2.75rem;border-radius:999px;font-size:1.75rem;line-height:1}
.ts-ba-gallery-lightbox__prev{left:max(.75rem,2vw)}
.ts-ba-gallery-lightbox__next{right:max(.75rem,2vw)}
.ts-ba-gallery-lightbox__close:focus-visible,.ts-ba-gallery-lightbox__prev:focus-visible,.ts-ba-gallery-lightbox__next:focus-visible{outline:2px solid #fff;outline-offset:2px}
@media (max-width:900px){
  .ts-ba-gallery{padding:2.75rem max(var(--section-x),1.25rem)}
  .ts-ba-gallery__tag{top:.65rem;padding:.3rem .55rem;font-size:.6rem}
  .ts-ba-gallery__tag--before{left:.65rem}
  .ts-ba-gallery__tag--after{right:.65rem}
}
@media (max-width:520px){
  .ts-ba-gallery{padding:2.25rem max(var(--section-x),1rem)}
  .ts-ba-gallery__copy{font-size:.9rem;margin-top:1rem}
  .ts-ba-gallery__knob{width:2.25rem;height:2.25rem;font-size:.95rem}
}

[data-reveal-children="1"] .ts-reveal-item{
  opacity:0;
  transform:translateY(1.125rem);
  transition:opacity .65s ease,transform .65s ease;
  transition-delay:calc(var(--ts-reveal-i,0) * 80ms);
}
[data-reveal-children="1"].is-ts-revealed .ts-reveal-item{
  opacity:1;
  transform:none;
}
@media (prefers-reduced-motion:reduce){
  [data-reveal-children="1"] .ts-reveal-item{
    opacity:1!important;
    transform:none!important;
    transition:none!important;
  }
}
CSS;
    }
}
