/** Native details keeps keyboard, touch and reduced-motion behavior reliable. */
export default class FaqAccordion {
  constructor() {
    document.querySelectorAll('.faqs__accordion').forEach(group => {
      const items = [...group.querySelectorAll('details.faq')];
      items.forEach(item => item.addEventListener('toggle', () => {
        if (item.open) items.forEach(other => { if (other !== item) other.open = false; });
      }));
    });
  }
}
