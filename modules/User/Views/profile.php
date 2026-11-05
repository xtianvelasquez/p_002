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
?>
<div class="page-shell">
  <div class="page-heading"><div><div class="eyebrow mb-2">Your account</div><h1>Profile settings</h1><p>Keep your professional profile and booking hours up to date.</p></div></div>
  <div class="row justify-content-center">
    <div class="col-12 col-md-11 col-lg-10">

      <!-- Public Booking Link Card -->
      <div class="card rounded-3 shadow-sm border-0 mb-4 bg-white p-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Public Profile & Booking Link</span>
              <span class="text-success small"><i class="bi bi-broadcast me-1"></i>Live</span>
            </div>
            <code class="small text-dark fw-semibold" id="shareLinkCode"><?= htmlspecialchars($shareableLink, ENT_QUOTES, 'UTF-8') ?></code>
          </div>
          <div class="d-flex gap-2">
            <a href="<?= htmlspecialchars($shareableLink, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm rounded-2">
              <i class="bi bi-box-arrow-up-right me-1"></i> Preview Profile
            </a>
            <button type="button" class="btn btn-secondary btn-sm rounded-2" id="copyBookingLink">Copy Link</button>
          </div>
        </div>
      </div>

      <!-- Profile & Availability Settings Card -->
      <div class="card rounded-3 shadow-sm border-0">
        <?php 
          $myFullName = trim($firstName . ' ' . $lastName);
        ?>
        <div class="card-header bg-white py-4 border-bottom text-center">
          <div class="position-relative d-inline-block mb-3">
            <img src="<?= htmlspecialchars(userAvatarUrl($currentProfilePicture, $myFullName, 110), ENT_QUOTES, 'UTF-8') ?>" alt="Profile Picture" class="rounded-circle border shadow-sm" style="width: 110px; height: 110px; object-fit: cover;" onerror="this.onerror=null; this.src='<?= getInitialsAvatarDataUri($myFullName, 110) ?>';">
          </div>
          <h4 class="card-title mb-1 fw-bold text-dark"><?= htmlspecialchars($myFullName, ENT_QUOTES, 'UTF-8') ?></h4>
          <p class="text-muted small mb-0">Manage your LinkedIn-style professional persona and booking preferences</p>
        </div>
        <div class="card-body p-4 p-md-5">
          <form action="<?= routeUrl('/profile') ?>" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-4">

              <!-- SECTION 1: PROFESSIONAL IDENTITY (LINKEDIN STYLE) -->
              <div class="col-12">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;"><i class="bi bi-briefcase"></i></div>
                  <h6 class="text-dark fw-bold mb-0">Professional Identity</h6>
                </div>
                <p class="text-secondary small mb-3">This appears at the top of your public profile and helps others discover your expertise.</p>
              </div>

              <div class="col-12">
                <label for="headline" class="form-label text-secondary small fw-semibold">Professional Headline</label>
                <input type="text" name="headline" id="headline" placeholder="e.g. Senior Full-Stack Architect | PostgreSQL & PHP Specialist | Speaker" value="<?= htmlspecialchars($headline ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
                <div class="form-text small">A concise summary of what you do, just like your LinkedIn headline.</div>
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

              <!-- SECTION 2: BIO & CONSULTATION TOPICS -->
              <div class="col-12 mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;"><i class="bi bi-person-lines-fill"></i></div>
                  <h6 class="text-dark fw-bold mb-0">About & Purpose of Meetings</h6>
                </div>
                <p class="text-secondary small mb-3">Explain who you are and clearly communicate why people should book time with you.</p>
              </div>

              <div class="col-12">
                <label for="bio" class="form-label text-secondary small fw-semibold">Professional Bio / About Me</label>
                <textarea name="bio" id="bio" rows="4" class="form-control rounded-2" placeholder="Write a summary about your journey, engineering philosophy, or leadership background..."><?= htmlspecialchars($bio ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>

              <div class="col-12">
                <label for="skills" class="form-label text-secondary small fw-semibold">Skills & Expertise (Comma-separated)</label>
                <input type="text" name="skills" id="skills" placeholder="e.g. PHP 8, PostgreSQL, Docker, Architecture Design, Mentorship, Microservices" value="<?= htmlspecialchars($skills ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
                <div class="form-text small">These will render as interactive skill badges on your profile and search cards.</div>
              </div>

              <div class="col-12">
                <label for="consultationTopics" class="form-label text-secondary small fw-semibold">Consultation Focus / "Why Meet With Me"</label>
                <textarea name="consultationTopics" id="consultationTopics" rows="3" class="form-control rounded-2" placeholder="e.g. System Architecture Reviews, Codebase Optimization, Career Coaching, 1:1 Mentoring..."><?= htmlspecialchars($consultationTopics ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="form-text small">Helps bookers see if their request is a strong mutual fit.</div>
              </div>

              <!-- SECTION 3: SOCIAL & PORTFOLIO LINKS -->
              <div class="col-12 mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;"><i class="bi bi-link-45deg"></i></div>
                  <h6 class="text-dark fw-bold mb-0">Web & Social Presence</h6>
                </div>
                <p class="text-secondary small mb-3">Add verified links to build trust with recruiters and clients.</p>
              </div>

              <div class="col-12 col-md-4">
                <label for="linkedinUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-linkedin text-primary me-1"></i> LinkedIn Profile</label>
                <input type="url" name="linkedinUrl" id="linkedinUrl" placeholder="https://linkedin.com/in/username" value="<?= htmlspecialchars($linkedinUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>

              <div class="col-12 col-md-4">
                <label for="githubUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-github text-dark me-1"></i> GitHub Profile</label>
                <input type="url" name="githubUrl" id="githubUrl" placeholder="https://github.com/username" value="<?= htmlspecialchars($githubUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>

              <div class="col-12 col-md-4">
                <label for="websiteUrl" class="form-label text-secondary small fw-semibold"><i class="bi bi-globe text-secondary me-1"></i> Portfolio / Website</label>
                <input type="url" name="websiteUrl" id="websiteUrl" placeholder="https://yoursite.com" value="<?= htmlspecialchars($websiteUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>

              <!-- SECTION 4: BASIC ACCOUNT DETAILS & PHOTO -->
              <div class="col-12 mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;"><i class="bi bi-person-badge"></i></div>
                  <h6 class="text-dark fw-bold mb-0">Account Details</h6>
                </div>
              </div>

              <div class="col-12 col-sm-6">
                <label for="firstName" class="form-label text-secondary small fw-semibold">First Name</label>
                <input type="text" name="firstName" id="firstName" value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="middleName" class="form-label text-secondary small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" id="middleName" value="<?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2">
              </div>
              <div class="col-12">
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
              <div class="col-12">
                <label for="profilePicture" class="form-label text-secondary small fw-semibold">Change Avatar (PNG/JPG up to 10MB)</label>
                <input type="file" name="profilePicture" id="profilePicture" accept="image/png, image/jpeg" class="form-control rounded-2">
              </div>

              <!-- SECTION 5: WORKING HOURS & AVAILABILITY -->
              <div class="col-12 mt-4 pt-3 border-top">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem;"><i class="bi bi-clock-history"></i></div>
                  <h6 class="text-dark fw-bold mb-0">Booking Availability & Working Hours</h6>
                </div>
                <p class="text-secondary small mb-3">Defines the daily window and slot intervals available to book on your schedule.</p>
              </div>

              <div class="col-12 col-sm-4">
                <label for="workStartTime" class="form-label text-secondary small fw-semibold">Day Starts</label>
                <input type="time" name="workStartTime" id="workStartTime" value="<?= htmlspecialchars($workStartTime ?? '09:00', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-4">
                <label for="workEndTime" class="form-label text-secondary small fw-semibold">Day Ends</label>
                <input type="time" name="workEndTime" id="workEndTime" value="<?= htmlspecialchars($workEndTime ?? '17:00', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-2" required>
              </div>
              <div class="col-12 col-sm-4">
                <label for="slotDuration" class="form-label text-secondary small fw-semibold">Slot Duration</label>
                <select name="slotDuration" id="slotDuration" class="form-select rounded-2" required>
                  <option value="15" <?= ($slotDuration == 15) ? 'selected' : '' ?>>15 Minutes</option>
                  <option value="30" <?= ($slotDuration == 30) ? 'selected' : '' ?>>30 Minutes</option>
                  <option value="45" <?= ($slotDuration == 45) ? 'selected' : '' ?>>45 Minutes</option>
                  <option value="60" <?= ($slotDuration == 60) ? 'selected' : '' ?>>60 Minutes</option>
                </select>
              </div>

              <div class="col-12 mt-4 pt-3 border-top">
                <button type="submit" name="update" class="btn btn-primary rounded-2 w-100 py-3 fw-bold shadow-sm">
                  <i class="bi bi-check2-circle me-1"></i> Save Profile & Availability
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById('copyBookingLink')?.addEventListener('click', async function () {
    const link = new URL(document.getElementById('shareLinkCode').textContent, window.location.origin).href;
    try {
      await navigator.clipboard.writeText(link);
      this.textContent = 'Copied!';
      setTimeout(() => { this.textContent = 'Copy Link'; }, 2000);
    } catch (error) {
      this.textContent = 'Copy failed';
    }
  });
</script>
