<div class="container-xl my-4">
  <div class="row justify-content-center align-items-start g-4">
    <!-- User Profile Sidebar -->
    <div class="col-12 col-md-4 col-lg-3">
      <div class="card rounded-3 shadow-sm border-0 text-center p-3 mb-3">
        <img src="<?= assetUrl($profilePicture) ?>" alt="Profile Picture" class="img-fluid rounded-circle mx-auto mb-3 shadow-sm" style="width: 110px; height: 110px; object-fit: cover;">
        <h5 class="fw-bold mb-1 text-dark"><?= htmlspecialchars(trim($firstName . ' ' . $lastName), ENT_QUOTES, 'UTF-8') ?></h5>
        <?php if (!empty($middleName)): ?>
          <p class="text-muted small mb-2"><?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 mb-3">
          Book a 1:1 Session
        </span>

        <hr class="my-2">
        <div class="text-start small text-secondary">
          <p class="mb-1"><i class="bi bi-telephone me-2 text-primary"></i>+63 <?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></p>
          <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i><?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></p>
          <div class="bg-light p-3 border rounded-3 mt-2">
            <span class="d-block fw-semibold text-dark"><i class="bi bi-clock me-1 text-primary"></i> Available Window:</span>
            <span><?= date('g:i A', strtotime('2000-01-01 ' . $workStartTime)) ?> – <?= date('g:i A', strtotime('2000-01-01 ' . $workEndTime)) ?></span>
            <small class="d-block text-muted mt-1">(Standard increments: <?= (int)$slotDuration ?> mins)</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Configuration & Form -->
    <div class="col-12 col-md-8 col-lg-8">
      <div class="card rounded-3 shadow-sm border-0">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 fw-bold text-dark">Schedule an Appointment</h5>
          <span class="badge bg-light text-secondary border">Step 1 & 2</span>
        </div>
        <div class="card-body p-4">
          <?php if (!$isAuthenticated): ?>
            <div class="alert alert-info rounded-3 d-flex justify-content-between align-items-center mb-4">
              <span><i class="bi bi-info-circle me-1"></i> You are viewing a public schedule. Sign in to confirm your booking.</span>
              <a href="<?= routeUrl('/login') ?>" class="btn btn-primary btn-sm rounded-2">Sign In</a>
            </div>
          <?php endif; ?>

          <form action="<?= routeUrl('/appointment/book') ?>" method="post" id="bookingForm">
            <?= csrfField() ?>
            <input type="hidden" name="receiverId" id="receiverId" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="appointmentTime" id="selectedTimeInput" required>
            <input type="hidden" name="eventType" id="eventTypeInput" value="consultation">
            <input type="hidden" name="meetingMode" id="meetingModeInput" value="google_meet">

            <!-- Section 1: Meeting Type Cards -->
            <div class="mb-4">
              <label class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                1. Select Meeting Format
              </label>
              <div class="row g-2">
                <div class="col-12 col-md-4">
                  <div class="p-3 border rounded-3 event-type-card h-100 cursor-pointer" data-type="quick_sync" data-duration="15" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fs-5">⚡</span>
                      <span class="badge bg-light text-secondary border">15m</span>
                    </div>
                    <div class="fw-bold text-dark">Quick Sync</div>
                    <div class="small text-muted" style="font-size: 0.8rem;">Brief standup, quick Q&A, or intro call.</div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 border rounded-3 event-type-card h-100 border-primary bg-primary-subtle cursor-pointer active" data-type="consultation" data-duration="30" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fs-5">💼</span>
                      <span class="badge bg-primary text-white">30m (Standard)</span>
                    </div>
                    <div class="fw-bold text-dark">Consultation</div>
                    <div class="small text-muted" style="font-size: 0.8rem;">In-depth review, alignment, or planning.</div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 border rounded-3 event-type-card h-100 cursor-pointer" data-type="deep_dive" data-duration="60" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fs-5">🚀</span>
                      <span class="badge bg-light text-secondary border">60m</span>
                    </div>
                    <div class="fw-bold text-dark">Deep Dive</div>
                    <div class="small text-muted" style="font-size: 0.8rem;">Technical walkthrough, pairing, or interview.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Section 2: Meeting Platform / Mode Selection -->
            <div class="mb-4">
              <label class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                2. Meeting Platform / Mode
              </label>
              <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill meeting-mode-btn active" data-mode="google_meet" data-default-loc="Google Meet">
                  🎥 Google Meet
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill meeting-mode-btn" data-mode="zoom" data-default-loc="Zoom Meeting">
                  💻 Zoom
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill meeting-mode-btn" data-mode="in_person" data-default-loc="Office Room / In-Person">
                  🏢 In-Person
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill meeting-mode-btn" data-mode="phone" data-default-loc="Phone Call (+63 <?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?>)">
                  📞 Phone Call
                </button>
              </div>
            </div>

            <!-- Section 3: Date & Interactive Slots -->
            <div class="row g-3 mb-4">
              <div class="col-12 col-sm-6">
                <label for="appointmentDate" class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                  3. Choose Date
                </label>
                <input type="date" name="appointmentDate" id="appointmentDate" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($initialDate, ENT_QUOTES, 'UTF-8') ?>" class="form-control rounded-2" required>
              </div>

              <div class="col-12 col-sm-6 d-flex align-items-end">
                <div class="small text-secondary mb-2" id="slotStatusText">
                  Selected Slot: <strong class="text-primary fs-6" id="selectedTimeDisplay">None selected</strong>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                  4. Available Time Slots
                </label>
                <div id="slotsContainer" class="d-flex flex-wrap gap-2 p-3 bg-light border rounded-3" style="max-height: 220px; overflow-y: auto;">
                  <?php if (!empty($initialSlots)): ?>
                    <?php foreach ($initialSlots as $slot): ?>
                      <?php if ($slot['available']): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-2 time-slot-btn" data-time="<?= htmlspecialchars($slot['time'], ENT_QUOTES, 'UTF-8') ?>" data-display="<?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?>">
                          <?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?>
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 disabled" disabled title="<?= htmlspecialchars($slot['reason'], ENT_QUOTES, 'UTF-8') ?>">
                          <?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?> <small>(<?= htmlspecialchars($slot['reason'], ENT_QUOTES, 'UTF-8') ?>)</small>
                        </button>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="text-muted small mb-0">No time slots available for this date.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Section 4: Details & Notes -->
            <div class="row g-3">
              <div class="col-12">
                <label for="eventName" class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                  5. Subject / Meeting Title
                </label>
                <input type="text" name="eventName" id="eventName" placeholder="e.g. Architecture Consultation & Portfolio Review" class="form-control rounded-2" required>
              </div>

              <div class="col-12">
                <label for="location" class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                  6. Location / Conference Link
                </label>
                <input type="text" name="location" id="location" value="Google Meet" placeholder="e.g. Google Meet URL, Zoom link, or address" class="form-control rounded-2" required>
              </div>

              <div class="col-12">
                <label for="agenda" class="form-label text-secondary small fw-semibold text-uppercase" style="letter-spacing: 0.04em;">
                  7. Agenda / Key Topics (Optional)
                </label>
                <textarea name="agenda" id="agenda" rows="3" class="form-control rounded-2" placeholder="Briefly describe what you'd like to achieve during this meeting..."></textarea>
              </div>

              <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <a href="<?= routeUrl(isset($_SESSION['userId']) ? '/search' : '/') ?>" class="btn btn-outline-secondary rounded-2 px-4">&larr; Cancel</a>
                <?php if ($isAuthenticated): ?>
                  <button type="submit" name="submit" id="submitBtn" class="btn btn-primary rounded-2 px-4 shadow-sm" disabled>Confirm Booking</button>
                <?php else: ?>
                  <a href="<?= routeUrl('/login') ?>" class="btn btn-primary rounded-2 px-4 shadow-sm">Sign In to Book</a>
                <?php endif; ?>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const dateInput = document.getElementById('appointmentDate');
  const receiverIdInput = document.getElementById('receiverId');
  const slotsContainer = document.getElementById('slotsContainer');
  const selectedTimeInput = document.getElementById('selectedTimeInput');
  const selectedTimeDisplay = document.getElementById('selectedTimeDisplay');
  const submitBtn = document.getElementById('submitBtn');
  const eventTypeInput = document.getElementById('eventTypeInput');
  const meetingModeInput = document.getElementById('meetingModeInput');
  const locationInput = document.getElementById('location');
  const slotsApiUrl = '<?= routeUrl("/api/appointment/slots") ?>';

  let currentDuration = 30;

  function attachSlotListeners() {
    document.querySelectorAll('.time-slot-btn').forEach(button => {
      button.addEventListener('click', function () {
        document.querySelectorAll('.time-slot-btn').forEach(btn => {
          btn.classList.remove('btn-primary', 'active');
          btn.classList.add('btn-outline-primary');
        });

        this.classList.remove('btn-outline-primary');
        this.classList.add('btn-primary', 'active');

        const chosenTime = this.getAttribute('data-time');
        const chosenDisplay = this.getAttribute('data-display');

        selectedTimeInput.value = chosenTime;
        selectedTimeDisplay.textContent = chosenDisplay;
        if (submitBtn) {
          submitBtn.removeAttribute('disabled');
        }
      });
    });
  }

  function fetchSlots() {
    const selectedDate = dateInput.value;
    const receiverId = receiverIdInput.value;

    selectedTimeInput.value = '';
    selectedTimeDisplay.textContent = 'None selected';
    if (submitBtn) {
      submitBtn.setAttribute('disabled', 'true');
    }

    slotsContainer.innerHTML = '<div class="text-center w-100 py-3 text-muted small"><span class="spinner-border spinner-border-sm me-2"></span>Loading open time slots...</div>';

    fetch(`${slotsApiUrl}?receiverId=${encodeURIComponent(receiverId)}&date=${encodeURIComponent(selectedDate)}&duration=${encodeURIComponent(currentDuration)}`)
      .then(response => response.json())
      .then(data => {
        slotsContainer.innerHTML = '';
        if (data.slots && data.slots.length > 0) {
          data.slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm rounded-2 ' + (slot.available ? 'btn-outline-primary time-slot-btn' : 'btn-outline-secondary disabled');
            if (slot.available) {
              btn.setAttribute('data-time', slot.time);
              btn.setAttribute('data-display', slot.display);
              btn.textContent = slot.display;
            } else {
              btn.setAttribute('disabled', 'true');
              btn.innerHTML = `${slot.display} <small>(${slot.reason})</small>`;
            }
            slotsContainer.appendChild(btn);
          });
          attachSlotListeners();
        } else {
          slotsContainer.innerHTML = '<p class="text-muted small mb-0 w-100 py-2">No time slots available for this date.</p>';
        }
      })
      .catch(error => {
        slotsContainer.innerHTML = '<p class="text-danger small mb-0 w-100 py-2">Error loading time slots. Please try refreshing.</p>';
      });
  }

  // Meeting Type card selection
  document.querySelectorAll('.event-type-card').forEach(card => {
    card.addEventListener('click', function () {
      document.querySelectorAll('.event-type-card').forEach(c => {
        c.classList.remove('border-primary', 'bg-primary-subtle', 'active');
      });
      this.classList.add('border-primary', 'bg-primary-subtle', 'active');
      eventTypeInput.value = this.getAttribute('data-type');
      currentDuration = parseInt(this.getAttribute('data-duration'), 10) || 30;
      fetchSlots();
    });
  });

  // Meeting Mode pill selection
  document.querySelectorAll('.meeting-mode-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.meeting-mode-btn').forEach(b => {
        b.classList.remove('btn-outline-primary', 'active');
        b.classList.add('btn-outline-secondary');
      });
      this.classList.remove('btn-outline-secondary');
      this.classList.add('btn-outline-primary', 'active');

      const mode = this.getAttribute('data-mode');
      const defaultLoc = this.getAttribute('data-default-loc');
      meetingModeInput.value = mode;
      locationInput.value = defaultLoc;
    });
  });

  dateInput.addEventListener('change', fetchSlots);
  attachSlotListeners();
});
</script>
