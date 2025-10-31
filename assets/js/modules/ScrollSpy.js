// assets/js/modules/ScrollSpy.js
class ScrollSpy {
  /**
   * @param {Object} opts
   * @param {string} opts.navSel   - container for the aside nav
   * @param {string} opts.linkSel  - selector for anchor links inside the nav
   * @param {number} opts.offset   - top offset in px (sticky header height + a bit)
   */
  constructor({ navSel = '.side-nav', linkSel = 'a[href^="#"]', offset = null } = {}) {
    this.nav = document.querySelector(navSel);
    if (!this.nav) return;

    // Collect links and target sections
    this.links = Array.from(this.nav.querySelectorAll(linkSel));
    this.map = new Map(); // id -> link element

    this.links.forEach((a) => {
      const id = (a.getAttribute('href') || '').trim();
      if (!id || id === '#') return;
      const target = document.querySelector(id);
      if (target) this.map.set(target.id, a);
    });

    if (this.map.size === 0) return;

    // Figure out offset (use CSS var --sticky-offset if present)
    this.offset = offset ?? this._getCssOffset('--sticky-offset', 120);

    // Bind handlers
    this._onClick = this._onClick.bind(this);

    // Observe sections entering viewport
    this._observer = new IntersectionObserver(
      (entries) => this._onIntersect(entries),
      {
        // Trigger when the section top clears the sticky header by a bit
        root: null,
        rootMargin: `-${this.offset}px 0px -60% 0px`,
        threshold: 0.1, // small portion visible
      }
    );

    // Start observing
    for (const id of this.map.keys()) {
      const el = document.getElementById(id);
      if (el) this._observer.observe(el);
    }

    // Immediate update when clicking a nav link (no waiting for IO)
    this.nav.addEventListener('click', this._onClick);
  }

  _getCssOffset(varName, fallback) {
    const raw = getComputedStyle(document.documentElement).getPropertyValue(varName);
    const n = parseInt(raw, 10);
    return Number.isFinite(n) && n > 0 ? n : fallback;
  }

  _onClick(e) {
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;

    const id = a.getAttribute('href');
    if (!id || id === '#') return;

    // Optimistically set active link on click
    this._setActiveById(id.replace('#', ''));
  }

  _onIntersect(entries) {
    // Pick the most visible/most recent intersecting section near the top
    let best = null;
    let bestRatio = 0;

    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      const ratio = entry.intersectionRatio;
      if (ratio >= bestRatio) {
        bestRatio = ratio;
        best = entry.target;
      }
    }

    if (best && this.map.has(best.id)) {
      this._setActiveById(best.id);
    }
  }

  _setActiveById(id) {
    // Clear current
    this.links.forEach((lnk) => lnk.classList.remove('is-active'));
    // Set new
    const link = this.map.get(id);
    if (link) link.classList.add('is-active');
  }
}

export default ScrollSpy;