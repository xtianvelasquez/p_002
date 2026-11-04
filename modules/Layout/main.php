<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$scriptDir = dirname($scriptName);
$subPath = $currentPath;
if ($scriptDir !== '/' && strpos($subPath, $scriptDir) === 0) {
    $subPath = substr($subPath, strlen($scriptDir));
}
$subPath = '/' . ltrim($subPath, '/');

function isNavActive($path, $subPath) {
    return (rtrim($subPath, '/') === rtrim($path, '/')) ? 'active fw-bold' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'More Than Blue', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="icon" type="image/png" href="<?= assetUrl('images/logo.png') ?>">
  <link rel="shortcut icon" type="image/png" href="<?= assetUrl('images/logo.png') ?>">
  <link rel="stylesheet" href="<?= assetUrl('bootstrap/css/bootstrap.min.css?v=5.3') ?>">
  <style>
    body {
      background-color: #F8F9FA;
    }
    .avatar-sm {
      width: 34px;
      height: 34px;
      object-fit: cover;
    }
  </style>
</head>
<body class="d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom py-2 px-3 shadow-sm">
    <div class="container-fluid">
      <?php if (isset($_SESSION["userId"])): ?>
        <a href="<?= routeUrl('/profile') ?>" class="navbar-brand d-flex align-items-center gap-2 fw-semibold text-dark">
          <img src="<?= assetUrl($_SESSION["profilePicture"] ?? 'images/logo.png') ?>" alt="Profile" class="avatar-sm rounded-circle border">
          <span><?= htmlspecialchars($_SESSION["searchName"] ?? 'My Profile', ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
      <?php else: ?>
        <a href="<?= routeUrl('/') ?>" class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark">
          <img src="<?= assetUrl('images/logo.png') ?>" alt="More Than Blue Logo" height="30">
          <span>MORE THAN BLUE</span>
        </a>
      <?php endif; ?>

      <button type="button" class="navbar-toggler" data-bs-target="#mainNavbar" data-bs-toggle="collapse" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center">
          <?php if (isset($_SESSION["userId"])): ?>
            <li class="nav-item">
              <a class="nav-link text-capitalize <?= isNavActive('/received', $subPath) ?>" href="<?= routeUrl('/received') ?>">Received</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-capitalize <?= isNavActive('/sent', $subPath) ?>" href="<?= routeUrl('/sent') ?>">Sent</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-capitalize <?= isNavActive('/search', $subPath) ?>" href="<?= routeUrl('/search') ?>">Search</a>
            </li>
            <li class="nav-item ms-md-2">
              <a class="btn btn-outline-danger btn-sm rounded-0 px-3" href="<?= routeUrl('/logout') ?>">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link text-capitalize <?= isNavActive('/login', $subPath) ?>" href="<?= routeUrl('/login') ?>">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-capitalize <?= isNavActive('/signup', $subPath) ?>" href="<?= routeUrl('/signup') ?>">Sign Up</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Flash Alerts & Content -->
  <main class="flex-grow-1">
    <div class="container-xl">
      <?php 
      $flashes = getFlashes();
      // Handle legacy query params gracefully if present
      if (isset($_GET['loginerror'])) {
          $flashes[] = ['type' => 'danger', 'message' => $_GET['loginerror']];
      }
      if (isset($_GET['signuperror'])) {
          $flashes[] = ['type' => 'danger', 'message' => $_GET['signuperror']];
      }
      if (isset($_GET['signupsuccess'])) {
          $flashes[] = ['type' => 'success', 'message' => $_GET['signupsuccess']];
      }
      if (isset($_GET['profileerror'])) {
          $flashes[] = ['type' => 'danger', 'message' => $_GET['profileerror']];
      }
      if (isset($_GET['appointmenterror'])) {
          $flashes[] = ['type' => 'danger', 'message' => $_GET['appointmenterror']];
      }
      if (isset($_GET['actionerror'])) {
          $flashes[] = ['type' => 'danger', 'message' => $_GET['actionerror']];
      }
      ?>

      <?php if (!empty($flashes)): ?>
        <div class="row justify-content-center mt-3">
          <div class="col-11 col-md-8">
            <?php foreach ($flashes as $flash): ?>
              <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?= $content ?>
  </main>

  <!-- Footer -->
  <footer class="mt-auto py-3 bg-white border-top text-center text-muted">
    <div class="container">
      <small>&copy; <?= date('Y') ?> Christian E. Velasquez. All rights reserved. &bull; More Than Blue Appointment System</small>
    </div>
  </footer>

  <script src="<?= assetUrl('bootstrap/js/bootstrap.min.js?v=5.3') ?>"></script>
</body>
</html>
