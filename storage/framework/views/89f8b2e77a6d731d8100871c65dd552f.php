<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f5f6fa; }
    .admin-navbar { border-bottom: 1px solid rgba(0,0,0,.06); }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white admin-navbar mb-4">
    <div class="container-fluid px-3">
      
      <a class="navbar-brand fw-semibold" href="<?php echo e(route('admin.index')); ?>">
        KiddieCheck Admin
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="adminNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.index') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.index')); ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.users') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.users')); ?>">Users</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.students') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.students')); ?>">Students</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.assessments') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.assessments')); ?>">Assessments</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.reports') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.reports')); ?>">Reports</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo e(request()->routeIs('admin.scales') ? 'active fw-semibold' : ''); ?>" href="<?php echo e(route('admin.scales')); ?>">Scales</a>
          </li>
        </ul>

        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo e(request()->routeIs('admin.profile') ? 'fw-semibold' : ''); ?>" href="#" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Admin Profile
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminProfileDropdown">
              <li><a class="dropdown-item" href="<?php echo e(route('admin.profile')); ?>">Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="post" action="<?php echo e(route('logout')); ?>" class="px-3 py-1">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-link p-0 text-danger">Logout</button>
                </form>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container mb-5">
    <?php if(session('success')): ?>
      <div class="alert alert-success" role="alert">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\layout.blade.php ENDPATH**/ ?>