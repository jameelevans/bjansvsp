/** Move only the decorative glass layers; links and text stay stationary. */
export default function initSidebarGlass() {
  const panes = [...document.querySelectorAll('.side-nav')];
  if (!panes.length) return;

  const mouse = window.matchMedia('(hover: hover) and (pointer: fine)');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const states = panes.map(pane => ({ pane, frame: 0, point: null }));
  const properties = ['--glass-x', '--glass-y', '--glass-rx', '--glass-ry'];
  let enabled = false;

  const reset = state => {
    if (state.frame) cancelAnimationFrame(state.frame);
    state.frame = 0;
    state.point = null;
    state.pane.classList.remove('is-glass-active');
    properties.forEach(property => state.pane.style.removeProperty(property));
  };

  const updateMode = () => {
    enabled = mouse.matches && !reducedMotion.matches;
    if (!enabled) states.forEach(reset);
  };

  states.forEach(state => {
    state.pane.addEventListener('pointermove', event => {
      if (!enabled || event.pointerType !== 'mouse') return;
      state.point = { x: event.clientX, y: event.clientY };
      if (state.frame) return;
      state.frame = requestAnimationFrame(() => {
        state.frame = 0;
        const rect = state.pane.getBoundingClientRect();
        if (!rect.width || !rect.height || !state.point) return;
        const x = Math.max(0, Math.min(1, (state.point.x - rect.left) / rect.width));
        const y = Math.max(0, Math.min(1, (state.point.y - rect.top) / rect.height));
        state.pane.style.setProperty('--glass-x', `${(x * 100).toFixed(1)}%`);
        state.pane.style.setProperty('--glass-y', `${(y * 100).toFixed(1)}%`);
        state.pane.style.setProperty('--glass-rx', `${((0.5 - y) * 2).toFixed(2)}deg`);
        state.pane.style.setProperty('--glass-ry', `${((x - 0.5) * 2).toFixed(2)}deg`);
        state.pane.classList.add('is-glass-active');
      });
    }, { passive: true });
    state.pane.addEventListener('pointerleave', () => reset(state), { passive: true });
    state.pane.addEventListener('pointercancel', () => reset(state), { passive: true });
  });

  // No animation loop runs while idle, on touch devices, or with reduced motion.
  mouse.addEventListener('change', updateMode);
  reducedMotion.addEventListener('change', updateMode);
  window.addEventListener('blur', () => states.forEach(reset));
  updateMode();
}
