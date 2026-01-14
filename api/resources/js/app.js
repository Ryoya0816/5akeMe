// resources/js/app.js
import './bootstrap';
import './top-bg';

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

      // ちょい溜め（ボタンが少し沈む）
      enter.classList.add('is-disabled');

      // 暖簾を左右に大きく開く（SAKEICE風の演出）
      setTimeout(() => {
        norenClose.classList.add('is-active');
      }, 200);

      // 暖簾をくぐった後、奥に消える演出
      setTimeout(() => {
        norenClose.classList.add('is-vanish');
      }, 1200);

      // 遷移（暖簾をくぐった後）
      setTimeout(() => {
        window.location.href = href;
      }, 1800);
    });
  }

  // 店内側（ページ表示で暖簾が開いた状態にしたい時用：必要なら使う）
  const norenOpen = document.getElementById('norenOpen');
  if (norenOpen) {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion) requestAnimationFrame(() => norenOpen.classList.add('is-active'));
    else norenOpen.classList.add('is-active');
  }
});
