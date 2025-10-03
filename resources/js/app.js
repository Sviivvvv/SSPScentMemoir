console.log('scroll controls loaded');

function handleScrollClick(e) {
  const btn = e.target.closest('[data-scroll]');
  if (!btn) return;

  e.preventDefault();

  const dir = Number(btn.dataset.dir || 1);
  const targetSel = btn.dataset.scroll;
  const target = document.querySelector(targetSel);
  if (!target) return;

  
  const first = target.querySelector(':scope > *');
  const gap = parseFloat(getComputedStyle(target).columnGap || getComputedStyle(target).gap || '0') || 0;
  const cardWidth = first ? first.clientWidth : Math.round(target.clientWidth * 0.5);
  const step = Math.max(Math.round(target.clientWidth * 0.8), (cardWidth + gap) * 2);

  target.scrollBy({ left: dir * step, behavior: 'smooth' });
  target.focus({ preventScroll: true });
}


document.addEventListener('click', handleScrollClick);


document.addEventListener('livewire:navigated', () => {
  console.log('navigated — scroll controls still active');
});
