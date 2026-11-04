<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-8 col-md-6 col-lg-5">
      <div class="card rounded-0 shadow-sm border-0 text-center">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title text-danger mb-0 fw-bold">Delete Appointment</h5>
        </div>
        <div class="card-body p-4">
          <p class="text-secondary mb-4">Are you sure you want to permanently delete this received appointment? This action cannot be undone.</p>
          <div class="d-flex justify-content-center gap-3">
            <a href="<?= routeUrl('/received') ?>" class="btn btn-outline-secondary rounded-0 px-4">Cancel</a>
            <form action="<?= routeUrl('/appointment/delete') ?>" method="post">
              <?= csrfField() ?>
              <input type="hidden" name="id" value="<?= htmlspecialchars($appointmentId, ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn btn-danger rounded-0 px-4">Yes, Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
