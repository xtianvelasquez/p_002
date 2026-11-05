<?php
/**
 * @var string $id
 * @var string $firstName
 * @var string|null $middleName
 * @var string $lastName
 * @var string $contactNumber
 * @var string $emailAddress
 * @var string $profilePicture
 * @var string $workStartTime
 * @var string $workEndTime
 * @var int $slotDuration
 * @var string $initialDate
 * @var array $initialSlots
 * @var bool $isAuthenticated
 * @var string|null $headline
 * @var string|null $jobTitle
 * @var string|null $company
 * @var string|null $locationCity
 * @var string|null $bio
 * @var string|null $skills
 * @var string|null $consultationTopics
 * @var string|null $linkedinUrl
 * @var string|null $githubUrl
 * @var string|null $websiteUrl
 */
$id = $id ?? '';
$firstName = $firstName ?? '';
$middleName = $middleName ?? '';
$lastName = $lastName ?? '';
$contactNumber = $contactNumber ?? '';
$emailAddress = $emailAddress ?? '';
$profilePicture = $profilePicture ?? 'images/logo.png';
$workStartTime = $workStartTime ?? '09:00';
$workEndTime = $workEndTime ?? '17:00';
$slotDuration = $slotDuration ?? 30;
$initialDate = $initialDate ?? date('Y-m-d');
$initialSlots = $initialSlots ?? [];
$isAuthenticated = !empty($isAuthenticated);

$fullName = trim($firstName . ' ' . $lastName);
$headline = trim($headline ?? '');
$jobTitle = trim($jobTitle ?? '');
$company = trim($company ?? '');
$locationCity = trim($locationCity ?? '');
$bio = trim($bio ?? '');
$skillsRaw = trim($skills ?? '');
$skillTags = array_filter(array_map('trim', explode(',', $skillsRaw)));
$consultationTopics = trim($consultationTopics ?? '');
$linkedinUrl = trim($linkedinUrl ?? '');
$githubUrl = trim($githubUrl ?? '');
$websiteUrl = trim($websiteUrl ?? '');
?>
<div class="container-xl my-4">
  <!-- 1. LINKEDIN-STYLE PROFILE HERO CARD -->
  <div class="card rounded-3 shadow-sm border-0 overflow-hidden mb-4 bg-white">
    <!-- Banner Header -->
    <div class="position-relative" style="height: 160px; background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0d9488 100%);">
      <div class="position-absolute top-0 end-0 p-3 text-white-50 small">
        <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-semibold">
          <i class="bi bi-shield-check text-primary me-1"></i> Verified Professional
        </span>
      </div>
    </div>

    <!-- Profile Header Content -->
    <div class="card-body px-4 pb-4 pt-0">
      <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
        <!-- Avatar & Core Info -->
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-4" style="margin-top: -65px;">
          <div class="position-relative">
            <img src="<?= htmlspecialchars(userAvatarUrl($profilePicture, $fullName, 130), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>" class="rounded-circle border border-4 border-white shadow bg-white" style="width: 130px; height: 130px; object-fit: cover;" onerror="this.onerror=null; this.src='<?= getInitialsAvatarDataUri($fullName, 130) ?>';">
            <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle p-2" title="Available for Appointments"></span>
          </div>

          <div class="text-center text-sm-start mt-2 mt-sm-4">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
              <h3 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?></h3>
              <?php if (!empty($middleName)): ?>
                <span class="text-muted fs-6">(<?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8') ?>)</span>
              <?php endif; ?>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                <i class="bi bi-calendar-check me-1"></i>Open to Meetings
              </span>
            </div>

            <?php if (!empty($headline)): ?>
              <p class="text-secondary fw-medium fs-6 mb-1 mt-1"><?= htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-3 small text-muted mt-2">
              <?php if (!empty($jobTitle) || !empty($company)): ?>
                <div>
                  <i class="bi bi-briefcase me-1 text-primary"></i>
                  <span class="fw-semibold text-dark"><?= htmlspecialchars(trim($jobTitle . ($company ? ' at ' . $company : '')), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
              <?php endif; ?>

              <?php if (!empty($locationCity)): ?>
                <div>
                  <i class="bi bi-geo-alt me-1 text-danger"></i>
                  <span><?= htmlspecialchars($locationCity, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
              <?php endif; ?>

              <div>
                <i class="bi bi-clock me-1 text-primary"></i>
                <span><?= date('g:i A', strtotime('2000-01-01 ' . $workStartTime)) ?> – <?= date('g:i A', strtotime('2000-01-01 ' . $workEndTime)) ?></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Social Links & Primary CTA -->
        <div class="align-self-center align-self-md-end mt-3 mt-md-0 d-flex flex-wrap align-items-center gap-2">
          <?php if (!empty($linkedinUrl)): ?>
            <a href="<?= htmlspecialchars($linkedinUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3" title="LinkedIn Profile">
              <i class="bi bi-linkedin me-1"></i> LinkedIn
            </a>
          <?php endif; ?>

          <?php if (!empty($githubUrl)): ?>
            <a href="<?= htmlspecialchars($githubUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="GitHub Profile">
              <i class="bi bi-github me-1"></i> GitHub
            </a>
          <?php endif; ?>

          <?php if (!empty($websiteUrl)): ?>
            <a href="<?= htmlspecialchars($websiteUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm rounded-pill px-3" title="Personal Website">
              <i class="bi bi-globe me-1"></i> Portfolio
            </a>
          <?php endif; ?>

          <a href="#schedulerCard" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-calendar-plus me-1"></i> Book Time
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- 2. TWO-COLUMN LAYOUT: PROFESSIONAL DOSSIER & CAL.COM SCHEDULER -->
  <div class="row g-4 align-items-start">
    
    <!-- LEFT COLUMN: ABOUT, SKILLS, CONSULTATION TOPICS -->
    <div class="col-12 col-lg-5">
      <!-- About / Summary Card -->
      <div class="card rounded-3 shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
          <i class="bi bi-person-lines-fill text-primary"></i>
          <h6 class="card-title mb-0 fw-bold text-dark">About</h6>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($bio)): ?>
            <div class="text-secondary small" style="line-height: 1.7; white-space: pre-line;"><?= htmlspecialchars($bio, ENT_QUOTES, 'UTF-8') ?></div>
          <?php else: ?>
            <p class="text-muted small mb-0">No bio provided yet. Book an appointment to connect directly!</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Skills & Expertise Card -->
      <div class="card rounded-3 shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center gap-2">
          <i class="bi bi-award text-primary"></i>
          <h6 class="card-title mb-0 fw-bold text-dark">Skills & Competencies</h6>
        </div>
        <div class="card-body p-4">
          <?php if (!empty($skillTags)): ?>
            <div class="d-flex flex-wrap gap-2">
              <?php foreach ($skillTags as $skill): ?>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-normal" style="font-size: 0.8rem;">
                  <?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>
                </span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted small mb-0">General Professional & Consultation</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Consultation Focus / Why Meet With Me Card -->
      <div class="card rounded-3 shadow-sm border-0 mb-4 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-lightbulb text-warning"></i>
            <h6 class="card-title mb-0 fw-bold text-dark">Consultation Focus</h6>
          </div>
          <span class="badge bg-light text-secondary border small">Mutual Fit</span>
        </div>
        <div class="card-body p-4">
          <p class="small text-secondary mb-2 fw-semibold">Topics & discussions this person is best suited for:</p>
          <?php if (!empty($consultationTopics)): ?>
            <div class="p-3 bg-light rounded-3 border small text-dark" style="white-space: pre-line; line-height: 1.6;">
              <?= htmlspecialchars($consultationTopics, ENT_QUOTES, 'UTF-8') ?>
            </div>
          <?php else: ?>
            <p class="text-muted small mb-0">Open for professional networking, project consultations, and career syncs.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Contact & Working Info -->
      <div class="card rounded-3 shadow-sm border-0 bg-white">
        <div class="card-body p-4">
          <h6 class="fw-bold text-dark mb-3 small text-uppercase" style="letter-spacing: 0.04em;">Direct Coordinates</h6>
          <div class="small text-secondary">
            <div class="mb-2"><i class="bi bi-envelope text-primary me-2"></i><?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="mb-2"><i class="bi bi-telephone text-primary me-2"></i>+63 <?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></div>
            <div><i class="bi bi-stopwatch text-primary me-2"></i><?= (int)$slotDuration ?> min default slots (adjustable per format)</div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN: CAL.COM SCHEDULING ENGINE -->
    <div class="col-12 col-lg-7" id="schedulerCard">
      <div class="card rounded-3 shadow-sm border-0 bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0 fw-bold text-dark">Schedule an Appointment</h5>
            <small class="text-muted">Select format, platform, and an available time slot</small>
          </div>
          <span class="badge bg-primary text-white px-3 py-2 rounded-pill">1:1 Session</span>
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

            <!-- Step 1: Select Format Cards -->
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
                    <div class="small text-muted" style="font-size: 0.8rem;">Standup, quick Q&A, or intro call.</div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 border rounded-3 event-type-card h-100 border-primary bg-primary-subtle cursor-pointer active" data-type="consultation" data-duration="30" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fs-5">💼</span>
                      <span class="badge bg-primary text-white">30m (Standard)</span>
                    </div>
                    <div class="fw-bold text-dark">Consultation</div>
                    <div class="small text-muted" style="font-size: 0.8rem;">In-depth review, alignment, or advice.</div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 border rounded-3 event-type-card h-100 cursor-pointer" data-type="deep_dive" data-duration="60" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                      <span class="fs-5">🚀</span>
                      <span class="badge bg-light text-secondary border">60m</span>
                    </div>
                    <div class="fw-bold text-dark">Deep Dive</div>
                    <div class="small text-muted" style="font-size: 0.8rem;">Pairing, architecture design, or interview.</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Step 2: Meeting Platform / Mode -->
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

            <!-- Step 3: Date & Interactive Slots -->
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

            <!-- Step 4: Subject, Location & Agenda -->
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
                <a href="<?= routeUrl(isset($_SESSION['userId']) ? '/search' : '/') ?>" class="btn btn-outline-secondary rounded-2 px-4">&larr; Back</a>
                <?php if ($isAuthenticated): ?>
                  <button type="submit" name="submit" id="submitBtn" class="btn btn-primary rounded-2 px-4 shadow-sm fw-bold" disabled>Confirm Booking</button>
                <?php else: ?>
                  <a href="<?= routeUrl('/login') ?>" class="btn btn-primary rounded-2 px-4 shadow-sm fw-bold">Sign In to Book</a>
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
              btn.textContent = `${slot.display} `;
              const reason = document.createElement('small');
              reason.textContent = `(${slot.reason})`;
              btn.appendChild(reason);
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
