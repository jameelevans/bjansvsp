export default class ContactHighlight {
  constructor() {
    document.addEventListener('click', event => {
      if (!event.target.closest('a[href="#contact-us"]') || event.ctrlKey || event.metaKey || event.shiftKey) return;
      const target = document.getElementById('contact-us');
      if (!target) return;
      clearTimeout(this.timer);
      target.classList.add('contact--highlight');
      this.timer = setTimeout(() => target.classList.remove('contact--highlight'), 1800);
    });
  }
}
