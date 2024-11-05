<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css?v=5.3">
  <title>Blulendar</title>
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
    <a href="user-profile.php" class="navbar-brand fw-bold">
      <img src="<?= htmlspecialchars($_SESSION["profilePicture"], ENT_QUOTES, "UTF-8"); ?>" alt="your profile picture" class="align-middle rounded-5" height="40" width="40">
      <?= htmlspecialchars($_SESSION["searchName"], ENT_QUOTES, "UTF-8"); ?>
    </a>
    <button type="button" class="navbar-toggler" data-bs-target="#ourDashboard" data-bs-toggle="collapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="ourDashboard">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="user-panel.php">received</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="user-sent.php">sent</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="user-search.php">search</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center">
      <div class="col-10 col-md-6">
        <div class="card rounded-0">
          <div class="card-img-top">
            <img src="<?= htmlspecialchars($_SESSION["profilePicture"], ENT_QUOTES, "UTF-8"); ?>" alt="your profile picture" class="img-fluid">
          </div>
          <div class="card-body">
            <?php
            require("connection.php");

            if (isset($_SESSION["userId"])) {
              $userId = $_SESSION["userId"];

              $accountDetailsQuery = "SELECT first_name, middle_name, last_name, contact_number, email_address, profile_picture FROM user_account WHERE user_id = ? LIMIT 1";
              $accountDetails = $dbConnect->prepare($accountDetailsQuery);
              $accountDetails->bind_param("s", $userId);
              $accountDetails->execute();
              $accountDetailsResult = $accountDetails->get_result();
              if ($accountDetailsResult->num_rows > 0) {
                $accountDetailsRow = $accountDetailsResult->fetch_assoc();
                $firstName = htmlspecialchars($accountDetailsRow["first_name"], ENT_QUOTES, "UTF-8");
                $middleName = htmlspecialchars($accountDetailsRow["middle_name"], ENT_QUOTES, "UTF-8");
                $lastName = htmlspecialchars($accountDetailsRow["last_name"], ENT_QUOTES, "UTF-8");
                $contactNumber = htmlspecialchars($accountDetailsRow["contact_number"], ENT_QUOTES, "UTF-8");
                $emailAddress = htmlspecialchars($accountDetailsRow["email_address"], ENT_QUOTES, "UTF-8");
                $currentProfilePicture = htmlspecialchars($accountDetailsRow["profile_picture"], ENT_QUOTES, "UTF-8");
              }
            ?>
              <form action="db-user-profile.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="currentProfilePicture" id="currentProfilePicture" value="<?= $currentProfilePicture; ?>">
                <div class="row">
                  <div class="col-12 mb-3">
                    <label for="firstName" class="mb-1">First Name</label>
                    <input type="text" name="firstName" id="firstName" value="<?= $firstName; ?>" class="form-control rounded-0" required>
                  </div>
                  <div class="col-12 mb-3">
                    <label for="middleName" class="mb-1">Middle Name</label>
                    <input type="text" name="middleName" id="middleName" value="<?= $middleName; ?>" class="form-control rounded-0">
                  </div>
                  <div class="col-12 mb-3">
                    <label for="lastName" class="mb-1">Last Name</label>
                    <input type="text" name="lastName" id="lastName" value="<?= $lastName; ?>" class="form-control rounded-0" required>
                  </div>
                  <div class="col-12 mb-3">
                    <label for="contactNumber" class="mb-1">Contact Number</label>
                    <div class="input-group">
                      <span class="input-group-text rounded-0">+63</span>
                      <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10}" minlength="10" maxlength="10" value="<?= $contactNumber; ?>" class="form-control rounded-0" required>
                    </div>
                  </div>
                  <div class="col-12 mb-3">
                    <label for="emailAddress" class="mb-1">Email</label>
                    <input type="email" name="emailAddress" id="emailAddress" value="<?= $emailAddress; ?>" class="form-control rounded-0"" required>
                </div>
                <div class=" col-12 mb-3">
                    <label for="profilePicture" class="mb-1">Please attach your profile picture here:</label>
                    <input type="file" name="profilePicture" id="profilePicture" class="form-control rounded-0" required>
                    <p class="form-text">Please ensure that you provide a clearer image. Below 10 MB in size. With only png, jpg or jpeg file extension.</p>
                  </div>
                  <div class="text-center">
                    <input type="submit" name="update" value="Update" class="col-12 btn btn-outline-primary px-4 rounded-0">
                  </div>
                </div>
              </form>
            <?php
              $accountDetails->close();
            } else {
              header("Location: user-login.php");
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