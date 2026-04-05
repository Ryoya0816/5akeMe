// CSRF token for fetch requests
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
  window._csrfToken = token.getAttribute('content');
}
