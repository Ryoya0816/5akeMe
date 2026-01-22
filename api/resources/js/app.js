import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Welcome: 暖簾をくぐる演出（TOPボタンクリックで左右に開いて遷移）
document.addEventListener('DOMContentLoaded', () => {
  const enter = document.querySelector('.js-enter');
  const norenClose = document.getElementById('norenClose');

  if (enter && norenClose) {
    enter.addEventListener('click', (e) => {
      e.preventDefault();
      const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const href = enter.getAttribute('href');
      if (reduceMotion) {
        window.location.href = href;
        return;
      }
      enter.classList.add('is-disabled');
      setTimeout(() => norenClose.classList.add('is-active'), 200);
      setTimeout(() => norenClose.classList.add('is-vanish'), 1200);
      setTimeout(() => { window.location.href = href; }, 1800);
    });
  }
});
