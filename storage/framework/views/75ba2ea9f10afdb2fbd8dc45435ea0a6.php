<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Family · KiddieCheck</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --bg:#a3c365;       
      --pill:#7eaf64;     
      --text:white;     
      --avatar:#b5db6b;   
      --hover:#a3c365;   
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--text);font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji"}
    .family-header{display:flex;align-items:center;gap:18px;padding:10px 16px;position:relative;z-index:10}
    .family-logo{height:44px;display:block}
    .pill{display:inline-block;text-decoration:none; background:var(--bg); color:var(--text); padding:2px 16px; border-radius:12px; font-weight:700; letter-spacing:.025rem; font-size:1.55rem}
    .pill:hover{opacity:.95; color:#fcf8a0;}
    .pill-italic{font-style:italic}
    .check{margin-left:8px; opacity:.8}
    .spacer{flex:1}
    .avatar{width:40px;height:40px;border-radius:12px; background:var(--avatar); display:inline-flex; align-items:center; justify-content:center; color:#2f5130; font-weight:800; text-decoration:none; overflow:hidden; border:none}
    .profile{position:relative}
    .menu{position:absolute; right:0; top:calc(100% + 8px); background:#b5db6b; color:var(--text); border-radius:12px; padding:8px; min-width:200px; display:none; z-index:9999}
    .profile.open .menu{display:block}
    .menu-item{display:block; width:100%; text-align:left; background:transparent; color:var(--text); font-weight:700; border:0; padding:10px 12px; border-radius:10px; cursor:pointer; text-decoration:none}
    .menu-item:hover{background:var(--hover)}
    .main{padding:16px}
    .card{background:#fff;color:#333;border-radius:12px;padding:12px;}
  </style>
</head>
<body>
  <header class="family-header">
    <a class="<?php echo e(request()->routeIs('family.index') ? 'active' : ''); ?>" href="<?php echo e(route('family.index')); ?>"><img class="family-logo" src="<?php echo e(asset('family-logo.svg')); ?>" alt="Kiddie Family"></a>
    <a class="pill <?php echo e(request()->routeIs('family.index') ? 'active' : ''); ?>" href="<?php echo e(route('family.index')); ?>">Home</span></a>
    <a class="pill <?php echo e(request()->routeIs('family.index') ? 'active' : ''); ?>" href="<?php echo e(route('family.index')); ?>">Children</a>
    <a class="pill <?php echo e(request()->routeIs('family.index') ? 'active' : ''); ?>" href="<?php echo e(route('family.index')); ?>">Tests</a>
    <a class="pill <?php echo e(request()->routeIs('family.index') ? 'active' : ''); ?>" href="<?php echo e(route('family.index')); ?>">Help</a>
    <div class="spacer"></div>
    <?php ($initial = strtoupper(substr(optional(Auth::user())->name ?? 'U', 0, 1))); ?>
    <div class="profile" id="familyProfileMenu">
      <button type="button" class="avatar" aria-haspopup="true" aria-expanded="false" title="Profile"><?php echo e($initial); ?></button>
      <div class="menu" role="menu" aria-labelledby="familyProfileMenu">
        <a href="<?php echo e(route('family.index')); ?>" class="menu-item" role="menuitem">Profile Settings</a>
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
      const profile = document.querySelector('#familyProfileMenu');
      if(!profile) return;
      const btn = profile.querySelector('.avatar');
      btn.addEventListener('click', function(e){ e.preventDefault(); profile.classList.toggle('open'); });
      document.addEventListener('click', function(e){ if(!profile.contains(e.target)) profile.classList.remove('open'); });
      document.addEventListener('keydown', function(e){ if(e.key === 'Escape') profile.classList.remove('open'); });
    })();
  </script>
</body>
</html><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views\family\layout.blade.php ENDPATH**/ ?>