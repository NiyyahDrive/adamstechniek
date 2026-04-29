# Adams Daktechniek — Multi-page website (4Seasons-template)

Volledig multi-page WordPress-stijl site, gekloond uit het 4seasonscleaning_live framework en gerebrand naar Adams Daktechniek (voorheen Mano Dak en Bouw) — Heerlen, werkgebied heel Limburg.

## Waar je nu staat

| Onderdeel | Status |
|---|---|
| Folder + framework gekopieerd uit 4seasons | ✅ klaar |
| URL (`adamstechniek.nl`) wereldwijd vervangen | ✅ klaar |
| Brandnaam (4 Seasons → Adams Daktechniek) | ✅ klaar |
| Telefoon (+32 → +31683396082) | ✅ klaar |
| Land (BE → NL, Vlaanderen → Limburg) | ✅ klaar |
| Brand kleuren (#0d47a1 → #0E2A4A, #FF6B35 → #F26B1F) | ✅ klaar |
| Header logo + navigatie | ✅ klaar (SVG-logo i.p.v. .jpeg) |
| Hero (h1, lead, CTAs, trust) | ✅ klaar |
| Why-us section | ✅ klaar |
| Services-grid (8 dakdekker-services) | ✅ klaar |
| Title + meta-tags + canonical | ✅ klaar |
| Explainer section (osmose-tech) | ⚠️ TODO — gaat nog over ramen wassen |
| Voor-syndici / Voor VvE's | ⚠️ TODO — copy hertalen |
| Value section | ⚠️ TODO |
| Reviews section | ⚠️ TODO — namen en plaatsen aanpassen |
| Gallery (foto's) | ⚠️ TODO — alle foto's verwijzen naar window-cleaning |
| FAQ-section | ⚠️ TODO — vragen hertalen voor dakdekker |
| Footer | ⚠️ TODO — bedrijfsinfo controleren |
| Subpagina's (47 stuks in /pages/) | ⚠️ TODO — branding klaar, content nog 4seasons |
| 391 foto's (assets/images*) | ⚠️ TODO — vervangen door dakwerk-foto's |

## Structuur

```
AdamsDaktechniek/
├── index.html               # homepage (2213 regels — gedeeltelijk hertaald)
├── 404.html                 # error page
├── .htaccess                # Apache routing (bewaart /pages routing)
├── robots.txt
├── sitemap.xml              # vervang oude paginanamen
├── mail_config.php          # PHP backend contactformulier (vereist PHP-host)
├── start-server.command
├── assets/
│   ├── favicons/            # favicon-pakket
│   ├── images/              # ~200 originele foto's
│   ├── images-optimized/    # webp/avif/jpg in 380w/768w/1440w
│   └── images/before-after/
├── css/
│   ├── style.css            # 60KB — kernstijl
│   ├── header.css           # nav-styling
│   ├── responsive.css
│   ├── enhancements.css
│   ├── floating-button.css
│   └── glightbox.min.css
├── js/
│   ├── main.js              # 13KB - core
│   ├── floating-button.js
│   ├── enhancements.js
│   ├── events.js
│   └── glightbox.min.js     # image lightbox
└── pages/
    ├── _template.html       # base template voor subpagina's
    ├── contact.html + contact.php
    ├── over-ons.html
    ├── reviews.html
    ├── realisaties.html     # portfolio
    ├── voor-syndici.html    # → moet voor-vve.html worden
    ├── ruitenwasser-{stad}.html × 22 # → moet dakdekker-{stad}.html worden
    └── dak{service}.html    # services per dienst
```

## Hosting

Dit is **geen** statische one-pager — je hebt een PHP-host nodig voor de contact-PHP en `.htaccess` voor URL-routing. GitHub Pages werkt **niet** voor de contact-form.

Aanbevolen hosts:
- TransIP/Hostnet (NL, ~€5/mnd)
- Vimexx (NL, ~€3/mnd)
- DirectAdmin shared hosting met PHP 8.x

## Belangrijke acties voordat het live kan

1. **Alle subpagina's hertalen** — 47 pagina's, branding is OK maar content gaat nog over ramen wassen
2. **Foto's vervangen** — 391 foto's in `/assets/images*`. Hernoem naar Adams-context, of vervang. Voor nu staan ze er nog als 4seasons-foto's.
3. **Sitemap.xml updaten** — verwijst nog naar oude paginanamen (ruitenwasser-{stad}, etc.)
4. **404.html controleren** op 4seasons-content
5. **Contactformulier testen** — `mail_config.php` heeft mogelijk SMTP-config nodig
6. **GA4 tracking ID** — `G-Q11L7Z055S` is de 4seasons-ID; bij Adams wisselen of weghalen

## Branding-tokens — mockup palette

Volledig palet uitgelijnd met de Adams Daktechniek mockup (single-pager). Alle hardcoded blauw/oranje hex-codes uit het 4seasons-template zijn doorgesweept naar deze palet.

```css
/* Primaire navy-tonen */
--color-primary:       #0E2A4A;   /* navy (hoofdkleur) */
--color-primary-dark:  #0A1F3A;   /* navy-2 (donker) */
--color-primary-light: #15375F;   /* navy-3 (mid, voor gradients) */

/* Accent — warm oranje */
--color-accent:       #F26B1F;
--color-accent-light: #FF8A3D;
--color-accent-dark:  #D85A1A;

/* Tekst & achtergronden */
--color-ink:    #0F1722;          /* primaire tekst */
--color-body:   #5A6373;          /* secundair tekst */
--color-muted:  #5A6373;
--color-line:   #DEE2E8;          /* lijntjes/borders */
--color-soft:   #F7F4EE;          /* cream achtergrond */
--color-bg:     #ffffff;

/* Semantisch */
--color-success: #1F9D55;
```

In `css/style.css` zijn dezelfde waarden als `--primary-color`, `--accent-color`, `--neutral-gray`, etc. mapped voor backwards-compatibility met het 4seasons-template.

## Bedrijfsgegevens (in de site)

- Naam: Adams Daktechniek (voorheen Mano Dak en Bouw)
- Telefoon: 06-83396082
- E-mail: info@adamstechniek.nl *(controleren of dit het echte adres is)*
- KvK: 89562135 *(controleren na rebrand)*
- Vestiging: Heerlen
- Werkgebied: heel Limburg

## Wat dit project niet is

Dit is een **template-rebrand**, niet een volledige content-rewrite. De architectuur, het ontwerp en alle technische lagen zijn klaar. De copy en foto's zijn voor ~70% nog 4seasons-context. Voltooien vraagt 8–16 uur extra werk per persoon, of een gefaseerde aanpak waarbij je per pagina hertaalt op tempo.

Voor een echte snelle Adams-website is de eerdere **single-pager** (`/Adams Daktechniek/index.html`) beter.
