<div class="container-xl my-4">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 fw-bold text-dark">Sent Appointments</h5>
          <span class="badge bg-secondary rounded-0"><?= count($appointments) ?> Total</span>
        </div>
        <div class="card-body p-0">
          <?php if (count($appointments) > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="ps-3">Recipient Name</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Event Name</th>
                    <th scope="col">Location</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-3">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($appointments as $appt): ?>
                    <?php
                      $status = strtolower(trim($appt['appointment_status'] ?? 'pending'));
                      $badgeClass = match ($status) {
                          'approved' => 'bg-success',
                          'denied'   => 'bg-danger',
                          'cancel'   => 'bg-secondary',
                          default    => 'bg-warning text-dark'
                      };
                    ?>
                    <tr>
                      <td class="ps-3 fw-semibold"><?= htmlspecialchars($appt['fullName'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['event_name'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><?= htmlspecialchars($appt['location'], ENT_QUOTES, 'UTF-8') ?></td>
                      <td><span class="badge rounded-0 <?= $badgeClass ?>"><?= htmlspecialchars(ucfirst($status), ENT_QUOTES, 'UTF-8') ?></span></td>
                      <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm" role="group">
                          <a href="<?= routeUrl('/appointment/view?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-secondary rounded-0">View</a>
                          <a href="<?= routeUrl('/appointment/edit?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-primary rounded-0">Status</a>
                          <a href="<?= routeUrl('/appointment/delete-sent?id=' . $appt['appointment_id']) ?>" class="btn btn-outline-danger rounded-0">Delete</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <p class="text-muted mb-3">You have not sent any appointments yet.</p>
              <a href="<?= routeUrl('/search') ?>" class="btn btn-primary btn-sm rounded-0">Find People to Book</a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
