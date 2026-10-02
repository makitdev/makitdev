#!/usr/bin/env node
/**
 * makitdev — contributors directory copy (no dependencies).
 *
 * The site is served both as `/contributors.html` and as `/contributors/`, so
 * the same page exists at two paths. They used to be maintained by hand and
 * drifted apart; this regenerates the nested copy from the canonical
 * `contributors.html` by rewriting only the relative paths:
 *
 *     assets/…      -> ../assets/…
 *     index.html    -> ../index.html
 *     contributors.html -> ./
 *
 * Run after editing contributors.html:
 *
 *     node scripts/sync-contributors-page.js
 */

"use strict";

const fs = require("fs");
const path = require("path");

const ROOT = path.join(__dirname, "..");
const SRC = path.join(ROOT, "contributors.html");
const DEST_DIR = path.join(ROOT, "contributors");
const DEST = path.join(DEST_DIR, "index.html");

function rewrite(html) {
  return html
    .replace(/(href|src)="assets\//g, '$1="../assets/')
    .replace(/href="index\.html"/g, 'href="../index.html"')
    .replace(/href="contributors\.html"/g, 'href="./"');
}

if (!fs.existsSync(SRC)) {
  console.error(`Error: ${SRC} not found`);
  process.exit(1);
}

const html = rewrite(fs.readFileSync(SRC, "utf8"));
fs.mkdirSync(DEST_DIR, { recursive: true });
fs.writeFileSync(DEST, html);
console.log(`contributors.html -> contributors/index.html (${html.length} bytes)`);