<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-10 col-md-8 col-lg-7">

      <!-- Public Booking Link Card -->
      <div class="card rounded-0 shadow-sm border-0 mb-4 bg-light">
        <div class="card-body p-3 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
          <div>
            <span class="text-secondary small fw-semibold d-block">Your Public Booking Link</span>
            <code class="small text-dark" id="shareLinkCode"><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost:8000') . $shareableLink ?></code>
          </div>
          <div class="d-flex gap-2">
            <a href="<?= $shareableLink ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-0">Preview</a>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($_SERVER['REQUEST_SCHEME'] ?? 'http') . '://' . htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'localhost:8000') . $shareableLink ?>'); this.innerText = 'Copied!'; setTimeout(() => this.innerText = 'Copy', 2000);">Copy</button>
          </div>
        </div>
      </div>

      <!-- Profile & Availability Settings Card -->
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom text-center">
          <div class="mb-3">
            <img src="<?= assetUrl($currentProfilePicture) ?>" alt="Profile Picture" class="rounded-circle border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
          </div>
          <h5 class="card-title mb-0 fw-bold text-dark">Profile & Availability Settings</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/profile') ?>" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-3">
              <div class="col-12">
                <h6 class="text-primary fw-semibold small text-uppercase mb-0">Personal Information</h6>
                <hr class="mt-1 mb-2">
              </div>

              <div class="col-12 col-sm-6">
                <label for="firstName" class="form-label text-secondary small fw-semibold">First Name</label>
                <input type="text" name="firstName" id="firstName" value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
              </div>
              <div class="col-12 col-sm-6">
                <label for="middleName" class="form-label text-secondary small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" id="middleName" value="<?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0">
              </div>
              <div class="col-12">
                <label for="lastName" class="form-label text-secondary small fw-semibold">Last Name</label>
                <input type="text" name="lastName" id="lastName" value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
              </div>
              <div class="col-12">
                <label for="contactNumber" class="form-label text-secondary small fw-semibold">Contact Number</label>
                <div class="input-group">
                  <span class="input-group-text rounded-0">+63</span>
                  <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10,11}" minlength="10" maxlength="11" value="<?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
                </div>
              </div>
              <div class="col-12">
                <label for="emailAddress" class="form-label text-secondary small fw-semibold">Email Address</label>
                <input type="email" name="emailAddress" id="emailAddress" value="<?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
              </div>
              <div class="col-12">
                <label for="profilePicture" class="form-label text-secondary small fw-semibold">Change Profile Picture (Optional, PNG/JPG max 10MB)</label>
                <input type="file" name="profilePicture" id="profilePicture" accept="image/png, image/jpeg" class="form-control rounded-0">
              </div>

              <!-- Working Hours & Scheduling Section -->
              <div class="col-12 mt-4">
                <h6 class="text-primary fw-semibold small text-uppercase mb-0">Booking Availability & Working Hours</h6>
                <hr class="mt-1 mb-2">
              </div>

              <div class="col-12 col-sm-4">
                <label for="workStartTime" class="form-label text-secondary small fw-semibold">Day Starts</label>
                <input type="time" name="workStartTime" id="workStartTime" value="<?= htmlspecialchars($workStartTime ?? '09:00', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
              </div>
              <div class="col-12 col-sm-4">
                <label for="workEndTime" class="form-label text-secondary small fw-semibold">Day Ends</label>
                <input type="time" name="workEndTime" id="workEndTime" value="<?= htmlspecialchars($workEndTime ?? '17:00', ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
              </div>
              <div class="col-12 col-sm-4">
                <label for="slotDuration" class="form-label text-secondary small fw-semibold">Slot Duration</label>
                <select name="slotDuration" id="slotDuration" class="form-select rounded-0" required>
                  <option value="15" <?= ($slotDuration == 15) ? 'selected' : '' ?>>15 Minutes</option>
                  <option value="30" <?= ($slotDuration == 30) ? 'selected' : '' ?>>30 Minutes</option>
                  <option value="45" <?= ($slotDuration == 45) ? 'selected' : '' ?>>45 Minutes</option>
                  <option value="60" <?= ($slotDuration == 60) ? 'selected' : '' ?>>60 Minutes</option>
                </select>
              </div>

              <div class="col-12 mt-4">
                <button type="submit" name="update" class="btn btn-primary rounded-0 w-100 py-2">Save Profile & Availability</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
