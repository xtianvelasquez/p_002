<div class="landing">
  <section class="hero" aria-labelledby="hero-title">
    <div class="hero-inner">
      <div class="hero-copy">
        <div class="eyebrow"><span class="eyebrow-dot"></span> A calmer way to make time</div>
        <h1 id="hero-title">Schedule appointments <span>with ease and clarity.</span></h1>
        <p>Make room for meaningful conversations. Share when you’re available, find the right people, and book time without the back and forth.</p>
        <div class="hero-actions"><a href="<?= routeUrl('/signup') ?>" class="btn btn-primary">Start your free trial <span aria-hidden="true">→</span></a><a href="<?= routeUrl('/login') ?>" class="btn btn-outline-primary">Sign in</a></div>
        <div class="hero-proof"><div class="proof-avatars"><span>MT</span><span>CV</span><span>JD</span></div><span>Simple scheduling for <strong>better conversations</strong></span></div>
      </div>
      <div class="hero-visual" aria-label="Preview of a weekly appointment calendar">
        <div class="visual-glow"></div>
        <div class="visual-window"><div class="window-top"><div class="window-dots"><i></i><i></i><i></i></div><span>YOUR WEEK, AT A GLANCE</span><span>•••</span></div><div class="schedule-card"><div class="schedule-head"><strong>November 2026</strong><span>+ New appointment</span></div><div class="schedule-grid"><?php foreach (['S','M','T','W','T','F','S','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','1','2','3','4','5'] as $i => $day): ?><span class="<?= $i === 17 ? 'selected' : (in_array($i,[12,25],true) ? 'booked' : '') ?>"><?= $day ?></span><?php endforeach; ?></div><div class="schedule-slots"><span>9:00 AM</span><span class="selected">10:30 AM</span><span>1:00 PM</span></div></div></div>
        <div class="floating-note"><strong><span class="floating-check">✓</span>You're all set</strong>Appointment confirmed · 10:30 AM</div>
      </div>
    </div>
  </section>
  <section class="section" id="features"><div class="section-inner"><div class="section-heading"><div class="eyebrow">Less coordinating, more connecting</div><h2>Your time should work for you.</h2><p>From sharing availability to staying on top of every request, the essentials live together in one clear space.</p></div><div class="feature-grid"><article class="feature-card"><div class="feature-icon">▦</div><h3>One clear dashboard</h3><p>See incoming requests, confirmations, and your calendar without jumping between tools.</p></article><article class="feature-card" id="how-it-works"><div class="feature-icon">◷</div><h3>Booking that feels easy</h3><p>Let people choose a time that works from the availability you share with them.</p></article><article class="feature-card"><div class="feature-icon">♧</div><h3>Your schedule, your rules</h3><p>Set working hours, meeting length, and the details people need before they book.</p></article></div></div></section>
  <section class="section" style="padding-top:0"><div class="section-inner"><div class="cta-panel"><div><h2>Make room for a better conversation.</h2><p>Set up your profile and share your first booking link in minutes.</p></div><a href="<?= routeUrl('/signup') ?>" class="btn btn-light text-primary">Get started <span aria-hidden="true">→</span></a></div></div></section>
</div>
