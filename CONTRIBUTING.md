# Contributing to makitdev

Thanks for helping build makitdev. This project is open source — your ideas,
reports, and contributions are welcome.

## Ground rules

- Be respectful. This is a small, community-oriented organization.
- Keep changes small, focused, and easy to review.
- Keep the site lightweight: HTML, CSS, and vanilla JavaScript only.
  No build step, no frameworks, no new dependencies unless discussed first.

## How to contribute

1. Fork the repository.
2. Create a branch: `git checkout -b my-change`.
3. Make your changes.
4. Test locally: open `index.html` or run `python3 -m http.server 8080`.
5. Commit with a clear, concise message.
6. Open a pull request against `main`.

## What to look out for

- **Copy**: page text lives in `index.html` and
  `makitdev-theme/front-page.php` — keep both in sync. Contributor
  cards read from `assets/data/contributors.js` (static) and
  `makitdev-theme/assets/data/contributors.json` (WordPress) — keep both
  data files in sync too.

## Build steps

The pages load the minified stylesheets, so regenerate and re-sync after editing
anything under `assets/css/` or `assets/js/`:

```bash
node scripts/minify.js            # assets/css/*.css -> assets/css/*.min.css
node scripts/sync-theme-assets.js # copies css/js/images/fonts into makitdev-theme/
```

`makitdev-theme/assets/` is a **copy** of the repository-root `assets/`
directory. The theme has to be self-contained — once installed in
`wp-content/themes/makitdev` it cannot reach the repository root — so every
asset `functions.php` and `header.php` reference must exist inside the theme.
`scripts/sync-theme-assets.js` is what keeps that true; commit the result
alongside your change.

Two directories are deliberately **not** synced by that script:

- `makitdev-theme/assets/data/contributors.json` — owned by
  `scripts/add-contributor.js` (see below).
- Nothing else; if you add a new asset folder, add it to the `DIRS` list in
  `scripts/sync-theme-assets.js`.

## Adding a contributor

To add or update a contributor in the organization roster across all data files:

### Automated (recommended)

Run the helper script with the contributor's GitHub username:

```bash
node scripts/add-contributor.js <github-username> --name "Full Name" --role "Role" --contribution "Description"
```

This automatically queries the GitHub profile and updates `assets/data/contributors.js` and the WordPress theme data simultaneously.

`assets/data/contributors.js` is the **single source of truth** for the roster.
`assets/js/contributors.js` deliberately holds no hardcoded copy of it — an
earlier "fallback" array silently drifted out of sync, hiding contributors
added through the script. Don't reintroduce one; the renderer shows a message
if the data file fails to load.

### Manual

Add the contributor object to `assets/data/contributors.js`:

```json
{
  "name": "Contributor Name",
  "username": "github-username",
  "role": "Core Contributor",
  "contribution": "Description of contributions.",
  "category": "Contributors",
  "github": "https://github.com/github-username",
  "avatar": "https://avatars.githubusercontent.com/u/..."
}
```

Keep the JSON strictly valid. `scripts/add-contributor.js` re-parses this file
to append new entries, and it **refuses to write** if it cannot read the array
back — so a syntax error here will block the script rather than silently
overwrite the roster. Mirror any manual edit into
`makitdev-theme/assets/data/contributors.json`, or just re-run the script for
the same username to regenerate both files consistently.

- **Accessibility**: semantic HTML, keyboard support, visible focus, and
  `prefers-reduced-motion` support must be preserved.
- **Design language**: minimal, editorial, typography-led. No neon colors,
  heavy effects, or stock visuals.

## Reporting issues

Open an issue describing what you saw, what you expected, and how to
reproduce it. Screenshots and browser details help a lot.

## License

By contributing you agree that your contributions are licensed under the
[MIT License](LICENSE).