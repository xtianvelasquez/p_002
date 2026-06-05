<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
      <?= htmlspecialchars($_SESSION["searchName"], ENT_QUOTES, 'UTF-8'); ?>
    </a>
    <button type="button" class="navbar-toggler" data-bs-target="#ourDashboard" data-bs-toggle="collapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ourDashboard">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?= routeUrl('/received') ?>">received</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= routeUrl('/sent') ?>">sent</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="<?= routeUrl('/search') ?>">search</a>
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
            <p class="card-title h5 text-dark">search</p>
          </div>
          <div class="card-body px-3">
            <form action="<?= routeUrl('/search') ?>" method="post">
              <div class="row">
                <div class="col-12 mb-2">
                  <input type="text" name="name" id="name" class="container-fluid form-control rounded-0" placeholder="Name" required>
                </div>
                <div class="text-center">
                  <input type="submit" name="search" value="Search" class="col-12 btn btn-outline-primary rounded-0">
                </div>
              </div>
            </form>
            <table class="table table-hover mt-5">
              <?php
              if ($searched && $searchResults !== null) {
                if (count($searchResults) > 0) {
                  foreach ($searchResults as $searchUserRow) {
                    $id = htmlspecialchars($searchUserRow["user_id"], ENT_QUOTES, "UTF-8");
                    $picture = htmlspecialchars($searchUserRow["profile_picture"], ENT_QUOTES, "UTF-8");
                    $fullName = htmlspecialchars($searchUserRow["first_name"] . " " . $searchUserRow["last_name"], ENT_QUOTES, "UTF-8");

                    echo "<tr>";
                    echo "<td><a href='" . routeUrl('/appointment/book?account=' . $id) . "' class='text-dark text-decoration-none'><img src='" . assetUrl($picture) . "' class='align-middle rounded-5 me-2' height='30' width='30'>{$fullName}</a></td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr>";
                  echo "<td>No user found.</td>";
                  echo "</tr>";
                }
              }
              ?>
            </table>
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
