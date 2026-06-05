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
      <div class="col-11">
        <?php if (count($appointments) > 0): ?>
          
          <!-- First View (large screens) -->
          <div class='card p-0 rounded-0 d-none d-lg-block'>
            <div class='card-header'>
              <p class='card-title h5 text-dark'>Your Appointments</p>
            </div>
            <div class='card-body table-responsive px-3'>
              <table class='table table-hover'>
                <thead>
                  <tr class='text-start'>
                    <th>Sender Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Event Name</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appt): ?>
                    <tr>
                      <td><?= htmlspecialchars($appt['fullName'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['event_name'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['location'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_status'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td>
                        <a href='<?= routeUrl("/appointment/view?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>View</a>
                        <a href='<?= routeUrl("/appointment/edit?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
                        <a href='<?= routeUrl("/appointment/delete?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Second View (medium screens) -->
          <div class='card p-0 rounded-0 d-none d-md-block d-lg-none'>
            <div class='card-header bg-none'>
              <p class='card-title h5 text-dark'>Your Appointments</p>
            </div>
            <div class='card-body table-responsive'>
              <table class='table table-hover'>
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appt): ?>
                    <tr>
                      <td><?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_status'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td>
                        <a href='<?= routeUrl("/appointment/view?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>View</a>
                        <a href='<?= routeUrl("/appointment/edit?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
                        <a href='<?= routeUrl("/appointment/delete?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Third View (small screens) -->
          <div class='card p-0 rounded-0 d-block d-md-none'>
            <div class='card-header bg-none'>
              <p class='card-title h5 text-dark'>Your Appointments</p>
            </div>
            <div class='card-body table-responsive'>
              <table class='table table-hover'>
                <tbody>
                  <?php foreach ($appointments as $appt): ?>
                    <tr>
                      <td><?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td>
                        <a href='<?= routeUrl("/appointment/view?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>View</a>
                        <a href='<?= routeUrl("/appointment/edit?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
                        <a href='<?= routeUrl("/appointment/delete?id=" . $appt["appointment_id"]) ?>' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

        <?php else: ?>
          <div class='card p-0 rounded-0'>
            <div class='card-body text-center'>
              <p>No appointments yet.</p>
            </div>
          </div>
        <?php endif; ?>
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
