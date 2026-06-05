<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= assetUrl('bootstrap/css/bootstrap.min.css?v=5.3') ?>">
  <title>One Calenday</title>
  <style>
    html,
    body {
      background-color: #F8F9FA;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-md py-3 px-3">
    <a href="<?= routeUrl('/profile') ?>" class="navbar-brand fw-bold">
      <img src="<?= assetUrl($_SESSION["profilePicture"]) ?>" alt="your profile picture" class="align-middle rounded-5" height="40" width="40">
      <?= htmlspecialchars($_SESSION["searchName"], ENT_QUOTES, "UTF-8"); ?>
    </a>
    <button type="button" class="navbar-toggler" data-bs-target="#ourDashboard" data-bs-toggle="collapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ourDashboard">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="<?= routeUrl('/received') ?>">received</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= routeUrl('/sent') ?>">sent</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= routeUrl('/search') ?>">search</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= routeUrl('/logout') ?>">logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center">
      <div class="col-10 col-md-6">
        <div class="card rounded-0">
          <div class="card-header">
            <p class="card-title h5 text-dark">appointment</p>
          </div>
          <div class="card-body px-3">
            <table class='table'>
              <tr>
                <th>Sender Name</th>
                <td><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Contact Number</th>
                <td><?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Email Address</th>
                <td><?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Date</th>
                <td><?= htmlspecialchars($appointment_date, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Time</th>
                <td><?= htmlspecialchars($appointment_time, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Event Name</th>
                <td><?= htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Location</th>
                <td><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th>Status</th>
                <td><?= htmlspecialchars($appointment_status, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            </table>
            <div class="text-center row g-1 mt-3">
              <a href="<?= routeUrl('/received') ?>" class="btn btn-secondary rounded-0">back</a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Copyright -->
  <div class="container-fluid bg-secondary py-3 text-center text-white">
    <p>&copy; 2024 Christian E. Velasquez. All rights reserved.</p>
  </div>

  <script src="<?= assetUrl('bootstrap/js/bootstrap.min.js?v=5.3') ?>"></script>
</body>

</html>
