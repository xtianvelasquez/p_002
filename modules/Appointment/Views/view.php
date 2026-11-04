<?php
$status = strtolower(trim($appointment_status ?? 'pending'));
$badgeClass = match ($status) {
    'approved' => 'bg-success',
    'denied'   => 'bg-danger',
    'cancel'   => 'bg-secondary',
    default    => 'bg-warning text-dark'
};
?>
<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-7 col-lg-6">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 fw-bold text-dark">Appointment Details</h5>
          <span class="badge rounded-0 <?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="card-body p-4">
          <table class="table table-borderless mb-0">
            <tbody>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0" style="width: 35%;">Participant</th>
                <td class="fw-bold"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Contact Number</th>
                <td><?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Email Address</th>
                <td><?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Event Name</th>
                <td><?= htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Date</th>
                <td><?= htmlspecialchars($appointment_date, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Time</th>
                <td><?= htmlspecialchars($appointment_time, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Location</th>
                <td><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="card-footer bg-light py-3 border-0 d-flex justify-content-between">
          <a href="<?= routeUrl('/received') ?>" class="btn btn-outline-secondary btn-sm rounded-0 px-3">&larr; Back to Received</a>
          <a href="<?= routeUrl('/appointment/edit?id=' . $appointmentId) ?>" class="btn btn-outline-primary btn-sm rounded-0 px-3">Update Status</a>
        </div>
      </div>
    </div>
  </div>
</div>
