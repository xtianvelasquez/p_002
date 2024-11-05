<?php
require("connection.php");

if (isset($_SESSION["userId"])) {
  $receiverId = $_SESSION["userId"];

  function getSenderName($dbConnect, $senderId)
  {
    $senderNameQuery = "SELECT first_name, last_name FROM user_account WHERE user_id = ? LIMIT 1";
    $senderName = $dbConnect->prepare($senderNameQuery);
    $senderName->bind_param("s", $senderId);
    $senderName->execute();
    return $senderName->get_result()->fetch_assoc();
  }

  function renderFirstView($fullName, $appointmentDate, $appointmentTime, $eventName, $location, $appointmentStatus, $appointmentId)
  {
    return
      "<tr>
      <td>{$fullName}</td>
      <td>{$appointmentDate}</td>
      <td>{$appointmentTime}</td>
      <td>{$eventName}</td>
      <td>{$location}</td>
      <td>{$appointmentStatus}</td>
      <td>
      <a href='view.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>View</a>
      <a href='edit.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
      <a href='delete.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
      </td>
      </tr>";
  }

  function renderSecondView($appointmentDate, $appointmentTime, $appointmentStatus, $appointmentId)
  {
    return
      "<tr>
      <td>{$appointmentDate}</td>
      <td>{$appointmentTime}</td>
      <td>{$appointmentStatus}</td>
      <td>
      <a href='view.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>View</a>
      <a href='edit.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
      <a href='delete.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
      </td>
      </tr>";
  }

  function renderThirdView($appointmentDate, $appointmentTime, $appointmentId)
  {
    return
      "<tr>
      <td>{$appointmentDate}</td>
      <td>{$appointmentTime}</td>
      <td>
      <a href='view.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>View</a>
      <a href='edit.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Edit</a>
      <a href='delete.php?id={$appointmentId}' class='btn btn-secondary btn-sm rounded-0'>Delete</a>
      </td>
      </tr>";
  }

  $appointmentDetailsQuery = "SELECT * FROM appointment_details WHERE receiver_id = ? ORDER BY appointment_date ASC";
  $appointmentDetails = $dbConnect->prepare($appointmentDetailsQuery);
  $appointmentDetails->bind_param('s', $receiverId);
  $appointmentDetails->execute();
  $appointmentDetailsResult = $appointmentDetails->get_result();

  if ($appointmentDetailsResult->num_rows > 0) {
    while ($appointmentDetailsrow = $appointmentDetailsResult->fetch_assoc()) {
      $senderName = getSenderName($dbConnect, $appointmentDetailsrow["sender_id"]);
      $fullName = htmlspecialchars(strtoupper($senderName["first_name"] . " " . $senderName["last_name"]), ENT_QUOTES, "UTF-8");
      $appointmentDate = htmlspecialchars($appointmentDetailsrow["appointment_date"], ENT_QUOTES, "UTF-8");
      $appointmentTime = htmlspecialchars($appointmentDetailsrow["appointment_time"], ENT_QUOTES, "UTF-8");
      $eventName = htmlspecialchars($appointmentDetailsrow["event_name"], ENT_QUOTES, "UTF-8");
      $location = htmlspecialchars($appointmentDetailsrow["location"], ENT_QUOTES, "UTF-8");
      $appointmentStatus = htmlspecialchars($appointmentDetailsrow["appointment_status"], ENT_QUOTES, "UTF-8");
      $appointmentId = htmlspecialchars($appointmentDetailsrow["appointment_id"], ENT_QUOTES, "UTF-8");

      $firstView[] = renderFirstView($fullName, $appointmentDate, $appointmentTime, $eventName, $location, $appointmentStatus, $appointmentId);
      $secondView[] = renderSecondView($appointmentDate, $appointmentTime, $appointmentStatus, $appointmentId);
      $thirdView[] = renderThirdView($appointmentDate, $appointmentTime, $appointmentId);
    }

    $first = implode('', $firstView);
    $second = implode('', $secondView);
    $third = implode('', $thirdView);

    // first view
    echo "<div class='card p-0 rounded-0 d-none d-lg-block'>
          <div class='card-header'>
          <p class='card-title h5 text-dark'>Your Appointments</p>
          </div>
          <div class='card-body table-responsive px-3'>
          <table class='table table-hover'>
          <thead>
          <tr class='text-start'>
          <th>Sender Name</th>
          <th>Date</th>
          <th>Time</th>
          <th>Event Name</th>
          <th>Location</th>
          <th>Status</th>
          <th>Action</th>
          </tr>
          </thead>
          <tbody>
          {$first}
          </tbody>
          </table>
          </div>
          </div>";

    // second view
    echo "<div class='card p-0 rounded-0 d-none d-md-block d-lg-none'>
          <div class='card-header bg-none'>
          <p class='card-title h5 text-dark'>Your Appointments</p>
          </div>
          <div class='card-body table-responsive'>
          <table class='table table-hover'>
          <thead>
          <tr>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          {$second}
          </tbody>
          </table>
          </div>
          </div>";

    // third view
    echo "<div class='card p-0 rounded-0 d-block d-md-none'>
          <div class='card-header bg-none'>
          <p class='card-title h5 text-dark'>Your Appointments</p>
          </div>
          <div class='card-body table-responsive'>
          <table class='table table-hover'>
          <tbody>
          {$third}
          </tbody>
          </table>
          </div>
          </div>";
  } else {
    echo "<div class='card p-0 rounded-0'>
          <div class='card-body text-center'>
          <p>No appointments yet.</p>
          </div>
          </div>";
  }

  $appointmentDetails->close();
  $dbConnect->close();
} else {
  header("Location: user-login.php");
  exit;
}
?>