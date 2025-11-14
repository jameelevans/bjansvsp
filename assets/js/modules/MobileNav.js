import $ from 'jquery';

class MobileNav {
  constructor() {
    // Elements
    this.mobileBackground = $('.mobile-navigation__background');
    this.mobileMenu      = $('.mobile-navigation__menu');   // toggle button (hamburger)
    this.mobileContent   = $('.mobile-navigation__nav');    // panel
    this.mobileIcon      = $('.mobile-navigation__icon');   // hamburger icon lines
    this.closeButton     = $('.mobile-navigation__close');  // new X button inside panel
    this.bodyContainer   = $('.container');                 // existing layout wrapper
    this.$body           = $('body');

    // If we don't have the core pieces, bail
    if (!this.mobileMenu.length || !this.mobileContent.length) return;

    // Focus trap config
    this.focusableSelector =
      'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';
    this.$focusables = $();
    this.$firstFocusable = null;
    this.$lastFocusable = null;

    // Bind handlers to this
    this.keyPressHandler = this.keyPressHandler.bind(this);
    this.trapFocus       = this.trapFocus.bind(this);

    this.bindEvents();
  }

  bindEvents() {
    // Toggle open/close via hamburger
    this.mobileMenu.on('click', this.toggleMenu.bind(this));

    // Close via X button
    if (this.closeButton.length) {
      this.closeButton.on('click', this.closeMenu.bind(this));
    }

    // ESC key (global)
    $(document).on('keyup', this.keyPressHandler);

    // Close menu when a navigation link is clicked
    this.mobileContent.on('click', 'a', () => {
      if (this.isOpen()) {
        this.closeMenu();
      }
    });
  }

  isOpen() {
    return this.mobileContent.hasClass('mobile-navigation__nav--is-visible');
  }

  keyPressHandler(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      if (this.isOpen()) {
        this.closeMenu();
      }
    }
  }

  // Set up list of focusable elements inside the panel
  setupFocusables() {
    this.$focusables = this.mobileContent.find(this.focusableSelector).filter(':visible');

    if (this.$focusables.length) {
      this.$firstFocusable = this.$focusables.eq(0);
      this.$lastFocusable  = this.$focusables.eq(this.$focusables.length - 1);
    } else {
      this.$firstFocusable = null;
      this.$lastFocusable  = null;
    }
  }

  // Trap focus inside the mobile nav when open
  trapFocus(e) {
    if (!this.isOpen()) return;
    if (e.key !== 'Tab' && e.keyCode !== 9) return;

    if (!this.$focusables.length) {
      // If somehow nothing is focusable, keep focus on menu button
      e.preventDefault();
      this.mobileMenu.trigger('focus');
      return;
    }

    const isShift = e.shiftKey;

    const active = $(document.activeElement);

    if (isShift) {
      // SHIFT + TAB on first element -> wrap to last
      if (active.is(this.$firstFocusable)) {
        e.preventDefault();
        this.$lastFocusable.trigger('focus');
      }
    } else {
      // TAB on last element -> wrap to first
      if (active.is(this.$lastFocusable)) {
        e.preventDefault();
        this.$firstFocusable.trigger('focus');
      }
    }
  }

  openMenu() {
    // Visual state
    this.mobileContent
      .addClass('mobile-navigation__nav--is-visible')
      .attr('aria-hidden', 'false');

    this.mobileMenu.attr('aria-expanded', 'true');

    if (this.mobileBackground.length) {
      this.mobileBackground.addClass('mobile-navigation__background--is-expanded');
    }

    this.mobileIcon.addClass('mobile-navigation__icon--close-x');

    // Lock layout scroll
    this.bodyContainer.addClass('fixed-position');
    this.$body.addClass('no-scroll');

    // Setup and move focus into the panel
    this.setupFocusables();

    if (this.$firstFocusable && this.$firstFocusable.length) {
      this.$firstFocusable.trigger('focus');
    } else {
      // Fallback: keep focus on the toggle button
      this.mobileMenu.trigger('focus');
    }

    // Start trapping focus while menu is open
    $(document).on('keydown', this.trapFocus);
  }

  closeMenu() {
    // Visual state
    this.mobileContent
      .removeClass('mobile-navigation__nav--is-visible')
      .attr('aria-hidden', 'true');

    this.mobileMenu.attr('aria-expanded', 'false');

    if (this.mobileBackground.length) {
      this.mobileBackground.removeClass('mobile-navigation__background--is-expanded');
    }

    this.mobileIcon.removeClass('mobile-navigation__icon--close-x');

    // Unlock layout scroll
    this.bodyContainer.removeClass('fixed-position');
    this.$body.removeClass('no-scroll');

    // Stop trapping focus
    $(document).off('keydown', this.trapFocus);

    // Return focus to the toggle for keyboard users
    this.mobileMenu.trigger('focus');
  }

  toggleMenu(e) {
    if (e) e.preventDefault();
    if (this.isOpen()) {
      this.closeMenu();
    } else {
      this.openMenu();
    }
  }
}

export default MobileNav;             