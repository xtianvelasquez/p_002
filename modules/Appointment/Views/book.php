<div class="container-xl my-4">
  <div class="row justify-content-center align-items-start g-4">
    <!-- User Card -->
    <div class="col-11 col-md-4 col-lg-3">
      <div class="card rounded-0 shadow-sm border-0 text-center p-3 mb-3">
        <img src="<?= assetUrl($profilePicture) ?>" alt="Profile Picture" class="img-fluid rounded mx-auto mb-3" style="max-height: 160px; object-fit: cover;">
        <h5 class="fw-bold mb-1"><?= htmlspecialchars(trim($firstName . ' ' . $lastName), ENT_QUOTES, 'UTF-8') ?></h5>
        <?php if (!empty($middleName)): ?>
          <p class="text-muted small mb-2"><?= htmlspecialchars($middleName, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <hr class="my-2">
        <div class="text-start small text-secondary">
          <p class="mb-1"><strong>Phone:</strong> +63 <?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?></p>
          <p class="mb-2"><strong>Email:</strong> <?= htmlspecialchars($emailAddress, ENT_QUOTES, 'UTF-8') ?></p>
          <div class="bg-light p-2 border rounded-0">
            <span class="d-block fw-semibold text-dark">Working Hours:</span>
            <span><?= date('g:i A', strtotime('2000-01-01 ' . $workStartTime)) ?> – <?= date('g:i A', strtotime('2000-01-01 ' . $workEndTime)) ?></span>
            <small class="d-block text-muted mt-1">(<?= (int)$slotDuration ?> min meeting slots)</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Booking Form Card -->
    <div class="col-11 col-md-8 col-lg-7">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">Select Date & Time Slot</h5>
        </div>
        <div class="card-body p-4">
          <?php if (!$isAuthenticated): ?>
            <div class="alert alert-info rounded-0 d-flex justify-content-between align-items-center mb-4">
              <span>You are viewing a public schedule. Please sign in to book an appointment.</span>
              <a href="<?= routeUrl('/login') ?>" class="btn btn-primary btn-sm rounded-0">Sign In</a>
            </div>
          <?php endif; ?>

          <form action="<?= routeUrl('/appointment/book') ?>" method="post" id="bookingForm">
            <?= csrfField() ?>
            <input type="hidden" name="receiverId" id="receiverId" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="appointmentTime" id="selectedTimeInput" required>

            <div class="row g-3">
              <!-- Date Picker -->
              <div class="col-12 col-sm-6">
                <label for="appointmentDate" class="form-label text-secondary small fw-semibold">1. Choose Date</label>
                <input type="date" name="appointmentDate" id="appointmentDate" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($initialDate, ENT_QUOTES, 'UTF-8') ?>" class="form-control rounded-0" required>
              </div>

              <!-- Time Slot Status Text -->
              <div class="col-12 col-sm-6 d-flex align-items-end">
                <div class="small text-secondary mb-2" id="slotStatusText">
                  Selected Time: <strong class="text-primary" id="selectedTimeDisplay">None selected</strong>
                </div>
              </div>

              <!-- Time Slots Grid -->
              <div class="col-12">
                <label class="form-label text-secondary small fw-semibold d-block">2. Choose an Open Time Slot</label>
                <div id="slotsContainer" class="d-flex flex-wrap gap-2 p-3 bg-light border rounded-0" style="max-height: 240px; overflow-y: auto;">
                  <?php if (!empty($initialSlots)): ?>
                    <?php foreach ($initialSlots as $slot): ?>
                      <?php if ($slot['available']): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-0 time-slot-btn" data-time="<?= htmlspecialchars($slot['time'], ENT_QUOTES, 'UTF-8') ?>" data-display="<?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?>">
                          <?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?>
                        </button>
                      <?php else: ?>
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-0 disabled" disabled title="<?= htmlspecialchars($slot['reason'], ENT_QUOTES, 'UTF-8') ?>">
                          <?= htmlspecialchars($slot['display'], ENT_QUOTES, 'UTF-8') ?> <small>(<?= htmlspecialchars($slot['reason'], ENT_QUOTES, 'UTF-8') ?>)</small>
                        </button>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="text-muted small mb-0">No time slots available for this date.</p>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Event Details -->
              <div class="col-12 mt-3">
                <label for="eventName" class="form-label text-secondary small fw-semibold">3. Event Name / Subject</label>
                <input type="text" name="eventName" id="eventName" placeholder="e.g. Portfolio Review or Consultation" class="form-control rounded-0" required>
              </div>

              <div class="col-12">
                <label for="location" class="form-label text-secondary small fw-semibold">4. Location / Virtual Meeting Link</label>
                <input type="text" name="location" id="location" placeholder="e.g. Google Meet, Zoom, Office Room 3" class="form-control rounded-0" required>
              </div>

              <div class="col-12 mt-4 d-flex justify-content-between">
                <a href="<?= routeUrl(isset($_SESSION['userId']) ? '/search' : '/') ?>" class="btn btn-outline-secondary rounded-0 px-4">Cancel</a>
                <?php if ($isAuthenticated): ?>
                  <button type="submit" name="submit" id="submitBtn" class="btn btn-primary rounded-0 px-4" disabled>Confirm Booking</button>
                <?php else: ?>
                  <a href="<?= routeUrl('/login') ?>" class="btn btn-primary rounded-0 px-4">Sign In to Book</a>
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
  const slotsApiUrl = '<?= routeUrl("/api/appointment/slots") ?>';

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

  attachSlotListeners();

  dateInput.addEventListener('change', function () {
    const selectedDate = this.value;
    const receiverId = receiverIdInput.value;

    selectedTimeInput.value = '';
    selectedTimeDisplay.textContent = 'None selected';
    if (submitBtn) {
      submitBtn.setAttribute('disabled', 'true');
    }

    slotsContainer.innerHTML = '<div class="text-center w-100 py-3 text-muted small"><span class="spinner-border spinner-border-sm me-2"></span>Loading open time slots...</div>';

    fetch(`${slotsApiUrl}?receiverId=${encodeURIComponent(receiverId)}&date=${encodeURIComponent(selectedDate)}`)
      .then(response => response.json())
      .then(data => {
        slotsContainer.innerHTML = '';
        if (data.slots && data.slots.length > 0) {
          data.slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm rounded-0 ' + (slot.available ? 'btn-outline-primary time-slot-btn' : 'btn-outline-secondary disabled');
            if (slot.available) {
              btn.setAttribute('data-time', slot.time);
              btn.setAttribute('data-display', slot.display);
              btn.textContent = slot.display;
            } else {
              btn.setAttribute('disabled', 'true');
              btn.innerHTML = `${slot.display} <small>(${slot.reason})</small>`;
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
  });
});
</script>
