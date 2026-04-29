# Adams Daktechniek — Website

Mobile-first one-pager voor Adams Daktechniek — dakdekker uit Heerlen, werkgebied heel Limburg.

## Inhoud

Eén bestand: `index.html` — bevat HTML, CSS en JavaScript inline. Geen build-stap, geen dependencies.

## Lokaal bekijken

Open `index.html` direct in een browser, of start een lokale server:

```bash
python3 -m http.server 8000
# open http://localhost:8000
```

## Deployen

### GitHub Pages
1. Repo aanmaken, `index.html` pushen naar `main`.
2. Settings → Pages → Source: `main` branch, root.
3. Site live op `https://[username].github.io/[repo]/`.

### Eigen domein (aanbevolen)
- Domein registreren (bijv. `adamstechniek.nl`)
- Hosting via Netlify, Vercel of Cloudflare Pages — sleep `index.html` erin

## Bedrijfsgegevens (in de site)

- Naam: Adams Daktechniek (voorheen Mano Dak en Bouw)
- Telefoon: 06-83396082
- E-mail: info@adamstechniek.nl *(placeholder — bevestigen of aanpassen)*
- KvK: 89562135 *(controleren of dit nog hetzelfde is na rebrand)*
- Vestiging: Heerlen
- Werkgebied: heel Limburg

## Branding

- Primair: `#0E2A4A` (donkerblauw)
- Accent: `#F26B1F` (warm oranje)
- Achtergrond: `#FFFFFF` / `#F7F4EE` (cream)

## Aandachtspunten

- Socials linken nog naar `@manodakenbouw` (Facebook/TikTok/Marktplaats). Vervangen zodra rebrand op socials is doorgevoerd.
- E-mailadres `info@adamstechniek.nl` is placeholder. Aanpassen naar werkelijk adres.
- Domein in JSON-LD staat op `adamstechniek.nl` (zoals door eigenaar opgegeven). Bestaande site loopt op `adamsdaktechniek.nl` — controleren welk domein wordt gebruikt.
- **Beelden:** alle scenes zijn inline SVG-illustraties (Nederlandse pannendak, plat dak met bitumen, dakkapel-profiel, zinkwerk-goot). Volledig onder controle, altijd snel laden, geen externe afhankelijkheden. Vervang door echte foto's zodra Adams er aanlevert: zoek de `<svg class="scene">`-blokken in `index.html` en vervang door `<img src="images/foto-1.jpg" alt="...">` met een `images/`-map naast het HTML-bestand.
- **Reviews:** namen en steden zijn fictief maar realistisch. Vervangen door echte klantreviews zodra je toestemming hebt.
- **Aggregate rating** in JSON-LD: 4.9 / 24 reviews — pas dit aan naar werkelijke aantallen zodra Google Business Profile gekoppeld is.

## Features in deze versie

- Hero met dakfoto-achtergrond + glasmorphism trust-card
- Spoed-strook ("binnen 1 uur") onder hero
- Trust-badges: 10 jaar garantie, VCA, A-merk, verzekerd
- Donkere USP-strip met grote getallen
- Projectfoto's met tag-overlay
- Reviews met avatars + locatie
- Zwevende WhatsApp-knop
- Sticky bel-balk op mobiel
- WhatsApp-link in contactblok
- Responsive vanaf 320px tot full HD

## Wat je nog kunt toevoegen

- Echte projectfoto's
- Logo (favicon en in de header)
- Postadres + openingstijden (versterkt lokale SEO)
- Echte klantreviews met toestemming
- WhatsApp-knop
- Google Bedrijfsprofiel koppelen
