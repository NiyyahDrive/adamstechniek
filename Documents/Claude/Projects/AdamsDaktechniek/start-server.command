#!/bin/bash
# 4 Seasons Cleaning — DEV preview server
# Dubbelklik dit bestand in Finder om de DEV-versie lokaal te bekijken.

cd "$(dirname "$0")"

PORT=8091

( sleep 1 && open "http://localhost:$PORT/" ) &

echo ""
echo "🔧 4 Seasons Cleaning — DEV / REVIEW server"
echo "   URL: http://localhost:$PORT/"
echo "   Folder: $(pwd)"
echo ""
echo "   ⚠ Deze versie is voor klant-review — niet voor publieke upload."
echo "   Druk Ctrl+C om de server te stoppen."
echo ""

if command -v python3 >/dev/null 2>&1; then
    python3 -m http.server $PORT
else
    python -m SimpleHTTPServer $PORT
fi
