/** A modal menu with a stable page position and native background focus isolation. */
export default class MobileNav {
  constructor() {
    this.trigger = document.querySelector('.mobile-navigation__menu');
    this.panel = document.querySelector('.mobile-navigation__nav');
    if (!this.trigger || !this.panel) return;
    this.wrapper = this.trigger.closest('.mobile-navigation');
    this.close = this.panel.querySelector('.mobile-navigation__close');
    this.back = this.panel.querySelector('.mobile-navigation__back');
    this.main = this.panel.querySelector('.mobile-navigation__list');
    this.submenuWrapper = this.panel.querySelector('.mobile-navigation__submenus');
    this.submenus = [...this.panel.querySelectorAll('.mobile-navigation__submenu')];
    this.toggles = [...this.panel.querySelectorAll('[data-submenu]')];
    this.resetSubmenus();
    this.trigger.addEventListener('click', () => this.openMenu());
    this.close.addEventListener('click', () => this.closeMenu());
    this.back.addEventListener('click', () => this.closeSubmenu());
    this.toggles.forEach(button => button.addEventListener('click', () => this.openSubmenu(button)));
    this.panel.addEventListener('click', event => {
      if (event.target.closest('a[href]')) this.closeMenu();
    });
    document.addEventListener('keydown', event => this.onKeydown(event));
    window.addEventListener('resize', () => {
      if (!this.isOpen) return;
      if (window.matchMedia('(min-width: 700px)').matches) this.closeMenu();
      else document.body.style.width = `${document.documentElement.getBoundingClientRect().width}px`;
    });
  }
  openMenu() {
    if (this.isOpen) return;
    this.scrollY = window.scrollY;
    const body = document.body;
    this.savedStyles = {};
    ['position', 'top', 'left', 'width', 'overflow'].forEach(key => this.savedStyles[key] = body.style[key]);
    const width = document.documentElement.getBoundingClientRect().width;
    body.style.position = 'fixed';
    body.style.top = `${-this.scrollY}px`;
    body.style.left = '0';
    body.style.width = `${width}px`;
    body.style.overflow = 'hidden';
    document.documentElement.classList.add('menu-open');
    this.background = [...body.children].filter(el => el !== this.wrapper && !['SCRIPT', 'STYLE', 'LINK'].includes(el.tagName));
    this.previousInert = this.background.map(el => el.inert);
    this.background.forEach(el => el.inert = true);
    this.trigger.inert = true;
    this.isOpen = true;
    this.panel.inert = false;
    this.panel.setAttribute('aria-hidden', 'false');
    this.panel.classList.add('mobile-navigation__nav--is-visible');
    this.trigger.setAttribute('aria-expanded', 'true');
    this.resetSubmenus();
    requestAnimationFrame(() => { if (this.isOpen) this.close.focus({preventScroll: true}); });
  }
  closeMenu() {
    if (!this.isOpen) return;
    this.isOpen = false;
    this.background.forEach((el, i) => el.inert = this.previousInert[i]);
    this.trigger.inert = false;
    Object.assign(document.body.style, this.savedStyles);
    document.documentElement.classList.remove('menu-open');
    window.scrollTo({top: this.scrollY, behavior: 'instant'});
    this.trigger.focus({preventScroll: true});
    this.panel.inert = true;
    this.panel.setAttribute('aria-hidden', 'true');
    this.panel.classList.remove('mobile-navigation__nav--is-visible');
    this.trigger.setAttribute('aria-expanded', 'false');
    this.resetSubmenus();
  }
  resetSubmenus() {
    this.activeToggle = null;
    this.main.inert = false;
    this.submenuWrapper.inert = true;
    this.submenuWrapper.setAttribute('aria-hidden', 'true');
    this.submenus.forEach(el => { el.inert = true; el.setAttribute('aria-hidden', 'true'); });
    this.toggles.forEach(el => el.setAttribute('aria-expanded', 'false'));
    this.close.hidden = false;
    this.back.hidden = true;
    this.back.removeAttribute('aria-hidden');
    this.panel.classList.remove('mobile-navigation__nav--submenu-open');
  }
  openSubmenu(button) {
    const submenu = document.getElementById(button.dataset.submenu);
    if (!submenu || !this.isOpen) return;
    this.resetSubmenus();
    this.activeToggle = button;
    this.submenuWrapper.inert = false;
    this.submenuWrapper.setAttribute('aria-hidden', 'false');
    submenu.inert = false;
    submenu.setAttribute('aria-hidden', 'false');
    button.setAttribute('aria-expanded', 'true');
    this.back.hidden = false;
    this.back.focus({preventScroll: true});
    this.close.hidden = true;
    this.main.inert = true;
    this.panel.classList.add('mobile-navigation__nav--submenu-open');
    this.panel.scrollTop = 0;
  }
  closeSubmenu() {
    const button = this.activeToggle;
    if (!button) return;
    this.main.inert = false;
    button.focus({preventScroll: true});
    this.resetSubmenus();
  }
  onKeydown(event) {
    if (!this.isOpen) return;
    if (event.key === 'Escape') {
      event.preventDefault();
      this.activeToggle ? this.closeSubmenu() : this.closeMenu();
    } else if (event.key === 'Tab') {
      const items = [...this.panel.querySelectorAll('a[href],button,input,select,textarea,[tabindex]:not([tabindex="-1"])')]
        .filter(el => !el.disabled && !el.closest('[inert],[hidden]') && getComputedStyle(el).visibility !== 'hidden' && el.getClientRects().length);
      const first = items[0], last = items[items.length - 1];
      if (!first) { event.preventDefault(); this.panel.focus(); }
      else if (event.shiftKey && (document.activeElement === first || !this.panel.contains(document.activeElement))) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
  }
}
