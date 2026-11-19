(function () {
  'use strict';

  var root = document.documentElement;
  var body = document.body;

  var sidebarToggle = document.getElementById('sidebarToggle');
  var backdrop = document.getElementById('sidebarBackdrop');
  var themeToggle = document.getElementById('themeToggle');

  function setSidebarCollapsed(collapsed) {
    root.classList.toggle('sidebar-collapsed', collapsed);
    try {
      localStorage.setItem('ratingtop-sidebar', collapsed ? '1' : '0');
    } catch (err) {}
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function () {
      if (window.matchMedia('(max-width: 900px)').matches) {
        body.classList.toggle('sidebar-open');
      } else {
        setSidebarCollapsed(!root.classList.contains('sidebar-collapsed'));
      }
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', function () {
      body.classList.remove('sidebar-open');
    });
  }

  function applyTheme(theme) {
    root.dataset.theme = theme;
    try {
      localStorage.setItem('ratingtop-theme', theme);
    } catch (err) {}
  }

  if (themeToggle) {
    themeToggle.addEventListener('click', function () {
      applyTheme(root.dataset.theme === 'light' ? 'dark' : 'light');
    });
  }

  document.querySelectorAll('.flash-close').forEach(function (button) {
    button.addEventListener('click', function () {
      var flash = button.closest('.flash');
      if (flash) flash.remove();
    });
  });

  window.setTimeout(function () {
    document.querySelectorAll('.flash').forEach(function (flash) {
      flash.style.transition = 'opacity .4s ease, transform .4s ease';
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-8px)';
      window.setTimeout(function () { flash.remove(); }, 400);
    });
  }, 6500);

  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
  });

  var photoInput = document.getElementById('foto');
  var photoPreview = document.getElementById('fotoPreview');
  if (photoInput && photoPreview) {
    photoInput.addEventListener('change', function () {
      var file = photoInput.files && photoInput.files[0];
      if (!file) return;
      if (!file.type.match(/^image\/(jpeg|png|webp|gif)$/)) {
        window.alert('Format gambar harus JPG, PNG, WEBP, atau GIF.');
        photoInput.value = '';
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        window.alert('Ukuran foto maksimal 2 MB.');
        photoInput.value = '';
        return;
      }
      photoPreview.src = URL.createObjectURL(file);
    });
  }
})();
