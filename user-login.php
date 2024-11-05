<?php
if (isset($_SESSION["userId"])) {
  header("Location: user-panel.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
          <a class="nav-link" href="user-signup.php">signup</a>
        </li>
      </ul>
    </div>
  </nav>

  <!-- Login -->
  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center">

      <div class="col-10 col-md-6">
        <div class="card rounded-0">
          <div class="card-header">login</div>
          <div class="card-body pt-3">
            <form action="db-login.php" method="post">
              <div class="row">
                <div class="col-12 mb-3">
                  <label for="emailAddress" class="mb-1">Email</label>
                  <input type="email" name="emailAddress" id="emailAddress" class="container-fluid form-control rounded-0" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="password" class="mb-1">Password</label>
                  <input type="password" name="password" id="password" class="container-fluid form-control rounded-0" required>
                </div>
                <div class="text-center">
                  <input type="submit" name="login" value="Login" class="col-12 btn btn-outline-primary rounded-0">
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="container-fluid bg-secondary py-3 text-center text-white">
    <p>&copy; 2024 Christian E. Velasquez. All rights reserved.</p>
  </div>

  <script src="bootstrap/js/bootstrap.min.js?v=5.3"></script>
</body>

</html>