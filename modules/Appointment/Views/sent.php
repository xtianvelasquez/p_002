<?php
/**
 * @var array $appointments
 */
$appointments = $appointments ?? [];
?>
<div class="page-shell">
  <div class="page-heading"><div><div class="eyebrow mb-2">Your schedule</div><h1>Sent appointments</h1><p>Keep track of the conversations you’ve booked.</p></div><a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm app-nav-action">Find a professional</a></div>
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card rounded-3 shadow-sm border-0 dashboard-main">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">Outgoing Appointments</h5>
            <span class="badge bg-light text-secondary border"><?= count($appointments) ?> Booked</span>
          </div>
          <a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm rounded-2">
            <i class="bi bi-plus-lg me-1"></i> New Booking
          </a>
        </div>
        <div class="card-body p-0">
          <?php if (count($appointments) > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="ps-4">Recipient</th>
                    <th scope="col">Date & Time</th>
                    <th scope="col">Format</th>
                    <th scope="col">Event & Platform</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-4">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appt): ?>
                    <?php
                      $status = strtolower(trim($appt['appointment_status'] ?? 'pending'));
                      $badgeClass = match ($status) {
                          'approved' => 'bg-success',
                          'denied'   => 'bg-danger',
                          'cancel'   => 'bg-secondary',
                          default    => 'bg-warning text-dark'
                      };

                      $evType = $appt['event_type'] ?? 'consultation';
                      $evTypeBadge = match ($evType) {
                          'quick_sync' => '<span class="badge bg-info-subtle text-info border">⚡ 15m Sync</span>',
                          'deep_dive' => '<span class="badge bg-primary-subtle text-primary border">🚀 60m Deep Dive</span>',
                          default => '<span class="badge bg-secondary-subtle text-secondary border">💼 30m Consult</span>'
                      };

                      $modeIcon = match ($appt['meeting_mode'] ?? 'google_meet') {
                          'zoom' => '💻 Zoom',
                          'in_person' => '🏢 Office',
                          'phone' => '📞 Phone',
                          default => '🎥 Meet'
                      };
                    ?>
                    <tr>
                      <td class="ps-4">
                        <div class="fw-bold text-dark"><?= htmlspecialchars($appt['fullName'], ENT_QUOTES, 'UTF-8') ?></div>
                        <?php if (!empty($appt['agenda'])): ?>
                          <small class="text-muted d-block text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($appt['agenda'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($appt['agenda'], ENT_QUOTES, 'UTF-8') ?>
                          </small>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="fw-semibold text-dark"><?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?></div>
                        <small class="text-primary fw-semibold"><?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?></small>
                      </td>
                      <td><?= $evTypeBadge ?></td>
                      <td>
                        <div class="fw-semibold small text-dark"><?= htmlspecialchars($appt['event_name'], ENT_QUOTES, 'UTF-8') ?></div>
                        <span class="badge bg-light text-secondary border" style="font-size: 0.72rem;"><?= htmlspecialchars($modeIcon, ENT_QUOTES, 'UTF-8') ?></span>
                      </td>
                      <td><span class="badge rounded-pill <?= $badgeClass ?> px-2 py-1"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span></td>
                      <td class="text-end pe-4">
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="<?= routeUrl('/appointment/view?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-secondary" title="View details">View</a>
                          <a href="<?= routeUrl('/appointment/export-ics?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-secondary" title="Download iCal (.ics)">
                            <i class="bi bi-calendar-arrow-down"></i>
                          </a>
                          <a href="<?= routeUrl('/appointment/edit?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-primary" title="Update status">Status</a>
                          <a href="<?= routeUrl('/appointment/delete-sent?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-danger" title="Cancel/Delete">Delete</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <i class="bi bi-calendar-plus fs-1 text-muted d-block mb-2"></i>
              <h6 class="text-dark fw-bold">You haven't booked any appointments yet</h6>
              <p class="text-muted small mb-3">Browse user profiles and book time directly on their schedule.</p>
              <a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm rounded-2">Find People to Book</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
