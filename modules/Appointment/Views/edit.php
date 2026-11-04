<?php
/**
 * @var string $appointmentId
 * @var array $appointment
 * @var bool $isReceiver
 */
$appointmentId = $appointmentId ?? '';
$isReceiver = !empty($isReceiver);
?>
<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-7 col-lg-5">
      <div class="card rounded-0 shadow-sm border-0 text-center">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">Update Appointment Status</h5>
        </div>
        <div class="card-body p-4">
          <p class="text-secondary small mb-4">Select the action you wish to take on this appointment:</p>
          <div class="d-flex flex-column gap-2">
            <?php if (!empty($isReceiver)): ?>
              <form action="<?= routeUrl('/appointment/status') ?>" method="post">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($appointmentId, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success rounded-0 w-100 py-2">Approve Appointment</button>
              </form>

              <form action="<?= routeUrl('/appointment/status') ?>" method="post">
                <?= csrfField() ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($appointmentId, ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="status" value="denied">
                <button type="submit" class="btn btn-warning rounded-0 w-100 py-2">Deny Appointment</button>
              </form>
            <?php endif; ?>

            <form action="<?= routeUrl('/appointment/status') ?>" method="post">
              <?= csrfField() ?>
              <input type="hidden" name="id" value="<?= htmlspecialchars($appointmentId, ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="status" value="cancel">
              <button type="submit" class="btn btn-outline-danger rounded-0 w-100 py-2">Cancel Appointment</button>
            </form>
          </div>
        </div>
        <div class="card-footer bg-light py-3 border-0">
          <a href="<?= routeUrl('/received') ?>" class="text-decoration-none small text-secondary">&larr; Back to Appointments</a>
        </div>
      </div>
    </div>
  </div>
</div>
