<?php
/**
 * Unified Appointments & Schedule Dashboard
 * 
 * @var array $currentUser
 * @var string $activeTab
 * @var array $appointments
 * @var array|null $allAppointments
 * @var int|null $totalCount
 * @var int|null $pendingCount
 * @var int|null $approvedCount
 * @var int|null $cancelledCount
 * @var array $sentAppointments
 * @var array|null $allSentAppointments
 * @var int|null $sentTotalCount
 * @var int|null $sentPendingCount
 * @var int|null $sentApprovedCount
 * @var int|null $sentCancelledCount
 * @var string|null $currentSort
 * @var string|null $currentStatus
 * @var string|null $currentQuery
 */

$currentUser = $currentUser ?? [];
$myFullName = trim(($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''));
$myHeadline = $currentUser['headline'] ?? ($currentUser['job_title'] ?? 'Professional Member');
$myAvatar = $currentUser['profile_picture'] ?? 'images/logo.png';

$activeTab = $activeTab ?? 'received';
if (!in_array($activeTab, ['received', 'sent'], true)) {
    $activeTab = 'received';
}

// Received appointments state
$appointments = $appointments ?? [];
$allAppointments = $allAppointments ?? $appointments;
$totalCount = $totalCount ?? count($allAppointments);
$pendingCount = $pendingCount ?? 0;
$approvedCount = $approvedCount ?? 0;
$cancelledCount = $cancelledCount ?? 0;

// Sent appointments state
$sentAppointments = $sentAppointments ?? [];
$allSentAppointments = $allSentAppointments ?? $sentAppointments;
$sentTotalCount = $sentTotalCount ?? count($allSentAppointments);
$sentPendingCount = $sentPendingCount ?? 0;
$sentApprovedCount = $sentApprovedCount ?? 0;
$sentCancelledCount = $sentCancelledCount ?? 0;

$currentSort = $currentSort ?? 'date_asc';
$currentStatus = $currentStatus ?? 'all';
$currentQuery = $currentQuery ?? '';
?>
<div class="page-shell panel-shell profile-shell">
  <!-- Page Header -->
  <div class="page-heading mb-2">
    <div>
      <div class="eyebrow mb-1">Your workspace</div>
      <h1 class="fs-4 fw-bold mb-0">Appointments & Schedule</h1>
      <p class="small text-secondary mb-0">Review incoming requests and manage your booked sessions in one place.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= routeUrl('/profile') ?>" class="btn btn-outline-primary btn-sm rounded-2">
        <i class="bi bi-sliders me-1"></i> Availability Settings
      </a>
    </div>
  </div>

  <!-- Two-Pane Layout (Profile Style) -->
  <div class="profile-layout appointments-layout">
    <!-- LEFT SIDEBAR -->
    <div class="profile-sidebar">
      <div class="position-relative d-inline-block mb-2 text-center">
        <img src="<?= htmlspecialchars(userAvatarUrl($myAvatar, $myFullName, 80), ENT_QUOTES, 'UTF-8') ?>" alt="Profile" class="rounded-circle border shadow-sm" style="width: 72px; height: 72px; object-fit: cover;" onerror="this.onerror=null; this.src='<?= getInitialsAvatarDataUri($myFullName, 80) ?>';">
      </div>
      <h2 class="fs-6 fw-bold mb-1 text-center"><?= htmlspecialchars($myFullName, ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="text-muted small mb-3 text-center"><?= htmlspecialchars($myHeadline, ENT_QUOTES, 'UTF-8') ?></p>
      
      <!-- Nav Tabs -->
      <nav class="nav nav-pills flex-column profile-nav-tabs" role="tablist" id="appointmentsTabList">
        <button class="nav-link <?= ($activeTab === 'received') ? 'active' : '' ?> text-start d-flex align-items-center justify-content-between" id="tabBtnReceived" data-bs-toggle="pill" data-bs-target="#tab-received" type="button" role="tab" aria-controls="tab-received" aria-selected="<?= ($activeTab === 'received') ? 'true' : 'false' ?>">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-inbox-fill text-primary"></i> <span>Received</span>
          </div>
          <?php if ($pendingCount > 0): ?>
            <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.7rem;"><?= $pendingCount ?> pending</span>
          <?php else: ?>
            <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;"><?= $totalCount ?></span>
          <?php endif; ?>
        </button>

        <button class="nav-link <?= ($activeTab === 'sent') ? 'active' : '' ?> text-start d-flex align-items-center justify-content-between" id="tabBtnSent" data-bs-toggle="pill" data-bs-target="#tab-sent" type="button" role="tab" aria-controls="tab-sent" aria-selected="<?= ($activeTab === 'sent') ? 'true' : 'false' ?>">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-send-fill text-primary"></i> <span>Sent</span>
          </div>
          <span class="badge bg-light text-secondary border rounded-pill" style="font-size: 0.7rem;"><?= $sentTotalCount ?></span>
        </button>
      </nav>

      <hr class="my-3 text-muted opacity-25">

      <div class="small text-muted text-start mb-2 px-1 fw-semibold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.04em;">Quick Shortcuts</div>
      <div class="d-flex flex-column gap-1">
        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 text-start d-flex align-items-center gap-2 py-1 px-2 border-0" onclick="const input = document.getElementById('headerSearchInput'); if (input) { input.focus(); input.dispatchEvent(new Event('focus')); }" style="font-size: 0.78rem;">
          <i class="bi bi-search"></i> <span>Find a Professional</span>
        </button>
        <a href="<?= routeUrl('/profile') ?>" class="btn btn-light btn-sm rounded-2 text-start text-secondary d-flex align-items-center gap-2 py-1 px-2 border" style="font-size: 0.78rem;">
          <i class="bi bi-clock-history"></i> <span>Set Working Hours</span>
        </a>
      </div>
    </div>

    <!-- RIGHT CONTENT PANE (Unboxed modules) -->
    <div class="appointments-content flex-grow-1 min-h-0 d-flex flex-column h-100">
      <div class="tab-content flex-grow-1 h-100 min-h-0 d-flex flex-column">
        
        <!-- ========================================== -->
        <!-- TAB 1: RECEIVED APPOINTMENTS               -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'received') ? 'show active' : '' ?> flex-grow-1 h-100 min-h-0 flex-column" id="tab-received" role="tabpanel" aria-labelledby="tabBtnReceived">
          <!-- Main Received Card: Own Separate Box -->
          <div class="card rounded-3 shadow-sm border dashboard-main flex-grow-1 min-h-0 bg-white" style="border-color: var(--line) !important;">
            <!-- Header with View Switcher -->
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 flex-shrink-0">
              <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">Incoming Requests</h5>
                <span class="badge bg-light text-secondary border"><?= $totalCount ?> Bookings</span>
              </div>
              
              <!-- View Toggle Pills -->
              <div class="btn-group p-1 bg-light rounded-pill border" role="group">
                <button type="button" class="btn btn-sm rounded-pill px-3 active fw-semibold" id="btnTableView">
                  <i class="bi bi-table me-1"></i> List View
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold text-secondary" id="btnCalendarView">
                  <i class="bi bi-calendar3 me-1"></i> Calendar View
                </button>
              </div>
            </div>

            <div class="card-body p-0 d-flex flex-column flex-grow-1 min-h-0">
              <!-- Sorting & Filtering Navigation Toolbar -->
              <div class="px-3 py-2 bg-light border-bottom dashboard-toolbar flex-shrink-0">
                <div class="row g-2 align-items-center">
                  <!-- Filter Tabs -->
                  <div class="col-12 col-xl-auto">
                    <div class="d-flex flex-wrap gap-1">
                      <a href="<?= routeUrl('/appointments?tab=received&status=all&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'all') ? 'btn-primary' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        All <span class="badge rounded-pill <?= ($currentStatus === 'all') ? 'bg-white text-primary' : 'bg-light text-secondary border' ?> ms-1"><?= $totalCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=received&status=pending&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        <i class="bi bi-hourglass-split me-1"></i>Pending <span class="badge rounded-pill <?= ($currentStatus === 'pending') ? 'bg-dark text-white' : 'bg-light text-secondary border' ?> ms-1"><?= $pendingCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=received&status=approved&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'approved') ? 'btn-success' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        <i class="bi bi-check-circle me-1"></i>Confirmed <span class="badge rounded-pill <?= ($currentStatus === 'approved') ? 'bg-white text-success' : 'bg-light text-secondary border' ?> ms-1"><?= $approvedCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=received&status=cancel&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'cancel') ? 'btn-secondary text-white' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        Cancelled <span class="badge rounded-pill <?= ($currentStatus === 'cancel') ? 'bg-white text-dark' : 'bg-light text-secondary border' ?> ms-1"><?= $cancelledCount ?></span>
                      </a>
                    </div>
                  </div>

                  <!-- Search & Sort Controls -->
                  <div class="col-12 col-xl ms-xl-auto">
                    <form method="get" action="<?= routeUrl('/appointments') ?>" class="row g-2 align-items-center justify-content-xl-end m-0">
                      <input type="hidden" name="tab" value="received">
                      <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8') ?>">
                      
                      <!-- Live search input -->
                      <div class="col-12 col-sm-auto">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                          <input type="text" name="q" id="receivedSearchInput" class="form-control border-start-0 bg-white" placeholder="Search sender/event..." value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>" style="max-width: 180px;">
                          <?php if (!empty($currentQuery)): ?>
                            <a href="<?= routeUrl('/appointments?tab=received&status=' . urlencode($currentStatus) . '&sort=' . urlencode($currentSort)) ?>" class="input-group-text bg-white text-muted text-decoration-none" title="Clear search">&times;</a>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Sort dropdown -->
                      <div class="col-12 col-sm-auto d-flex align-items-center gap-1">
                        <label for="receivedSortSelect" class="small text-secondary fw-semibold text-nowrap mb-0"><i class="bi bi-arrow-down-up me-1"></i>Sort:</label>
                        <select name="sort" id="receivedSortSelect" class="form-select form-select-sm bg-white" onchange="this.form.submit()" style="min-width: 175px;">
                          <option value="date_asc" <?= $currentSort === 'date_asc' ? 'selected' : '' ?>>📅 Soonest first</option>
                          <option value="date_desc" <?= $currentSort === 'date_desc' ? 'selected' : '' ?>>📅 Latest first</option>
                          <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>🔤 Sender: A to Z</option>
                          <option value="name_desc" <?= $currentSort === 'name_desc' ? 'selected' : '' ?>>🔤 Sender: Z to A</option>
                          <option value="status_pending" <?= $currentSort === 'status_pending' ? 'selected' : '' ?>>⏳ Pending first</option>
                          <option value="status_approved" <?= $currentSort === 'status_approved' ? 'selected' : '' ?>>✅ Confirmed first</option>
                        </select>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- 1. TABLE VIEW SECTION -->
              <div id="tableViewSection" class="flex-grow-1 min-h-0 d-flex flex-column">
                <?php if (count($appointments) > 0): ?>
                  <div class="table-responsive dashboard-table-wrap flex-grow-1 min-h-0">
                    <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th scope="col" class="ps-3">
                            <a href="<?= routeUrl('/appointments?tab=received&status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'name_asc' ? 'name_desc' : 'name_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                              Sender 
                              <?php if ($currentSort === 'name_asc'): ?><i class="bi bi-sort-alpha-down text-primary"></i><?php elseif ($currentSort === 'name_desc'): ?><i class="bi bi-sort-alpha-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                            </a>
                          </th>
                          <th scope="col">
                            <a href="<?= routeUrl('/appointments?tab=received&status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'date_asc' ? 'date_desc' : 'date_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                              Schedule 
                              <?php if ($currentSort === 'date_asc'): ?><i class="bi bi-sort-numeric-down text-primary"></i><?php elseif ($currentSort === 'date_desc'): ?><i class="bi bi-sort-numeric-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                            </a>
                          </th>
                          <th scope="col">Format</th>
                          <th scope="col">Event & Mode</th>
                          <th scope="col">Status</th>
                          <th scope="col" class="text-end pe-3">Actions</th>
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
                            <td class="ps-3">
                              <div class="fw-bold text-dark"><?= htmlspecialchars($appt['fullName'], ENT_QUOTES, 'UTF-8') ?></div>
                              <?php if (!empty($appt['agenda'])): ?>
                                <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($appt['agenda'], ENT_QUOTES, 'UTF-8') ?>">
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
                            <td class="text-end pe-3">
                              <div class="btn-group btn-group-sm" role="group">
                                <?php if ($status === 'pending'): ?>
                                  <form action="<?= routeUrl('/appointment/status') ?>" method="post" class="d-inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($appt['appointment_id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <input type="hidden" name="redirect" value="/appointments?tab=received">
                                    <button type="submit" class="btn btn-outline-success btn-sm" title="Accept appointment"><i class="bi bi-check-lg"></i></button>
                                  </form>
                                  <form action="<?= routeUrl('/appointment/status') ?>" method="post" class="d-inline">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($appt['appointment_id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="status" value="cancel">
                                    <input type="hidden" name="redirect" value="/appointments?tab=received">
                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Decline appointment"><i class="bi bi-x-lg"></i></button>
                                  </form>
                                <?php endif; ?>
                                <a href="<?= routeUrl('/appointment/view?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="View details">View</a>
                                <a href="<?= routeUrl('/appointment/export-ics?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="Download iCal (.ics)">
                                  <i class="bi bi-calendar-arrow-down"></i>
                                </a>
                                <a href="<?= routeUrl('/appointment/delete?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-danger" title="Delete record"><i class="bi bi-trash3"></i></a>
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
                      <h6 class="text-dark fw-bold">No appointments match your filters</h6>
                      <p class="text-muted small mb-3">Try choosing a different status filter or clearing your search term.</p>
                      <a href="<?= routeUrl('/appointments?tab=received') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Clear filters</a>
                    <?php else: ?>
                      <img class="empty-state-illustration" src="<?= assetUrl('images/illustrations/online-calendar.svg') ?>" alt="Calendar ready" style="max-width: 140px; margin-bottom: 12px;">
                      <h6 class="text-dark fw-bold">No received appointments yet</h6>
                      <p class="text-muted small mb-0">Share your public link to start receiving appointment requests.</p>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- 2. CALENDAR VIEW SECTION -->
              <div id="calendarViewSection" class="p-2 p-md-3 d-none dashboard-cal-wrap flex-grow-1 min-h-0" style="display: none !important;">
                <!-- Calendar Navigation Header -->
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                  <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6" id="calCurrentMonthYear">Month Year</h5>
                    <small class="text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Click any appointment to preview details or download calendar invites.</small>
                  </div>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="calPrevBtn" style="font-size: 0.75rem;">&larr; Prev</button>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" id="calTodayBtn" style="font-size: 0.75rem;">Today</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="calNextBtn" style="font-size: 0.75rem;">Next &rarr;</button>
                  </div>
                </div>

                <!-- Calendar Days of Week Header -->
                <div class="cal-dow-grid text-center fw-semibold text-secondary border-bottom pb-1 mb-1" style="font-size: 0.72rem;">
                  <div>Sun</div>
                  <div>Mon</div>
                  <div>Tue</div>
                  <div>Wed</div>
                  <div>Thu</div>
                  <div>Fri</div>
                  <div>Sat</div>
                </div>

                <!-- Calendar Days Grid Container -->
                <div id="calGrid" class="flex-grow-1 min-h-0"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: SENT APPOINTMENTS                   -->
        <!-- ========================================== -->
        <div class="tab-pane fade <?= ($activeTab === 'sent') ? 'show active' : '' ?> flex-grow-1 h-100 min-h-0 flex-column" id="tab-sent" role="tabpanel" aria-labelledby="tabBtnSent">
          <!-- Main Sent Card: Own Separate Box -->
          <div class="card rounded-3 shadow-sm border dashboard-main flex-grow-1 min-h-0 bg-white" style="border-color: var(--line) !important;">
            <div class="card-header bg-white py-2 px-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 flex-shrink-0">
              <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold text-dark fs-6">Outgoing Bookings</h5>
                <span class="badge bg-light text-secondary border"><?= $sentTotalCount ?> Booked</span>
              </div>
              <!-- View Toggle Pills -->
              <div class="btn-group p-1 bg-light rounded-pill border" role="group">
                <button type="button" class="btn btn-sm rounded-pill px-3 active fw-semibold" id="btnSentTableView">
                  <i class="bi bi-table me-1"></i> List View
                </button>
                <button type="button" class="btn btn-sm rounded-pill px-3 fw-semibold text-secondary" id="btnSentCalendarView">
                  <i class="bi bi-calendar3 me-1"></i> Calendar View
                </button>
              </div>
            </div>

            <div class="card-body p-0 d-flex flex-column flex-grow-1 min-h-0">
              <!-- Toolbar -->
              <div class="px-3 py-2 bg-light border-bottom dashboard-toolbar flex-shrink-0">
                <div class="row g-2 align-items-center">
                  <!-- Filter Tabs -->
                  <div class="col-12 col-xl-auto">
                    <div class="d-flex flex-wrap gap-1">
                      <a href="<?= routeUrl('/appointments?tab=sent&status=all&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'all') ? 'btn-primary' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        All <span class="badge rounded-pill <?= ($currentStatus === 'all') ? 'bg-white text-primary' : 'bg-light text-secondary border' ?> ms-1"><?= $sentTotalCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=sent&status=pending&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        <i class="bi bi-hourglass-split me-1"></i>Pending <span class="badge rounded-pill <?= ($currentStatus === 'pending') ? 'bg-dark text-white' : 'bg-light text-secondary border' ?> ms-1"><?= $sentPendingCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=sent&status=approved&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'approved') ? 'btn-success' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        <i class="bi bi-check-circle me-1"></i>Confirmed <span class="badge rounded-pill <?= ($currentStatus === 'approved') ? 'bg-white text-success' : 'bg-light text-secondary border' ?> ms-1"><?= $sentApprovedCount ?></span>
                      </a>
                      <a href="<?= routeUrl('/appointments?tab=sent&status=cancel&sort=' . urlencode($currentSort) . '&q=' . urlencode($currentQuery)) ?>" 
                         class="btn btn-sm rounded-pill <?= ($currentStatus === 'cancel') ? 'btn-secondary text-white' : 'btn-outline-secondary bg-white' ?>" style="font-size: 0.78rem;">
                        Cancelled <span class="badge rounded-pill <?= ($currentStatus === 'cancel') ? 'bg-white text-dark' : 'bg-light text-secondary border' ?> ms-1"><?= $sentCancelledCount ?></span>
                      </a>
                    </div>
                  </div>

                  <!-- Search & Sort Controls -->
                  <div class="col-12 col-xl ms-xl-auto">
                    <form method="get" action="<?= routeUrl('/appointments') ?>" class="row g-2 align-items-center justify-content-xl-end m-0">
                      <input type="hidden" name="tab" value="sent">
                      <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus, ENT_QUOTES, 'UTF-8') ?>">
                      
                      <!-- Live search input -->
                      <div class="col-12 col-sm-auto">
                        <div class="input-group input-group-sm">
                          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                          <input type="text" name="q" id="sentSearchInput" class="form-control border-start-0 bg-white" placeholder="Search recipient/event..." value="<?= htmlspecialchars($currentQuery, ENT_QUOTES, 'UTF-8') ?>" style="max-width: 180px;">
                          <?php if (!empty($currentQuery)): ?>
                            <a href="<?= routeUrl('/appointments?tab=sent&status=' . urlencode($currentStatus) . '&sort=' . urlencode($currentSort)) ?>" class="input-group-text bg-white text-muted text-decoration-none" title="Clear search">&times;</a>
                          <?php endif; ?>
                        </div>
                      </div>

                      <!-- Sort dropdown -->
                      <div class="col-12 col-sm-auto d-flex align-items-center gap-1">
                        <label for="sentSortSelect" class="small text-secondary fw-semibold text-nowrap mb-0"><i class="bi bi-arrow-down-up me-1"></i>Sort:</label>
                        <select name="sort" id="sentSortSelect" class="form-select form-select-sm bg-white" onchange="this.form.submit()" style="min-width: 175px;">
                          <option value="date_asc" <?= $currentSort === 'date_asc' ? 'selected' : '' ?>>📅 Soonest first</option>
                          <option value="date_desc" <?= $currentSort === 'date_desc' ? 'selected' : '' ?>>📅 Latest first</option>
                          <option value="name_asc" <?= $currentSort === 'name_asc' ? 'selected' : '' ?>>🔤 Recipient: A to Z</option>
                          <option value="name_desc" <?= $currentSort === 'name_desc' ? 'selected' : '' ?>>🔤 Recipient: Z to A</option>
                          <option value="status_pending" <?= $currentSort === 'status_pending' ? 'selected' : '' ?>>⏳ Pending first</option>
                          <option value="status_approved" <?= $currentSort === 'status_approved' ? 'selected' : '' ?>>✅ Confirmed first</option>
                        </select>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- 1. SENT TABLE VIEW SECTION -->
              <div id="sentTableViewSection" class="flex-grow-1 min-h-0 d-flex flex-column">
                <?php if (count($sentAppointments) > 0): ?>
                  <div class="table-responsive dashboard-table-wrap flex-grow-1 min-h-0">
                    <table class="table table-hover align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th scope="col" class="ps-3">
                            <a href="<?= routeUrl('/appointments?tab=sent&status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'name_asc' ? 'name_desc' : 'name_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                              Recipient 
                              <?php if ($currentSort === 'name_asc'): ?><i class="bi bi-sort-alpha-down text-primary"></i><?php elseif ($currentSort === 'name_desc'): ?><i class="bi bi-sort-alpha-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                            </a>
                          </th>
                          <th scope="col">
                            <a href="<?= routeUrl('/appointments?tab=sent&status=' . urlencode($currentStatus) . '&q=' . urlencode($currentQuery) . '&sort=' . ($currentSort === 'date_asc' ? 'date_desc' : 'date_asc')) ?>" class="text-decoration-none text-dark d-inline-flex align-items-center gap-1">
                              Schedule 
                              <?php if ($currentSort === 'date_asc'): ?><i class="bi bi-sort-numeric-down text-primary"></i><?php elseif ($currentSort === 'date_desc'): ?><i class="bi bi-sort-numeric-down-alt text-primary"></i><?php else: ?><i class="bi bi-arrow-down-up text-muted opacity-50 small"></i><?php endif; ?>
                            </a>
                          </th>
                          <th scope="col">Format</th>
                          <th scope="col">Event & Mode</th>
                          <th scope="col">Status</th>
                          <th scope="col" class="text-end pe-3">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($sentAppointments as $appt): ?>
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
                            <td class="ps-3">
                              <div class="fw-bold text-dark"><?= htmlspecialchars($appt['fullName'], ENT_QUOTES, 'UTF-8') ?></div>
                              <?php if (!empty($appt['agenda'])): ?>
                                <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($appt['agenda'], ENT_QUOTES, 'UTF-8') ?>">
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
                            <td class="text-end pe-3">
                              <div class="btn-group btn-group-sm" role="group">
                                <?php if ($status === 'pending' || $status === 'approved'): ?>
                                  <form action="<?= routeUrl('/appointment/status') ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this scheduled appointment?');">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($appt['appointment_id'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="status" value="cancel">
                                    <input type="hidden" name="redirect" value="/appointments?tab=sent">
                                    <button type="submit" class="btn btn-outline-warning btn-sm" title="Cancel appointment">Cancel</button>
                                  </form>
                                <?php endif; ?>
                                <a href="<?= routeUrl('/appointment/view?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="View details">View</a>
                                <a href="<?= routeUrl('/appointment/export-ics?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-secondary" title="Download iCal (.ics)">
                                  <i class="bi bi-calendar-arrow-down"></i>
                                </a>
                                <a href="<?= routeUrl('/appointment/delete-sent?id=' . rawurlencode($appt['appointment_id'])) ?>" class="btn btn-outline-danger" title="Delete record"><i class="bi bi-trash3"></i></a>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="text-center py-5">
                    <?php if ($sentTotalCount > 0): ?>
                      <i class="bi bi-funnel fs-1 text-muted d-block mb-2"></i>
                      <h6 class="text-dark fw-bold">No appointments match your filters</h6>
                      <p class="text-muted small mb-3">Try choosing a different status filter or clearing your search term.</p>
                      <a href="<?= routeUrl('/appointments?tab=sent') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">Clear filters</a>
                    <?php else: ?>
                      <img class="empty-state-illustration" src="<?= assetUrl('images/illustrations/online-calendar.svg') ?>" alt="Calendar ready" style="max-width: 140px; margin-bottom: 12px;">
                      <h6 class="text-dark fw-bold">No outgoing bookings yet</h6>
                      <p class="text-muted small mb-3">Discover colleagues and mentors to book your first appointment.</p>
                      <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" onclick="const input = document.getElementById('headerSearchInput'); if (input) { input.focus(); input.dispatchEvent(new Event('focus')); }">
                        <i class="bi bi-search me-1"></i> Browse Professionals
                      </button>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>

              <!-- 2. SENT CALENDAR VIEW SECTION -->
              <div id="sentCalendarViewSection" class="p-2 p-md-3 d-none dashboard-cal-wrap flex-grow-1 min-h-0" style="display: none !important;">
                <!-- Calendar Navigation Header -->
                <div class="d-flex justify-content-between align-items-center mb-1 flex-shrink-0">
                  <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6" id="calSentCurrentMonthYear">Month Year</h5>
                    <small class="text-muted d-none d-sm-inline" style="font-size: 0.72rem;">Click any outgoing booking to preview details or download calendar invites.</small>
                  </div>
                  <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="calSentPrevBtn" style="font-size: 0.75rem;">&larr; Prev</button>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2" id="calSentTodayBtn" style="font-size: 0.75rem;">Today</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" id="calSentNextBtn" style="font-size: 0.75rem;">Next &rarr;</button>
                  </div>
                </div>

                <!-- Calendar Days of Week Header -->
                <div class="cal-dow-grid text-center fw-semibold text-secondary border-bottom pb-1 mb-1" style="font-size: 0.72rem;">
                  <div>Sun</div>
                  <div>Mon</div>
                  <div>Tue</div>
                  <div>Wed</div>
                  <div>Thu</div>
                  <div>Fri</div>
                  <div>Sat</div>
                </div>

                <!-- Calendar Days Grid Container -->
                <div id="calSentGrid" class="flex-grow-1 min-h-0"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Quick Event Modal (Calendar Previews) -->
<div class="modal fade" id="eventQuickModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 border-0 shadow">
      <div class="modal-header border-bottom py-3">
        <h5 class="modal-title fw-bold" id="modalEventTitle">Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded-3 border">
          <div class="fs-3 text-primary"><i class="bi bi-clock-history"></i></div>
          <div>
            <div class="fw-bold text-dark fs-5" id="modalEventTime">10:00 AM</div>
            <div class="text-muted small" id="modalEventDate">2026-11-04</div>
          </div>
          <div class="ms-auto">
            <span class="badge" id="modalEventStatusBadge">Status</span>
          </div>
        </div>

        <dl class="row small mb-0">
          <dt class="col-sm-4 text-secondary">With</dt>
          <dd class="col-sm-8 fw-bold" id="modalParticipant">-</dd>

          <dt class="col-sm-4 text-secondary">Format</dt>
          <dd class="col-sm-8" id="modalFormat">-</dd>

          <dt class="col-sm-4 text-secondary">Location</dt>
          <dd class="col-sm-8" id="modalLocation">-</dd>

          <dt class="col-sm-4 text-secondary" id="modalAgendaLabel">Agenda</dt>
          <dd class="col-sm-8" id="modalAgenda">-</dd>
        </dl>
      </div>
      <div class="modal-footer bg-light py-2 px-3 border-top d-flex justify-content-between">
        <a href="#" id="modalIcsBtn" class="btn btn-outline-primary btn-sm rounded-2">
          <i class="bi bi-calendar-check me-1"></i> Export .ICS
        </a>
        <a href="#" id="modalViewBtn" class="btn btn-primary btn-sm rounded-2">
          View Full Details &rarr;
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // Sync tab clicks with URL ?tab=received / ?tab=sent
  const tabList = document.getElementById('appointmentsTabList');
  if (tabList) {
    tabList.querySelectorAll('button[data-bs-toggle="pill"]').forEach(btn => {
      btn.addEventListener('shown.bs.tab', function (e) {
        const target = e.target.getAttribute('data-bs-target');
        const tabKey = (target === '#tab-sent') ? 'sent' : 'received';
        if (window.history && window.history.replaceState) {
          const url = new URL(window.location.href);
          url.searchParams.set('tab', tabKey);
          window.history.replaceState({}, document.title, url.pathname + url.search + url.hash);
        }
      });
    });
  }

  // Appointments Data
  const appointments = <?= json_encode($appointments, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  const sentAppointments = <?= json_encode($sentAppointments, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

  function matchAppointmentViewToSidebar() {
    const sidebar = document.querySelector('.appointments-layout > .profile-sidebar');
    if (!sidebar) return;

    const allPanes = document.querySelectorAll('.appointments-layout > .appointments-content .tab-pane');
    if (window.innerWidth < 992) {
      allPanes.forEach(pane => {
        pane.style.removeProperty('height');
        pane.style.removeProperty('min-height');
        const dash = pane.querySelector('.dashboard-main');
        if (dash) {
          dash.style.removeProperty('height');
          dash.style.removeProperty('min-height');
          dash.style.removeProperty('flex');
        }
      });
      return;
    }

    const sidebarHeight = Math.round(sidebar.getBoundingClientRect().height);
    if (sidebarHeight > 0) {
      allPanes.forEach(pane => {
        const dash = pane.querySelector('.dashboard-main');
        if (pane.classList.contains('active')) {
          pane.style.setProperty('height', `${sidebarHeight}px`, 'important');
          pane.style.setProperty('min-height', `${sidebarHeight}px`, 'important');
          if (dash) {
            dash.style.setProperty('height', `${sidebarHeight}px`, 'important');
            dash.style.setProperty('min-height', `${sidebarHeight}px`, 'important');
            dash.style.setProperty('flex', '1 1 auto', 'important');
          }
        } else {
          pane.style.removeProperty('height');
          pane.style.removeProperty('min-height');
          if (dash) {
            dash.style.removeProperty('height');
            dash.style.removeProperty('min-height');
          }
        }
      });
    }
  }
  requestAnimationFrame(matchAppointmentViewToSidebar);
  window.addEventListener('resize', matchAppointmentViewToSidebar);

  const sidebarForSizing = document.querySelector('.appointments-layout > .profile-sidebar');
  if (sidebarForSizing && 'ResizeObserver' in window) {
    new ResizeObserver(() => requestAnimationFrame(matchAppointmentViewToSidebar)).observe(sidebarForSizing);
  }

  function setupCalendarController({
    appointmentsData,
    btnTableId,
    btnCalId,
    tableSecId,
    calSecId,
    calGridId,
    calTitleId,
    prevBtnId,
    todayBtnId,
    nextBtnId,
    isSent = false
  }) {
    const btnTable = document.getElementById(btnTableId);
    const btnCalendar = document.getElementById(btnCalId);
    const tableViewSection = document.getElementById(tableSecId);
    const calendarViewSection = document.getElementById(calSecId);
    const calGrid = document.getElementById(calGridId);
    const calTitle = document.getElementById(calTitleId);
    const prevBtn = document.getElementById(prevBtnId);
    const todayBtn = document.getElementById(todayBtnId);
    const nextBtn = document.getElementById(nextBtnId);

    if (!btnTable || !btnCalendar || !tableViewSection || !calendarViewSection || !calGrid || !calTitle) {
      return null;
    }

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let isCalendarVisible = false;

    btnTable.addEventListener('click', function () {
      isCalendarVisible = false;
      btnTable.classList.add('active');
      btnTable.classList.remove('text-secondary');
      btnCalendar.classList.remove('active');
      btnCalendar.classList.add('text-secondary');
      tableViewSection.classList.remove('d-none');
      tableViewSection.classList.add('d-flex');
      tableViewSection.style.setProperty('display', 'flex', 'important');
      calendarViewSection.classList.add('d-none');
      calendarViewSection.classList.remove('d-flex');
      calendarViewSection.style.setProperty('display', 'none', 'important');
      requestAnimationFrame(matchAppointmentViewToSidebar);
    });

    btnCalendar.addEventListener('click', function () {
      isCalendarVisible = true;
      btnCalendar.classList.add('active');
      btnCalendar.classList.remove('text-secondary');
      btnTable.classList.remove('active');
      btnTable.classList.add('text-secondary');
      calendarViewSection.classList.remove('d-none');
      calendarViewSection.classList.add('d-flex');
      calendarViewSection.style.setProperty('display', 'flex', 'important');
      tableViewSection.classList.add('d-none');
      tableViewSection.classList.remove('d-flex');
      tableViewSection.style.setProperty('display', 'none', 'important');
      render();
      requestAnimationFrame(matchAppointmentViewToSidebar);
    });

    prevBtn?.addEventListener('click', () => {
      currentMonth--;
      if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
      }
      render();
    });

    nextBtn?.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
      }
      render();
    });

    todayBtn?.addEventListener('click', () => {
      const today = new Date();
      currentMonth = today.getMonth();
      currentYear = today.getFullYear();
      render();
    });

    function render() {
      const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ];
      calTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
      calGrid.innerHTML = '';

      const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
      const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
      const prevMonthLastDate = new Date(currentYear, currentMonth, 0).getDate();

      const apptMap = Object.create(null);
      (appointmentsData || []).forEach(a => {
        const d = a.appointment_date;
        if (!apptMap[d]) apptMap[d] = [];
        apptMap[d].push(a);
      });

      const now = new Date();
      const todayStr = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;

      // Leading empty/prev-month days
      for (let i = firstDayIndex; i > 0; i--) {
        const dayNum = prevMonthLastDate - i + 1;
        const col = document.createElement('div');
        col.className = 'calendar-cell text-start bg-light text-muted opacity-50';
        col.innerHTML = `<span class="fw-semibold text-secondary" style="font-size: 0.68rem; line-height: 1;">${dayNum}</span>`;
        calGrid.appendChild(col);
      }

      // Days of current month
      for (let d = 1; d <= daysInMonth; d++) {
        const mm = String(currentMonth + 1).padStart(2, '0');
        const dd = String(d).padStart(2, '0');
        const dateString = `${currentYear}-${mm}-${dd}`;
        const isToday = (dateString === todayStr);

        const col = document.createElement('div');
        col.className = 'calendar-cell text-start ' + (isToday ? 'bg-primary-subtle border-primary' : 'bg-white');

        const dayHeading = document.createElement('div');
        dayHeading.className = 'd-flex justify-content-between align-items-center flex-shrink-0';
        dayHeading.style.cssText = 'margin-bottom: 2px; line-height: 1;';
        const dayLabel = document.createElement('span');
        dayLabel.className = `fw-bold ${isToday ? 'badge bg-primary text-white rounded-circle p-0' : 'text-dark'}`;
        dayLabel.style.cssText = 'width: 17px; height: 17px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.68rem;';
        dayLabel.textContent = String(d);
        dayHeading.appendChild(dayLabel);
        col.appendChild(dayHeading);

        if (apptMap[dateString] && apptMap[dateString].length > 0) {
          const eventList = document.createElement('div');
          eventList.className = 'cal-cell-events';
          apptMap[dateString].forEach(appt => {
            const status = (appt.appointment_status || 'pending').toLowerCase();
            let bgPill = 'bg-warning text-dark';
            if (status === 'approved') bgPill = 'bg-success text-white';
            else if (status === 'denied' || status === 'cancel') bgPill = 'bg-secondary text-white';

            const pill = document.createElement('button');
            pill.type = 'button';
            pill.className = `btn text-truncate text-start border-0 cal-event-pill ${bgPill} flex-shrink-0`;
            pill.dataset.id = String(appt.appointment_id);
            pill.title = `${appt.appointment_time} ${appt.fullName} (${appt.event_name || 'Appointment'})`;
            pill.style.cssText = 'font-size: 0.62rem; line-height: 1.15; padding: 1px 3px; border-radius: 3px; width: 100%;';
            pill.textContent = `${appt.appointment_time} ${appt.fullName}`;
            pill.addEventListener('click', function (e) {
              e.stopPropagation();
              openEventModal(appt);
            });
            eventList.appendChild(pill);
          });
          col.appendChild(eventList);
        }

        calGrid.appendChild(col);
      }

      // Trailing days
      const totalCells = firstDayIndex + daysInMonth;
      const nextDays = (7 - (totalCells % 7)) % 7;
      for (let j = 1; j <= nextDays; j++) {
        const col = document.createElement('div');
        col.className = 'calendar-cell text-start bg-light text-muted opacity-50';
        col.innerHTML = `<span class="fw-semibold text-secondary" style="font-size: 0.68rem; line-height: 1;">${j}</span>`;
        calGrid.appendChild(col);
      }

      calGrid.style.setProperty('--calendar-weeks', String(Math.ceil(calGrid.children.length / 7)));
    }

    return {
      render,
      isCalendarVisible: () => isCalendarVisible
    };
  }

  const receivedCal = setupCalendarController({
    appointmentsData: appointments,
    btnTableId: 'btnTableView',
    btnCalId: 'btnCalendarView',
    tableSecId: 'tableViewSection',
    calSecId: 'calendarViewSection',
    calGridId: 'calGrid',
    calTitleId: 'calCurrentMonthYear',
    prevBtnId: 'calPrevBtn',
    todayBtnId: 'calTodayBtn',
    nextBtnId: 'calNextBtn',
    isSent: false
  });

  const sentCal = setupCalendarController({
    appointmentsData: sentAppointments,
    btnTableId: 'btnSentTableView',
    btnCalId: 'btnSentCalendarView',
    tableSecId: 'sentTableViewSection',
    calSecId: 'sentCalendarViewSection',
    calGridId: 'calSentGrid',
    calTitleId: 'calSentCurrentMonthYear',
    prevBtnId: 'calSentPrevBtn',
    todayBtnId: 'calSentTodayBtn',
    nextBtnId: 'calSentNextBtn',
    isSent: true
  });

  document.querySelectorAll('#appointmentsTabList button[data-bs-toggle="pill"]').forEach(btn => {
    btn.addEventListener('show.bs.tab', () => {
      requestAnimationFrame(matchAppointmentViewToSidebar);
    });
    btn.addEventListener('shown.bs.tab', (e) => {
      const target = e.target.getAttribute('data-bs-target');
      if (target === '#tab-sent' && sentCal?.isCalendarVisible()) {
        sentCal.render();
      } else if (target === '#tab-received' && receivedCal?.isCalendarVisible()) {
        receivedCal.render();
      }
      requestAnimationFrame(matchAppointmentViewToSidebar);
    });
  });

  function openEventModal(appt) {
    document.getElementById('modalEventTitle').textContent = appt.event_name || 'Appointment';
    document.getElementById('modalEventTime').textContent = appt.appointment_time;
    document.getElementById('modalEventDate').textContent = appt.appointment_date;
    document.getElementById('modalParticipant').textContent = appt.fullName;
    document.getElementById('modalLocation').textContent = appt.location || 'Online';
    
    let formatLabel = '30m Consultation';
    if (appt.event_type === 'quick_sync') formatLabel = '⚡ 15m Quick Sync';
    else if (appt.event_type === 'deep_dive') formatLabel = '🚀 60m Deep Dive';
    document.getElementById('modalFormat').textContent = formatLabel;

    const agendaEl = document.getElementById('modalAgenda');
    if (appt.agenda && appt.agenda.trim().length > 0) {
      agendaEl.textContent = appt.agenda;
      document.getElementById('modalAgendaLabel').style.display = '';
      agendaEl.style.display = '';
    } else {
      document.getElementById('modalAgendaLabel').style.display = 'none';
      agendaEl.style.display = 'none';
    }

    const badge = document.getElementById('modalEventStatusBadge');
    const st = (appt.appointment_status || 'pending').toLowerCase();
    badge.textContent = st.toUpperCase();
    badge.className = 'badge ' + (st === 'approved' ? 'bg-success' : (st === 'denied' || st === 'cancel' ? 'bg-secondary' : 'bg-warning text-dark'));

    document.getElementById('modalViewBtn').href = '<?= routeUrl("/appointment/view?id=") ?>' + encodeURIComponent(appt.appointment_id);
    document.getElementById('modalIcsBtn').href = '<?= routeUrl("/appointment/export-ics?id=") ?>' + encodeURIComponent(appt.appointment_id);

    const modal = new bootstrap.Modal(document.getElementById('eventQuickModal'));
    modal.show();
  }
});
</script>
