document.addEventListener('DOMContentLoaded', () => {
  const userPill = document.querySelector('.user-pill');
  if (!userPill) return;

  const base = userPill.dataset.base || '';
  const userId = userPill.dataset.userId || '';

  const icons = {
    user: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>',
    help: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>',
    logout: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>',
  };

  const extraMenuItems = [
    { label: 'Mon Profil', icon: icons.user, href: userId ? `${base}public_profile/public_profile.php?id=${encodeURIComponent(userId)}` : '#' },
    { label: 'Paramètres', icon: icons.settings, href: '#' },
    { label: "Centre d'aide", icon: icons.help, href: '#' },
    { type: 'separator' },
    { label: 'Déconnexion', icon: icons.logout, href: `${base}pagelogin/deconnexion/deco.php`, danger: true },
  ];

  const wrapper = document.createElement('div');
  wrapper.className = 'profile-wrapper';
  userPill.parentNode.insertBefore(wrapper, userPill);
  wrapper.appendChild(userPill);

  const menu = document.createElement('div');
  menu.className = 'profile-menu';
  menu.id = 'profile-menu';
  menu.style.display = 'none';
  wrapper.appendChild(menu);

  const name = userPill.querySelector('.user-name')?.textContent || '';
  const role = userPill.querySelector('.user-role')?.textContent || '';
  const email = userPill.dataset.email || '';
  const avatar = userPill.querySelector('.user-avatar')?.textContent || '?';

  const menuHeader = document.createElement('div');
  menuHeader.className = 'profile-menu-header';

  const headerAvatar = userPill.querySelector('.user-avatar');
  const menuAvatar = headerAvatar?.tagName === 'IMG'
    ? headerAvatar.cloneNode(false)
    : document.createElement('span');
  menuAvatar.className = 'profile-menu-avatar';
  if (menuAvatar.tagName === 'IMG') {
    menuAvatar.alt = '';
    menuAvatar.loading = 'eager';
  } else {
    menuAvatar.textContent = avatar;
  }
  menuHeader.appendChild(menuAvatar);

  const menuUser = document.createElement('div');
  menuUser.className = 'profile-menu-user';
  [
    ['profile-menu-name', name],
    ['profile-menu-email', email],
    ['profile-menu-role', role],
  ].forEach(([className, text]) => {
    const element = document.createElement('div');
    element.className = className;
    element.textContent = text;
    menuUser.appendChild(element);
  });
  menuHeader.appendChild(menuUser);
  menu.appendChild(menuHeader);

  extraMenuItems.forEach(item => {
    if (item.type === 'separator') {
      const separator = document.createElement('div');
      separator.className = 'profile-menu-sep';
      menu.appendChild(separator);
      return;
    }

    const link = document.createElement('a');
    link.className = item.danger ? 'profile-menu-item danger' : 'profile-menu-item';
    link.href = item.href;

    const icon = document.createElement('span');
    icon.className = 'profile-menu-icon';
    icon.innerHTML = item.icon;
    link.append(icon, document.createTextNode(item.label));
    menu.appendChild(link);
  });

  function setMenuOpen(isOpen) {
    menu.style.display = isOpen ? 'block' : 'none';
    userPill.setAttribute('aria-expanded', String(isOpen));
  }

  userPill.addEventListener('click', (e) => {
    e.stopPropagation();
    const isOpen = userPill.getAttribute('aria-expanded') === 'true';
    closeAllMenus();
    if (!isOpen) {
      setMenuOpen(true);
    }
  });

  userPill.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      userPill.click();
    }
  });

  document.addEventListener('click', closeAllMenus);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && userPill.getAttribute('aria-expanded') === 'true') {
      closeAllMenus();
      userPill.focus();
    }
  });

  function closeAllMenus() {
    setMenuOpen(false);
  }
});
