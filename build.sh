#!/bin/bash
set -e
mkdir -p public/assets/assignments
for d in assignments/*/; do
  name=$(basename "$d")
  if [ -d "$d/public/assets" ]; then
    mkdir -p "public/assets/assignments/$name"
    cp "$d/public/assets/"*.css "public/assets/assignments/$name/" 2>/dev/null || true
  fi
done
echo 'Assets copied'
