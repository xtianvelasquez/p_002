<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-center text-dark">Sign In to Your Account</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/login') ?>" method="post">
            <?= csrfField() ?>
            <div class="mb-3">
              <label for="emailAddress" class="form-label text-secondary small fw-semibold">Email Address</label>
              <input type="email" name="emailAddress" id="emailAddress" class="form-control rounded-0" placeholder="name@example.com" required autofocus>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label text-secondary small fw-semibold">Password</label>
              <input type="password" name="password" id="password" class="form-control rounded-0" placeholder="••••••••" required>
            </div>
            <div class="d-grid mt-4">
              <button type="submit" name="login" class="btn btn-primary rounded-0 py-2">Sign In</button>
            </div>
          </form>
        </div>
        <div class="card-footer bg-light text-center py-3 border-0">
          <span class="text-muted small">Don't have an account? </span>
          <a href="<?= routeUrl('/signup') ?>" class="text-decoration-none small fw-semibold">Sign Up</a>
        </div>
      </div>
    </div>
  </div>
</div>
