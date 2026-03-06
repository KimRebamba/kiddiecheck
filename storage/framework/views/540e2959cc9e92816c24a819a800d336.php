<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Teacher · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --bg:#f5f6fa;      
      --pill:#e77a74;     
      --text:#ffffff;     
      --accent:#fce38a;  
      --avatar:#ffb5ae;   
      --hover:#e88f88;  
      --success:#28a745;
      --warning:#ffc107;
      --info:#17a2b8;
      --primary:#007bff;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg)!important;color:var(--text)!important;font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"}
    .navbar{display:flex;align-items:center;gap:18px;padding:10px 16px;position:relative;z-index:10;background:var(--bg)!important;border-bottom:1px solid var(--accent)!important;box-shadow:0 2px 8px rgba(0,0,0,0.05)!important;}
    .teacher-logo{height:44px;display:block}
    .pill{display:inline-block;text-decoration:none; background:var(--pill)!important;color:var(--text)!important;padding:2px 16px; border-radius:12px; font-weight:700; letter-spacing:.025rem; font-size:1.55rem}
    .pill:hover{opacity:.95; color:#fffaa1!important;}
    .pill-italic{font-style:italic; color:var(--accent)!important;}
    .check{margin-left:8px; opacity:.8}
    .spacer{flex:1}
    .avatar{width:40px;height:40px;border-radius:12px; background:var(--avatar)!important; display:inline-flex; align-items:center; justify-content:center; color:var(--text)!important; font-weight:800; text-decoration:none; overflow:hidden; border:none}
    .profile{position:relative}
    .menu{position:absolute; right:0; top:calc(100% + 8px); background:var(--bg)!important; color:var(--text)!important; border-radius:12px; padding:8px; min-width:200px; display:none; z-index:9999;box-shadow:0 4px 12px rgba(0,0,0,0.15)!important;}
    .profile.open .menu{display:block!important;}
    .menu-item{display:block!important; width:100%!important; text-align:left!important; background:transparent!important; color:var(--text)!important; font-weight:700!important; border:0!important; padding:10px 12px!important; border-radius:10px!important; cursor:pointer!important; text-decoration:none!important; transition:all 0.2s!important;}
    .menu-item:hover{background:var(--accent)!important;color:var(--text)!important;}
    .main{padding:16px!important;}
    /* DEBUG: Force color application */
    .card { background: #fff !important; }
    .card-header { background: #f8f9fa !important; }
    .card-title { color: #495057 !important; }
    .btn-primary { background: #007bff !important; }
    .btn-primary:hover { background: #0056b3 !important; }
    
    /* Enhanced card styling */
    .card {
      border: 1px solid #e9ecef !important;
      box-shadow: 0 2px 8px rgba(0,0,0,0,0.1) !important;
      transition: box-shadow 0.3s !important;
    }
    
    .card:hover {
      box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }
    
    /* Enhanced button styling */
    .btn {
      border: 1px solid var(--primary) !important;
      transition: all 0.2s !important;
    }
    
    .btn:hover {
      transform: translateY(-1px) !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    
    /* Enhanced alert styling */
    .alert {
      border: 1px solid var(--success) !important;
      border-radius: 12px !important;
    }
    
    /* Debug styles */
    .bg-test { background: #ff000 !important; }
    .text-test { color: #ff000 !important; }
    .border-test { border: 2px solid #ff0000 !important; }
    .pill-test { background: #e77a74 !important; }
    .btn-primary { background: #007bff !important; }
    .btn-outline-primary{color:var(--primary)!important;border-color:var(--primary)!important;background:transparent!important;}
    .btn-outline-secondary{color:var(--secondary)!important;border-color:var(--secondary)!important;background:transparent!important;}
    .btn-outline-warning{color:var(--warning)!important;border-color:var(--warning)!important;background:transparent!important;}
    .btn-outline-danger{color:#dc3545!important;border-color:#dc3545!important;background:transparent!important;}
    .table{width:100%!important;border-collapse:collapse!important;}
    .table th{border-top:1px solid #dee2e6!important;background:#f8f9fa!important;font-weight:500!important;}
    .table td{border-top:1px solid #dee2e6!important;}
    .table-hover tbody tr:hover{background:var(--bg)!important;}
    .alert{padding:12px!important;border-radius:12px!important;margin-bottom:1.5rem!important;}
    .alert-success{background:var(--success)!important;border-color:var(--success)!important;color:var(--success)!important;}
    .alert-error{background:#dc3545!important;border-color:#dc3545!important;color:var(--error)!important;}
    .alert-info{background:var(--info)!important;border-color:var(--info)!important;color:var(--info)!important;}
    .badge{font-size:0.75rem!important;padding:4px 8px!important;border-radius:12px!important;font-weight:500!important;}
    .bg-primary{background:var(--primary)!important;color:#fff!important;}
    .bg-success{background:var(--success)!important;color:#fff!important;}
    .bg-warning{background:var(--warning)!important;color:#fff!important;}
    .bg-secondary{background:var(--secondary)!important;color:#fff!important;}
    .text-muted{color:#6c757d!important;}
    .text-primary{color:var(--primary)!important;}
    .text-success{color:var(--success)!important;}
    .text-warning{color:var(--warning)!important;}
    .text-info{color:var(--info)!important;}
    .display-4{font-size:2rem!important;font-weight:700!important;}
    .display-6{font-size:1rem!important;font-weight:700!important;}
    .fs-1{font-size:2.5rem!important;}
    .fs-2{font-size:1rem!important;}
    .fw-bold{font-weight:700!important;}
    .fw-semibold{font-weight:600!important;}
    .small{font-size:0.875rem!important;}
    .shadow-sm{box-shadow:0 .125rem .25rem rgba(0,0,0,0.05)!important;}
    .border-0{border:1px solid #e9ecef!important;}
    .btn-group-sm .btn{padding:0.25rem 0.5rem!important;font-size:0.8rem!important;}
    .btn-group-sm .btn i{margin-right:4px!important;}
    .rounded-pill{border-radius:50rem!important;}
    .me-1{margin-right:0.25rem!important;}
    .me-2{margin-right:0.5rem!important;}
    .py-1{padding-top:0.25rem!important;padding-bottom:0.25rem!important;}
    .py-4{padding-top:0.25rem!important;padding-bottom:0.25rem!important;}
    .mb-0{margin-bottom:0!important;}
    .mb-1{margin-bottom:0.25rem!important;}
    .mb-2{margin-bottom:0.5rem!important;}
    .mb-3{margin-bottom:1rem!important;}
    .mb-4{margin-bottom:1.5rem!important;}
    .mt-0{margin-top:0!important;}
    .mt-2{margin-top:0.25rem!important;}
    .mt-3{margin-top:0.5rem!important;}
    .mt-4{margin-top:1rem!important;}
  </style>
</head>
<body>
  <header class="teacher-header">
    <a class="<?php echo e(request()->routeIs('teacher.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.index')); ?>"><img class="teacher-logo" src="<?php echo e(asset('teacher-logo.svg')); ?>" alt="Kiddie Teacher"></a>
    <a class="pill <?php echo e(request()->routeIs('teacher.index') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.index')); ?>">Home</a>
    <a class="pill <?php echo e(request()->routeIs('teacher.family*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.family')); ?>">Family</a>
    <a class="pill <?php echo e(request()->routeIs('teacher.reports*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.reports')); ?>">Report</a>
    <a class="pill <?php echo e(request()->routeIs('teacher.sections*') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.sections')); ?>">Section</a>
    <a class="pill <?php echo e(request()->routeIs('teacher.help') ? 'active' : ''); ?>" href="<?php echo e(route('teacher.help')); ?>">Help</a>
    <div class="spacer"></div>
    <?php ($initial = strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1))); ?>
    <div class="profile" id="teacherProfileMenu">
      <button type="button" class="avatar" aria-haspopup="true" aria-expanded="false" title="Profile"><?php echo e($initial); ?></button>
      <div class="menu" role="menu" aria-labelledby="teacherProfileMenu">
        <a href="<?php echo e(route('teacher.profile')); ?>" class="menu-item" role="menuitem">Profile Settings</a>
        <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0" role="none">
          <?php echo csrf_field(); ?>
          <button type="submit" class="menu-item" role="menuitem">Logout</button>
        </form>
      </div>
    </div>
  </header>

  <main class="main">
    <?php if(session('success')): ?>
      <div class="card" role="alert"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
      <div class="card" role="alert"><?php echo e(session('error')); ?></div>
    <?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function(){
      const profile = document.querySelector('#teacherProfileMenu');
      if(!profile) return;
      const btn = profile.querySelector('.avatar');
      btn.addEventListener('click', function(e){ e.preventDefault(); profile.classList.toggle('open'); });
      document.addEventListener('click', function(e){ if(!profile.contains(e.target)) profile.classList.remove('open'); });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') profile.classList.remove('open'); });
    })();
  </script>
</body>
</html><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/layout.blade.php ENDPATH**/ ?>