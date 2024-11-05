<?php
require("connection.php");

function getSenderDetails($dbConnect, $senderId)
{
  $senderDetailsQuery = "SELECT first_name, last_name, contact_number, email_address FROM user_account WHERE user_id = ? LIMIT 1";
  $senderDetails = $dbConnect->prepare($senderDetailsQuery);
  $senderDetails->bind_param("s", $senderId);
  $senderDetails->execute();
  return $senderDetails->get_result()->fetch_assoc();
}

if (isset($_GET["id"])) {
  $appointmentId = htmlspecialchars($_GET["id"], ENT_QUOTES, "UTF-8");

  $viewInformationsQuery = "SELECT * FROM appointment_details WHERE appointment_id = ? LIMIT 1";
  $viewInformations = $dbConnect->prepare($viewInformationsQuery);
  if ($viewInformations === false) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $viewInformations->bind_param("s", $appointmentId);
  if (!$viewInformations->execute()) {
    $error = htmlspecialchars("We're currently experiencing some technical difficulties. Please try again later.", ENT_QUOTES, "UTF-8");
    $viewInformations->close();
    header("Location: feedbacks.php?actionerror={$error}");
    exit;
  }

  $viewInformationsResult = $viewInformations->get_result();
  if ($viewInformationsResult->num_rows == 1) {
    $viewInformationsRow = $viewInformationsResult->fetch_assoc();
    $senderId = $viewInformationsRow["sender_id"];
    $senderDetails = getSenderDetails($dbConnect, $senderId);

    $fullName = htmlspecialchars(strtoupper($senderDetails["first_name"] . " " . $senderDetails["last_name"]), ENT_QUOTES, "UTF-8");
    $contactNumber = htmlspecialchars("+63" . $senderDetails["contact_number"], ENT_QUOTES, "UTF-8");
    $emailAddress = htmlspecialchars($senderDetails["email_address"], ENT_QUOTES, "UTF-8");
    $appointmentDate = htmlspecialchars($viewInformationsRow["appointment_date"], ENT_QUOTES, "UTF-8");
    $appointmentTime = htmlspecialchars($viewInformationsRow["appointment_time"], ENT_QUOTES, "UTF-8");
    $eventName = htmlspecialchars($viewInformationsRow["event_name"], ENT_QUOTES, "UTF-8");
    $location = htmlspecialchars($viewInformationsRow["location"], ENT_QUOTES, "UTF-8");
    $appointmentStatus = htmlspecialchars($viewInformationsRow["appointment_status"], ENT_QUOTES, "UTF-8");

    echo "<table class='table'>";
    echo "<tr>";
    echo "<th>Sender Name</th>";
    echo "<td>{$fullName}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Contact Number</th>";
    echo "<td>{$contactNumber}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Email Address</th>";
    echo "<td>{$emailAddress}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Date</th>";
    echo "<td>{$appointmentDate}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Time</th>";
    echo "<td>{$appointmentTime}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Event Name</th>";
    echo "<td>{$eventName}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Location</th>";
    echo "<td>{$location}</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<th>Status</th>";
    echo "<td>{$appointmentStatus}</td>";
    echo "</tr>";
    echo "</table>";
  } else {
    echo "<div class='card p-0 rounded-0'>";
    echo "<div class='card-body text-center'>";
    echo "<p>No further informations found.</p>";
    echo "</div>";
    echo "</div>";
  }

  $viewInformations->close();
  $dbConnect->close();
} else {
  header("Location: user-panel.php");
  exit;
}
?>