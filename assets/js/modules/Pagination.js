// assets/js/modules/Pagination.js
class Pagination {
  constructor({ containerSel = '.resources__container', paginationSel = '.pagination', sectionId = '#resources' } = {}) {
    this.containerSel = containerSel;
    this.paginationSel = paginationSel;
    this.sectionId = sectionId;

    this.container  = document.querySelector(this.containerSel);
    this.pagination = document.querySelector(this.paginationSel);
    if (!this.container || !this.pagination) return;

    // Make updates polite for AT (screen readers)
    this.container.setAttribute('aria-live', 'polite');

    // Fallback-friendly URLs (only inside pagination)
    this.addHashToLinks();

    // Bind events
    this.onClick    = this.onClick.bind(this);
    this.onPopState = this.onPopState.bind(this);

    // Capture helps if other handlers stop propagation
    document.addEventListener('click', this.onClick, true);
    window.addEventListener('popstate', this.onPopState);
  }

  addHashToLinks() {
    // Scope strictly to the pagination node
    this.pagination.querySelectorAll('a').forEach(a => {
      try {
        const u = new URL(a.href, window.location.origin);
        // Append #resources so after hard reload it jumps back to the section
        u.hash = this.sectionId.replace('#', '');
        a.href = u.toString();
      } catch (_) {}
    });
  }

  onClick(e) {
    // Find the nearest <a>, then ensure it lives inside .pagination
    const link = e.target.closest('a');
    if (!link || !this.pagination.contains(link)) return;

    // allow new tab / middle click / modifiers
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button === 1) return;

    e.preventDefault();
    this.navigate(link.href);
  }

  onPopState() {
    // Only handle history events that involve pagination
    const qs = window.location.search || '';
    if (!/[?&](paged|page)=\d+/.test(qs)) return;
    this.navigate(window.location.href, { push: false });
  }

  async navigate(href, { push = true } = {}) {
    if (!this.container) return;
    this.setLoading(true);

    try {
      // Fetch the new page (hash is ignored by the network, so href is fine)
      const html = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.text());
      const doc  = new DOMParser().parseFromString(html, 'text/html');

      const nextContainer  = doc.querySelector(this.containerSel);
      const nextPagination = doc.querySelector(this.paginationSel);

      if (nextContainer) this.container.replaceWith(nextContainer);
      if (nextPagination) {
        const existingPag = document.querySelector(this.paginationSel);
        if (existingPag) existingPag.replaceWith(nextPagination);
      }

      // refresh refs after swap
      this.container  = document.querySelector(this.containerSel);
      this.pagination = document.querySelector(this.paginationSel);

      // keep aria-live and reapply hash to the new pagination links
      if (this.container) this.container.setAttribute('aria-live', 'polite');
      if (this.pagination) this.addHashToLinks();

      // update URL (keep #resources)
      if (push) {
        const u = new URL(href, window.location.origin);
        u.hash = this.sectionId.replace('#', '');
        history.pushState({}, '', u.toString());
      }

      // focus + scroll for accessibility & UX
      const section = document.querySelector(this.sectionId);
      if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        this.container?.setAttribute('tabindex', '-1');
        this.container?.focus({ preventScroll: true });
      }

    } catch (_) {
      // graceful fallback
      window.location.href = href;
    } finally {
      this.setLoading(false);
    }
  }

  setLoading(on) {
    if (!this.container) return;
    if (on) {
      this.container.setAttribute('aria-busy', 'true');
      this.container.style.opacity = '0.6';
      this.container.style.pointerEvents = 'none';
    } else {
      this.container.removeAttribute('aria-busy');
      this.container.style.opacity = '';
      this.container.style.pointerEvents = '';
    }
  }
}

export default Pagination;