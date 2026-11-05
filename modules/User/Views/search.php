<?php
/**
 * @var string $searchTerm
 * @var bool $searched
 * @var array|null $searchResults
 */
$searchTerm = $searchTerm ?? '';
$searched = !empty($searched);
$searchResults = $searchResults ?? null;
?>
<div class="page-shell">
  <div class="page-heading"><div><div class="eyebrow mb-2">Find your next conversation</div><h1>Search professionals</h1><p>Discover people and book a time that works for both of you.</p></div><span class="text-secondary small">Thoughtful meetings start here</span></div>
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card rounded-3 shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom">
                  <h5 class="card-title mb-0 fw-bold text-dark">Find the right person</h5>
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
                      $name = htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name']), ENT_QUOTES, 'UTF-8');
                      $pic = $user['profile_picture'] ?? '';
                      $avatarUrl = userAvatarUrl($pic, $name, 64);
                      $avatarFallback = getInitialsAvatarDataUri($name, 64);
                      $isSelf = (isset($_SESSION['userId']) && $user['user_id'] === $_SESSION['userId']);
                      $headline = trim($user['headline'] ?? '');
                      $company = trim($user['company'] ?? '');
                      $jobTitle = trim($user['job_title'] ?? '');
                      $location = trim($user['location_city'] ?? '');
                      $skillsRaw = trim($user['skills'] ?? '');
                      $skillTags = array_filter(array_map('trim', explode(',', $skillsRaw)));
                    ?>
                    <div class="col-12 col-md-6 col-xl-4">
                      <div class="professional-card h-100">
                        <div class="professional-cover"></div><div class="professional-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                          <div class="d-flex align-items-start gap-3 flex-grow-1" style="min-width: 0;">
                            <img src="<?= $avatarUrl ?>" alt="<?= $name ?>" class="professional-avatar rounded-circle flex-shrink-0" onerror="this.onerror=null; this.src='<?= $avatarFallback ?>';">
                            <div class="flex-grow-1" style="min-width: 0;">
                              <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <a href="<?= routeUrl('/u/' . $uid) ?>" class="professional-name"><?= $name ?></a>
                                <?php if ($isSelf): ?>
                                  <span class="badge bg-secondary-subtle text-secondary border">You</span>
                                <?php else: ?>
                                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;"><i class="bi bi-patch-check-fill me-1"></i>Verified Member</span>
                                <?php endif; ?>
                              </div>

                              <?php if (!empty($headline)): ?>
                                <p class="professional-tagline mb-1 mt-1 fw-medium text-break"><?= htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') ?></p>
                              <?php elseif (!empty($jobTitle) || !empty($company)): ?>
                                <p class="professional-tagline mb-1 mt-1 text-break">
                                  <?= htmlspecialchars(trim($jobTitle . ($company ? ' at ' . $company : '')), ENT_QUOTES, 'UTF-8') ?>
                                </p>
                              <?php endif; ?>

                              <?php if (!empty($location)): ?>
                                <div class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($location, ENT_QUOTES, 'UTF-8') ?></div>
                              <?php endif; ?>

                              <?php if (!empty($skillTags)): ?>
                                <div class="professional-skills d-flex flex-wrap gap-1 mt-2">
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

                          <a href="<?= routeUrl('/u/' . $uid) ?>" class="btn btn-primary btn-sm professional-action"><i class="bi bi-calendar2-check me-1"></i> View profile & book</a>
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
