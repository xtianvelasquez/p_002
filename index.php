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
          <a class="nav-link active" href="user-login.php">login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="user-signup.php">signup</a>
        </li>
      </ul>
    </div>
  </nav>

  <section id="about-us">
    <div class="container-xl mt-3 mb-5">
      <div class="row justify-content-center align-items-center">
        <img src="images/logo.png" alt="more than blulendar logo" class="img-fluid d-md-none d-block">
        <p class="text-md-start col-md-6 lead text-center" style="white-space: pre-wrap; text-indent: 10%">Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate necessitatibus deleniti provident molestiae neque ipsa placeat ipsam eos, voluptatum corporis blanditiis ab maxime laudantium vitae esse consequuntur? Itaque, magnam neque perferendis saepe quo odio suscipit aliquam, reiciendis ex nam dolor repudiandae adipisci perspiciatis labore voluptas cumque cum, doloribus incidunt obcaecati veritatis. Dignissimos, sint illo. Beatae repellat dolorum nemo nesciunt quae culpa nam exercitationem. Inventore iste quisquam aperiam repudiandae excepturi commodi magnam incidunt enim eligendi? Nobis veritatis aliquid recusandae maxime nam? Earum excepturi ratione explicabo? Architecto, magni illum voluptatem nemo non numquam itaque iusto, placeat veritatis deleniti facere aliquid ea voluptatibus?</p>
        <div class="col-md-5">
          <img src="images/logo.png" alt="more than blulendar logo" class="img-fluid d-md-block d-none">
        </div>
      </div>
    </div>
  </section>

  <!-- Copyright -->
  <div class="container-fluid bg-secondary py-3 text-center text-white">
    <p>&copy; 2024 Christian E. Velasquez. All rights reserved.</p>
  </div>

  <script src="bootstrap/js/bootstrap.min.js?v=5.3"></script>
</body>

</html>