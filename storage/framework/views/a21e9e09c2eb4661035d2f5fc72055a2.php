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
      margin: 0;
      background: #f5f5f5;
      color: var(--teacher-dark);
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Header styled to match family layout */
    .teacher-header {
      background: #ffffff;
      border-bottom: 2px solid #e5e5e5;
      padding: 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .header-container {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      padding: 0 20px;
      height: 70px;
    }

    .brand {
      font-size: 1.5rem;
      font-weight: 800;
      text-decoration: none;
      margin-right: 3rem;
      letter-spacing: -0.02em;
    }

    .brand span:nth-child(4n+1) { color: #3B82F6; }
    .brand span:nth-child(4n+2) { color: #F59E0B; }
    .brand span:nth-child(4n+3) { color: #EF4444; }
    .brand span:nth-child(4n+0) { color: #10B981; }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      flex: 1;
    }

    .nav-link {
      text-decoration: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      transition: all 0.2s;
    }

    .nav-link:hover {
      color: #667eea;
      background: #f3f4f6;
    }

    .nav-link.active {
      color: #667eea;
      background: transparent;
      font-weight: 700;
    }

    .profile-section {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-left: auto;
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

    .profile-menu { position: relative; }

    .profile-button {
      background: transparent;
      border: 2px solid #e5e5e5;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.9rem;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
    }

    .profile-button:hover {
      border-color: #667eea;
      color: #667eea;
      background: #f9fafb;
    }

    .dropdown-menu {
      position: absolute;
      right: 0;
      top: calc(100% + 8px);
      background: #ffffff;
      border: 2px solid #e5e5e5;
      border-radius: 12px;
      padding: 0.5rem;
      min-width: 200px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.2s;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      z-index: 1000;
    }

    .profile-menu:hover .dropdown-menu,
    .profile-menu:focus-within .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-item {
      display: block;
      width: 100%;
      text-align: left;
      background: transparent;
      border: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.9rem;
      padding: 0.7rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }

    .dropdown-item:hover {
      background: #f3f4f6;
      color: #667eea;
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
  <header class="teacher-header">
    <div class="header-container">
      <a class="brand" href="<?php echo e(route('teacher.index')); ?>">
        <?php $__currentLoopData = str_split('KiddieCheck'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($l); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </a>

      <div class="nav-links">
        <a class="nav-link <?php echo e(request()->routeIs('teacher.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.index')); ?>">Dashboard</a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.sections*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.sections')); ?>">Sections</a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.family*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.family')); ?>">Families</a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.eccd') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.eccd')); ?>">ECCD</a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.reports*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.reports')); ?>">Reports</a>
        <a class="nav-link <?php echo e(request()->routeIs('teacher.help') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.help')); ?>">Help</a>
      </div>

      <div class="profile-section">
        <div class="profile-menu">
          <a href="<?php echo e(route('teacher.profile')); ?>" class="profile-button" tabindex="0">
            <span><?php echo e(optional(Auth::user())->username ?? 'Account'); ?></span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </a>
          <div class="dropdown-menu">
            <a href="<?php echo e(route('teacher.profile')); ?>" class="dropdown-item">Profile Settings</a>
            <a href="<?php echo e(route('teacher.help')); ?>" class="dropdown-item">Help</a>
            <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0">
              <?php echo csrf_field(); ?>
              <button type="submit" class="dropdown-item">Logout</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </header>

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
</body>
</html><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\layout.blade.php ENDPATH**/ ?>