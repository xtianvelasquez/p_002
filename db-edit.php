<?php
require("connection.php");

if (isset($_GET["approved"])) {
  $id = $_GET["approved"];
  $status = "approved";

  $approvedAppointmentQuery = "UPDATE appointment_details SET appointment_status = ? WHERE appointment_id = ?";
  $approvedAppointment = $dbConnect->prepare($approvedAppointmentQuery);
  if ($approvedAppointment === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $approvedAppointment->bind_param("ss", $status, $id);
  if (!$approvedAppointment->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $approvedAppointment->close();
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $approvedAppointment->close();
  header("Location: user-panel.php");
  exit;
} elseif (isset($_GET["denied"])) {
  $id = $_GET["denied"];
  $status = "denied";

  $deniedAppointmentQuery = "UPDATE appointment_details SET appointment_status = ? WHERE appointment_id = ?";
  $deniedAppointment = $dbConnect->prepare($deniedAppointmentQuery);
  if ($deniedAppointment === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $deniedAppointment->bind_param("ss", $status, $id);
  if (!$deniedAppointment->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $deniedAppointment->close();
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $deniedAppointment->close();
  header("Location: user-panel.php");
  exit;
} elseif (isset($_GET['cancel'])) {
  $id = $_GET['cancel'];
  $status = "cancel";

  $cancelAppointmentQuery = "UPDATE appointment_details SET appointment_status = ? WHERE appointment_id = ?";
  $cancelAppointment = $dbConnect->prepare($cancelAppointmentQuery);
  if ($cancelAppointment === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $cancelAppointment->bind_param("ss", $status, $id);
  if (!$cancelAppointment->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $cancelAppointment->close();
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $cancelAppointment->close();
  header("Location: user-panel.php");
  exit;
} else {
  header("Location: user-panel.php");
  exit;
}
?>