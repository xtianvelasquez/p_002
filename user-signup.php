<?php
session_start();

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
          <a class="nav-link" href="user-login.php">login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="user-signup.php">signup</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center">

      <div class="col-10 col-md-6">
        <div class="card rounded-0">

          <div class="card-header">signup</div>
          <div class="card-body pt-3">
            <form action="db-signup.php" method="post" enctype="multipart/form-data">
              <div class="row">
                <div class="col-12 mb-3">
                  <label for="firstName" class="mb-1">First Name</label>
                  <input type="text" name="firstName" id="firstName" class="form-control rounded-0" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="middleName" class="mb-1">Middle Name</label>
                  <input type="text" name="middleName" id="middleName" class="form-control rounded-0">
                </div>
                <div class="col-12 mb-3">
                  <label for="lastName" class="mb-1">Last Name</label>
                  <input type="text" name="lastName" id="lastName" class="form-control rounded-0" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="contactNumber" class="mb-1">Contact Number</label>
                  <div class="input-group">
                    <span class="input-group-text rounded-0">+63</span>
                    <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10}" minlength="10" maxlength="10" class="form-control rounded-0" required>
                  </div>
                </div>
                <div class="col-12 mb-3">
                  <label for="emailAddress" class="mb-1">Email</label>
                  <input type="email" name="emailAddress" id="emailAddress" class="form-control rounded-0"" required>
                </div>
                <div class=" col-12 mb-3">
                  <label for="password" class="mb-1">Password</label>
                  <input type="password" name="password" id="password" class="form-control rounded-0" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="profilePicture" class="mb-1">Please attach your profile picture here:</label>
                  <input type="file" name="profilePicture" id="profilePicture" class="form-control rounded-0" required>
                  <p class="form-text">Please ensure that you provide a clearer image. Below 10 MB in size. With only png, jpg or jpeg file extension.</p>
                </div>
                <div class="text-center">
                  <input type="submit" name="signup" value="Signup" class="col-12 btn btn-outline-primary rounded-0">
                </div>
              </div>
            </form>
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