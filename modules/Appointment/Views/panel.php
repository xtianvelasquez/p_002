<?php
/**
 * @var array $appointments
 */
$appointments = $appointments ?? [];
$totalCount = count($appointments);
$approvedCount = 0;
$pendingCount = 0;
foreach ($appointments as $a) {
    $st = strtolower(trim($a['appointment_status'] ?? ''));
    if ($st === 'approved') $approvedCount++;
    elseif ($st === 'pending') $pendingCount++;
}
?>
<div class="container-xl my-4">
  <!-- Dashboard Stats Banner -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">Total Received</div>
            <div class="fs-3 fw-bold text-dark mt-1"><?= $totalCount ?></div>
          </div>
          <div class="rounded-circle bg-primary-subtle p-3 text-primary fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
            <i class="bi bi-inbox"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">Confirmed</div>
            <div class="fs-3 fw-bold text-success mt-1"><?= $approvedCount ?></div>
          </div>
          <div class="rounded-circle bg-success-subtle p-3 text-success fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
            <i class="bi bi-check-circle"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">Pending Action</div>
            <div class="fs-3 fw-bold text-warning mt-1"><?= $pendingCount ?></div>
          </div>
          <div class="rounded-circle bg-warning-subtle p-3 text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
            <i class="bi bi-hourglass-split"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card rounded-3 shadow-sm border-0">
        <!-- Card Header with View Switcher -->
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div class="d-flex align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-dark">Schedule & Appointments</h5>
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

        <div class="card-body p-0">
          <!-- 1. TABLE VIEW SECTION -->
          <div id="tableViewSection">
            <?php if (count($appointments) > 0): ?>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" class="ps-4">Sender</th>
                      <th scope="col">Schedule</th>
                      <th scope="col">Meeting Format</th>
                      <th scope="col">Event & Mode</th>
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
                            <a href="<?= routeUrl('/appointment/delete?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-danger" title="Delete record">Delete</a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-2"></i>
                <h6 class="text-dark fw-bold">No received appointments yet</h6>
                <p class="text-muted small mb-0">Share your public link to start receiving appointment requests.</p>
              </div>
            <?php endif; ?>
          </div>

          <!-- 2. CALENDAR VIEW SECTION -->
          <div id="calendarViewSection" class="p-4 d-none">
            <!-- Calendar Navigation Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <div>
                <h4 class="fw-bold mb-0 text-dark" id="calCurrentMonthYear">Month Year</h4>
                <small class="text-muted">Click any appointment to preview details or download calendar invites.</small>
              </div>
              <div class="btn-group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="calPrevBtn">&larr; Prev</button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="calTodayBtn">Today</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="calNextBtn">Next &rarr;</button>
              </div>
            </div>

            <!-- Calendar Days of Week Header -->
            <div class="row g-1 text-center fw-semibold text-secondary small mb-2 border-bottom pb-2">
              <div class="col">Sun</div>
              <div class="col">Mon</div>
              <div class="col">Tue</div>
              <div class="col">Wed</div>
              <div class="col">Thu</div>
              <div class="col">Fri</div>
              <div class="col">Sat</div>
            </div>

            <!-- Calendar Days Grid Container -->
            <div id="calGrid" class="row g-1"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Quick Event Modal -->
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
  const appointments = <?= json_encode($appointments, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  const btnTable = document.getElementById('btnTableView');
  const btnCalendar = document.getElementById('btnCalendarView');
  const tableViewSection = document.getElementById('tableViewSection');
  const calendarViewSection = document.getElementById('calendarViewSection');
  const calGrid = document.getElementById('calGrid');
  const calTitle = document.getElementById('calCurrentMonthYear');

  // View toggle logic
  btnTable.addEventListener('click', function () {
    btnTable.classList.add('active');
    btnTable.classList.remove('text-secondary');
    btnCalendar.classList.remove('active');
    btnCalendar.classList.add('text-secondary');
    tableViewSection.classList.remove('d-none');
    calendarViewSection.classList.add('d-none');
  });

  btnCalendar.addEventListener('click', function () {
    btnCalendar.classList.add('active');
    btnCalendar.classList.remove('text-secondary');
    btnTable.classList.remove('active');
    btnTable.classList.add('text-secondary');
    calendarViewSection.classList.remove('d-none');
    tableViewSection.classList.add('d-none');
    renderCalendar();
  });

  // Calendar State
  let currentDate = new Date();
  let currentMonth = currentDate.getMonth();
  let currentYear = currentDate.getFullYear();

  document.getElementById('calPrevBtn').addEventListener('click', () => {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    renderCalendar();
  });

  document.getElementById('calNextBtn').addEventListener('click', () => {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar();
  });

  document.getElementById('calTodayBtn').addEventListener('click', () => {
    const today = new Date();
    currentMonth = today.getMonth();
    currentYear = today.getFullYear();
    renderCalendar();
  });

  function renderCalendar() {
    const monthNames = [
      "January", "February", "March", "April", "May", "June",
      "July", "August", "September", "October", "November", "December"
    ];
    calTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    calGrid.innerHTML = '';

    const firstDayIndex = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonthLastDate = new Date(currentYear, currentMonth, 0).getDate();

    // Map appointments by date (YYYY-MM-DD)
    const apptMap = {};
    appointments.forEach(a => {
      const d = a.appointment_date;
      if (!apptMap[d]) apptMap[d] = [];
      apptMap[d].push(a);
    });

    const todayStr = (new Date()).toISOString().split('T')[0];

    // Leading empty/prev-month days
    for (let i = firstDayIndex; i > 0; i--) {
      const dayNum = prevMonthLastDate - i + 1;
      const col = document.createElement('div');
      col.className = 'col p-2 border bg-light text-muted opacity-50 text-start';
      col.style.cssText = 'flex: 0 0 14.2857%; max-width: 14.2857%; min-height: 105px;';
      col.innerHTML = `<span class="small fw-semibold">${dayNum}</span>`;
      calGrid.appendChild(col);
    }

    // Days of current month
    for (let d = 1; d <= daysInMonth; d++) {
      const mm = String(currentMonth + 1).padStart(2, '0');
      const dd = String(d).padStart(2, '0');
      const dateString = `${currentYear}-${mm}-${dd}`;
      const isToday = (dateString === todayStr);

      const col = document.createElement('div');
      col.className = 'col p-2 border position-relative text-start ' + (isToday ? 'bg-primary-subtle border-primary' : 'bg-white');
      col.style.cssText = 'flex: 0 0 14.2857%; max-width: 14.2857%; min-height: 105px;';

      let inner = `<div class="d-flex justify-content-between align-items-center mb-1">
        <span class="small fw-bold ${isToday ? 'badge bg-primary text-white rounded-circle p-1' : 'text-dark'}" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center;">${d}</span>
      </div>`;

      if (apptMap[dateString] && apptMap[dateString].length > 0) {
        inner += `<div class="d-flex flex-column gap-1 overflow-hidden" style="max-height: 80px; overflow-y: auto;">`;
        apptMap[dateString].forEach(appt => {
          const status = (appt.appointment_status || 'pending').toLowerCase();
          let bgPill = 'bg-warning text-dark';
          if (status === 'approved') bgPill = 'bg-success text-white';
          else if (status === 'denied' || status === 'cancel') bgPill = 'bg-secondary text-white';

          inner += `<button type="button" class="btn btn-sm text-truncate text-start p-1 border-0 cal-event-pill ${bgPill}" 
            data-id="${appt.appointment_id}" 
            style="font-size: 0.72rem; line-height: 1.2; border-radius: 4px;">
            <strong>${appt.appointment_time}</strong> ${appt.fullName}
          </button>`;
        });
        inner += `</div>`;
      }

      col.innerHTML = inner;
      calGrid.appendChild(col);
    }

    // Trailing days to round to complete week
    const totalCells = firstDayIndex + daysInMonth;
    const nextDays = (7 - (totalCells % 7)) % 7;
    for (let j = 1; j <= nextDays; j++) {
      const col = document.createElement('div');
      col.className = 'col p-2 border bg-light text-muted opacity-50 text-start';
      col.style.cssText = 'flex: 0 0 14.2857%; max-width: 14.2857%; min-height: 105px;';
      col.innerHTML = `<span class="small fw-semibold">${j}</span>`;
      calGrid.appendChild(col);
    }

    // Attach listeners to calendar pills
    document.querySelectorAll('.cal-event-pill').forEach(pill => {
      pill.addEventListener('click', function (e) {
        e.stopPropagation();
        const apptId = this.getAttribute('data-id');
        const found = appointments.find(a => a.appointment_id === apptId);
        if (found) {
          openEventModal(found);
        }
      });
    });
  }

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
