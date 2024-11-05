<?php
require("connection.php");

function generateUserId($length = 20)
{
  $characters = '00112233445566778899aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzzAABBCCDDEEFFGGHHIIJJKKLLMMNNOOPPQQRRSSTTUUVVWWXXYYZZ';
  $generatedUserId = '';
  for ($i = 0; $i < $length; $i++) {
    $generatedUserId .= $characters[random_int(0, strlen($characters) - 1)];
  }
  return $generatedUserId;
}


if (isset($_POST["signup"])) {
  $userId = generateUserId();
  $firstName = trim($_POST["firstName"]);
  $middleName = trim($_POST["middleName"]);
  $lastName = trim($_POST["lastName"]);
  $searchName = strtoupper($firstName . " " . $lastName);
  $contactNumber = trim($_POST["contactNumber"]);
  $emailAddress = filter_var(trim($_POST["emailAddress"]), FILTER_SANITIZE_EMAIL);
  $password = trim($_POST["password"]);

  if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    $error = htmlspecialchars("Invalid email address.", ENT_QUOTES, "UTF-8");
    header("Location: zone.php?signuperror={$error}");
    exit;
  }

  if (isset($_FILES["profilePicture"])) {
    $profilePictureError = $_FILES["profilePicture"]["error"];
    if ($profilePictureError !== UPLOAD_ERR_OK) {
      $error = htmlspecialchars("Error uploading files. Please try again later.", ENT_QUOTES, "UTF-8");
      header("Location: zone.php?signuperror={$error}");
      exit;
    }

    $allowedExtensions = array("png", "jpg", "jpeg");
    $profilePictureName = basename($_FILES["profilePicture"]["name"]);
    $profilePictureExtension = strtolower(pathinfo($profilePictureName, PATHINFO_EXTENSION));
    if (!in_array($profilePictureExtension, $allowedExtensions)) {
      $error = htmlspecialchars("Invalid file type. Only PNG, JPG, and JPEG files are allowed.", ENT_QUOTES, "UTF-8");
      header("Location: zone.php?signuperror={$error}");
      exit;
    }

    $profilePictureSize = $_FILES["profilePicture"]["size"];
    if ($profilePictureSize >= 10000000) {
      $error = htmlspecialchars("File size too large. Maximum size allowed is 10MB.", ENT_QUOTES, "UTF-8");
      header("Location: zone.php?signuperror={$error}");
      exit;
    }

    $userCheckQuery = "SELECT email_address FROM user_account WHERE contact_number = ? OR email_address = ?";
    $userCheck = $dbConnect->prepare($userCheckQuery);
    if ($userCheck === false) {
      $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
      header("Location: zone.php?signuperror={$error}");
      exit;
    }

    $userCheck->bind_param('ss',  $contactNumber, $emailAddress);
    if (!$userCheck->execute()) {
      $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
      $userCheck->close();
      header("Location: zone.php?signuperror={$error}");
      exit;
    }

    $userCheckResult = $userCheck->get_result();
    if ($userCheckResult->num_rows == 0) {
      $targetDirectory = 'uploads/';
      if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0755, true);
      }

      $profilePicturePath = $targetDirectory . uniqid() . '.' . $profilePictureExtension;
      if (!move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $profilePicturePath)) {
        $error = htmlspecialchars("Error moving uploaded files. Please try again later.", ENT_QUOTES, "UTF-8");
        $userCheck->close();
        header("Location: zone.php?signuperror={$error}");
        exit;
      }

      $newUserQuery = "INSERT INTO user_account (user_id, first_name, middle_name, last_name, search_name, contact_number, email_address, user_password, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $newUser = $dbConnect->prepare($newUserQuery);
      if ($newUser === false) {
        $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");;
        $userCheck->close();
        header("Location: zone.php?signuperror={$error}");
        exit;
      }

      $newUser->bind_param("sssssssss", $userId, $firstName, $middleName, $lastName, $searchName, $contactNumber, $emailAddress, $password, $profilePicturePath);
      if (!$newUser->execute()) {
        $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
        $newUser->close();
        $userCheck->close();
        header("Location: zone.php?signuperror={$error}");
        exit;
      }

      $success = htmlspecialchars("Signup successful!", ENT_QUOTES, "UTF-8");
      $newUser->close();
      header("Location: zone.php?signupsuccess={$success}");
    } else {
      $error = htmlspecialchars("Email or contact number already exists.", ENT_QUOTES, "UTF-8");
      header("Location: zone.php?signuperror={$error}");
    }

    $userCheck->close();
  }
}
?>