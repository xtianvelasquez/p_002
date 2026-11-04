<div class="container-xl my-4">
  <div class="row justify-content-center align-items-start g-4">
    <!-- User Card -->
    <div class="col-11 col-md-4 col-lg-3">
      <div class="card rounded-0 shadow-sm border-0 text-center p-3">
        <img src="<?= assetUrl($profilePicture) ?>" alt="Profile Picture" class="img-fluid rounded mx-auto mb-3" style="max-height: 160px; object-fit: cover;">
        <h5 class="fw-bold mb-1"><?= htmlspecialchars(trim($firstName . ' ' . $lastName), ENT_QUOTES, 'UTF-8') ?></h5>
        <?php if (!empty($middleName)): ?>
          <p class="text-muted small mb-2"><?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <hr class="my-2">
        <div class="text-start small text-secondary">
          <p class="mb-1"><strong>Phone:</strong> +63 <?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></p>
          <p class="mb-0"><strong>Email:</strong> <?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      </div>
    </div>

    <!-- Booking Form Card -->
    <div class="col-11 col-md-8 col-lg-6">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">Schedule an Appointment</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/appointment/book') ?>" method="post">
            <?= csrfField() ?>
            <input type="hidden" name="receiverId" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

            <div class="row g-3">
              <div class="col-12 col-sm-6">
                <label for="appointmentDate" class="form-label text-secondary small fw-semibold">Date</label>
                <input type="date" name="appointmentDate" id="appointmentDate" min="<?= date('Y-m-d') ?>" class="form-control rounded-0" required>
              </div>

              <div class="col-12 col-sm-6">
                <label for="appointmentTime" class="form-label text-secondary small fw-semibold">Time</label>
                <input type="time" name="appointmentTime" id="appointmentTime" class="form-control rounded-0" required>
              </div>

              <div class="col-12">
                <label for="eventName" class="form-label text-secondary small fw-semibold">Event Name / Subject</label>
                <input type="text" name="eventName" id="eventName" placeholder="e.g. Project Consultation" class="form-control rounded-0" required>
              </div>

              <div class="col-12">
                <label for="location" class="form-label text-secondary small fw-semibold">Location / Meeting Link</label>
                <input type="text" name="location" id="location" placeholder="e.g. Google Meet, Zoom, Office Room 3" class="form-control rounded-0" required>
              </div>

              <div class="col-12 mt-4 d-flex justify-content-between">
                <a href="<?= routeUrl('/search') ?>" class="btn btn-outline-secondary rounded-0 px-4">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary rounded-0 px-4">Submit Appointment</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
