/** Ordinary navigation links: Enter follows the link; ArrowDown explores categories. */
export default class Dropdown {
  constructor() {
    document.querySelectorAll('.nav__has-dropdown').forEach(parent => {
      const submenu = parent.querySelector('.nav-dropdown');
      const link = parent.querySelector('.nav__item');
      if (!submenu || !link) {
        link?.removeAttribute('aria-expanded');
        link?.removeAttribute('aria-controls');
        return;
      }
      const setOpen = open => {
        submenu.inert = !open;
        parent.classList.toggle('is-open', open);
        link.setAttribute('aria-expanded', String(open));
      };
      setOpen(false);
      let restoringFocus = false;
      parent.addEventListener('mouseenter', () => setOpen(true));
      parent.addEventListener('mouseleave', () => {
        if (!parent.contains(document.activeElement)) setOpen(false);
      });
      parent.addEventListener('focusin', () => { if (!restoringFocus) setOpen(true); });
      parent.addEventListener('focusout', event => {
        if (!parent.contains(event.relatedTarget)) setOpen(false);
      });
      parent.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
          event.preventDefault();
          restoringFocus = true;
          link.focus();
          setOpen(false);
          restoringFocus = false;
        } else if (event.target === link && (event.key === 'ArrowDown' || event.key === ' ')) {
          event.preventDefault();
          setOpen(true);
          submenu.querySelector('a')?.focus();
        }
      });
      // Touch users can disclose categories with the first tap, then follow the link.
      link.addEventListener('click', event => {
        if (event.detail && !parent.classList.contains('is-open') && !event.ctrlKey && !event.metaKey) {
          event.preventDefault(); setOpen(true);
        }
      });
    });
  }
}
