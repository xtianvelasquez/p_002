<?php
/**
 * @var string $shareableLink
 * @var string $currentProfilePicture
 * @var string $firstName
 * @var string|null $middleName
 * @var string $lastName
 * @var string $contactNumber
 * @var string $emailAddress
 * @var string $workStartTime
 * @var string $workEndTime
 * @var int $slotDuration
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
$shareableLink = $shareableLink ?? '';
$currentProfilePicture = $currentProfilePicture ?? 'images/logo.png';
$firstName = $firstName ?? '';
$middleName = $middleName ?? '';
$lastName = $lastName ?? '';
$contactNumber = $contactNumber ?? '';
$emailAddress = $emailAddress ?? '';
$workStartTime = $workStartTime ?? '09:00';
$workEndTime = $workEndTime ?? '17:00';
$slotDuration = $slotDuration ?? 30;
$headline = $headline ?? '';
$jobTitle = $jobTitle ?? '';
$company = $company ?? '';
$locationCity = $locationCity ?? '';
$bio = $bio ?? '';
$skills = $skills ?? '';
$consultationTopics = $consultationTopics ?? '';
$linkedinUrl = $linkedinUrl ?? '';
$githubUrl = $githubUrl ?? '';
$websiteUrl = $websiteUrl ?? '';
$myFullName = trim($firstName . ' ' . $lastName);
?>
<div class="page-shell profile-shell">
  <div class="page-heading mb-2">
    <div>
      <div class="eyebrow mb-1">Your account</div>
      <h1 class="mb-0 fs-4 fw-bold">Profile & Availability</h1>
    </div>
    <!-- Quick preview & copy booking link -->
    <div class="d-flex align-items-center gap-2">
      <a href="<?= htmlspecialchars($shareableLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-2">
        <i class="bi bi-box-arrow-up-right me-1"></i> Preview Profile
      </a>
      <button type="button" class="btn btn-secondary btn-sm rounded-2" id="copyBookingLink" data-url="<?= htmlspecialchars($shareableLink, ENT_QUOTES, 'UTF-8') ?>">Copy Link</button>
    </div>
  </div>

  <div class="profile-layout">
    <!-- LEFT SIDEBAR -->
    <div class="profile-sidebar">
      <div class="position-relative d-inline-block mb-2 text-center profile-avatar-wrapper">
        <div class="position-absolute" style="top: -8px; left: -8px; z-index: 4; pointer-events: none;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.6" stroke-linecap="round">
            <line x1="3" y1="12" x2="1" y2="12"></line>
            <line x1="6" y1="5" x2="3" y2="2"></line>
            <line x1="12" y1="4" x2="12" y2="1"></line>
          </svg>
        </div>
        <img src="<?= htmlspecialchars(userAvatarUrl($currentProfilePicture, $myFullName, 80), ENT_QUOTES, 'UTF-8') ?>" alt="Profile Picture" class="rounded-circle border border-3 border-white shadow-sm" style="width: 76px; height: 76px; object-fit: cover;" onerror="this.onerror=null; this.src='<?= getInitialsAvatarDataUri($myFullName, 80) ?>';">
        <span class="position-absolute rounded-circle border border-white border-2" style="bottom: 2px; right: 4px; width: 14px; height: 14px; background-color: #22c55e; z-index: 5;" title="Online / Active"></span>
      </div>
      <h2 class="fs-6 fw-bold mb-1 text-center d-flex align-items-center justify-content-center gap-1">
        <?= htmlspecialchars($myFullName, ENT_QUOTES, 'UTF-8') ?>
        <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.95rem;" title="Verified Profile"></i>
      </h2>
      <p class="text-muted small mb-3 text-center"><?= htmlspecialchars($headline ?: ($jobTitle ?: 'Dream • Create • Innovate'), ENT_QUOTES, 'UTF-8') ?></p>
      
      <!-- Nav Tabs -->
      <nav class="nav nav-pills flex-column profile-nav-tabs" role="tablist">
        <button class="nav-link active text-start d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-identity" type="button" role="tab">
          <i class="bi bi-briefcase text-primary"></i> <span>Professional Identity</span>
        </button>
        <button class="nav-link text-start d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-bio" type="button" role="tab">
          <i class="bi bi-person-lines-fill text-primary"></i> <span>About & Topics</span>
        </button>
        <button class="nav-link text-start d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">
          <i class="bi bi-link-45deg text-primary"></i> <span>Web & Social</span>
        </button>
        <button class="nav-link text-start d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-account" type="button" role="tab">
          <i class="bi bi-person-badge text-primary"></i> <span>Account & Photo</span>
        </button>
        <button class="nav-link text-start d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-availability" type="button" role="tab">
          <i class="bi bi-clock-history text-primary"></i> <span>Availability</span>
        </button>
      </nav>
    </div>

    <!-- RIGHT CONTENT PANE -->
    <div class="profile-content">
      <form action="<?= routeUrl('/profile') ?>" method="post" enctype="multipart/form-data" id="profileForm" class="d-flex flex-column h-100">
        <?= csrfField() ?>
        
        <div class="tab-content flex-grow-1 profile-tab-content">
          <!-- TAB 1: IDENTITY -->
          <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
            <h6 class="fw-bold text-dark mb-1">Professional Identity</h6>
            <p class="text-muted small mb-3">This appears at the top of your public profile and helps others discover your expertise.</p>
            <div class="row g-3">
              <div class="col-12">
                <label for="headline" class="form-label text-secondary small fw-semibold">Professional Headline</label>
                <input type="text" name="headline" id="headline" placeholder="e.g. Senior Full-Stack Architect | PostgreSQL & PHP Specialist" value="<?= htmlspecialchars($headline ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12 col-sm-6">
                <label for="jobTitle" class="form-label text-secondary small fw-semibold">Current Job Title / Role</label>
                <input type="text" name="jobTitle" id="jobTitle" placeholder="e.g. Lead Software Engineer" value="<?= htmlspecialchars($jobTitle ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12 col-sm-6">
                <label for="company" class="form-label text-secondary small fw-semibold">Company / Organization</label>
                <input type="text" name="company" id="company" placeholder="e.g. More Than Blue Labs" value="<?= htmlspecialchars($company ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12">
                <label for="locationCity" class="form-label text-secondary small fw-semibold">Location / Base City</label>
                <input type="text" name="locationCity" id="locationCity" placeholder="e.g. Manila, Philippines" value="<?= htmlspecialchars($locationCity ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
            </div>
          </div>

          <!-- TAB 2: BIO & TOPICS -->
          <div class="tab-pane fade" id="tab-bio" role="tabpanel">
            <h6 class="fw-bold text-dark mb-1">About & Purpose of Meetings</h6>
            <p class="text-muted small mb-3">Explain your background and what topics people should book time with you for.</p>
            <div class="row g-3">
              <div class="col-12">
                <label for="bio" class="form-label text-secondary small fw-semibold">Professional Bio / About Me</label>
                <textarea name="bio" id="bio" rows="3" class="form-control rounded-2" placeholder="Write a summary about your journey, philosophy, or background..."><?= htmlspecialchars($bio ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>
              <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <label for="skills" class="form-label text-secondary small fw-semibold mb-0">Skills & Expertise (Comma-separated)</label>
                  <span class="badge bg-secondary-subtle text-secondary fw-medium" style="font-size: 0.68rem;">Max 5 badges</span>
                </div>
                <input type="text" name="skills" id="skills" placeholder="e.g. PHP 8, PostgreSQL, Docker, Architecture Design" value="<?= htmlspecialchars($skills ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" maxlength="200">
                <div class="form-text text-muted small" style="font-size: 0.72rem;">Enter up to 5 core competencies separated by commas to keep your profile badge bar concise.</div>
              </div>
              <div class="col-12">
                <label for="consultationTopics" class="form-label text-secondary small fw-semibold">Consultation Focus / "Why Meet With Me"</label>
                <textarea name="consultationTopics" id="consultationTopics" rows="2" class="form-control rounded-2" placeholder="e.g. System Architecture Reviews, Codebase Optimization, Career Coaching..."><?= htmlspecialchars($consultationTopics ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>
            </div>
          </div>

          <!-- TAB 3: SOCIAL -->
          <div class="tab-pane fade" id="tab-social" role="tabpanel">
            <h6 class="fw-bold text-dark mb-1">Web & Social Presence</h6>
            <p class="text-muted small mb-3">Add verified links to build trust with colleagues, recruiters, and clients.</p>
            <div class="row g-3">
              <div class="col-12">
                <label for="linkedinUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-linkedin text-primary me-1"></i> LinkedIn Profile</label>
                <input type="url" name="linkedinUrl" id="linkedinUrl" placeholder="https://linkedin.com/in/username" value="<?= htmlspecialchars($linkedinUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12">
                <label for="githubUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-github text-dark me-1"></i> GitHub Profile</label>
                <input type="url" name="githubUrl" id="githubUrl" placeholder="https://github.com/username" value="<?= htmlspecialchars($githubUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12">
                <label for="websiteUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-globe text-secondary me-1"></i> Portfolio / Website</label>
                <input type="url" name="websiteUrl" id="websiteUrl" placeholder="https://yoursite.com" value="<?= htmlspecialchars($websiteUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
            </div>
          </div>

          <!-- TAB 4: ACCOUNT -->
          <div class="tab-pane fade" id="tab-account" role="tabpanel">
            <h6 class="fw-bold text-dark mb-1">Account Details & Avatar</h6>
            <p class="text-muted small mb-3">Manage your name, contact information, and account image.</p>
            <div class="row g-3">
              <div class="col-12 col-sm-6">
                <label for="firstName" class="form-label text-secondary small fw-semibold">First Name</label>
                <input type="text" name="firstName" id="firstName" value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="middleName" class="form-label text-secondary small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" id="middleName" value="<?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12 col-sm-6">
                <label for="lastName" class="form-label text-secondary small fw-semibold">Last Name</label>
                <input type="text" name="lastName" id="lastName" value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="contactNumber" class="form-label text-secondary small fw-semibold">Contact Number</label>
                <div class="input-group">
                  <span class="input-group-text rounded-start-2">+63</span>
                  <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10,11}" minlength="10" maxlength="11" value="<?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-end-2" required>
                </div>
              </div>
              <div class="col-12 col-sm-6">
                <label for="emailAddress" class="form-label text-secondary small fw-semibold">Email Address</label>
                <input type="email" name="emailAddress" id="emailAddress" value="<?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="profilePicture" class="form-label text-secondary small fw-semibold">Change Avatar (PNG/JPG)</label>
                <input type="file" name="profilePicture" id="profilePicture" accept="image/png, image/jpeg" class="form-control rounded-2">
              </div>
            </div>
          </div>

          <!-- TAB 5: AVAILABILITY -->
          <div class="tab-pane fade" id="tab-availability" role="tabpanel">
            <h6 class="fw-bold text-dark mb-1">Availability & Working Hours</h6>
            <p class="text-muted small mb-3">Define your daily window and preferred slot increments for booking.</p>
            <div class="row g-3">
              <div class="col-12 col-sm-6">
                <label for="workStartTime" class="form-label text-secondary small fw-semibold">Daily Start Time</label>
                <input type="time" name="workStartTime" id="workStartTime" value="<?= htmlspecialchars($workStartTime, ENT_QUOTES, 'UTF-8') ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="workEndTime" class="form-label text-secondary small fw-semibold">Daily End Time</label>
                <input type="time" name="workEndTime" id="workEndTime" value="<?= htmlspecialchars($workEndTime, ENT_QUOTES, 'UTF-8') ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12">
                <label for="slotDuration" class="form-label text-secondary small fw-semibold">Default Slot Duration (Minutes)</label>
                <select name="slotDuration" id="slotDuration" class="form-select rounded-2" required>
                  <option value="15" <?= ((int)$slotDuration === 15) ? 'selected' : '' ?>>15 minutes (Quick Sync)</option>
                  <option value="30" <?= ((int)$slotDuration === 30) ? 'selected' : '' ?>>30 minutes (Standard Meeting)</option>
                  <option value="45" <?= ((int)$slotDuration === 45) ? 'selected' : '' ?>>45 minutes (Extended Session)</option>
                  <option value="60" <?= ((int)$slotDuration === 60) ? 'selected' : '' ?>>60 minutes (Deep Dive)</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Sticky Submit Footer in Content Area -->
        <div class="pt-3 border-top mt-auto d-flex flex-wrap justify-content-between align-items-center gap-2">
          <span class="text-muted small"><i class="bi bi-shield-check text-success me-1"></i> Changes apply across your booking link</span>
          <button type="submit" name="update" class="btn btn-primary rounded-2 px-4 py-2 fw-semibold shadow-sm">
            <i class="bi bi-check2-circle me-1"></i> Save Profile & Availability
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  document.getElementById('copyBookingLink')?.addEventListener('click', async function () {
    const rawUrl = this.dataset.url || '';
    const link = new URL(rawUrl, window.location.origin).href;
    try {
      await navigator.clipboard.writeText(link);
      this.textContent = 'Copied!';
      setTimeout(() => { this.textContent = 'Copy Link'; }, 2000);
    } catch (error) {
      this.textContent = 'Copy failed';
    }
  });
</script>
