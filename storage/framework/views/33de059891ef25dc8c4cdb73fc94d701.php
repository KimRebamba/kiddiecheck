

<?php $__env->startSection('content'); ?>

<?php
  $user    = Auth::user();
  $teacher = $user->teacher ?? null;
  $userId  = $user->user_id;
  $assignedStudents = $teacher ? $teacher->students()->count() : 0;
  $totalTests       = \App\Models\Test::where('examiner_id', $userId)->count();
  $completedTests   = \App\Models\Test::where('examiner_id', $userId)->whereIn('status', ['completed', 'finalized'])->count();
  $inProgressTests  = \App\Models\Test::where('examiner_id', $userId)->where('status', 'in_progress')->count();
  $hireDate = $teacher ? (is_string($teacher->hire_date) ? \Carbon\Carbon::parse($teacher->hire_date) : $teacher->hire_date) : null;
?>

<!-- Header -->
<div class="d-flex align-items-center mb-4">
  <div>
    <h1 class="h3 mb-0 fw-bold">Profile Settings</h1>
    <div class="page-subhead">Manage your account information</div>
  </div>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.index')); ?>" class="btn btn-ghost-back">
      <i class="fas fa-arrow-left me-1"></i>Back
    </a>
  </div>
</div>

<!-- Profile Hero -->
<div class="profile-hero mb-4">
  <div class="profile-hero-avatar">
    <?php echo e(strtoupper(substr($user->username ?? 'T', 0, 1))); ?>

  </div>
  <div class="profile-hero-info">
    <div class="profile-hero-name"><?php echo e($teacher->first_name ?? $user->username); ?> <?php echo e($teacher->last_name ?? ''); ?></div>
    <div class="profile-hero-email"><?php echo e($user->email); ?></div>
    <span class="role-badge"><?php echo e(ucfirst($user->role)); ?></span>
  </div>
</div>

<div class="row g-3">

  <!-- Stat Cards -->
  <div class="col-6 col-md-3">
    <div class="stat-card stat-violet">
      <div class="stat-icon-wrap stat-icon-violet"><i class="fas fa-user-graduate"></i></div>
      <div class="stat-num"><?php echo e($assignedStudents); ?></div>
      <div class="stat-label">Assigned Students</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-teal">
      <div class="stat-icon-wrap stat-icon-teal"><i class="fas fa-clipboard-list"></i></div>
      <div class="stat-num"><?php echo e($totalTests); ?></div>
      <div class="stat-label">Total Tests</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-mint">
      <div class="stat-icon-wrap stat-icon-mint"><i class="fas fa-check-circle"></i></div>
      <div class="stat-num"><?php echo e($completedTests); ?></div>
      <div class="stat-label">Completed Tests</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card stat-lemon">
      <div class="stat-icon-wrap stat-icon-lemon"><i class="fas fa-spinner"></i></div>
      <div class="stat-num"><?php echo e($inProgressTests); ?></div>
      <div class="stat-label">In Progress</div>
    </div>
  </div>

  <!-- Personal Information -->
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header header-violet">
        <div class="section-icon si-violet">👤</div>
        <h5 class="mb-0">Personal Information</h5>
      </div>
      <div class="card-body">
        <div class="info-row">
          <span class="info-label">Username</span>
          <span class="info-value"><?php echo e($user->username ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Email</span>
          <span class="info-value"><?php echo e($user->email ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Role</span>
          <span class="info-value"><span class="role-badge"><?php echo e(ucfirst($user->role)); ?></span></span>
        </div>
        <?php if($teacher): ?>
          <div class="info-row">
            <span class="info-label">First Name</span>
            <span class="info-value"><?php echo e($teacher->first_name ?? 'N/A'); ?></span>
          </div>
          <div class="info-row">
            <span class="info-label">Last Name</span>
            <span class="info-value"><?php echo e($teacher->last_name ?? 'N/A'); ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Teacher Details -->
  <?php if($teacher): ?>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header header-teal">
        <div class="section-icon si-teal">📋</div>
        <h5 class="mb-0">Teacher Details</h5>
      </div>
      <div class="card-body">
        <div class="info-row">
          <span class="info-label">Home Address</span>
          <span class="info-value"><?php echo e($teacher->home_address ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Phone Number</span>
          <span class="info-value"><?php echo e($teacher->phone_number ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
          <span class="info-label">Hire Date</span>
          <span class="info-value"><?php echo e($hireDate ? $hireDate->format('M d, Y') : 'N/A'); ?></span>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Account Settings -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header header-sky">
        <div class="section-icon si-sky">⚙️</div>
        <h5 class="mb-0">Account Settings</h5>
      </div>
      <div class="card-body">
        <div class="help-alert alert-info-custom">
          <span class="alert-icon">💡</span>
          <span><strong>Note:</strong> To change your password or email address, please contact your administrator.</span>
        </div>
        <p class="mt-3 mb-0" style="font-size:0.86rem; color:var(--text-muted); font-weight:600;">
          Account management options will be available in future updates.
        </p>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="col-md-12 d-flex gap-2 mb-4">
    <a href="<?php echo e(route('teacher.index')); ?>" class="btn btn-ghost-back">
      <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
    </a>
        <a href="<?php echo e(route('logout')); ?>" class="btn btn-logout">
      <i class="fas fa-sign-out-alt me-1"></i>Logout
    </a>
  </div>

</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --teal:        #2EC4B6;
  --teal-soft:   #C8F4F1;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --lemon:       #F9C74F;
  --lemon-soft:  #FFF6CC;
  --sky:         #4EA8DE;
  --sky-soft:    #D6EEFF;
  --peach:       #FF9A76;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE HEADER ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important; font-size: 1.6rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.page-subhead { font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin-top: 2px; }

/* ── PROFILE HERO ── */
.profile-hero {
  background: white; border-radius: var(--radius); box-shadow: var(--shadow);
  padding: 22px 24px; display: flex; align-items: center; gap: 20px;
  animation: fadeUp 0.35s ease both;
}
.profile-hero-avatar {
  width: 64px; height: 64px; border-radius: 16px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  display: flex; align-items: center; justify-content: center;
  font-family: 'Baloo 2', cursive; font-size: 1.8rem; font-weight: 800; color: white;
  box-shadow: 0 4px 16px rgba(132,94,194,0.3);
}
.profile-hero-name {
  font-family: 'Baloo 2', cursive; font-size: 1.15rem; font-weight: 800; color: var(--text); line-height: 1.2;
}
.profile-hero-email {
  font-size: 0.82rem; font-weight: 700; color: var(--text-muted); margin: 3px 0 7px;
}
.role-badge {
  display: inline-block; background: var(--violet-soft); color: var(--violet);
  font-size: 0.7rem; font-weight: 800; padding: 3px 11px; border-radius: 20px;
}

/* ── STAT CARDS ── */
.stat-card {
  border-radius: var(--radius); padding: 18px 16px; text-align: center;
  animation: fadeUp 0.4s ease both; transition: transform 0.2s, box-shadow 0.2s;
  box-shadow: var(--shadow);
}
.stat-card:hover { transform: translateY(-3px); }
.stat-violet { background: white; border-top: 4px solid var(--violet); animation-delay:0.05s; }
.stat-teal   { background: white; border-top: 4px solid var(--teal);   animation-delay:0.10s; }
.stat-mint   { background: white; border-top: 4px solid var(--mint);   animation-delay:0.15s; }
.stat-lemon  { background: white; border-top: 4px solid var(--lemon);  animation-delay:0.20s; }

.stat-icon-wrap {
  width: 36px; height: 36px; border-radius: 10px; margin: 0 auto 8px;
  display: flex; align-items: center; justify-content: center; font-size: 0.9rem;
}
.stat-icon-violet { background: var(--violet-soft); color: var(--violet); }
.stat-icon-teal   { background: var(--teal-soft);   color: var(--teal);   }
.stat-icon-mint   { background: var(--mint-soft);   color: var(--mint);   }
.stat-icon-lemon  { background: var(--lemon-soft);  color: #b8860b;       }

.stat-num   { font-family: 'Baloo 2', cursive; font-size: 2rem; font-weight: 800; color: var(--text); line-height: 1; }
.stat-label { font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); margin-top: 4px; }

/* ── CARDS ── */
.card {
  border: none !important; border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important; overflow: hidden;
  animation: fadeUp 0.4s ease both; transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(100,60,160,0.13) !important; }
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }
.col-md-6:nth-child(1) .card { animation-delay: 0.25s; }
.col-md-6:nth-child(2) .card { animation-delay: 0.30s; }
.col-md-12:nth-child(3) .card { animation-delay: 0.35s; }

/* ── CARD HEADERS ── */
.card-header {
  padding: 13px 18px !important; display: flex; align-items: center; gap: 10px;
  border-bottom: 2px solid #F0E8FF !important;
}
.card-header h5 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important; margin: 0 !important;
}
.header-violet { background: var(--violet-bg) !important; border-left: 4px solid var(--violet) !important; }
.header-teal   { background: var(--teal-soft)  !important; border-left: 4px solid var(--teal)   !important; }
.header-sky    { background: var(--sky-soft)   !important; border-left: 4px solid var(--sky)    !important; }

.section-icon {
  width: 30px; height: 30px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center; font-size: 0.95rem; flex-shrink: 0;
}
.si-violet { background: var(--violet-soft); }
.si-teal   { background: var(--teal-soft);   }
.si-sky    { background: var(--sky-soft);     }

.card-body { padding: 18px 20px !important; }

/* ── INFO ROWS ── */
.info-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 9px 0; border-bottom: 1px solid #F5F0FF;
}
.info-row:last-child { border-bottom: none; }
.info-label { font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); }
.info-value { font-size: 0.88rem; font-weight: 700; color: var(--text); text-align: right; }

/* ── ALERT ── */
.help-alert {
  border-radius: 10px; padding: 11px 15px;
  font-size: 0.86rem; font-weight: 600; line-height: 1.6;
  display: flex; gap: 10px; align-items: flex-start;
}
.alert-icon { font-size: 1rem; flex-shrink: 0; margin-top: 1px; }
.alert-info-custom { background: var(--sky-soft); border-left: 4px solid var(--sky); color: #1a508a; }

/* ── BUTTONS ── */
.btn { font-family: 'Nunito', sans-serif !important; font-weight: 800 !important; border-radius: 10px !important; transition: all 0.18s !important; font-size: 0.82rem !important; }
.btn-ghost-back {
  background: white; color: var(--text-muted); border: 1.5px solid #E8E0F0 !important;
  padding: 7px 16px; text-decoration: none; display: inline-flex; align-items: center;
}
.btn-ghost-back:hover { background: var(--violet-soft); color: var(--violet); border-color: var(--violet-soft) !important; }
.btn-logout {
  background: var(--coral-soft) !important; color: #c0294a !important;
  border: none !important; padding: 7px 16px;
  display: inline-flex; align-items: center;
}
.btn-logout:hover { background: var(--coral) !important; color: white !important; }
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/profile.blade.php ENDPATH**/ ?>