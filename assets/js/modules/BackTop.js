class BackTop {
  constructor({ btnSel = '.back-top', headerSel = 'header' } = {}) {
    this.btn = document.querySelector(btnSel);
    this.header = document.querySelector(headerSel);

    if (!this.btn || !this.header) return;

    this.lastState = false;
    this.events();
  }

  events() {
    window.addEventListener('scroll', () => this.toggleVisibility());
  }

  toggleVisibility() {
    const headerBottom = this.header.offsetTop + this.header.offsetHeight;
    const scrollY = window.scrollY || window.pageYOffset;
    const isPastHeader = scrollY > headerBottom;

    // Add/remove class only when state changes
    if (isPastHeader !== this.lastState) {
      this.btn.classList.toggle('back-top--is-visible', isPastHeader);
      this.lastState = isPastHeader;
    }
  }
}

export default BackTop;