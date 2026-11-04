<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-7 col-lg-6">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom text-center">
          <div class="mb-3">
            <img src="<?= assetUrl($currentProfilePicture) ?>" alt="Profile Picture" class="rounded-circle border shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">
          </div>
          <h5 class="card-title mb-0 fw-bold text-dark">Manage Profile</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/profile') ?>" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-3">
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
                  <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10}" minlength="10" maxlength="10" value="<?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8'); ?>" class="form-control rounded-0" required>
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
              <div class="col-12 mt-4">
                <button type="submit" name="update" class="btn btn-primary rounded-0 w-100 py-2">Save Profile Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
