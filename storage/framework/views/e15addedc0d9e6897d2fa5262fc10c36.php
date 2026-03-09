<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; }

    body {
      margin: 0;
      background: #f4f5fb;
      color: #334155;
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .admin-header {
      background: white;
      border-bottom: 2px solid #e5e7eb;
      padding: 0;
      box-shadow: 0 2px 10px rgba(15,23,42,0.05);
    }

    .admin-header-inner {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      padding: 0 20px;
      height: 70px;
    }

    .admin-brand {
      font-size: 1.45rem;
      font-weight: 800;
      text-decoration: none;
      margin-right: 2.5rem;
      letter-spacing: -0.02em;
      display: inline-flex;
      align-items: baseline;
      gap: 0.45rem;
    }

    .admin-brand span:nth-child(4n+1) { color: #3B82F6; }
    .admin-brand span:nth-child(4n+2) { color: #F59E0B; }
    .admin-brand span:nth-child(4n+3) { color: #EF4444; }
    .admin-brand span:nth-child(4n+0) { color: #10B981; }

    .admin-brand-badge {
      font-size: 0.65rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.12em;
      padding: 0.15rem 0.55rem;
      border-radius: 999px;
      background: #EEF2FF;
      color: #4F46E5;
      border: 1px solid rgba(79,70,229,0.2);
    }

    .admin-nav-links {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      flex: 1;
    }

    .admin-nav-link {
      text-decoration: none;
      color: #6b7280;
      font-weight: 600;
      font-size: 0.9rem;
      padding: 0.45rem 0.95rem;
      border-radius: 999px;
      transition: all 0.18s ease;
      border: 1px solid transparent;
    }

    .admin-nav-link:hover {
      color: #4F46E5;
      background: #F3F4FF;
      border-color: rgba(79,70,229,0.25);
    }

    .admin-nav-link.active {
      color: #1f2937;
      background: linear-gradient(135deg,#4F46E5,#6366F1);
      border-color: transparent;
      box-shadow: 0 4px 12px rgba(79,70,229,0.4);
      color: white;
    }

    .admin-profile {
      margin-left: auto;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .admin-avatar {
      width: 36px;
      height: 36px;
      border-radius: 999px;
      background: #EEF2FF;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.95rem;
      font-weight: 700;
      color: #4F46E5;
      border: 2px solid rgba(148,163,184,0.6);
    }

    .admin-profile-menu {
      position: relative;
    }

    .admin-profile-button {
      background: transparent;
      border-radius: 999px;
      border: 1px solid #E5E7EB;
      padding: 0.35rem 0.85rem;
      font-size: 0.85rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      cursor: pointer;
      color: #4B5563;
      text-decoration: none;
      transition: all 0.18s ease;
      background: #F9FAFB;
    }

    .admin-profile-button:hover {
      border-color: #4F46E5;
      color: #111827;
      background: white;
      box-shadow: 0 4px 10px rgba(15,23,42,0.08);
    }

    .admin-dropdown-menu {
      position: absolute;
      right: 0;
      top: calc(100% + 8px);
      background: white;
      border-radius: 14px;
      border: 1px solid #E5E7EB;
      padding: 0.35rem;
      min-width: 180px;
      box-shadow: 0 14px 35px rgba(15,23,42,0.18);
      opacity: 0;
      visibility: hidden;
      transform: translateY(-8px);
      transition: all 0.18s ease;
      z-index: 1000;
    }

    .admin-profile-menu:hover .admin-dropdown-menu,
    .admin-profile-menu:focus-within .admin-dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .admin-dropdown-item {
      display: block;
      width: 100%;
      border: none;
      text-align: left;
      font-size: 0.85rem;
      font-weight: 600;
      padding: 0.55rem 0.75rem;
      border-radius: 10px;
      cursor: pointer;
      background: transparent;
      color: #4B5563;
      text-decoration: none;
      transition: all 0.16s ease;
    }

    .admin-dropdown-item:hover {
      background: #F3F4FF;
      color: #4F46E5;
    }

    .admin-main {
      max-width: 1400px;
      margin: 0 auto;
      padding: 1.75rem 20px 2.5rem;
    }

    .admin-alert {
      border-radius: 14px;
      border-width: 1.5px;
      padding: 0.85rem 1.15rem;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .admin-alert-success {
      background: #ECFDF3;
      border-color: #BBF7D0;
      color: #166534;
    }

    .admin-alert-error {
      background: #FEF2F2;
      border-color: #FCA5A5;
      color: #B91C1C;
    }

    .admin-card-title {
      font-size: 0.9rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: #6B7280;
    }

    .admin-page-title {
      font-weight: 800;
      letter-spacing: -0.02em;
      color: #0F172A;
    }

    .admin-section-subtitle {
      font-size: 0.85rem;
      color: #6B7280;
    }

    .card {
      border-radius: 18px;
      border: 1px solid #E5E7EB;
      box-shadow: 0 8px 24px rgba(15,23,42,0.06);
    }

    .card-header {
      border-bottom-color: #E5E7EB;
    }

    table.table-sm thead tr th {
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #6B7280;
      background: #F9FAFB;
    }

    table.table-sm tbody td {
      font-size: 0.85rem;
    }

    .badge {
      font-weight: 600;
      font-size: 0.75rem;
      padding: 0.3rem 0.65rem;
      border-radius: 999px;
    }

    @media (max-width: 768px) {
      .admin-header-inner {
        flex-wrap: wrap;
        height: auto;
        padding: 12px 16px;
        gap: 0.75rem;
      }
      .admin-brand {
        margin-right: 0;
        font-size: 1.25rem;
      }
      .admin-nav-links {
        width: 100%;
        order: 3;
        overflow-x: auto;
        padding-bottom: 4px;
      }
      .admin-nav-link {
        white-space: nowrap;
        font-size: 0.82rem;
      }
      .admin-profile {
        margin-left: 0;
        width: 100%;
        justify-content: flex-end;
      }
      .admin-main {
        padding: 1.25rem 12px 2rem;
      }
    }
  </style>
</head>
<body>
  <header class="admin-header">
    <div class="admin-header-inner">
      <a href="<?php echo e(route('admin.index')); ?>" class="admin-brand">
        <span><?php $__currentLoopData = str_split('KiddieCheck'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($l); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></span>
        <span class="admin-brand-badge">Admin</span>
      </a>

      <nav class="admin-nav-links">
        <a href="<?php echo e(route('admin.index')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.index') ? 'active' : ''); ?>">Dashboard</a>
        <a href="<?php echo e(route('admin.users')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.users*') ? 'active' : ''); ?>">Users</a>
        <a href="<?php echo e(route('admin.students')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.students*') ? 'active' : ''); ?>">Students</a>
        <a href="<?php echo e(route('admin.assessments')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.assessments*') ? 'active' : ''); ?>">Assessments</a>
        <a href="<?php echo e(route('admin.reports')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.reports*') ? 'active' : ''); ?>">Reports</a>
        <a href="<?php echo e(route('admin.scales')); ?>" class="admin-nav-link <?php echo e(request()->routeIs('admin.scales') || request()->routeIs('admin.eccd') ? 'active' : ''); ?>">Scales</a>
      </nav>

      <div class="admin-profile">
        <?php $user = Auth::user(); ?>
        <div class="admin-avatar">
          <?php echo e($user ? strtoupper(substr($user->username ?? 'A', 0, 1)) : 'A'); ?>

        </div>

        <div class="admin-profile-menu">
          <a href="<?php echo e(route('admin.profile')); ?>" class="admin-profile-button" tabindex="0">
            <span><?php echo e($user->username ?? 'Admin'); ?></span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </a>
          <div class="admin-dropdown-menu">
            <a href="<?php echo e(route('admin.profile')); ?>" class="admin-dropdown-item">Profile</a>
            <a href="<?php echo e(route('logout')); ?>" class="admin-dropdown-item" style="color:#B91C1C;">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="admin-main">
    <?php if(session('success')): ?>
      <div class="admin-alert admin-alert-success mb-3">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="admin-alert admin-alert-error mb-3">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/admin/layout.blade.php ENDPATH**/ ?>