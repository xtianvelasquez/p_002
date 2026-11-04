<?php
$status = strtolower(trim($appointment_status ?? 'pending'));
$badgeClass = match ($status) {
    'approved' => 'bg-success',
    'denied'   => 'bg-danger',
    'cancel'   => 'bg-secondary',
    default    => 'bg-warning text-dark'
};

$eventType = $event_type ?? 'consultation';
$eventTypeLabel = match ($eventType) {
    'quick_sync' => '⚡ 15-min Quick Sync',
    'deep_dive' => '🚀 60-min Deep Dive',
    default => '💼 30-min Consultation'
};

$meetingMode = $meeting_mode ?? 'google_meet';
$meetingModeLabel = match ($meetingMode) {
    'zoom' => '💻 Zoom Meeting',
    'in_person' => '🏢 In-Person Office Meeting',
    'phone' => '📞 Phone Call',
    default => '🎥 Google Meet'
};
?>
<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-8 col-lg-7">
      <div class="card rounded-3 shadow border-0 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
          <div>
            <span class="badge bg-white text-primary text-uppercase me-2" style="letter-spacing: 0.05em; font-size: 0.72rem;">More Than Blue</span>
            <span class="fw-bold fs-6">Appointment Overview</span>
          </div>
          <span class="badge rounded-pill <?= $badgeClass ?> px-3 py-2 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.04em;">
            <?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>

        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light mb-4 border">
            <div>
              <div class="text-secondary small text-uppercase fw-semibold" style="letter-spacing: 0.04em;">Session Format</div>
              <div class="fw-bold fs-5 text-dark"><?= htmlspecialchars($eventTypeLabel, ENT_QUOTES, 'UTF-8') ?></div>
              <div class="small text-muted"><i class="bi bi-broadcast me-1"></i><?= htmlspecialchars($meetingModeLabel, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <div class="text-end">
              <div class="fs-4 fw-bold text-primary"><?= htmlspecialchars($appointment_time, ENT_QUOTES, 'UTF-8') ?></div>
              <div class="small text-secondary"><?= htmlspecialchars($appointment_date, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <!-- Calendar Integration Actions -->
          <div class="mb-4 p-3 bg-primary-subtle rounded-3 border border-primary-subtle">
            <div class="fw-bold text-primary mb-2 small text-uppercase" style="letter-spacing: 0.04em;">
              <i class="bi bi-calendar-event me-1"></i> Add to your Calendar
            </div>
            <div class="d-flex flex-wrap gap-2">
              <a href="<?= htmlspecialchars($googleCalUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-2 d-inline-flex align-items-center">
                <i class="bi bi-google me-1"></i> Google Calendar
              </a>
              <a href="<?= routeUrl('/appointment/export-ics?id=' . urlencode($appointmentId)) ?>" class="btn btn-primary btn-sm rounded-2 d-inline-flex align-items-center">
                <i class="bi bi-calendar-check me-1"></i> Download .ICS (Outlook / Apple)
              </a>
            </div>
          </div>

          <table class="table table-borderless align-middle mb-0">
            <tbody>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0" style="width: 32%;">Participant</th>
                <td class="fw-bold text-dark"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Email</th>
                <td><a href="mailto:<?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none"><?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></a></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Contact</th>
                <td><?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Event Title</th>
                <td class="fw-semibold"><?= htmlspecialchars($event_name, ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0">Location / Link</th>
                <td><span class="badge bg-secondary-subtle text-secondary border px-2 py-1"><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></span></td>
              </tr>
              <?php if (!empty($agenda)): ?>
              <tr>
                <th scope="row" class="text-secondary small fw-semibold ps-0 align-top pt-3">Agenda & Notes</th>
                <td class="pt-3">
                  <div class="p-3 bg-light rounded-2 border small" style="white-space: pre-wrap;"><?= htmlspecialchars($agenda, ENT_QUOTES, 'UTF-8') ?></div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="card-footer bg-light py-3 px-4 border-top d-flex justify-content-between align-items-center">
          <a href="<?= routeUrl('/received') ?>" class="btn btn-outline-secondary btn-sm rounded-2 px-3">&larr; Back to Dashboard</a>
          <a href="<?= routeUrl('/appointment/edit?id=' . $appointmentId) ?>" class="btn btn-outline-primary btn-sm rounded-2 px-3">Update Status</a>
        </div>
      </div>
    </div>
  </div>
</div>
