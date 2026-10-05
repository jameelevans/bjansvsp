export default class BackTop {
  constructor({btnSel = '.back-top', headerSel = '.header'} = {}) {
    const button = document.querySelector(btnSel), header = document.querySelector(headerSel);
    if (!button || !header) return;
    this.observer = new IntersectionObserver(entries => {
      const visible = entries[0].boundingClientRect.bottom < 0;
      button.classList.toggle('back-top--is-visible', visible);
    });
    this.observer.observe(header);
  }
}
