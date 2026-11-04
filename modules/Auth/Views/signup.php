<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-10 col-md-8 col-lg-6">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-center text-dark">Create Your Account</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/signup') ?>" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-3">
              <div class="col-12 col-sm-6">
                <label for="firstName" class="form-label text-secondary small fw-semibold">First Name</label>
                <input type="text" name="firstName" id="firstName" class="form-control rounded-0" required autofocus>
              </div>
              <div class="col-12 col-sm-6">
                <label for="middleName" class="form-label text-secondary small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" id="middleName" class="form-control rounded-0">
              </div>
              <div class="col-12">
                <label for="lastName" class="form-label text-secondary small fw-semibold">Last Name</label>
                <input type="text" name="lastName" id="lastName" class="form-control rounded-0" required>
              </div>
              <div class="col-12">
                <label for="contactNumber" class="form-label text-secondary small fw-semibold">Contact Number</label>
                <div class="input-group">
                  <span class="input-group-text rounded-0">+63</span>
                  <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10}" minlength="10" maxlength="10" placeholder="9123456789" class="form-control rounded-0" required>
                </div>
              </div>
              <div class="col-12">
                <label for="emailAddress" class="form-label text-secondary small fw-semibold">Email Address</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control rounded-0" placeholder="name@example.com" required>
              </div>
              <div class="col-12">
                <label for="password" class="form-label text-secondary small fw-semibold">Password (min 6 characters)</label>
                <input type="password" name="password" id="password" minlength="6" class="form-control rounded-0" required>
              </div>
              <div class="col-12">
                <label for="profilePicture" class="form-label text-secondary small fw-semibold">Profile Picture (JPG/PNG, max 10MB)</label>
                <input type="file" name="profilePicture" id="profilePicture" accept="image/png, image/jpeg" class="form-control rounded-0" required>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" name="signup" class="btn btn-primary rounded-0 w-100 py-2">Create Account</button>
              </div>
            </div>
          </form>
        </div>
        <div class="card-footer bg-light text-center py-3 border-0">
          <span class="text-muted small">Already have an account? </span>
          <a href="<?= routeUrl('/login') ?>" class="text-decoration-none small fw-semibold">Sign In</a>
        </div>
      </div>
    </div>
  </div>
</div>
