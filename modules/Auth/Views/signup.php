<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-10 col-md-8 col-lg-6">
      <div class="card rounded-3 shadow-sm border-0 bg-white">
        <div class="card-header bg-white py-4 border-bottom text-center">
          <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary mb-2" style="width: 48px; height: 48px;">
            <i class="bi bi-person-plus fs-4"></i>
          </div>
          <h4 class="card-title mb-1 fw-bold text-dark">Create Your Account</h4>
          <p class="text-secondary small mb-0">Join More Than Blue for smart appointment scheduling</p>
        </div>
        <div class="card-body p-4 p-md-4">
          <!-- Google OAuth Signup Button -->
          <a href="<?= routeUrl('/auth/google') ?>" class="btn btn-outline-dark w-100 py-2 d-flex align-items-center justify-content-center gap-2 mb-3 rounded-2 shadow-sm border bg-white text-dark hover-shadow" style="transition: all 0.2s ease;">
            <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
              <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z" fill="#4285F4"/>
              <path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z" fill="#34A853"/>
              <path d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.173 0 7.548 0 9s.347 2.827.957 4.039l3.007-2.332z" fill="#FBBC05"/>
              <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z" fill="#EA4335"/>
            </svg>
            <span class="fw-semibold small">Sign up with Google</span>
          </a>

          <div class="position-relative text-center my-3">
            <hr class="text-muted opacity-25">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-2 text-muted small">or register with email</span>
          </div>

          <form action="<?= routeUrl('/signup') ?>" method="post" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="row g-3">
              <div class="col-12 col-sm-6">
                <label for="firstName" class="form-label text-secondary small fw-semibold">First Name</label>
                <input type="text" name="firstName" id="firstName" class="form-control rounded-2" required autofocus>
              </div>
              <div class="col-12 col-sm-6">
                <label for="middleName" class="form-label text-secondary small fw-semibold">Middle Name</label>
                <input type="text" name="middleName" id="middleName" class="form-control rounded-2">
              </div>
              <div class="col-12">
                <label for="lastName" class="form-label text-secondary small fw-semibold">Last Name</label>
                <input type="text" name="lastName" id="lastName" class="form-control rounded-2" required>
              </div>
              <div class="col-12">
                <label for="contactNumber" class="form-label text-secondary small fw-semibold">Contact Number</label>
                <div class="input-group">
                  <span class="input-group-text rounded-start-2">+63</span>
                  <input type="text" name="contactNumber" id="contactNumber" pattern="\d{10,11}" minlength="10" maxlength="11" placeholder="9123456789" class="form-control rounded-end-2" required>
                </div>
              </div>
              <div class="col-12">
                <label for="emailAddress" class="form-label text-secondary small fw-semibold">Email Address</label>
                <input type="email" name="emailAddress" id="emailAddress" class="form-control rounded-2" placeholder="name@example.com" required>
              </div>
              <div class="col-12">
                <label for="password" class="form-label text-secondary small fw-semibold">Password (min 6 characters)</label>
                <input type="password" name="password" id="password" minlength="6" class="form-control rounded-2" required>
              </div>
              <div class="col-12">
                <label for="profilePicture" class="form-label text-secondary small fw-semibold">Profile Picture (Optional, PNG/JPG max 10MB)</label>
                <input type="file" name="profilePicture" id="profilePicture" accept="image/png, image/jpeg" class="form-control rounded-2">
              </div>
              <div class="col-12 mt-4">
                <button type="submit" name="signup" class="btn btn-primary rounded-2 w-100 py-2 fw-semibold shadow-sm">Create Account</button>
              </div>
            </div>
          </form>
        </div>
        <div class="card-footer bg-light text-center py-3 border-top rounded-bottom-3">
          <span class="text-muted small">Already have an account? </span>
          <a href="<?= routeUrl('/login') ?>" class="text-decoration-none small fw-semibold">Sign In</a>
        </div>
      </div>
    </div>
  </div>
</div>
