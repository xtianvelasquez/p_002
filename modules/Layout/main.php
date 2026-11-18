<?php
/** @var string $content HTML rendered by BaseController before the layout is included. */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$subPath = $currentPath;
if ($scriptDir !== '/' && strpos($subPath, $scriptDir) === 0) $subPath = substr($subPath, strlen($scriptDir));
$subPath = '/' . ltrim($subPath, '/');
if (!function_exists('isNavActive')) {
    /**
     * @param string $path
     * @param string $subPath
     * @return string
     */
    function isNavActive($path, $subPath) { return rtrim($subPath, '/') === rtrim($path, '/') ? 'active' : ''; }
}
$isSignedIn = isset($_SESSION['userId']);
$headerNotifications = [];
$unreadNotifCount = 0;
if ($isSignedIn) {
    try {
        $notifModel = new \App\Modules\Appointment\Models\AppointmentModel();
        $headerNotifications = $notifModel->getUserNotifications($_SESSION['userId']);
        foreach ($headerNotifications as $n) {
            if (!empty($n['is_unread'])) {
                $unreadNotifCount++;
            }
        }
    } catch (\Throwable $e) {
        $headerNotifications = [];
        $unreadNotifCount = 0;
    }
}
$isAppFitPage = in_array(rtrim($subPath, '/'), [
    '/login',
    '/signup',
    '/appointments',
    '/schedule',
    '/received',
    '/sent',
    '/search',
    '/profile'
], true);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'More Than Blue', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" type="image/png" href="<?= assetUrl('images/logo.png') ?>">
  <link rel="stylesheet" href="<?= assetUrl('bootstrap/css/bootstrap.min.css?v=5.3') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= assetUrl('css/app.css?v=39') ?>">
</head>
<body class="app-body <?= $isAppFitPage ? 'app-fit-screen' : '' ?>">
<header class="app-header">
  <nav class="navbar navbar-expand-lg app-nav" aria-label="Main navigation">
    <div class="container-fluid app-nav-inner">
      <a href="<?= routeUrl($isSignedIn ? '/appointments' : '/') ?>" class="navbar-brand app-brand">
        <img src="<?= assetUrl('images/logo.png') ?>" alt="" class="brand-logo">
        <span>MORE THAN BLUE</span>
      </a>

      <?php if ($isSignedIn): ?>
        <!-- Title Bar Live Search Bar (LinkedIn / Facebook style) -->
        <div class="header-search-wrapper position-relative ms-lg-3 me-lg-auto my-2 my-lg-0" style="width: min(340px, 100%);">
          <div class="input-group input-group-sm header-search-box rounded-pill overflow-hidden bg-light border">
            <span class="input-group-text bg-transparent border-0 text-muted ps-3 pe-1">
              <i class="bi bi-search"></i>
            </span>
            <input type="text" id="headerSearchInput" class="form-control bg-transparent border-0 shadow-none ps-2 py-1" placeholder="Search professionals, skills..." autocomplete="off" aria-label="Search professionals">
            <button type="button" class="btn btn-sm text-muted bg-transparent border-0 pe-3 d-none" id="headerSearchClear" title="Clear search">
              <i class="bi bi-x-circle-fill"></i>
            </button>
          </div>

          <!-- Instant Search Dropdown Menu -->
          <div class="dropdown-menu shadow-lg border-0 p-0 fb-search-dropdown" id="headerSearchDropdown" style="display: none;">
            <div class="fb-search-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-white">
              <span class="fw-bold text-dark small" style="font-size: 0.82rem;" id="headerSearchTitle">Search Professionals</span>
              <span class="badge bg-light text-secondary border rounded-pill" id="headerSearchCount" style="display: none;">0 Found</span>
            </div>
            
            <div class="fb-search-list" id="headerSearchList" style="max-height: 380px; overflow-y: auto;">
              <!-- Dynamic Results or Empty State -->
            </div>
          </div>
        </div>
      <?php endif; ?>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
          <?php if ($isSignedIn): ?>
            <li class="nav-item"><a class="nav-link <?= (isNavActive('/appointments', $subPath) || isNavActive('/schedule', $subPath) || isNavActive('/received', $subPath) || isNavActive('/sent', $subPath)) ? 'active' : '' ?>" href="<?= routeUrl('/appointments') ?>">Appointments</a></li>
            
            <!-- Notification Bell Dropdown (Facebook Style) -->
            <li class="nav-item dropdown mx-lg-1 notif-dropdown-wrapper position-relative">
              <button class="nav-link position-relative border-0 bg-transparent py-2 px-2 notif-bell-btn rounded-circle" id="headerNotifDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" title="Notifications" type="button">
                <i class="bi bi-bell-fill fs-5"></i>
                <span class="position-absolute badge rounded-pill bg-danger header-notif-badge" id="headerNotifBadge" style="top: 3px; right: 2px; font-size: 0.65rem; padding: 0.22em 0.45em; display: <?= $unreadNotifCount > 0 ? 'inline-block' : 'none' ?>;">
                  <?= $unreadNotifCount > 9 ? '9+' : $unreadNotifCount ?>
                  <span class="visually-hidden">unread notifications</span>
                </span>
              </button>

              <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 fb-notif-dropdown notif-dropdown-menu" id="headerNotifMenu" aria-labelledby="headerNotifDropdown">
                <!-- Facebook-style Header -->
                <div class="fb-notif-header">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem; letter-spacing: -0.02em;">Notifications</h5>
                    <div>
                      <button type="button" class="btn btn-sm btn-light rounded-pill px-2 py-1 text-primary d-inline-flex align-items-center gap-1 border-0" id="markAllReadBtn" title="Mark all as read" style="font-size: 0.76rem; font-weight: 600; <?= $unreadNotifCount > 0 ? '' : 'display: none !important;' ?>">
                        <i class="bi bi-check2-all"></i> Mark all read
                      </button>
                    </div>
                  </div>
                  <!-- Facebook Filter Pills -->
                  <div class="fb-filter-pills">
                    <button type="button" class="fb-filter-pill active" data-filter="all">All</button>
                    <button type="button" class="fb-filter-pill" data-filter="unread">
                      Unread <span class="badge rounded-pill bg-primary ms-1" id="unreadFilterBadge" style="font-size: 0.65rem; display: <?= $unreadNotifCount > 0 ? 'inline-block' : 'none' ?>;"><?= $unreadNotifCount ?></span>
                    </button>
                  </div>
                </div>

                <!-- Notification Feed -->
                <div class="fb-notif-list" id="fbNotifList">
                  <?php if (!empty($headerNotifications)): ?>
                    <?php foreach ($headerNotifications as $notif): ?>
                      <?php
                        $actorName = $notif['actor_name'] ?? 'Someone';
                        $avatar = userAvatarUrl($notif['actor_picture'] ?? '', $actorName, 48);
                        $avatarFallback = getInitialsAvatarDataUri($actorName, 48);
                        $isUnread = !empty($notif['is_unread']);
                      ?>
                      <div class="fb-notif-item <?= $isUnread ? 'is-unread' : 'is-read' ?>" 
                           data-notif-id="<?= htmlspecialchars($notif['id'], ENT_QUOTES, 'UTF-8') ?>"
                           data-url="<?= htmlspecialchars($notif['url'], ENT_QUOTES, 'UTF-8') ?>"
                           data-unread="<?= $isUnread ? '1' : '0' ?>"
                           role="button" tabindex="0">
                        
                        <!-- Avatar with Reaction Badge -->
                        <div class="fb-avatar-wrap">
                          <img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" 
                               alt="<?= htmlspecialchars($actorName, ENT_QUOTES, 'UTF-8') ?>" 
                               class="fb-avatar-img shadow-sm"
                               onerror="this.onerror=null; this.src='<?= $avatarFallback ?>';">
                          <span class="fb-avatar-badge bg-<?= $notif['badge_color'] ?>">
                            <i class="bi <?= $notif['icon'] ?>"></i>
                          </span>
                        </div>

                        <!-- Content Text -->
                        <div class="fb-notif-content">
                          <div class="fb-notif-text">
                            <strong><?= htmlspecialchars($actorName, ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if ($notif['type'] === 'received_pending'): ?>
                              requested an appointment: <span class="fw-semibold text-primary">"<?= htmlspecialchars($notif['event_name'], ENT_QUOTES, 'UTF-8') ?>"</span>
                            <?php elseif ($notif['type'] === 'received_approved'): ?>
                              has a scheduled appointment: <span class="fw-semibold text-success">"<?= htmlspecialchars($notif['event_name'], ENT_QUOTES, 'UTF-8') ?>"</span>
                            <?php elseif ($notif['type'] === 'sent_approved'): ?>
                              approved your appointment request: <span class="fw-semibold text-success">"<?= htmlspecialchars($notif['event_name'], ENT_QUOTES, 'UTF-8') ?>"</span>
                            <?php elseif ($notif['type'] === 'sent_declined'): ?>
                              declined your appointment request: <span class="text-secondary">"<?= htmlspecialchars($notif['event_name'], ENT_QUOTES, 'UTF-8') ?>"</span>
                            <?php else: ?>
                              cancelled the appointment: <span class="text-secondary">"<?= htmlspecialchars($notif['event_name'], ENT_QUOTES, 'UTF-8') ?>"</span>
                            <?php endif; ?>
                          </div>

                          <div class="fb-notif-meta">
                            <i class="bi bi-calendar3 me-1 text-primary"></i><?= htmlspecialchars($notif['schedule_text'], ENT_QUOTES, 'UTF-8') ?>
                          </div>

                          <div class="fb-notif-time">
                            <?= htmlspecialchars($notif['time_ago'] ?? 'Recent', ENT_QUOTES, 'UTF-8') ?>
                          </div>

                          <?php if (!empty($notif['can_quick_action'])): ?>
                            <div class="fb-notif-actions">
                              <a href="<?= $notif['url'] ?>" class="btn btn-primary btn-sm">View Request</a>
                            </div>
                          <?php endif; ?>
                        </div>

                        <!-- Unread Blue Dot -->
                        <div class="fb-unread-dot" style="<?= $isUnread ? '' : 'display: none !important;' ?>"></div>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>

                  <!-- Empty State -->
                  <div id="notifEmptyState" class="text-center py-5 px-3 <?= !empty($headerNotifications) ? 'd-none' : '' ?>">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 mb-2 shadow-sm text-muted" style="width: 56px; height: 56px;">
                      <i class="bi bi-bell-slash fs-3"></i>
                    </div>
                    <div class="fw-bold text-dark small" id="notifEmptyTitle">No notifications right now</div>
                    <div class="text-muted" id="notifEmptySubtitle" style="font-size: 0.75rem;">When you get appointments or updates, they'll show up here.</div>
                  </div>
                </div>

                <!-- Facebook-style Footer -->
                <div class="fb-notif-footer">
                  <a href="<?= routeUrl('/received') ?>">View all received appointments &rarr;</a>
                </div>
              </div>
            </li>

            <li class="nav-item"><a class="nav-link <?= isNavActive('/profile', $subPath) ?>" href="<?= routeUrl('/profile') ?>">Profile settings</a></li>
            <li class="nav-item">
              <form method="post" action="<?= routeUrl('/logout') ?>" class="m-0">
                <?= csrfField() ?>
                <button type="submit" class="nav-link border-0 bg-transparent">Logout</button>
              </form>
            </li>
          <?php else: ?>
            <?php if (rtrim($subPath, '/') === '/'): ?>
              <li class="nav-item"><a class="nav-link" href="#how-it-works">Schedule</a></li>
              <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="<?= routeUrl('/login') ?>">Sign in</a></li>
            <li class="nav-item ms-lg-2"><a class="btn btn-primary btn-sm app-nav-primary" href="<?= routeUrl('/signup') ?>">Get started</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
<?php
$flashes = getFlashes();
foreach (['loginerror','signuperror','signupsuccess','profileerror','appointmenterror','actionerror'] as $param) {
  if (isset($_GET[$param])) $flashes[] = ['type' => in_array($param, ['signupsuccess'], true) ? 'success' : 'danger', 'message' => $_GET[$param]];
}
?>
<?php if (!empty($flashes)): ?>
  <?php
    $primaryType = 'info';
    $types = array_column($flashes, 'type');
    if (in_array('danger', $types, true)) {
      $primaryType = 'danger';
    } elseif (in_array('warning', $types, true)) {
      $primaryType = 'warning';
    } elseif (in_array('success', $types, true)) {
      $primaryType = 'success';
    }

    $flashThemes = [
      'success' => [
        'title' => 'Success',
        'badgeBg' => '#ecfdf5',
        'badgeBorder' => '#a7f3d0',
        'btnClass' => 'btn-success',
        'btnText' => 'Continue',
        'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
      ],
      'danger' => [
        'title' => 'Notice',
        'badgeBg' => '#fef2f2',
        'badgeBorder' => '#fecaca',
        'btnClass' => 'btn-danger',
        'btnText' => 'Dismiss',
        'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>'
      ],
      'warning' => [
        'title' => 'Attention',
        'badgeBg' => '#fffbeb',
        'badgeBorder' => '#fde68a',
        'btnClass' => 'btn-warning text-dark',
        'btnText' => 'Understood',
        'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'
      ],
      'info' => [
        'title' => 'Information',
        'badgeBg' => '#eff6ff',
        'badgeBorder' => '#bfdbfe',
        'btnClass' => 'btn-primary',
        'btnText' => 'Got it',
        'icon' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
      ]
    ];
    $theme = $flashThemes[$primaryType] ?? $flashThemes['info'];
  ?>
  <!-- Flash Notification Modal -->
  <div class="modal fade app-flash-modal" id="appFlashModal" tabindex="-1" aria-labelledby="appFlashModalTitle" aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
        <div class="d-flex justify-content-end mb-1">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle border shadow-sm" style="width: 64px; height: 64px; background: <?= $theme['badgeBg'] ?>; border-color: <?= $theme['badgeBorder'] ?> !important;">
          <?= $theme['icon'] ?>
        </div>
        <h5 class="modal-title fw-bold text-dark mb-2" id="appFlashModalTitle"><?= htmlspecialchars($theme['title']) ?></h5>
        <div class="modal-body p-0 mb-4 text-muted" style="font-size: 0.95rem; line-height: 1.55;">
          <?php foreach ($flashes as $flash): ?>
            <p class="mb-1 text-secondary"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></p>
          <?php endforeach; ?>
        </div>
        <div class="d-grid">
          <button type="button" class="btn <?= $theme['btnClass'] ?> py-2 rounded-3 fw-semibold shadow-sm" data-bs-dismiss="modal"><?= $theme['btnText'] ?></button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
<main class="app-main"><?= $content ?></main>
<footer class="app-footer"><div class="container">© <?= date('Y') ?> More Than Blue <span class="footer-dot">·</span> Thoughtful scheduling, made simple.</div></footer>
<script src="<?= assetUrl('bootstrap/js/bootstrap.bundle.min.js?v=5.3.3') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const flashModalEl = document.getElementById('appFlashModal');
  if (flashModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
    const flashModal = new bootstrap.Modal(flashModalEl);
    flashModal.show();
  }
  if (window.history && window.history.replaceState) {
    const url = new URL(window.location.href);
    const flashParams = ['loginerror','signuperror','signupsuccess','profileerror','appointmenterror','actionerror'];
    let changed = false;
    flashParams.forEach(function(p) {
      if (url.searchParams.has(p)) {
        url.searchParams.delete(p);
        changed = true;
      }
    });
    if (changed) {
      window.history.replaceState({}, document.title, url.pathname + (url.search ? url.search : '') + url.hash);
    }
  }
});
</script>
<?php if ($isSignedIn): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const notifBtn = document.getElementById('headerNotifDropdown');
  const notifMenu = document.getElementById('headerNotifMenu');
  const badge = document.getElementById('headerNotifBadge');
  const unreadFilterBadge = document.getElementById('unreadFilterBadge');
  const markReadBtn = document.getElementById('markAllReadBtn');
  const filterBtns = document.querySelectorAll('.fb-filter-pill');
  const notifList = document.getElementById('fbNotifList');
  const emptyState = document.getElementById('notifEmptyState');
  const emptyTitle = document.getElementById('notifEmptyTitle');
  const emptySubtitle = document.getElementById('notifEmptySubtitle');

  let activeFilter = 'all';

  function getUnreadCount() {
    return document.querySelectorAll('.fb-notif-item.is-unread').length;
  }

  function updateUnreadBadges() {
    const unreadCount = getUnreadCount();
    if (badge) {
      if (unreadCount > 0) {
        badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }
    }
    if (unreadFilterBadge) {
      if (unreadCount > 0) {
        unreadFilterBadge.textContent = unreadCount;
        unreadFilterBadge.style.display = 'inline-block';
      } else {
        unreadFilterBadge.style.display = 'none';
      }
    }
    if (markReadBtn) {
      if (unreadCount > 0) {
        markReadBtn.style.setProperty('display', 'inline-flex', 'important');
      } else {
        markReadBtn.style.setProperty('display', 'none', 'important');
      }
    }
    applyFilter(activeFilter);
  }

  function applyFilter(filter) {
    activeFilter = filter;
    filterBtns.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.filter === filter);
    });

    const items = document.querySelectorAll('.fb-notif-item');
    let visibleCount = 0;

    items.forEach(item => {
      const isUnread = item.classList.contains('is-unread');
      if (filter === 'all' || (filter === 'unread' && isUnread)) {
        item.style.display = 'flex';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });

    if (emptyState) {
      if (visibleCount === 0) {
        emptyState.classList.remove('d-none');
        if (filter === 'unread') {
          emptyTitle.textContent = "You're all caught up!";
          emptySubtitle.textContent = "No unread notifications at this time.";
        } else {
          emptyTitle.textContent = "No notifications right now";
          emptySubtitle.textContent = "When you get appointments or updates, they'll show up here.";
        }
      } else {
        emptyState.classList.add('d-none');
      }
    }
  }

  // Filter tabs listener
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      applyFilter(this.dataset.filter);
    });
  });

  // Mark all as read button
  if (markReadBtn) {
    markReadBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      document.querySelectorAll('.fb-notif-item.is-unread').forEach(item => {
        item.classList.remove('is-unread');
        item.classList.add('is-read');
        item.dataset.unread = '0';
        const dot = item.querySelector('.fb-unread-dot');
        if (dot) dot.style.setProperty('display', 'none', 'important');
      });

      updateUnreadBadges();
      fetch('<?= routeUrl('/notifications/mark-all-read') ?>', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>' }
      }).catch(function() {});
    });
  }

  // Individual notification item clicks
  document.querySelectorAll('.fb-notif-item').forEach(item => {
    item.addEventListener('click', function(e) {
      const notifId = this.dataset.notifId;
      const targetUrl = this.dataset.url;
      const isUnread = this.classList.contains('is-unread');

      if (isUnread && notifId) {
        this.classList.remove('is-unread');
        this.classList.add('is-read');
        this.dataset.unread = '0';
        const dot = this.querySelector('.fb-unread-dot');
        if (dot) dot.style.setProperty('display', 'none', 'important');

        // Post async mark-read
        const formData = new FormData();
        formData.append('id', notifId);
        fetch('<?= routeUrl('/notifications/mark-read') ?>', {
          method: 'POST',
          headers: { 'X-CSRF-Token': '<?= htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') ?>' },
          body: formData
        }).catch(function() {});

        updateUnreadBadges();
      }

      if (targetUrl) {
        window.location.href = targetUrl;
      }
    });

    item.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.click();
      }
    });
  });

  // Live Title Bar Search Dropdown Logic
  const searchInput = document.getElementById('headerSearchInput');
  const searchDropdown = document.getElementById('headerSearchDropdown');
  const searchList = document.getElementById('headerSearchList');
  const searchCount = document.getElementById('headerSearchCount');
  const searchTitle = document.getElementById('headerSearchTitle');
  const searchClear = document.getElementById('headerSearchClear');

  let debounceTimer = null;
  let currentAbortCtrl = null;

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function showSearchDropdown() {
    if (searchDropdown) searchDropdown.style.display = 'block';
  }

  function hideSearchDropdown() {
    if (searchDropdown) searchDropdown.style.display = 'none';
  }

  if (searchInput && searchDropdown && searchList) {
    // Check URL for ?focus_search=1 or &q=...
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('focus_search')) {
      const qVal = urlParams.get('q') || '';
      searchInput.value = qVal;
      setTimeout(() => {
        searchInput.focus();
        if (qVal) {
          performSearch(qVal);
        } else {
          showPromptState();
          showSearchDropdown();
        }
      }, 100);
    }

    searchInput.addEventListener('focus', function () {
      const val = this.value.trim();
      if (val.length >= 1) {
        performSearch(val);
      } else {
        showPromptState();
      }
      showSearchDropdown();
    });

    searchInput.addEventListener('input', function () {
      const val = this.value.trim();
      if (searchClear) {
        searchClear.classList.toggle('d-none', val.length === 0);
      }
      clearTimeout(debounceTimer);
      if (val.length === 0) {
        showPromptState();
        showSearchDropdown();
        return;
      }
      debounceTimer = setTimeout(() => {
        performSearch(val);
      }, 200);
    });

    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        hideSearchDropdown();
        this.blur();
      }
    });

    if (searchClear) {
      searchClear.addEventListener('click', function () {
        searchInput.value = '';
        this.classList.add('d-none');
        showPromptState();
        searchInput.focus();
      });
    }

    // Close when clicking outside
    document.addEventListener('click', function (e) {
      if (!searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
        hideSearchDropdown();
      }
    });

    function showPromptState() {
      if (searchCount) searchCount.style.display = 'none';
      if (searchTitle) searchTitle.textContent = 'Search Professionals';
      searchList.innerHTML = `
        <div class="p-4 text-center text-muted small">
          <i class="bi bi-search fs-3 text-secondary d-block mb-2"></i>
          <div>Search colleagues, mentors, and experts</div>
          <div class="text-secondary opacity-75 mt-1" style="font-size: 0.75rem;">Type a name, job role, company, or skill (e.g. Stacy, PHP, Cloud)...</div>
        </div>
      `;
    }

    function performSearch(query) {
      showSearchDropdown();
      if (searchTitle) searchTitle.textContent = 'Searching...';
      searchList.innerHTML = `
        <div class="p-4 text-center text-muted small">
          <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
          <div>Finding matching professionals...</div>
        </div>
      `;

      if (currentAbortCtrl) currentAbortCtrl.abort();
      currentAbortCtrl = new AbortController();

      fetch('<?= routeUrl('/api/search?q=') ?>' + encodeURIComponent(query), { signal: currentAbortCtrl.signal })
        .then(res => res.json())
        .then(data => {
          const results = data.results || [];
          if (searchTitle) searchTitle.textContent = 'Search Results';
          if (searchCount) {
            searchCount.textContent = `${results.length} Found`;
            searchCount.style.display = 'inline-block';
          }

          if (results.length === 0) {
            searchList.innerHTML = `
              <div class="p-4 text-center text-muted small">
                <i class="bi bi-person-x fs-2 text-secondary d-block mb-2"></i>
                <div class="fw-semibold text-dark">No professionals found matching "${escapeHtml(query)}"</div>
                <div class="text-muted mt-1" style="font-size: 0.75rem;">Try searching with a broader keyword, company, or skill tag.</div>
              </div>
            `;
            return;
          }

          let html = '<div class="py-1">';
          results.forEach(u => {
            const skillsHtml = (u.skills && u.skills.length > 0)
              ? u.skills.map(s => `<span class="badge">${escapeHtml(s)}</span>`).join('') + (u.extraSkills > 0 ? `<span class="badge bg-light text-muted border">+${u.extraSkills}</span>` : '')
              : '';
            
            html += `
              <a href="${escapeHtml(u.url)}" class="fb-search-item">
                <img src="${escapeHtml(u.avatarUrl)}" alt="${escapeHtml(u.fullName)}" class="fb-search-avatar" onerror="this.onerror=null; this.src='${escapeHtml(u.avatarFallback)}';">
                <div class="fb-search-content">
                  <div class="fb-search-name">${escapeHtml(u.fullName)} ${u.isSelf ? '<span class="badge bg-secondary-subtle text-secondary small py-0 px-1 ms-1" style="font-size: 0.65rem;">You</span>' : ''}</div>
                  <div class="fb-search-headline text-truncate">${escapeHtml(u.headline)}</div>
                  ${skillsHtml ? `<div class="fb-search-skills d-flex flex-wrap gap-1">${skillsHtml}</div>` : ''}
                </div>
              </a>
            `;
          });
          html += '</div>';
          searchList.innerHTML = html;
        })
        .catch(err => {
          if (err.name === 'AbortError') return;
          searchList.innerHTML = `
            <div class="p-3 text-center text-danger small">
              <i class="bi bi-exclamation-triangle me-1"></i> Search request failed. Please try again.
            </div>
          `;
        });
    }
  }
});
</script>
<?php endif; ?>
</body>
</html>
