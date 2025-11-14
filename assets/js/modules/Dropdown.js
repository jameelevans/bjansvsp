// assets/js/modules/Dropdown.js
export default class Dropdown {
  constructor({
    parentSel = '.nav__li',
    submenuSel = '.nav-dropdown',
    toggleSel = '.nav__item',
    openClass = 'is-open'
  } = {}) {
    this.parentSel = parentSel;
    this.submenuSel = submenuSel;
    this.toggleSel = toggleSel;
    this.openClass = openClass;

    this.parents = Array.from(document.querySelectorAll(`${parentSel} ${submenuSel}`))
      .map(ul => ul.closest(parentSel))
      .filter(Boolean);

    this.parents.forEach(parent => this.bindOne(parent));
  }

  bindOne(parent) {
    const submenu = parent.querySelector(this.submenuSel);
    const toggle  = parent.querySelector(this.toggleSel);
    if (!submenu || !toggle) return;

    // Ensure ARIA defaults
    toggle.setAttribute('aria-haspopup', 'true');
    toggle.setAttribute('aria-controls', submenu.id || this.ensureId(submenu, 'submenu'));
    toggle.setAttribute('aria-expanded', 'false');

    // Hover intent open/close
    let hoverTimer;
    parent.addEventListener('mouseenter', () => {
      clearTimeout(hoverTimer);
      this.open(parent, toggle, submenu);
    });
    parent.addEventListener('mouseleave', () => {
      hoverTimer = setTimeout(() => this.close(parent, toggle, submenu), 120);
    });

    // Focus management (keyboard tabbing in/out)
    parent.addEventListener('focusin', () => {
      clearTimeout(hoverTimer);
      this.open(parent, toggle, submenu);
    });
    parent.addEventListener('focusout', (e) => {
      // Close only when focus truly leaves the parent
      setTimeout(() => {
        if (!parent.contains(document.activeElement)) {
          this.close(parent, toggle, submenu);
        }
      }, 0);
    });

    // Keyboard controls on the toggle
    toggle.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowDown' || e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.open(parent, toggle, submenu);
        // focus first item
        const first = submenu.querySelector('a, button, [tabindex]:not([tabindex="-1"])');
        first?.focus();
      } else if (e.key === 'Escape') {
        this.close(parent, toggle, submenu);
        toggle.focus();
      }
    });

    // ESC inside submenu closes it
    submenu.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.close(parent, toggle, submenu);
        toggle.focus();
      }
    });

    // Optional: first click on toggle opens, second click follows link
    toggle.addEventListener('click', (e) => {
      // If closed, open instead of navigating
      if (!parent.classList.contains(this.openClass)) {
        e.preventDefault();
        this.open(parent, toggle, submenu);
      }
      // If already open, allow normal navigation (to #resources / home#resources)
    });
  }

  open(parent, toggle, submenu) {
    parent.classList.add(this.openClass);
    toggle.setAttribute('aria-expanded', 'true');
  }

  close(parent, toggle, submenu) {
    parent.classList.remove(this.openClass);
    toggle.setAttribute('aria-expanded', 'false');
  }

  ensureId(el, prefix) {
    const id = `${prefix}-${Math.random().toString(36).slice(2, 9)}`;
    el.id = id;
    return id;
  }
}