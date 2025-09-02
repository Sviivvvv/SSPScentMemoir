document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-scroll]').forEach(btn => {
    const dir = Number(btn.dataset.dir || 1);
    const targetSel = btn.dataset.scroll;
    btn.addEventListener('click', () => {
      const target = document.querySelector(targetSel);
      if (!target) return;
      target.scrollBy({ left: dir * Math.round(target.clientWidth * 0.8), behavior: 'smooth' });
    });
  });
});