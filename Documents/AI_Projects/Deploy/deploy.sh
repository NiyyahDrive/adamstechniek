#!/bin/bash
# ============================================================
# MOJO NAS100 DASHBOARD — GITHUB PAGES DEPLOY SCRIPT
# Gebruik: bash deploy.sh JOUW_GITHUB_USERNAME
# ============================================================

set -e

USERNAME=$1

if [ -z "$USERNAME" ]; then
  echo ""
  echo "❌  Geef je GitHub gebruikersnaam mee."
  echo "    Gebruik: bash deploy.sh JOUW_USERNAME"
  echo ""
  exit 1
fi

REPO="mojo-dashboard"
DIR="mojo-dashboard"

echo ""
echo "=================================================="
echo "  MOJO DASHBOARD DEPLOY"
echo "  GitHub: $USERNAME / $REPO"
echo "=================================================="
echo ""

# ── 1. Node.js check ──────────────────────────────────────
if ! command -v node &>/dev/null; then
  echo "❌  Node.js niet gevonden."
  echo "    Download: https://nodejs.org (LTS versie)"
  exit 1
fi

NODE_VER=$(node -v | sed 's/v//' | cut -d. -f1)
if [ "$NODE_VER" -lt 16 ]; then
  echo "❌  Node.js versie te oud ($NODE_VER). Minimaal v16 vereist."
  echo "    Download: https://nodejs.org"
  exit 1
fi
echo "✅  Node.js $(node -v)"

# ── 2. Git check ──────────────────────────────────────────
if ! command -v git &>/dev/null; then
  echo "❌  Git niet gevonden."
  echo "    Download: https://git-scm.com"
  exit 1
fi
echo "✅  Git $(git --version | cut -d' ' -f3)"

# ── 3. GitHub CLI check (optioneel maar handig) ───────────
GH_AVAILABLE=false
if command -v gh &>/dev/null; then
  GH_AVAILABLE=true
  echo "✅  GitHub CLI aanwezig"
else
  echo "ℹ️   GitHub CLI niet gevonden — je maakt de repo handmatig aan (stap 6)"
fi

# ── 4. Projectmap aanmaken ───────────────────────────────
if [ -d "$DIR" ]; then
  echo ""
  echo "ℹ️   Map '$DIR' bestaat al. Overschrijven? (j/n)"
  read -r OVERWRITE
  if [ "$OVERWRITE" != "j" ]; then
    echo "Gestopt. Verwijder de map handmatig en probeer opnieuw."
    exit 0
  fi
  rm -rf "$DIR"
fi

echo ""
echo "📁  Projectmap aanmaken..."
mkdir -p "$DIR/src" "$DIR/public"
cd "$DIR"

# ── 5. package.json ──────────────────────────────────────
cat > package.json << PKGJSON
{
  "name": "mojo-dashboard",
  "version": "1.0.0",
  "private": true,
  "homepage": "https://$USERNAME.github.io/$REPO",
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "react-scripts": "5.0.1"
  },
  "scripts": {
    "start": "react-scripts start",
    "build": "react-scripts build",
    "predeploy": "npm run build",
    "deploy": "gh-pages -d build"
  },
  "devDependencies": {
    "gh-pages": "^6.0.0"
  },
  "browserslist": {
    "production": [">0.2%", "not dead", "not op_mini all"],
    "development": ["last 1 chrome version"]
  }
}
PKGJSON

# ── 6. public/index.html ─────────────────────────────────
cat > public/index.html << 'HTML'
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mojo NAS100 Dashboard</title>
    <style>* { margin:0; padding:0; box-sizing:border-box; } body { background:#070B14; }</style>
  </head>
  <body>
    <div id="root"></div>
  </body>
</html>
HTML

# ── 7. src/index.js ──────────────────────────────────────
cat > src/index.js << 'JSX'
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);
JSX

# ── 8. Kopieer App.jsx van het gedownloade bestand ───────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_SRC="$SCRIPT_DIR/App.jsx"

if [ -f "$APP_SRC" ]; then
  cp "$APP_SRC" src/App.jsx
  echo "✅  App.jsx gekopieerd"
else
  echo "❌  App.jsx niet gevonden naast dit script."
  echo "    Zorg dat App.jsx in dezelfde map staat als deploy.sh"
  exit 1
fi

# ── 9. npm install ───────────────────────────────────────
echo ""
echo "📦  Dependencies installeren (dit duurt 1–3 minuten)..."
npm install --silent

# ── 10. GitHub repo aanmaken ─────────────────────────────
echo ""
if [ "$GH_AVAILABLE" = true ]; then
  echo "🔧  GitHub repo aanmaken via CLI..."
  gh repo create "$REPO" --public --source=. --remote=origin --push 2>/dev/null || {
    echo "ℹ️   Repo bestaat mogelijk al. Remote toevoegen..."
    git remote add origin "https://github.com/$USERNAME/$REPO.git" 2>/dev/null || true
  }
else
  echo "=================================================="
  echo "  HANDMATIGE STAP VEREIST:"
  echo ""
  echo "  1. Ga naar: https://github.com/new"
  echo "  2. Repository name: $REPO"
  echo "  3. Zet op: Public"
  echo "  4. Klik: Create repository"
  echo "  5. Druk daarna hier op ENTER om door te gaan"
  echo "=================================================="
  read -r _
fi

# ── 11. Git init + eerste commit ─────────────────────────
echo ""
echo "📤  Git initialiseren en pushen..."

git init -q
git add .
git commit -m "feat: Mojo NAS100 Dashboard initial deploy" -q

git branch -M main
git remote add origin "https://github.com/$USERNAME/$REPO.git" 2>/dev/null || \
  git remote set-url origin "https://github.com/$USERNAME/$REPO.git"

git push -u origin main

# ── 12. Deploy naar GitHub Pages ─────────────────────────
echo ""
echo "🚀  Deployen naar GitHub Pages..."
npm run deploy

# ── 13. Klaar ────────────────────────────────────────────
echo ""
echo "=================================================="
echo "  ✅  DEPLOY GESLAAGD"
echo ""
echo "  🌐  URL: https://$USERNAME.github.io/$REPO"
echo ""
echo "  ⏱️   GitHub Pages is live in 1–3 minuten."
echo "       Refresh de pagina als het er nog niet is."
echo ""
echo "  🔑  API Key: voer je Anthropic key in op het"
echo "      inlogscherm (sk-ant-...)"
echo "      Verkrijgen via: platform.anthropic.com"
echo "=================================================="
echo ""
