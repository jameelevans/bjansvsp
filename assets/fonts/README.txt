NSVSP self-hosted fonts

The active stylesheet loads Roboto and Open Sans from this theme's assets/fonts
folder. It does not call Google Fonts or Adobe Fonts. The browser still makes
normal same-origin HTTP requests; caching lets subsequent visits reuse the files.

Roboto: normal + italic, variable weight 100–900, width 75%–100%.
Open Sans: normal + italic, variable weight 300–800, width 75%–100%.
These are the actual axes supported by the original font files.

*-Latin.woff2 files cover common Latin text and punctuation. The full WOFF2
faces preserve the originals' additional language coverage and load only when
characters outside the Latin subset are needed. Italics load only when used.
The normal Latin Roboto and Open Sans faces are preloaded in functions.php;
preload paths must exactly match the stylesheet to avoid duplicate downloads.
font-display: swap keeps text visible while a font request completes.

Original TTF sources are retained. Roboto-OFL.txt and OpenSans-OFL.txt contain
the redistribution licenses. Keep those licenses with redistributed fonts.

Regeneration (development only):
  python3 -m pip install 'fonttools[woff]'
  python3 build-fonts.py
Then rebuild CSS with gulp styles. Runtime hosting needs no Python dependencies.
When font binaries change, rename/version the font filenames in both SCSS and
functions.php before deploying, then purge hosting caches. Use long-lived font
cache headers and gzip/Brotli for text/SVG/CSS/JS at the hosting layer; WOFF2 is
already compressed. Do not make HTML or mutable, unversioned assets immutable.
