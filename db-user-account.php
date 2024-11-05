<?php
require("connection.php");

function generateAppointmentId($receiverId, $senderId, $length = 20)
{
  $characters = "00112233445566778899aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzzAABBCCDDEEFFGGHHIIJJKKLLMMNNOOPPQQRRSSTTUUVVWWXXYYZZ";
  $generateAppointmentId = "";
  for ($i = 0; $i < $length; $i++) {
    $generateAppointmentId .= $characters[random_int(0, strlen($characters) - 1)];
  }

  $generatedId = trim($receiverId . $generateAppointmentId . $senderId);
  $generatedAppointmentId = "";
  for ($i = 0; $i < 60; $i++) {
    $generatedAppointmentId .= $generatedId[random_int(0, strlen($generatedId) - 1)];
  }

  return $generatedAppointmentId;
}

if (isset($_POST["submit"])) {
  $receiverId = $_POST["receiverId"] ?? "";
  $senderId = $_POST["senderId"] ?? "";
  $appointmentDate = $_POST["appointmentDate"] ?? "";
  $appointmentTime = $_POST["appointmentTime"] ?? "";
  $eventName = $_POST["eventName"] ?? "";
  $location = $_POST["location"] ?? "";

  if (empty($receiverId) || empty($senderId) || empty($appointmentDate) || empty($appointmentTime) || empty($eventName) || empty($location)) {
    $error = htmlspecialchars("All fields are required.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?appointmenterror={$error}");
    exit;
  }

  $appointmentId = generateAppointmentId($receiverId, $senderId);
  $appointmentSatus = "pending";

  $addAppointmentQuery = "INSERT INTO appointment_details(appointment_id, receiver_id, sender_id, appointment_date, appointment_time, event_name, location, appointment_status) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
  $addAppointment = $dbConnect->prepare($addAppointmentQuery);
  if ($addAppointment === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?appointmenterror={$error}");
    exit;
  }

  $addAppointment->bind_param("ssssssss", $appointmentId, $receiverId, $senderId, $appointmentDate, $appointmentTime, $eventName, $location, $appointmentSatus);
  if (!$addAppointment->execute()) {
    $addAppointment->close();
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?appointmenterror={$error}");
    exit;
  }

  $addAppointment->close();
  header("Location: user-search.php");
}
?>