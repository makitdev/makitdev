#!/usr/bin/env node
/**
 * makitdev — WordPress theme asset sync (no dependencies).
 *
 * The WordPress theme must be self-contained: once it is dropped into
 * wp-content/themes/makitdev it can no longer reach the repository root, so the
 * CSS/JS/images/fonts it enqueues have to live inside the theme directory.
 *
 * This copies them from the canonical sources in assets/ into the theme, so the
 * two can never drift:
 *
 *     assets/css/*      ->  makitdev-theme/assets/css/*
 *     assets/js/*       ->  makitdev-theme/assets/js/*
 *     assets/images/*   ->  makitdev-theme/assets/images/*
 *     assets/fonts/*    ->  makitdev-theme/assets/fonts/*
 *
 * makitdev-theme/assets/data/ is NOT touched here — that roster is owned and
 * kept in sync by scripts/add-contributor.js.
 *
 * Run after editing any stylesheet or script (and after scripts/minify.js):
 *
 *     node scripts/minify.js
 *     node scripts/sync-theme-assets.js
 */

"use strict";

const fs = require("fs");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const SRC = path.join(ROOT, "assets");
const DEST = path.join(ROOT, "makitdev-theme", "assets");

const DIRS = ["css", "js", "images", "fonts"];

function copyDir(from, to) {
  let copied = 0;
  for (const entry of fs.readdirSync(from, { withFileTypes: true })) {
    const src = path.join(from, entry.name);
    const dst = path.join(to, entry.name);
    if (entry.isDirectory()) {
      copied += copyDir(src, dst);
    } else if (entry.isFile()) {
      fs.mkdirSync(path.dirname(dst), { recursive: true });
      fs.copyFileSync(src, dst);
      copied += 1;
    }
  }
  return copied;
}

/** Remove files in `dir` that no longer exist in `from`, so deletions propagate. */
function pruneStale(from, dir) {
  if (!fs.existsSync(dir)) return 0;
  const keep = new Set(fs.readdirSync(from, { withFileTypes: true }).map((e) => e.name));
  let removed = 0;
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    if (entry.isFile() && !keep.has(entry.name)) {
      fs.unlinkSync(path.join(dir, entry.name));
      removed += 1;
    }
  }
  return removed;
}

if (!fs.existsSync(SRC)) {
  console.error(`Error: source assets directory not found: ${SRC}`);
  process.exit(1);
}

let total = 0;
for (const dir of DIRS) {
  const from = path.join(SRC, dir);
  if (!fs.existsSync(from)) {
    console.error(`Error: missing source directory assets/${dir}`);
    process.exit(1);
  }
  const to = path.join(DEST, dir);
  fs.mkdirSync(to, { recursive: true });
  pruneStale(from, to);
  total += copyDir(from, to);
  console.log(`assets/${dir} -> makitdev-theme/assets/${dir}`);
}

console.log(`Synced ${total} file(s) into the WordPress theme.`);
