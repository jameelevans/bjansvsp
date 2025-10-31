/**
 * Accessible Accordion with smooth open/close animation
 * Works with your existing <details class="faq"> markup.
 * Only one FAQ remains open at a time.
 */
class FaqAccordion {
  constructor(rootSel = '#faqs') {
    this.root = document.querySelector(rootSel);
    if (!this.root) return;

    this.items = [...this.root.querySelectorAll('details.faq')];
    this.items.forEach((d, i) => this.setupItem(d, i));

    // Keep only the first open item on load (close any additional opens)
    const firstOpen = this.items.find(d => d.open);
    if (firstOpen) {
      this.items.forEach(d => {
        if (d !== firstOpen && d.open) this.setClosed(d);
      });
    }
  }

  setupItem(detailsEl, i) {
    const summary = detailsEl.querySelector('summary');
    const content = detailsEl.querySelector('.faq__answer');
    if (!summary || !content) return;

    // Add unique IDs if missing
    const panelId = content.id || `faq-panel-${i}`;
    const summaryId = summary.id || `faq-summary-${i}`;
    content.id = panelId;
    summary.id = summaryId;

    // Set ARIA attributes
    summary.setAttribute('role', 'button');
    summary.setAttribute('aria-controls', panelId);
    summary.setAttribute('aria-expanded', detailsEl.open ? 'true' : 'false');

    content.setAttribute('role', 'region');
    content.setAttribute('aria-labelledby', summaryId);
    content.setAttribute('aria-hidden', detailsEl.open ? 'false' : 'true');

    // Bind click behavior
    summary.addEventListener('click', (e) => {
      e.preventDefault();
      if (detailsEl.dataset.animating === '1') return;

      if (detailsEl.open) {
        this.animateClose(detailsEl, content, summary);
      } else {
        // Close any other open items before opening this one
        this.closeOthers(detailsEl);
        this.animateOpen(detailsEl, content, summary);
      }
    });
  }

  /** Close all items except the one provided */
  closeOthers(current) {
    this.items.forEach((d) => {
      if (d !== current && d.open && d.dataset.animating !== '1') {
        this.setClosed(d);
      }
    });
  }

  /** Instantly close an item and update ARIA/state (no animation) */
  setClosed(detailsEl) {
    const summary = detailsEl.querySelector('summary');
    const content = detailsEl.querySelector('.faq__answer');
    if (!summary || !content) return;

    // Clear inline styles and close
    content.style.maxHeight = '';
    content.style.opacity = '';
    content.style.transform = '';
    detailsEl.open = false;

    // Update ARIA
    summary.setAttribute('aria-expanded', 'false');
    content.setAttribute('aria-hidden', 'true');
  }

  animateOpen(detailsEl, content, summary) {
    detailsEl.dataset.animating = '1';
    detailsEl.open = true; // open immediately to reveal content

    content.style.maxHeight = '0px';
    content.style.opacity = '0';
    content.style.transform = 'translateY(-4px)';

    requestAnimationFrame(() => {
      content.style.maxHeight = content.scrollHeight + 'px';
      content.style.opacity = '1';
      content.style.transform = 'translateY(0)';

      summary.setAttribute('aria-expanded', 'true');
      content.setAttribute('aria-hidden', 'false');

      const onEnd = (ev) => {
        if (ev.propertyName !== 'max-height') return;
        content.style.maxHeight = '';
        detailsEl.dataset.animating = '';
        content.removeEventListener('transitionend', onEnd);
      };
      content.addEventListener('transitionend', onEnd);
    });
  }

  animateClose(detailsEl, content, summary) {
    detailsEl.dataset.animating = '1';
    content.style.maxHeight = content.scrollHeight + 'px';

    // Force reflow to allow transition
    void content.offsetHeight;

    requestAnimationFrame(() => {
      content.style.maxHeight = '0px';
      content.style.opacity = '0';
      content.style.transform = 'translateY(-4px)';

      summary.setAttribute('aria-expanded', 'false');
      content.setAttribute('aria-hidden', 'true');

      const onEnd = (ev) => {
        if (ev.propertyName !== 'max-height') return;
        detailsEl.open = false;
        content.style.maxHeight = '';
        detailsEl.dataset.animating = '';
        content.removeEventListener('transitionend', onEnd);
      };
      content.addEventListener('transitionend', onEnd);
    });
  }
}

export default FaqAccordion;