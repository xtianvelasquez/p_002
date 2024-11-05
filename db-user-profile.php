<?php
date_default_timezone_set("Asia/Manila");
require("connection.php");
session_start();

if (isset($_SESSION["userId"]) && isset($_POST["update"])) {

  $userId = $_SESSION["userId"];
  $firstName = trim($_POST["firstName"]);
  $middleName = trim($_POST["middleName"]);
  $lastName = trim($_POST["lastName"]);
  $searchName = strtoupper($firstName . " " . $lastName);
  $contactNumber = trim($_POST["contactNumber"]);
  $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
  $currentProfilePicture = $_POST["currentProfilePicture"];
  $dateUpdate = date("Y-m-d");

  if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $error = htmlspecialchars("Invalid email address.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?profileerror={$error}");
    exit;
  }

  $validateUpdateQuery = "SELECT last_update FROM user_account WHERE user_id = ? LIMIT 1";
  $validateUpdate = $dbConnect->prepare($validateUpdateQuery);
  if ($validateUpdate === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?profileerror={$error}");
    exit;
  }

  $validateUpdate->bind_param("s", $userId);
  if (!$validateUpdate->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $validateUpdate->close();
    header("Location: feedbacks.php?profileerror={$error}");
    exit;
  }

  $validateUpdateResult = $validateUpdate->get_result();
  if ($validateUpdateResult->num_rows == 1) {
    $displayUpdateResult = $validateUpdateResult->fetch_assoc();
    $lastDateUpdate = $displayUpdateResult["last_update"];
    if ($lastDateUpdate == $dateUpdate) {
      $error = htmlspecialchars("Updating is done once a day only.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }
  }

  if (isset($_FILES["profilePicture"])) {
    $profilePictureError = $_FILES["profilePicture"]["error"];
    if ($profilePictureError !== UPLOAD_ERR_OK) {
      $error = htmlspecialchars("Error uploading files. Please try again later.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    $allowedExtensions = array("png", "jpg", "jpeg");
    $profilePictureName = basename($_FILES["profilePicture"]["name"]);
    $profilePictureExtension = strtolower(pathinfo($profilePictureName, PATHINFO_EXTENSION));
    if (!in_array($profilePictureExtension, $allowedExtensions)) {
      $error = htmlspecialchars("Invalid file type. Only PNG, JPG, and JPEG files are allowed.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    $profilePictureSize = $_FILES["profilePicture"]["size"];
    if ($profilePictureSize >= 10000000) {
      $error = htmlspecialchars("File size too large. Maximum size allowed is 10MB.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    $targetDirectory = "uploads/";
    $profilePicturePath = $targetDirectory . uniqid() . '.' . $profilePictureExtension;
    if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicturePath)) {
      $error = htmlspecialchars("Failed to move uploaded files to the target directory. Please try again later.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    if (!empty($currentProfilePicture)) {
      unlink($currentProfilePicture);
    }

    $updatePictureQuery = "UPDATE user_account SET first_name = ?, middle_name = ?, last_name = ?, search_name = ?, contact_number = ?, email_address = ?, profile_picture = ?, last_update = ? WHERE user_id = ? LIMIT 1";
    $updatePicture = $dbConnect->prepare($updatePictureQuery);
    if ($updatePicture === false) {
      $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    $updatePicture->bind_param("sssssssss",  $firstName, $middleName, $lastName, $searchName, $contactNumber, $emailAddress, $profilePicturePath, $dateUpdate, $userId);
    if (!$updatePicture->execute()) {
      $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
      $updatePicture->close();
      header("Location: feedbacks.php?profileerror={$error}");
      exit;
    }

    $_SESSION['profilePicture'] = $profilePicturePath;
    $updatePicture->close();
    header("Location: user-profile.php");
  }
}
?>