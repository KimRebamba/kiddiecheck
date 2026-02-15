<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
      <a class="navbar-brand" href="<?php echo e(route('index')); ?>">KiddieCheck</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?php echo e(route('family.index')); ?>">Family</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo e(route('teacher.index')); ?>">Teacher</a></li>
          <li class="nav-item"><a class="nav-link" href="<?php echo e(route('admin.index')); ?>">Admin</a></li>
        </ul>
        <div class="d-flex">
          <?php if(auth()->guard()->check()): ?>
            <form method="post" action="<?php echo e(route('logout')); ?>">
              <?php echo csrf_field(); ?>
              <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
            </form>
          <?php else: ?>
            <a class="btn btn-outline-light btn-sm" href="<?php echo e(route('login')); ?>">Login</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
    <main class="container mb-5">
      <?php if(session('success')): ?>
        <div class="container">
          <div class="alert alert-success" role="alert">
            <?php echo e(session('success')); ?>

          </div>
        </div>
      <?php endif; ?>
      <?php if(session('error')): ?>
        <div class="container">
          <div class="alert alert-danger" role="alert">
            <?php echo e(session('error')); ?>

          </div>
        </div>
      <?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views\layouts\app.blade.php ENDPATH**/ ?>