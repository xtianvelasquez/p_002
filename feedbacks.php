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
    <a href="user-profile.php" class="navbar-brand fw-bold">
      <img src="<?php echo htmlspecialchars($_SESSION["profilePicture"], ENT_QUOTES, "UTF-8"); ?>" alt="your profile picture" class="align-middle rounded-5" height="40" width="40">
      <?php echo htmlspecialchars($_SESSION["searchName"], ENT_QUOTES, "UTF-8"); ?>
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
      <div class="col-10 col-md-8">
        <div class="card rounded-0">
          <div class="card-body text-center">
            <?php
            if (isset($_GET["profileerror"])) {
              echo "<p>" . htmlspecialchars($_GET["profileerror"], ENT_QUOTES, "UTF-8") . "</p>";
              echo "<a href='user-profile.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            } elseif (isset($_GET["actionerror"])) {
              echo "<p>" . htmlspecialchars($_GET["actionerror"], ENT_QUOTES, 'UTF-8') . "</p>";
              echo "<a href='user-panel.php' class='btn btn-secondary btn-sm rounded-0 px-3'>received</a>";
              echo "<a href='user-sent.php' class='btn btn-secondary btn-sm rounded-0 px-3'>sent</a>";
            } elseif (isset($_GET["accounterror"])) {
              echo "<p>" . htmlspecialchars($_GET["accounterror"], ENT_QUOTES, "UTF-8") . "</p>";
              echo "<a href='user-search.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            } elseif (isset($_GET["appointmenterror"])) {
              echo "<p>" . htmlspecialchars($_GET["appointmenterror"], ENT_QUOTES, "UTF-8") . "</p>";
              echo "<a href='user-search.php' class='btn btn-secondary btn-sm rounded-0 px-3'>ok</a>";
            } else {
              header("Location: user-panel.php");
              exit;
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