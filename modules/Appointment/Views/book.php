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
 * @var bool $isOwner
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
$isOwner = !empty($isOwner);

$fullName = trim($firstName . ' ' . $lastName);
$headline = trim($headline ?? '');
$jobTitle = trim($jobTitle ?? '');
$company = trim($company ?? '');
$locationCity = trim($locationCity ?? '');
$bio = trim($bio ?? '');
$skillsRaw = trim($skills ?? '');
$allSkillTags = array_filter(array_map('trim', explode(',', $skillsRaw)));
$maxHeaderBadges = 5;
$skillTags = array_slice($allSkillTags, 0, $maxHeaderBadges);
$extraBadgesCount = count($allSkillTags) - count($skillTags);
$consultationTopics = trim($consultationTopics ?? '');
$linkedinUrl = trim($linkedinUrl ?? '');
$githubUrl = trim($githubUrl ?? '');
$websiteUrl = trim($websiteUrl ?? '');
?>
<div class="container-xl my-4">
  <?php if ($isOwner): ?>
    <!-- Owner Preview Banner -->
    <div class="alert alert-primary border-0 shadow-sm rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 bg-primary-subtle text-primary-emphasis">
      <div class="d-flex align-items-center gap-3">
        <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
          <i class="bi bi-eye-fill fs-5 text-primary"></i>
        </div>
        <div>
          <div class="fw-bold">Profile Preview Mode</div>
          <div class="small text-secondary">This is how your profile and availability appear to clients and visitors. Self-booking is disabled.</div>
        </div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <a href="<?= routeUrl('/profile') ?>" class="btn btn-primary btn-sm rounded-2 fw-semibold">
          <i class="bi bi-pencil-square me-1"></i> Back to Profile Settings
        </a>
      </div>
    </div>
  <?php endif; ?>

  <!-- 1. LINKEDIN-STYLE PROFILE HERO CARD -->
  <div class="card rounded-3 shadow-sm border-0 overflow-hidden mb-4 bg-white profile-hero-card">
    <!-- Banner Header with More Than Blue Branding & Heart Wave Illustration -->
    <div class="position-relative profile-hero-banner" style="min-height: 180px; background: linear-gradient(90deg, #1d4ed8 0%, #2563eb 45%, #0284c7 80%, #0d9488 100%);">
      <!-- MB Logo Branding on Upper Left (matches reference banner) -->
      <div class="position-absolute top-0 start-0 m-3 m-md-4 d-flex align-items-center gap-2 text-white" style="z-index: 2;">
        <span class="fw-bold px-2 py-0.5 rounded bg-white text-primary fs-6 shadow-sm" style="letter-spacing: -0.05em; font-weight: 800;">MB</span>
        <span class="border-start border-white border-opacity-50 ps-2 small text-uppercase fw-bold text-white text-shadow-sm" style="letter-spacing: 0.12em; font-size: 0.72rem;">More Than Blue</span>
      </div>

      <!-- Continuous Line Art Heart Watermark on Upper Right (matches reference banner) -->
      <div class="position-absolute end-0 top-0 h-100 pe-3 pe-md-5 d-flex align-items-center pointer-events-none" style="opacity: 0.35; z-index: 1;">
        <svg width="190" height="130" viewBox="0 0 160 100" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M 10 70 C 40 85, 70 85, 95 65 C 115 50, 130 30, 140 18 C 148 7, 160 14, 153 25 C 145 38, 120 70, 95 82 C 75 90, 45 75, 55 55 C 65 35, 90 28, 105 45 C 118 60, 130 75, 155 70" />
        </svg>
      </div>

      <!-- Gentle Curved Wave Divider at Bottom (matches reference banner) -->
      <div class="position-absolute bottom-0 start-0 w-100 overflow-hidden" style="line-height: 0; pointer-events: none; z-index: 2;">
        <svg viewBox="0 0 1200 60" preserveAspectRatio="none" style="width: 100%; height: 35px; display: block;">
          <path d="M0,0 C150,45 350,-20 500,25 C650,60 900,10 1200,35 L1200,60 L0,60 Z" fill="#ffffff"></path>
        </svg>
      </div>
    </div>

    <!-- Profile Header Content -->
    <div class="card-body px-3 px-md-4 pb-4 pt-0">
      <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
        <!-- Avatar & Core Info -->
        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 gap-md-4 position-relative" style="z-index: 3;">
          
          <!-- Avatar Frame with Sparkle Accents & Online Dot (only avatar peeks up into banner) -->
          <div class="position-relative profile-avatar-wrapper flex-shrink-0" style="margin-top: -65px;">
            <!-- Sparkle Action Accents on Top-Left (matches reference) -->
            <div class="position-absolute profile-avatar-sparkle" style="top: -12px; left: -10px; z-index: 4; pointer-events: none;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round">
                <line x1="3" y1="12" x2="1" y2="12"></line>
                <line x1="6" y1="5" x2="3" y2="2"></line>
                <line x1="12" y1="4" x2="12" y2="1"></line>
              </svg>
            </div>

            <img src="<?= htmlspecialchars(userAvatarUrl($profilePicture, $fullName, 140), ENT_QUOTES, 'UTF-8') ?>" 
                 alt="<?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>" 
                 class="rounded-circle border border-4 border-white shadow bg-white profile-avatar-img" 
                 style="width: 130px; height: 130px; object-fit: cover; display: block;" 
                 onerror="this.onerror=null; this.src='<?= getInitialsAvatarDataUri($fullName, 140) ?>';">
            
            <!-- Online status indicator dot on bottom right (matches reference) -->
            <span class="position-absolute profile-online-dot rounded-circle border border-white border-2" 
                  style="bottom: 4px; right: 4px; width: 22px; height: 22px; background-color: #22c55e; z-index: 5;" 
                  title="Available for Appointments"></span>
          </div>

          <!-- Text Information (strictly positioned below the blue wave in the white area for pristine legibility) -->
          <div class="text-center text-sm-start pt-2 pt-sm-3">
            <!-- Name & Verified Check -->
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-2">
              <h2 class="fw-bold mb-0 text-dark fs-4 d-inline-flex align-items-center gap-1">
                <?= htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') ?>
                <i class="bi bi-patch-check-fill text-primary" style="font-size: 1.15rem;" title="Verified Professional"></i>
              </h2>
              <?php if (!empty($middleName)): ?>
                <span class="text-muted small">(<?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8') ?>)</span>
              <?php endif; ?>
            </div>

            <!-- Headline / Slogan -->
            <p class="text-secondary fw-medium small mb-2 mt-1">
              <?= htmlspecialchars($headline ?: 'Dream • Create • Innovate', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <!-- Pill Badges (Skills & Highlights, max 5) -->
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-sm-start gap-1 profile-pills-bar">
              <?php if (!empty($skillTags)): ?>
                <?php foreach ($skillTags as $tag): ?>
                  <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill fw-medium" style="font-size: 0.74rem;">
                    <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                  </span>
                <?php endforeach; ?>
                <?php if ($extraBadgesCount > 0): ?>
                  <span class="badge bg-light text-muted border px-2 py-1 rounded-pill fw-medium" style="font-size: 0.74rem;" title="<?= $extraBadgesCount ?> more skills">
                    +<?= $extraBadgesCount ?> more
                  </span>
                <?php endif; ?>
              <?php else: ?>
                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill fw-medium" style="font-size: 0.74rem;">Professional Consultation</span>
                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill fw-medium" style="font-size: 0.74rem;">Available for Booking</span>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Action Buttons on Right (Settings & Go to Bookings / Book Time) -->
        <div class="align-self-center align-self-md-start mt-3 mt-md-0 pt-md-3 d-flex flex-wrap align-items-center gap-2" style="position: relative; z-index: 3;">
          <?php if (!empty($linkedinUrl)): ?>
            <a href="<?= htmlspecialchars($linkedinUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-pill px-3" title="LinkedIn Profile">
              <i class="bi bi-linkedin"></i>
            </a>
          <?php endif; ?>

          <?php if (!empty($githubUrl)): ?>
            <a href="<?= htmlspecialchars($githubUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm rounded-pill px-3" title="GitHub Profile">
              <i class="bi bi-github"></i>
            </a>
          <?php endif; ?>

          <?php if ($isOwner): ?>
            <a href="<?= routeUrl('/profile') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1">
              <i class="bi bi-gear-fill"></i> Settings
            </a>
            <a href="<?= routeUrl('/appointments') ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm">
              <i class="bi bi-calendar3"></i> Go to Bookings &rarr;
            </a>
          <?php else: ?>
            <a href="#schedulerCard" class="btn btn-primary btn-sm rounded-pill px-4 py-1.5 shadow-sm fw-bold d-inline-flex align-items-center gap-1">
              <i class="bi bi-calendar-plus me-1"></i> Book Time
            </a>
          <?php endif; ?>
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
                <a href="<?= routeUrl(isset($_SESSION['userId']) ? ($isOwner ? '/profile' : '/search') : '/') ?>" class="btn btn-outline-secondary rounded-2 px-4">&larr; <?= $isOwner ? 'Back to Settings' : 'Back' ?></a>
                <?php if ($isOwner): ?>
                  <button type="button" class="btn btn-secondary rounded-2 px-4 shadow-sm fw-bold disabled" disabled title="Self-booking is disabled in preview mode">Self-Booking Disabled (Preview Mode)</button>
                <?php elseif ($isAuthenticated): ?>
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
