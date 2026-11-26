function initSidebarToggle() {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;

  const overlay = document.createElement('div');
  overlay.className = 'sidebar-overlay';
  document.body.appendChild(overlay);

  const isDesktop = () => window.innerWidth >= 992;

  const closeMobile = () => {
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('show');
  };

  document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      if (isDesktop()) {
        sidebar.classList.toggle('sidebar-collapsed');
      } else if (sidebar.classList.contains('sidebar-open')) {
        closeMobile();
      } else {
        sidebar.classList.add('sidebar-open');
        overlay.classList.add('show');
      }
    });
  });

  document.getElementById('sidebarClose')?.addEventListener('click', closeMobile);
  overlay.addEventListener('click', closeMobile);
}

function initUserDropdown() {
  const toggle = document.getElementById('userMenuToggle');
  if (!toggle) return;

  new bootstrap.Dropdown(toggle, {
    popperConfig(defaultConfig) {
      return { ...defaultConfig, strategy: 'fixed' };
    }
  });

  const chevron = toggle.querySelector('.user-menu-chevron');
  if (chevron) {
    toggle.addEventListener('show.bs.dropdown', () => chevron.classList.add('rotated'));
    toggle.addEventListener('hide.bs.dropdown', () => chevron.classList.remove('rotated'));
  }
}

function initTooltips() {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
    new bootstrap.Tooltip(el);
  });
}

let deferredInstallPrompt = null;

function initPwaInstall() {
  const installButton = document.getElementById('installApplication');
  if (!installButton) return;

  const isInstalled = window.matchMedia('(display-mode: standalone)').matches
    || window.navigator.standalone === true;

  if (isInstalled) {
    installButton.classList.add('d-none');
  }

  window.addEventListener('beforeinstallprompt', event => {
    event.preventEvent?.();
    event.preventDefault();
    deferredInstallPrompt = event;

    if (!isInstalled) {
      installButton.classList.remove('d-none');
      installButton.title = 'Instalar Farmacia San Juan Bautista';
    }
  });

  installButton.addEventListener('click', async () => {
    if (!deferredInstallPrompt) {
      alert('La instalación no está disponible en este navegador o ya fue ofrecida. Use el menú de Chrome: Instalar aplicación.');
      return;
    }

    await deferredInstallPrompt.prompt();
    const result = await deferredInstallPrompt.userChoice;

    if (result.outcome === 'accepted') {
      installButton.classList.add('d-none');
    }

    deferredInstallPrompt = null;
  });

  window.addEventListener('appinstalled', () => {
    installButton.classList.add('d-none');
    deferredInstallPrompt = null;
  });
}

function initConnectionGuard() {
  const offlineAlert = document.getElementById('offlineAlert');

  function updateConnectionState() {
    if (!navigator.onLine) {
      offlineAlert?.classList.remove('d-none');
    } else {
      offlineAlert?.classList.add('d-none');
    }
  }

  window.addEventListener('online', updateConnectionState);
  window.addEventListener('offline', updateConnectionState);
  updateConnectionState();

  document.addEventListener('submit', event => {
    if (!navigator.onLine) {
      event.preventDefault();
      alert('Sin conexión. Las ventas y cambios de inventario no se pueden registrar. Restaure la conexión e intente nuevamente.');
    }
  }, true);
}

document.addEventListener('DOMContentLoaded', () => {
  initSidebarToggle();
  initUserDropdown();
  initTooltips();
  initPwaInstall();
  initConnectionGuard();
});
