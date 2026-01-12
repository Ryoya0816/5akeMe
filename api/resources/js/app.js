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

      // ちょい溜め
      enter.classList.add('is-disabled');

      setTimeout(() => {
        norenClose.classList.add('is-active'); // 開く（左右へ）
      }, 180);

      // 開き終わったら、奥に吸い込まれて消える
      setTimeout(() => {
        norenClose.classList.add('is-vanish');
      }, 900);

      // 遷移
      setTimeout(() => {
        window.location.href = href;
      }, 900 + 450);
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
