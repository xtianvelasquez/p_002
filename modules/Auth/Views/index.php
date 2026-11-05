<div class="container-xl my-5">
  <div class="row justify-content-center align-items-center g-5 py-4">
    <div class="col-lg-6 text-center text-lg-start">
      <h1 class="display-5 fw-bold text-dark mb-3">Schedule appointments with ease and clarity.</h1>
      <p class="lead text-secondary mb-4">
        More Than Blue helps you organize your schedule, share your availability, and book appointments seamlessly. Built with a decoupled modular architecture and secure authentication.
      </p>
      <div class="d-grid gap-2 d-sm-flex justify-content-sm-center justify-content-lg-start mb-3">
        <a href="<?= routeUrl('/signup') ?>" class="btn btn-primary btn-lg rounded-2 px-4 fw-semibold shadow-sm">Get Started</a>
        <a href="<?= routeUrl('/login') ?>" class="btn btn-outline-secondary btn-lg rounded-2 px-4 fw-semibold">Sign In</a>
      </div>
      <div>
        <a href="<?= routeUrl('/auth/google') ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 shadow-sm bg-white">
          <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z" fill="#4285F4"/>
            <path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z" fill="#34A853"/>
            <path d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.173 0 7.548 0 9s.347 2.827.957 4.039l3.007-2.332z" fill="#FBBC05"/>
            <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.166 6.656 3.58 9 3.58z" fill="#EA4335"/>
          </svg>
          <span class="small fw-semibold">Continue with Google</span>
        </a>
      </div>
    </div>
    <div class="col-10 col-sm-8 col-lg-5 text-center">
      <img src="<?= assetUrl('images/logo.png') ?>" alt="More Than Blue Logo" class="img-fluid rounded shadow-sm p-4 bg-white" style="max-height: 280px;">
    </div>
  </div>
</div>
