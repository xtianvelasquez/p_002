<?php
require("connection.php");

if (isset($_GET["yes"])) {
  $id = $_GET["yes"];

  $deleteAppointmentQuery = "DELETE FROM appointment_details WHERE appointment_id = ?";
  $deleteAppointment = $dbConnect->prepare($deleteAppointmentQuery);
  if ($deleteAppointment === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $deleteAppointment->bind_param("i", $id);
  if (!$deleteAppointment->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $deleteAppointment->close();
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }
  
  $deleteAppointment->close();
  header("Location: user-panel.php");
  exit;
} else {
  header("Location: user-panel.php");
  exit;
}
?>