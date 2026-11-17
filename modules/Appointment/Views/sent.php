<?php
/**
 * @var array $appointments
 * @var array|null $allAppointments
 * @var int|null $totalCount
 * @var int|null $pendingCount
 * @var int|null $approvedCount
 * @var int|null $cancelledCount
 * @var string|null $currentSort
 * @var string|null $currentStatus
 * @var string|null $currentQuery
 */
$appointments = $appointments ?? [];
$allAppointments = $allAppointments ?? $appointments;
$totalCount = $totalCount ?? count($allAppointments);
$pendingCount = $pendingCount ?? 0;
$approvedCount = $approvedCount ?? 0;
$cancelledCount = $cancelledCount ?? 0;
$currentSort = $currentSort ?? 'date_asc';
$currentStatus = $currentStatus ?? 'all';
$currentQuery = $currentQuery ?? '';

if ($totalCount === 0 && count($appointments) > 0) {
    $totalCount = count($appointments);
}
if ($pendingCount === 0 && $approvedCount === 0 && $cancelledCount === 0) {
    foreach ($allAppointments as $a) {
        $st = strtolower(trim($a['appointment_status'] ?? ''));
        if ($st === 'approved') $approvedCount++;
        elseif ($st === 'cancel' || $st === 'denied') $cancelledCount++;
        else $pendingCount++;
    }
}
?>
<div class="page-shell panel-shell">
  <div class="page-heading mb-2"><div><div class="eyebrow mb-1">Your schedule</div><h1 class="fs-4 fw-bold mb-0">Sent appointments</h1><p class="small text-secondary mb-0">Keep track of the conversations you’ve booked.</p></div><a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm app-nav-action">Find a professional</a></div>
  <div class="row justify-content-center flex-grow-1 min-h-0">
    <div class="col-12 d-flex flex-column h-100 min-h-0">
      <div class="card rounded-3 shadow-sm border-0 dashboard-main flex-grow-1 min-h-0">
        <div class="card-header bg-white py-2 px-3 px-md-4 d-flex justify-content-between align-items-center">
          <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark fs-6">Outgoing Appointments</h5>
            <span class="badge bg-light text-secondary border"><?= $totalCount ?> Booked</span>
          </div>
          <a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm rounded-2">
            <i class="bi bi-plus-lg me-1"></i> New Booking
          </a>
        </div>
        <div class="card-body p-0 d-flex flex-column flex-grow-1 min-h-0">
          <!-- Sorting & Filtering Navigation Toolbar -->
          <div class="px-3 px-md-4 py-2 py-lg-3 bg-light border-bottom dashboard-toolbar">
            <div class="row g-2 align-items-center">
              <!-- Filter Tabs -->
              <div class="col-12 col-xl-auto">
                <div class="d-flex flex-wrap gap-1">
                  <a href="<?= routeUrl('/sent?status=all&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                     class="btn btn-sm rounded-pill <?= ($currentStatus === 'all') ? 'btn-primary' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                    All <span class="badge rounded-pill <?= ($currentStatus === 'all') ? 'bg-white text-primary' : 'bg-light text-secondary border' ?> ms-1"><?= $totalCount ?></span>
                  </a>
                  <a href="<?= routeUrl('/sent?status=pending&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                     class="btn btn-sm rounded-pill <?= ($currentStatus === 'pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                    <i class="bi bi-hourglass-split me-1"></i>Pending <span class="badge rounded-pill <?= ($currentStatus === 'pending') ? 'bg-dark text-white' : 'bg-light text-secondary border' ?> ms-1"><?= $pendingCount ?></span>
                  </a>
                  <a href="<?= routeUrl('/sent?status=approved&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                     class="btn btn-sm rounded-pill <?= ($currentStatus === 'approved') ? 'btn-success' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                    <i class="bi bi-check-circle me-1"></i>Confirmed <span class="badge rounded-pill <?= ($currentStatus === 'approved') ? 'bg-white text-success' : 'bg-light text-secondary border' ?> ms-1"><?= $approvedCount ?></span>
                  </a>
                  <a href="<?= routeUrl('/sent?status=cancel&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                     class="btn btn-sm rounded-pill <?= ($currentStatus === 'cancel') ? 'btn-secondary text-white' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                    Cancelled <span class="badge rounded-pill <?= ($currentStatus === 'cancel') ? 'bg-white text-dark' : 'bg-light text-secondary border' ?> ms-1"><?= $cancelledCount ?></span>
                  </a>
                </div>
              </div>

              <!-- Search & Sort Controls -->
              <div class="col-12 col-xl ms-xl-auto">
                <form method="get" action="<?= routeUrl('/sent') ?>" class="row g-2 align-items-center justify-content-xl-end m-0">
                  <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8') ?>">
                  
                  <!-- Live search input -->
                  <div class="col-12 col-sm-auto">
                    <div class="input-group input-group-sm">
                      <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                      <input type="text" name="q" id="sentSearchInput" class="form-control border-start-0 bg-white" placeholder="Search recipient/event..." value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>" style="max-width: 190px;">
                      <?php if (!empty($currentQuery)): ?>
                        <a href="<?= routeUrl('/sent?status=' . urlencode($currentStatus) . '&sort=' . urlencode($currentSort)) ?>" class="input-group-text bg-white text-muted text-decoration-none" title="Clear search">&times;</a>
                      <?php endif; ?>
                    </div>
                  </div>

                  <!-- Sort dropdown -->
                  <div class="col-12 col-sm-auto d-flex align-items-center gap-1">
                    <label for="sentSortSelect" class="small text-secondary fw-semibold text-nowrap mb-0"><i class="bi bi-arrow-down-up me-1"></i>Sort:</label>
                    <select name="sort" id="sentSortSelect" class="form-select form-select-sm bg-white" onchange="this.form.submit()" style="min-width: 180px;">
                      <option value="date_asc" <?= $currentSort === 'date_asc' ? 'selected' : '' ?>>📅 Date: Soonest first</option>
                      <option value="date_desc" <?= $currentSort === 'date_desc' ? 'selected' : '' ?>>📅 Date: Latest first</option>
                      <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>🔤 Recipient: A to Z</option>
                      <option value="name_desc" <?= $currentSort === 'name_desc' ? 'selected' : '' ?>>🔤 Recipient: Z to A</option>
                      <option value="status_pending" <?= $currentSort === 'status_pending' ? 'selected' : '' ?>>⏳ Status: Pending first</option>
                      <option value="status_approved" <?= $currentSort === 'status_approved' ? 'selected' : '' ?>>✅ Status: Confirmed first</option>
                    </select>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <?php if (count($appointments) > 0): ?>
            <div class="table-responsive dashboard-table-wrap flex-grow-1 min-h-0">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="ps-4">
                      <a href="<?= routeUrl('/sent?status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'name_asc' ? 'name_desc' : 'name_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1" title="Click to sort by Recipient">
                        Recipient 
                        <?php if ($currentSort === 'name_asc'): ?><i class="bi bi-sort-alpha-down text-primary"></i><?php elseif ($currentSort === 'name_desc'): ?><i class="bi bi-sort-alpha-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                      </a>
                    </th>
                    <th scope="col">
                      <a href="<?= routeUrl('/sent?status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'date_asc' ? 'date_desc' : 'date_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1" title="Click to sort by Date">
                        Date & Time 
                        <?php if ($currentSort === 'date_asc'): ?><i class="bi bi-sort-numeric-down text-primary"></i><?php elseif ($currentSort === 'date_desc'): ?><i class="bi bi-sort-numeric-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                      </a>
                    </th>
                    <th scope="col">Format</th>
                    <th scope="col">Event & Platform</th>
                    <th scope="col">
                      <a href="<?= routeUrl('/sent?status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'status_pending' ? 'status_approved' : 'status_pending')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1" title="Click to sort by Status">
                        Status 
                        <?php if (str_starts_with($currentSort, 'status_')): ?><i class="bi bi-sort-down text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                      </a>
                    </th>
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
                          <a href="<?= routeUrl('/appointment/view?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="View details">View</a>
                          <a href="<?= routeUrl('/appointment/export-ics?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="Download iCal (.ics)">
                            <i class="bi bi-calendar-arrow-down me-1"></i>iCal
                          </a>
                          <a href="<?= routeUrl('/appointment/edit?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-primary" title="Update status">Status</a>
                          <a href="<?= routeUrl('/appointment/delete-sent?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-danger" title="Cancel/Delete">Delete</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <?php if ($totalCount > 0): ?>
                <i class="bi bi-funnel fs-1 text-muted d-block mb-2"></i>
                <h6 class="text-dark fw-bold">No outgoing bookings match your filters</h6>
                <p class="text-muted small mb-3">Try choosing a different status filter or clearing your search term.</p>
                <a href="<?= routeUrl('/sent') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Clear filters</a>
              <?php else: ?>
                <img class="empty-state-illustration" src="<?= assetUrl('images/illustrations/online-calendar.svg') ?>" alt="A calendar ready for appointments">
                <h6 class="text-dark fw-bold">You haven't booked any appointments yet</h6>
                <p class="text-muted small mb-3">Browse user profiles and book time directly on their schedule.</p>
                <a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm rounded-2">Find People to Book</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
