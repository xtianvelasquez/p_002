<?php
session_start();
require("connection.php");

if (isset($_SESSION["userId"])) {
  header("Location: user-panel.php");
  exit;
} else {
  header("Location: user-login.php");
}

if (isset($_POST["login"])) {
  $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
  $password = trim($_POST["password"]);

  $userCredentialsQuery = "SELECT * FROM user_account WHERE email_address = ? AND user_password = ? LIMIT 1";
  $userCredentials = $dbConnect->prepare($userCredentialsQuery);
  if ($userCredentials === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: zone.php?loginerror={$error}");
    exit;
  }

  $userCredentials->bind_param('ss', $emailAddress, $password);
  if (!$userCredentials->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $userCredentials->close();
    header("Location: zone.php?loginerror={$error}");
    exit;
  }

  $userCredentialsResult = $userCredentials->get_result();
  if ($userCredentialsResult->num_rows == 1) {
    $userCredentialsRow = $userCredentialsResult->fetch_assoc();
    $_SESSION["profilePicture"] = $userCredentialsRow["profile_picture"];
    $_SESSION["searchName"] = $userCredentialsRow["search_name"];
    $_SESSION["emailAddress"] = $userCredentialsRow["email_address"];
    $_SESSION["userId"] = $userCredentialsRow["user_id"];
    header("Location: user-panel.php");
  } else {
    $error = htmlspecialchars("The email or password you entered is incorrect. Please try again.", ENT_QUOTES, "UTF-8");
    header("Location: zone.php?loginerror={$error}");
  }

  $userCredentials->close();
}
?>