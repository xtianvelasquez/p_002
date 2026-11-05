<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$subPath = $currentPath;
if ($scriptDir !== '/' && strpos($subPath, $scriptDir) === 0) $subPath = substr($subPath, strlen($scriptDir));
$subPath = '/' . ltrim($subPath, '/');
function isNavActive($path, $subPath) { return rtrim($subPath, '/') === rtrim($path, '/') ? 'active' : ''; }
$isSignedIn = isset($_SESSION['userId']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'More Than Blue', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" type="image/png" href="<?= assetUrl('images/logo.png') ?>">
  <link rel="stylesheet" href="<?= assetUrl('bootstrap/css/bootstrap.min.css?v=5.3') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= assetUrl('css/app.css?v=2') ?>">
</head>
<body class="app-body">
<header class="app-header">
  <nav class="navbar navbar-expand-lg app-nav" aria-label="Main navigation">
    <div class="container-fluid app-nav-inner">
      <a href="<?= routeUrl($isSignedIn ? '/received' : '/') ?>" class="navbar-brand app-brand">
        <img src="<?= assetUrl('images/logo.png') ?>" alt="" class="brand-logo">
        <span>MORE THAN BLUE</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
          <?php if ($isSignedIn): ?>
            <li class="nav-item"><a class="nav-link <?= isNavActive('/received', $subPath) ?>" href="<?= routeUrl('/received') ?>">Received</a></li>
            <li class="nav-item"><a class="nav-link <?= isNavActive('/sent', $subPath) ?>" href="<?= routeUrl('/sent') ?>">Sent</a></li>
            <li class="nav-item"><a class="nav-link <?= isNavActive('/search', $subPath) ?>" href="<?= routeUrl('/search') ?>">Search</a></li>
            <li class="nav-item ms-lg-2"><a class="btn btn-outline-secondary btn-sm app-nav-action" href="<?= routeUrl('/profile') ?>">Profile settings</a></li>
            <li class="nav-item ms-lg-1"><form method="post" action="<?= routeUrl('/logout') ?>" class="m-0"><?= csrfField() ?><button class="btn btn-outline-danger btn-sm app-nav-action" type="submit">Sign out</button></form></li>
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
<?php if ($flashes): ?><div class="container app-flashes"><?php foreach ($flashes as $flash): ?><div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show" role="alert"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endforeach; ?></div><?php endif; ?>
<main class="app-main"><?= $content ?></main>
<footer class="app-footer"><div class="container">© <?= date('Y') ?> More Than Blue <span class="footer-dot">·</span> Thoughtful scheduling, made simple.</div></footer>
<script src="<?= assetUrl('bootstrap/js/bootstrap.min.js?v=5.3') ?>"></script>
</body>
</html>
