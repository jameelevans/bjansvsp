// assets/js/modules/ContactHighlight.js
class ContactHighlight {
  /**
   * @param {Object} opts
   * @param {string} opts.linkSel   Selector for links that trigger the effect
   * @param {string} opts.targetSel Selector for the contact section/anchor to find
   * @param {string|null} opts.wrapSel Optional wrapper to highlight via closest()
   * @param {number} opts.delayMs   Delay (ms) before starting highlight (let smooth scroll finish)
   * @param {number} opts.showMs    Total highlight duration (ms)
   * @param {number} opts.blinkMs   Blink interval (ms)
   */
  constructor({
    linkSel   = 'a[href="#contact-us"]',
    targetSel = '#contact-us',
    wrapSel   = 'footer, .site-footer, .footer, .footer__content, .contact',
    delayMs   = 800,
    showMs    = 2600,
    blinkMs   = 300
  } = {}) {
    this.linkSel  = linkSel;
    this.targetSel = targetSel;
    this.wrapSel  = wrapSel;
    this.delayMs  = delayMs;
    this.showMs   = showMs;
    this.blinkMs  = blinkMs;

    this._onClick = this._onClick.bind(this);
    document.addEventListener('click', this._onClick);
  }

  _onClick(e) {
    const link = e.target.closest(this.linkSel);
    if (!link) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button === 1) return;

    clearTimeout(this._timer);
    this._timer = setTimeout(() => this._flash(), this.delayMs);
  }

  /** Resolve the correct element at click time */
  _resolveTarget() {
    // If there are duplicate #contact-us (shouldn't be, but defensive), pick the last one (footer)
    const nodes = Array.from(document.querySelectorAll(this.targetSel));
    let el = nodes.length ? nodes[nodes.length - 1] : null;
    if (!el) return null;

    // If we provided a wrapper selector, highlight that container if present
    if (this.wrapSel) {
      const wrapper = el.closest(this.wrapSel);
      if (wrapper) el = wrapper;
    }
    return el;
  }

  _flash() {
    const el = this._resolveTarget();
    if (!el) return;

    // Resolve brand purple fallback
    const brand = (getComputedStyle(document.documentElement).getPropertyValue('--color-purple') || '#726C9A').trim() || '#726C9A';

    // Save original inline styles to restore
    const original = {
      outline:       el.style.outline,
      outlineOffset: el.style.outlineOffset,
      boxShadow:     el.style.boxShadow
    };

    // Base dashed outline + offset
    el.style.outline       = `4px dashed ${brand}`;
    el.style.outlineOffset = '9px';

    // Blink by toggling visibility + a subtle pulse ring
    let visible = true;
    const tick = () => {
      if (visible) {
        el.style.outlineColor = brand;
        el.style.boxShadow = `0 0 0 10px ${this._alpha(brand, 0.06)}`;
      } else {
        el.style.outlineColor = 'transparent';
        el.style.boxShadow = 'none';
      }
      visible = !visible;
    };

    tick();
    clearInterval(this._blink);
    this._blink = setInterval(tick, this.blinkMs);

    clearTimeout(this._clear);
    this._clear = setTimeout(() => {
      clearInterval(this._blink);
      // restore
      el.style.outline       = original.outline;
      el.style.outlineOffset = original.outlineOffset;
      el.style.boxShadow     = original.boxShadow;
    }, this.showMs);
  }

  _alpha(hex, a = 0.1) {
    let c = hex.replace('#', '');
    if (c.length === 3) c = c.split('').map(ch => ch + ch).join('');
    const r = parseInt(c.slice(0, 2), 16);
    const g = parseInt(c.slice(2, 4), 16);
    const b = parseInt(c.slice(4, 6), 16);
    return `rgba(${r}, ${g}, ${b}, ${a})`;
  }
}

export default ContactHighlight;