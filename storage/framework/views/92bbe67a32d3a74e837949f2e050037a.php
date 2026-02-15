<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Teacher · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --bg:#e77a74;      
      --pill:#e77a74;     
      --text:#ffffff;     
      --accent:#fce38a;  
      --avatar:#ffb5ae;   
      --hover:#e88f88;  
  
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--text);font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"}
    .teacher-header{display:flex;align-items:center;gap:18px;padding:10px 16px;position:relative;z-index:10}
    .teacher-logo{height:44px;display:block}
    .pill{display:inline-block;text-decoration:none; background:var(--pill); color:var(--text); padding:2px 16px; border-radius:12px; font-weight:700; letter-spacing:.025rem; font-size:1.55rem}
    .pill:hover{opacity:.95; color:#fffaa1;}
    .pill-italic{font-style:italic; color:var(--accent)}
    .check{margin-left:8px; opacity:.8}
    .spacer{flex:1}
    .avatar{width:40px;height:40px;border-radius:12px; background:#ff988f; display:inline-flex; align-items:center; justify-content:center; color:#7a2f2f; font-weight:800; text-decoration:none; overflow:hidden; border:none}
    .profile{position:relative}
    .menu{position:absolute; right:0; top:calc(100% + 8px); background:#ff988f; color:var(--text); border-radius:12px; padding:8px; min-width:200px; display:none; z-index:9999}
    .profile.open .menu{display:block}
    .menu-item{display:block; width:100%; text-align:left; background:transparent; color:var(--text); font-weight:700; border:0; padding:10px 12px; border-radius:10px; cursor:pointer; text-decoration:none}
    .menu-item:hover{background:#e77a74}
    .main{padding:16px}
    .card{background:#fff;color:#333;border-radius:12px;padding:12px;}
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
</html><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views/teacher/layout.blade.php ENDPATH**/ ?>