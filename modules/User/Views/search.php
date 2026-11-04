<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-11 col-sm-9 col-md-7 col-lg-6">
      <div class="card rounded-0 shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark">Search Users</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/search') ?>" method="get">
            <div class="input-group">
              <input type="text" name="name" id="name" class="form-control rounded-0" placeholder="Type a name to search (e.g. Stacy)..." value="<?= htmlspecialchars($searchTerm ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>
              <button type="submit" class="btn btn-primary rounded-0 px-4">Search</button>
            </div>
          </form>

          <?php if ($searched && $searchResults !== null): ?>
            <div class="mt-4">
              <h6 class="text-secondary small fw-semibold mb-3">Search Results (<?= count($searchResults) ?>)</h6>
              <?php if (count($searchResults) > 0): ?>
                <div class="list-group rounded-0">
                  <?php foreach ($searchResults as $user): ?>
                    <?php 
                      $uid = htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8');
                      $pic = htmlspecialchars($user['profile_picture'] ?? 'images/logo.png', ENT_QUOTES, 'UTF-8');
                      $name = htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name']), ENT_QUOTES, 'UTF-8');
                      $isSelf = ($user['user_id'] === $_SESSION['userId']);
                    ?>
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                      <div class="d-flex align-items-center gap-3">
                        <img src="<?= assetUrl($pic) ?>" alt="<?= $name ?>" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                        <div>
                          <span class="fw-semibold text-dark d-block"><?= $name ?></span>
                          <?php if ($isSelf): ?>
                            <small class="badge bg-secondary rounded-0">You</small>
                          <?php endif; ?>
                        </div>
                      </div>
                      <?php if (!$isSelf): ?>
                        <a href="<?= routeUrl('/appointment/book?account=' . $uid) ?>" class="btn btn-outline-primary btn-sm rounded-0 px-3">Book Appointment</a>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="alert alert-light border rounded-0 text-center py-4 mb-0">
                  <p class="text-muted mb-0">No users found matching "<strong><?= htmlspecialchars($searchTerm ?? '', ENT_QUOTES, 'UTF-8') ?></strong>".</p>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
