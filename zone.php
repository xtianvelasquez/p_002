<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css?v=5.3">
  <title>One Calenday</title>
  <style>
    html,
    body {
      background-color: F8F9FA;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-md py-3 px-3">
    <a href="index.php" class="navbar-brand fw-bold">
      <img src="images/logo.png" alt="one calenday logo" class="align-middle" height="30" width="60">
      ONE CALENDAY
    </a>
    <button type="button" class="navbar-toggler" data-bs-target="#ourDashboard" data-bs-toggle="collapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ourDashboard">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" href="user-login.php">login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="user-signup.php">signup</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center">
      <div class="col-10 col-md-8">
        <div class="card rounded-0">
          <div class="card-body text-center">
            <?php
            if (isset($_GET['loginerror'])) {
              echo "<p>" . htmlspecialchars($_GET['loginerror'], ENT_QUOTES, 'UTF-8') . "</p>";
              echo "<a href='user-login.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            }

            if (isset($_GET['signuperror'])) {
              echo "<p>" . htmlspecialchars($_GET['signuperror'], ENT_QUOTES, 'UTF-8') . "</p>";
              echo "<a href='user-signup.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            }

            if (isset($_GET['signupsuccess'])) {
              echo "<p>" . htmlspecialchars($_GET['signupsuccess'], ENT_QUOTES, 'UTF-8') . "</p>";
              echo "<a href='user-login.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Copyright -->
  <div class="container-fluid bg-secondary py-3 text-center text-white">
    <p>&copy; 2024 Christian E. Velasquez. All rights reserved.</p>
  </div>

  <script src="bootstrap/js/bootstrap.min.js?v=5.3"></script>
</body>

</html>