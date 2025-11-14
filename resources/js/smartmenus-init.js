// public/js/smartmenus-init.js

(function () {
  function initSmartMenus() {
    if (!window.SmartMenus) return;

    var navbar1 = document.querySelector('#navbar1');
    if (!navbar1) return;

    if (navbar1._smartMenusInstance) return;

    navbar1._smartMenusInstance = new window.SmartMenus(navbar1);
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    // DOM is ready enough
    initSmartMenus();
  } else {
    document.addEventListener('DOMContentLoaded', initSmartMenus);
  }
})();
