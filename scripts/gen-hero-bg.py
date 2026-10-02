#!/usr/bin/env python3
"""Generate the hero background art for the makitdev site.

Pure standard-library Python (zlib + struct), matching the approach used by
scripts/gen-og-image.py: no Pillow, no numpy, no ImageMagick.

The image is an abstract software-infrastructure landscape: a dark atmospheric
base, a few very soft graphite/green light sources, a faint perspective grid,
a network topology of nodes and links, ghosted code fragments and drifting
particles. Grain, vignette and the readable-text overlay are done in CSS so they
stay sharp and cheap.

    python3 scripts/gen-hero-bg.py

Outputs:
    assets/images/hero-bg.png            1920x1200  landscape (desktop)
    assets/images/hero-bg-mobile.png      900x1500  portrait  (mobile crop)
    assets/images/grain.png               256x256   tileable noise
"""

import math
import random
import struct
import zlib
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
OUT = ROOT / "assets" / "images"

SEED = 20260402  # fixed so regenerating produces the same artwork

# ---- Palette (mirrors the CSS custom properties) ------------------------------

INK_TOP = (6, 7, 6)      # #070807
INK_MID = (13, 16, 14)    # #0D100E
INK_LOW = (7, 8, 7)

GRAPHITE = (26, 36, 48)   # muted blue haze
GREEN = (20, 44, 18)      # restrained accent haze
GREEN_2 = (14, 30, 12)
STEEL = (52, 68, 58)      # topology lines
STEEL_HI = (104, 148, 92)  # topology highlights
GLYPH = (120, 140, 128)    # code fragments


# ---- Tiny PNG writer ---------------------------------------------------------


def write_png(path, width, height, rows, channels=3):
    """Write 8-bit rows (list[bytearray], `channels` per pixel) as a PNG."""

    def chunk(tag, data):
        out = struct.pack(">I", len(data)) + tag + data
        return out + struct.pack(">I", zlib.crc32(tag + data) & 0xFFFFFFFF)

    raw = bytearray()
    for row in rows:
        # Filter type 1 (Sub) keeps smooth gradients and thin bright lines
        # compressing far better than unfiltered scanlines.
        filtered = bytearray(len(row))
        for i, value in enumerate(row):
            left = row[i - channels] if i >= channels else 0
            filtered[i] = (value - left) & 0xFF
        raw.append(1)
        raw += filtered

    colour_type = 2 if channels == 3 else 0
    header = struct.pack(">IIBBBBB", width, height, 8, colour_type, 0, 0, 0)
    png = (
        b"\x89PNG\r\n\x1a\n"
        + chunk(b"IHDR", header)
        + chunk(b"IDAT", zlib.compress(bytes(raw), 9))
        + chunk(b"IEND", b"")
    )
    path.write_bytes(png)
    return len(png)


# ---- Drawing helpers ---------------------------------------------------------


def clamp8(v):
    if v < 0:
        return 0
    if v > 255:
        return 255
    return int(v)


def add_pixel(rows, width, height, x, y, rgb, amount):
    """Additive blend of a single pixel (skips out-of-bounds writes)."""
    if x < 0 or y < 0 or x >= width or y >= height:
        return
    i = x * 3
    row = rows[y]
    row[i] = clamp8(row[i] + rgb[0] * amount)
    row[i + 1] = clamp8(row[i + 1] + rgb[1] * amount)
    row[i + 2] = clamp8(row[i + 2] + rgb[2] * amount)


def add_rect(rows, width, height, x0, y0, x1, y1, rgb, amount):
    """Additive rectangle, used for ghosts of code fragments."""
    x0 = max(0, int(x0))
    y0 = max(0, int(y0))
    x1 = min(width, int(x1))
    y1 = min(height, int(y1))
    for y in range(y0, y1):
        row = rows[y]
        r = rgb[0] * amount
        g = rgb[1] * amount
        b = rgb[2] * amount
        for x in range(x0, x1):
            i = x * 3
            row[i] = clamp8(row[i] + r)
            row[i + 1] = clamp8(row[i + 1] + g)
            row[i + 2] = clamp8(row[i + 2] + b)


def add_line(rows, width, height, x0, y0, x1, y1, rgb, amount):
    """Additive line with 1px coverage falloff at the ends."""
    steps = int(max(abs(x1 - x0), abs(y1 - y0))) + 1
    for s in range(steps + 1):
        t = s / steps
        x = x0 + (x1 - x0) * t
        y = y0 + (y1 - y0) * t
        fade = 1.0 - abs(t - 0.5) * 0.35
        add_pixel(rows, width, height, int(round(x)), int(round(y)), rgb, amount * fade)


def add_glow(rows, width, height, cx, cy, rx, ry, rgb, amount):
    """Soft elliptical light source. Separable falloff keeps it fast."""
    x0 = max(0, int(cx - rx))
    x1 = min(width, int(cx + rx) + 1)
    y0 = max(0, int(cy - ry))
    y1 = min(height, int(cy + ry) + 1)
    if x1 <= x0 or y1 <= y0:
        return
    fx = [max(0.0, 1.0 - ((x - cx) / rx) ** 2) ** 1.6 for x in range(x0, x1)]
    fy = [max(0.0, 1.0 - ((y - cy) / ry) ** 2) ** 1.6 for y in range(y0, y1)]
    r0, g0, b0 = rgb
    for y in range(y0, y1):
        wy = fy[y - y0]
        if wy <= 0.0:
            continue
        row = rows[y]
        for x in range(x0, x1):
            w = fx[x - x0] * wy * amount
            i = x * 3
            row[i] = clamp8(row[i] + r0 * w)
            row[i + 1] = clamp8(row[i + 1] + g0 * w)
            row[i + 2] = clamp8(row[i + 2] + b0 * w)


def add_node(rows, width, height, cx, cy, radius, rgb, core=1.0):
    """A topology node: soft halo plus a slightly brighter core."""
    add_glow(rows, width, height, cx, cy, radius * 3.4, radius * 3.4, rgb, 0.55)
    add_glow(rows, width, height, cx, cy, radius, radius, rgb, core)


# ---- Scene --------------------------------------------------------------------


def base_field(width, height):
    """Vertical atmosphere: dark top, lifted middle, dark bottom."""
    rows = []
    for y in range(height):
        t = y / (height - 1)
        if t < 0.58:
            k = t / 0.58
            base = tuple(
                INK_TOP[i] + (INK_MID[i] - INK_TOP[i]) * (k * k * (3 - 2 * k))
                for i in range(3)
            )
        else:
            k = (t - 0.58) / 0.42
            base = tuple(
                INK_MID[i] + (INK_LOW[i] - INK_MID[i]) * (k * k * (3 - 2 * k))
                for i in range(3)
            )
        rows.append(bytearray(bytes(int(v) for v in base) * width))
    return rows


def draw_perspective_grid(rows, width, height, rng, vp=(0.5, 0.56)):
    """A faint floor grid converging on the vanishing point."""
    vpx, vpy = vp[0] * width, vp[1] * height

    # Rays from the vanishing point out past the bottom edge.
    for i in range(-16, 17):
        x = vpx + i * width * 0.11
        add_line(rows, width, height, vpx, vpy, x, height + 40, STEEL, 0.16)

    # Compressed horizontals (1/z spacing) fading out near the horizon.
    z = 0.06
    while z < 6.0:
        y = vpy + (height - vpy) * z / (z + 1.0)
        if y > height:
            break
        fade = max(0.0, 0.20 - z * 0.028)
        add_line(rows, width, height, -20, y, width + 20, y, STEEL, fade)
        z *= 1.34


def draw_topology(rows, width, height, rng, count=26):
    """Network topology: scattered nodes, links between close neighbours."""
    nodes = []
    for _ in range(count):
        x = rng.uniform(0.03, 0.97) * width
        # Bias towards the mid band so the composition keeps a clear centre.
        y = (0.5 + rng.gauss(0, 0.22)) * height
        y = min(max(y, 0.06 * height), 0.94 * height)
        nodes.append((x, y))

    threshold = width * 0.24
    for i, (x0, y0) in enumerate(nodes):
        for x1, y1 in nodes[i + 1 :]:
            dist = math.hypot(x1 - x0, y1 - y0)
            if dist < threshold:
                weight = (1.0 - dist / threshold) * 0.75 + 0.10
                tint = STEEL_HI if rng.random() < 0.18 else STEEL
                add_line(rows, width, height, x0, y0, x1, y1, tint, weight)

    for x, y in nodes:
        bright = rng.random()
        rgb = STEEL_HI if bright > 0.72 else (STEEL_HI[0] // 2, 68, 56)
        add_node(rows, width, height, x, y, rng.uniform(2.2, 3.8), rgb, core=rng.uniform(1.0, 2.1))


def draw_code_fragments(rows, width, height, rng):
    """Ghosted monospace fragments — the suggestion of a terminal, not the cliché."""
    block = max(3, int(width * 0.0035))
    for side in (0.0, 1.0):
        ox = width * (0.045 if side == 0.0 else 0.60)
        top = height * (0.10 if side == 0.0 else 0.16)
        for row in range(rng.randint(7, 11)):
            y = top + row * (block * 3.4)
            if y > height * 0.56:
                break
            x = ox
            for _ in range(rng.randint(3, 8)):
                w = block * rng.randint(2, 7)
                if x + w > width * 0.95:
                    break
                tone = rng.uniform(0.10, 0.34)
                add_rect(rows, width, height, x, y, x + w, y + block * 1.15, GLYPH, tone)
                x += w + block * rng.randint(1, 3)
            # Occasional brighter token/cursor.
            if rng.random() < 0.3:
                add_rect(
                    rows, width, height, x, y, x + block, y + block * 1.15, STEEL_HI, 0.5
                )


def draw_particles(rows, width, height, rng, count=520):
    """Fine dust, denser where the light sources are."""
    lights = ((0.5, 0.42), (0.24, 0.68), (0.82, 0.5))
    for _ in range(count):
        x = rng.uniform(0, width)
        y = rng.uniform(0, height)
        weight = 0.0
        for lx, ly in lights:
            d = math.hypot((x / width - lx), (y / height - ly))
            weight += max(0.0, 1.0 - d / 0.55)
        weight = min(1.0, weight)
        if weight <= 0.02:
            continue
        rgb = GREEN_2 if rng.random() < 0.45 else GRAPHITE
        add_pixel(rows, width, height, int(x), int(y), rgb, rng.uniform(0.14, 0.85) * weight)


def render(width, height, portrait=False):
    rng = random.Random(SEED + (7 if portrait else 0))
    rows = base_field(width, height)

    w, h = float(width), float(height)

    # Atmosphere. Green stays restrained: two small sources, one soft horizon.
    add_glow(rows, width, height, w * 0.50, h * (0.36 if portrait else 0.28),
             w * 0.80, h * 0.46, GRAPHITE, 0.44)
    add_glow(rows, width, height, w * 0.18, h * 0.76, w * 0.40, h * 0.32, GREEN, 0.34)
    add_glow(rows, width, height, w * 0.86, h * 0.48, w * 0.32, h * 0.34, GREEN_2, 0.26)
    add_glow(rows, width, height, w * 0.50, h * 0.585, w * 0.98, h * 0.07, GRAPHITE, 0.42)

    draw_perspective_grid(rows, width, height, rng,
                          vp=(0.5, 0.60 if portrait else 0.56))
    draw_topology(rows, width, height, rng, count=22 if portrait else 26)
    draw_code_fragments(rows, width, height, rng)
    draw_particles(rows, width, height, rng, count=420 if portrait else 520)

    # A soft dark floor at the bottom keeps the hero metrics legible.
    for y in range(int(h * 0.62), height):
        k = (y - h * 0.62) / (h * 0.38)
        fade = k * k
        row = rows[y]
        for x in range(0, width, 2):
            i = x * 3
            row[i] = clamp8(row[i] * (1 - 0.8 * fade))
            row[i + 1] = clamp8(row[i + 1] * (1 - 0.8 * fade))
            row[i + 2] = clamp8(row[i + 2] * (1 - 0.8 * fade))
    return rows


def render_grain(size=256):
    """Tileable monochrome noise, laid over the page in CSS.

    Greyscale and quantised to 32 steps: unfiltered RGB noise is incompressible
    and would cost ~180 KB for something the browser tiles at 3% opacity.
    """
    rng = random.Random(SEED + 99)
    rows = []
    for _ in range(size):
        row = bytearray()
        for _ in range(size):
            v = 128 + int(rng.gauss(0, 40))
            row.append(max(0, min(255, (v // 8) * 8)))
        rows.append(row)
    return rows


def main():
    OUT.mkdir(parents=True, exist_ok=True)

    for name, w, h, portrait in (
        ("hero-bg.png", 1920, 1200, False),
        ("hero-bg-mobile.png", 900, 1500, True),
    ):
        rows = render(w, h, portrait=portrait)
        size = write_png(OUT / name, w, h, rows)
        print(f"{name}  {w}x{h}  {size / 1024:.0f} KB")

    rows = render_grain()
    size = write_png(OUT / "grain.png", 256, 256, rows, channels=1)
    print(f"grain.png  256x256  {size / 1024:.0f} KB")


if __name__ == "__main__":
    main()