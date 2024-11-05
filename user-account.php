<?php
session_start();

if (!isset($_SESSION["userId"])) {
  header("Location: user-login.php");
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
  <title>One Calendar</title>
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
          <a class="nav-link active" href="user-search.php">search</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">logout</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-xl mt-3 mb-5">
    <div class="row justify-content-center align-items-top">
      <?php
      require("connection.php");

      if (isset($_SESSION["userId"]) && isset($_GET["account"])) {
        $userId = $_SESSION["userId"];
        $id = htmlspecialchars($_GET["account"], ENT_QUOTES, "UTF-8");

        $accountDetailsQuery = "SELECT first_name, middle_name, last_name, contact_number, email_address, profile_picture FROM user_account WHERE user_id = ? LIMIT 1";
        $accountDetails = $dbConnect->prepare($accountDetailsQuery);
        if ($accountDetails === false) {
          $error = htmlspecialchars("Server problem. Please try again later.", ENT_QUOTES, "UTF-8");
          header("Location: feedbacks.php?accounterror={$error}");
          exit;
        }

        $accountDetails->bind_param("s", $id);
        if (!$accountDetails->execute()) {
          $error = htmlspecialchars("Server problem. Please try again later.", ENT_QUOTES, "UTF-8");
          $accountDetails->close();
          header("Location: feedbacks.php?accounterror={$error}");
          exit;
        }

        $accountDetailsResult = $accountDetails->get_result();
        if ($accountDetailsResult->num_rows > 0) {
          $accountDetailsRow = $accountDetailsResult->fetch_assoc();
          $firstName = htmlspecialchars($accountDetailsRow["first_name"], ENT_QUOTES, "UTF-8");
          $middleName = htmlspecialchars($accountDetailsRow["middle_name"], ENT_QUOTES, "UTF-8");
          $lastName = htmlspecialchars($accountDetailsRow["last_name"], ENT_QUOTES, "UTF-8");
          $contactNumber = htmlspecialchars($accountDetailsRow["contact_number"], ENT_QUOTES, "UTF-8");
          $emailAddress = htmlspecialchars($accountDetailsRow["email_address"], ENT_QUOTES, "UTF-8");
          $profilePicture = htmlspecialchars($accountDetailsRow["profile_picture"], ENT_QUOTES, "UTF-8");
        } else {
          $error = htmlspecialchars("Account not found.", ENT_QUOTES, "UTF-8");
          header("Location: feedbacks.php?accounterror={$error}");
          exit;
        }

        $accountDetails->close();
      ?>

        <div class="col-10 col-sm-3">
          <img src="<?= $profilePicture; ?>" alt="Profile Picture" class="img-fluid mb-1">
          <p class="mb-1">First Name: <?= $firstName; ?></p>
          <p class="mb-1">Middle Name: <?= $middleName; ?></p>
          <p class="mb-1">Last Name: <?= $lastName; ?></p>
          <p class="mb-1">Contact Number: <?= $contactNumber; ?></p>
          <p class="mb-1">Email Address: <?= $emailAddress; ?></p>
        </div>

        <div class="col-10 col-sm-6">
          <div class="card p-0 rounded-0">
            <div class="card-header">Make an Appointment</div>
            <div class="card-body pt-3">
              <form action="db-user-account.php" method="post">
                <input type="hidden" name="receiverId" id="receiverId" value="<?= $id; ?>">
                <input type="hidden" name="senderId" id="senderId" value="<?= $userId; ?>">

                <div class="row">
                  <div class="col-12 col-sm-6 mb-3">
                    <label for="appointmentDate" class="mb-1">Date</label>
                    <input type="date" name="appointmentDate" id="appointmentDate" class="form-control rounded-0" required>
                  </div>

                  <div class="col-12 col-sm-6 mb-3">
                    <label for="appointmentTime" class="mb-1">Time</label>
                    <input type="time" name="appointmentTime" id="appointmentTime" class="form-control rounded-0" required>
                  </div>

                  <div class="col-12 col-sm-6 mb-3">
                    <label for="eventName" class="mb-1">Event Name</label>
                    <input type="text" name="eventName" id="eventName" class="form-control rounded-0" required>
                  </div>

                  <div class="col-12 col-sm-6 mb-3">
                    <label for="location" class="mb-1">Location</label>
                    <input type="text" name="location" id="location" class="form-control rounded-0" required>
                  </div>

                  <div class="text-center">
                    <input type="submit" name="submit" value="Submit" class="col-12 btn btn-outline-primary rounded-0">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php
      } else {
        $error = htmlspecialchars("Missing account ID.", ENT_QUOTES, "UTF-8");
        header("Location: feedbacks.php?accounterror={$error}");
        exit;
      }
      ?>
    </div>
  </div>

  <!-- Copyright -->
  <footer class="container-fluid bg-secondary py-3 text-center text-white">
    <p>&copy; 2024 Christian E. Velasquez. All rights reserved.</p>
  </footer>

  <script src="bootstrap/js/bootstrap.min.js?v=5.3"></script>
</body>

</html>