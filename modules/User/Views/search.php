<div class="container-xl my-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
      <div class="card rounded-3 shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
          <h5 class="card-title mb-0 fw-bold text-dark"><i class="bi bi-people me-2 text-primary"></i>Find Professionals & Book Time</h5>
        </div>
        <div class="card-body p-4">
          <form action="<?= routeUrl('/search') ?>" method="get">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
              <input type="text" name="name" id="name" class="form-control border-start-0 py-2" placeholder="Search by name, role, company, or skills (e.g. Stacy, Architect, Cloud, PHP)..." value="<?= htmlspecialchars($searchTerm ?? '', ENT_QUOTES, 'UTF-8') ?>" required autofocus>
              <button type="submit" class="btn btn-primary px-4 fw-semibold">Search</button>
            </div>
          </form>

          <?php if ($searched && $searchResults !== null): ?>
            <div class="mt-4 pt-3 border-top">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-secondary small fw-bold text-uppercase mb-0" style="letter-spacing: 0.04em;">
                  Professionals Found (<?= count($searchResults) ?>)
                </h6>
              </div>

              <?php if (count($searchResults) > 0): ?>
                <div class="row g-3">
                  <?php foreach ($searchResults as $user): ?>
                    <?php 
                      $uid = htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8');
                      $pic = htmlspecialchars($user['profile_picture'] ?? 'images/logo.png', ENT_QUOTES, 'UTF-8');
                      $name = htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name']), ENT_QUOTES, 'UTF-8');
                      $isSelf = ($user['user_id'] === $_SESSION['userId']);
                      $headline = trim($user['headline'] ?? '');
                      $company = trim($user['company'] ?? '');
                      $jobTitle = trim($user['job_title'] ?? '');
                      $location = trim($user['location_city'] ?? '');
                      $skillsRaw = trim($user['skills'] ?? '');
                      $skillTags = array_filter(array_map('trim', explode(',', $skillsRaw)));
                    ?>
                    <div class="col-12">
                      <div class="card border rounded-3 p-3 h-100 hover-shadow transition" style="transition: all 0.2s ease;">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3">
                          <div class="d-flex align-items-start gap-3">
                            <img src="<?= assetUrl($pic) ?>" alt="<?= $name ?>" class="rounded-circle border shadow-sm flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;">
                            <div>
                              <div class="d-flex align-items-center gap-2">
                                <a href="<?= routeUrl('/u/' . $uid) ?>" class="fw-bold text-dark fs-5 text-decoration-none hover-primary"><?= $name ?></a>
                                <?php if ($isSelf): ?>
                                  <span class="badge bg-secondary-subtle text-secondary border">You</span>
                                <?php else: ?>
                                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;"><i class="bi bi-patch-check-fill me-1"></i>Verified Member</span>
                                <?php endif; ?>
                              </div>

                              <?php if (!empty($headline)): ?>
                                <p class="text-secondary small mb-1 mt-1 fw-medium"><?= htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') ?></p>
                              <?php elseif (!empty($jobTitle) || !empty($company)): ?>
                                <p class="text-secondary small mb-1 mt-1">
                                  <?= htmlspecialchars(trim($jobTitle . ($company ? ' at ' . $company : '')), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                              <?php endif; ?>

                              <?php if (!empty($location)): ?>
                                <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></div>
                              <?php endif; ?>

                              <?php if (!empty($skillTags)): ?>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                  <?php foreach (array_slice($skillTags, 0, 5) as $skill): ?>
                                    <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.72rem;"><?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?></span>
                                  <?php endforeach; ?>
                                  <?php if (count($skillTags) > 5): ?>
                                    <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.72rem;">+<?= count($skillTags) - 5 ?> more</span>
                                  <?php endif; ?>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>

                          <div class="align-self-sm-center flex-shrink-0 mt-3 mt-sm-0 d-flex flex-column gap-2 w-100 w-sm-auto" style="min-width: 170px;">
                            <a href="<?= routeUrl('/u/' . $uid) ?>" class="btn btn-outline-primary btn-sm rounded-2 text-center">
                              <i class="bi bi-person-badge me-1"></i> View Profile & Book
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="alert alert-light border rounded-3 text-center py-5 mb-0">
                  <i class="bi bi-search text-muted fs-1 d-block mb-2"></i>
                  <h6 class="fw-bold text-dark">No professionals matched your search</h6>
                  <p class="text-muted small mb-0">Try searching with a broader keyword, job title, or skill tag.</p>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
