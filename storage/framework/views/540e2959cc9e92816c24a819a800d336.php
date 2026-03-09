<!DOCTYPE html>

<html lang="en">

<head>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Teacher · KiddieCheck</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>

    :root {

      --teacher-primary: #e77a74;

      --teacher-secondary: #f8f9fa;

      --teacher-accent: #fce38a;

      --teacher-dark: #495057;

      --teacher-light: #ffffff;

      --teacher-success: #28a745;

      --teacher-warning: #ffc107;

      --teacher-info: #17a2b8;

      --teacher-danger: #dc3545;

    }

    

    * {

      box-sizing: border-box;

    }

    

    html, body {

      height: 100%;

      margin: 0;

      padding: 0;

    }

    

    body {

      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);

      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

      color: var(--teacher-dark);

    }

    

    .navbar {

      display: flex;

      align-items: center;

      gap: 18px;

      padding: 12px 24px;

      background: var(--teacher-light) !important;

      border-bottom: 2px solid var(--teacher-primary) !important;

      box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;

    }

    

    .teacher-logo {

      height: 48px;

      display: block;

    }

    

    .pill {

      display: inline-block;

      text-decoration: none;

      background: var(--teacher-primary) !important;

      color: var(--teacher-light) !important;

      padding: 8px 20px;

      border-radius: 20px;

      font-weight: 600;

      font-size: 0.95rem;

      transition: all 0.3s ease;

    }

    

    .pill:hover {

      background: #d63447 !important;

      transform: translateY(-1px);

      box-shadow: 0 4px 12px rgba(231, 122, 116, 0.3);

    }

    

    .spacer {

      flex: 1;

    }

    

    .avatar {

      width: 42px;

      height: 42px;

      border-radius: 50%;

      background: var(--teacher-primary);

      display: inline-flex;

      align-items: center;

      justify-content: center;

      color: var(--teacher-light);

      font-weight: 700;

      text-decoration: none;

      overflow: hidden;

      border: 2px solid var(--teacher-primary);

    }

    

    .profile {

      position: relative;

    }

    

    .menu {

      position: absolute;

      right: 0;

      top: calc(100% + 12px);

      background: var(--teacher-light);

      border: 1px solid #e9ecef;

      border-radius: 12px;

      padding: 8px;

      min-width: 220px;

      display: none;

      z-index: 9999;

      box-shadow: 0 8px 25px rgba(0,0,0,0.15);

    }

    

    .profile.open .menu {

      display: block !important;

    }

    

    .menu-item {

      display: block !important;

      width: 100% !important;

      text-align: left !important;

      background: transparent !important;

      color: var(--teacher-dark) !important;

      font-weight: 500 !important;

      border: 0 !important;

      padding: 12px 16px !important;

      border-radius: 8px !important;

      cursor: pointer !important;

      text-decoration: none !important;

      transition: all 0.2s ease !important;

    }

    

    .menu-item:hover {

      background: var(--teacher-secondary) !important;

      color: var(--teacher-primary) !important;

    }

    

    .main {

      padding: 32px !important;

      max-width: 1400px;

      margin: 0 auto;

    }

    

    /* Enhanced Card Styling */

    .card {

      border: none !important;

      border-radius: 16px !important;

      box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;

      transition: all 0.3s ease !important;

      overflow: hidden !important;

    }

    

    .card:hover {

      transform: translateY(-4px) !important;

      box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important;

    }

    

    .card-header {

      background: var(--teacher-light) !important;

      border-bottom: 2px solid var(--teacher-primary) !important;

      font-weight: 600 !important;

      color: var(--teacher-dark) !important;

      padding: 16px 20px !important;

    }

    

    .card-body {

      padding: 32px !important;

    }

    

    .card-title {

      color: var(--teacher-dark) !important;

      font-weight: 600 !important;

      margin-bottom: 8px !important;

    }

    

    /* Enhanced Button Styling */

    .btn {

      border-radius: 10px !important;

      font-weight: 500 !important;

      padding: 10px 20px !important;

      transition: all 0.3s ease !important;

      border: none !important;

    }

    

    .btn-primary {

      background: var(--teacher-primary) !important;

      border-color: var(--teacher-primary) !important;

    }

    

    .btn-primary:hover {

      background: #d63447 !important;

      border-color: #d63447 !important;

      transform: translateY(-1px) !important;

      box-shadow: 0 4px 12px rgba(231, 122, 116, 0.3) !important;

    }

    

    .btn-outline-primary {

      color: var(--teacher-primary) !important;

      border-color: var(--teacher-primary) !important;

      background: transparent !important;

    }

    

    .btn-outline-primary:hover {

      background: var(--teacher-primary) !important;

      color: var(--teacher-light) !important;

    }

    

    /* Enhanced Table Styling */

    .table {

      border-radius: 12px !important;

      overflow: hidden !important;

      box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;

    }

    

    .table th {

      background: var(--teacher-secondary) !important;

      color: var(--teacher-dark) !important;

      font-weight: 600 !important;

      border: none !important;

      padding: 16px 12px !important;

    }

    

    .table td {

      padding: 12px !important;

      vertical-align: middle !important;

      border-top: 1px solid #e9ecef !important;

    }

    

    .table-hover tbody tr:hover {

      background: #f8f9fa !important;

    }

    

    /* Enhanced Alert Styling */

    .alert {

      border: none !important;

      border-radius: 12px !important;

      padding: 16px 20px !important;

      margin-bottom: 20px !important;

    }

    

    .alert-success {

      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%) !important;

      color: #155724 !important;

      border-left: 4px solid var(--teacher-success) !important;

    }

    

    .alert-danger {

      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%) !important;

      color: #721c24 !important;

      border-left: 4px solid var(--teacher-danger) !important;

    }

    

    .alert-info {

      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%) !important;

      color: #0c5460 !important;

      border-left: 4px solid var(--teacher-info) !important;

    }

    

    /* Badge Styling */

    .badge {

      font-size: 0.8rem !important;

      padding: 6px 12px !important;

      border-radius: 20px !important;

      font-weight: 500 !important;

    }

    

    .no-status {

      color: #6c757d !important;

      font-weight: 600 !important;

    }

    

    .bg-success {

      background: var(--teacher-success) !important;

      color: #ffffff !important;

    }

    

    .bg-warning {

      background: var(--teacher-warning) !important;

      color: #000000 !important;

    }

    

    .bg-info {

      background: var(--teacher-info) !important;

      color: #ffffff !important;

    }

    

    .bg-primary {

      background: var(--teacher-primary) !important;

      color: #ffffff !important;

    }

    

    .text-muted {

      color: #6c757d !important;

    }

    

    .text-primary {

      color: var(--teacher-primary) !important;

    }

    

    .text-success {

      color: var(--teacher-success) !important;

    }

    

    .text-warning {

      color: var(--teacher-warning) !important;

    }

    

    .text-info {

      color: var(--teacher-info) !important;

    }

    

    /* Utility Classes */

    .display-4 {

      font-size: 2.5rem !important;

      font-weight: 700 !important;

      line-height: 1.2 !important;

    }

    

    .fs-1 {

      font-size: 1.25rem !important;

    }

    

    .fs-2 {

      font-size: 2rem !important;

    }

    

    .shadow-sm {

      box-shadow: 0 2px 8px rgba(0,0,0,0.08) !important;

    }

    

    .border-0 {

      border: none !important;

    }

    @media (max-width: 768px) {
      .header-container {
        padding: 15px;
        height: auto;
        flex-wrap: wrap;
      }
      .brand {
        margin-right: auto;
        font-size: 1.2rem;
      }
      .nav-links {
        width: 100%;
        justify-content: flex-start;
        margin-top: 10px;
        gap: 0.3rem;
        overflow-x: auto;
      }
      .nav-link {
        font-size: 0.85rem;
        padding: 0.5rem 0.9rem;
        white-space: nowrap;
      }
      .profile-section {
        margin-left: auto;
      }
      .profile-button {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
      }
    }
  </style>

</head>

<body>

  <nav class="navbar">

    <img src="<?php echo e(asset('teacher-logo.svg')); ?>" alt="KiddieCheck" class="teacher-logo">

    <div class="spacer"></div>

    <a href="<?php echo e(route('teacher.index')); ?>" class="pill <?php echo e(request()->routeIs('teacher.index') ? 'active' : ''); ?>">

      <i class="fas fa-tachometer-alt me-2"></i>Dashboard

    </a>

    <a href="<?php echo e(route('teacher.family')); ?>" class="pill <?php echo e(request()->routeIs('teacher.family') ? 'active' : ''); ?>">

      <i class="fas fa-home me-2"></i>Family

    </a>

    <a href="<?php echo e(route('teacher.sections')); ?>" class="pill <?php echo e(request()->routeIs('teacher.sections') ? 'active' : ''); ?>">

      <i class="fas fa-users me-2"></i>Sections

    </a>

    <a href="<?php echo e(route('teacher.reports')); ?>" class="pill <?php echo e(request()->routeIs('teacher.reports') ? 'active' : ''); ?>">

      <i class="fas fa-clipboard-list me-2"></i>Reports

    </a>

    <div class="profile">

      <a href="#" class="avatar" onclick="toggleMenu()">

        <?php echo e(strtoupper(substr(auth()->user()->username, 0, 1))); ?>


      </a>

      <div class="menu" id="profileMenu">

        <a href="<?php echo e(route('teacher.profile')); ?>" class="menu-item">

          <i class="fas fa-user me-2"></i>Profile

        </a>

        <a href="<?php echo e(route('teacher.help')); ?>" class="menu-item">

          <i class="fas fa-question-circle me-2"></i>Help

        </a>

        <div class="border-top my-2"></div>

        <a href="<?php echo e(route('logout')); ?>" class="menu-item">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>

      </div>
    </div>

  </nav>



  <main class="main">

    <?php if(session('success')): ?>

      <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>


        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

      </div>

    <?php endif; ?>



    <?php if(session('error')): ?>

      <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="fas fa-exclamation-triangle me-2"></i><?php echo e(session('error')); ?>


        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

      </div>

    <?php endif; ?>



    <?php if($errors->any()): ?>

      <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="fas fa-exclamation-triangle me-2"></i>

        <ul class="mb-0">

          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <li><?php echo e($error); ?></li>

          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

      </div>

    <?php endif; ?>



    <?php echo $__env->yieldContent('content'); ?>

  </main>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>

    function toggleMenu() {

      const profile = document.querySelector('.profile');

      const menu = document.getElementById('profileMenu');

      profile.classList.toggle('open');

      

      // Close menu when clicking outside

      document.addEventListener('click', function closeMenu(e) {

        if (!profile.contains(e.target)) {

          profile.classList.remove('open');

          document.removeEventListener('click', closeMenu);

        }

      });

    }

  </script>

</body>

</html><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/layout.blade.php ENDPATH**/ ?>