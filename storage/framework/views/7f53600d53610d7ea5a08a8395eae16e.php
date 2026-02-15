

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    <?php if($user->profile_path): ?>
      <img src="<?php echo e(asset($user->profile_path)); ?>" alt="Profile" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;">
    <?php else: ?>
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:1.25rem;">
        <?php echo e(strtoupper(substr($user->username, 0, 1))); ?>

      </div>
    <?php endif; ?>
    <div>
      <h1 class="h4 mb-1"><?php echo e($user->username); ?></h1>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="badge bg-primary text-capitalize"><?php echo e($user->role); ?></span>
        <?php $status = $user->status ?? 'active'; ?>
        <?php if($status === 'disabled'): ?>
          <span class="badge bg-secondary">Disabled</span>
        <?php elseif($status === 'reset_required'): ?>
          <span class="badge bg-warning text-dark">Reset Required</span>
        <?php else: ?>
          <span class="badge bg-success">Active</span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.users.edit', $user->user_id)); ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
    <form method="post" action="<?php echo e(route('admin.users.status', $user->user_id)); ?>" class="d-inline">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="status" value="<?php echo e(($status === 'disabled') ? 'active' : 'disabled'); ?>">
      <button type="submit" class="btn btn-outline-secondary btn-sm"><?php echo e($status === 'disabled' ? 'Enable' : 'Disable'); ?></button>
    </form>
    <form method="post" action="<?php echo e(route('admin.users.reset_password', $user->user_id)); ?>" class="d-inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-outline-secondary btn-sm">Reset Password</button>
    </form>
    <form method="post" action="<?php echo e(route('admin.users.force_reset', $user->user_id)); ?>" class="d-inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-outline-secondary btn-sm">Force Reset on Next Login</button>
    </form>
    <form method="post" action="<?php echo e(route('admin.users.resend_notification', $user->user_id)); ?>" class="d-inline">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn btn-outline-secondary btn-sm">Resend Notification</button>
    </form>
    <form method="post" action="<?php echo e(route('admin.users.destroy', $user->user_id)); ?>" class="d-inline" onsubmit="return confirm('Deleting this user may remove important linked data. Prefer disabling the account instead. Continue?');">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
    </form>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Account Information</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Username</dt>
          <dd class="col-7"><?php echo e($user->username); ?></dd>

          <dt class="col-5">Email</dt>
          <dd class="col-7"><?php echo e($user->email); ?></dd>

          <dt class="col-5">Role</dt>
          <dd class="col-7 text-capitalize"><?php echo e($user->role); ?></dd>

          <dt class="col-5">Created</dt>
          <dd class="col-7"><?php echo e(optional($user->created_at)->format('Y-m-d H:i')); ?></dd>

          <dt class="col-5">Last Updated</dt>
          <dd class="col-7"><?php echo e(optional($user->updated_at)->format('Y-m-d H:i')); ?></dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    <?php if($user->role === 'teacher'): ?>
      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Teacher Details</h2>
        </div>
        <div class="card-body small">
          <dl class="row mb-0">
            <dt class="col-4">First name</dt>
            <dd class="col-8"><?php echo e($user->teacher_first_name ?? '—'); ?></dd>
            <dt class="col-4">Last name</dt>
            <dd class="col-8"><?php echo e($user->teacher_last_name ?? '—'); ?></dd>
            <dt class="col-4">Home address</dt>
            <dd class="col-8"><?php echo e($user->teacher_home_address ?? '—'); ?></dd>
            <dt class="col-4">Phone number</dt>
            <dd class="col-8"><?php echo e($user->teacher_phone_number ?? '—'); ?></dd>
            <dt class="col-4">Hire date</dt>
            <dd class="col-8"><?php echo e($user->teacher_hire_date ?? '—'); ?></dd>
          </dl>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
          <h2 class="h6 mb-1">Assigned Students</h2>
          <span class="small text-muted"><?php echo e($teacherStudents->count()); ?> students · <?php echo e($teacherAssessmentsCount); ?> assessments</span>
        </div>
        <div class="card-body small">
          <?php if($teacherStudents->isEmpty()): ?>
            <p class="text-muted mb-0">No students are currently assigned to this teacher.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php $__currentLoopData = $teacherStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item px-0 d-flex justify-content-between">
                  <span><?php echo e($s->last_name); ?>, <?php echo e($s->first_name); ?></span>
                  <span class="text-muted">DOB: <?php echo e($s->date_of_birth); ?></span>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    <?php elseif($user->role === 'family'): ?>
      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Family Details</h2>
        </div>
        <div class="card-body small">
          <dl class="row mb-0">
            <dt class="col-4">Family name</dt>
            <dd class="col-8"><?php echo e($user->family_name ?? '—'); ?></dd>
            <dt class="col-4">Address</dt>
            <dd class="col-8"><?php echo e($user->family_home_address ?? '—'); ?></dd>
            <dt class="col-4">Emergency contact</dt>
            <dd class="col-8"><?php echo e($user->family_emergency_contact ?? '—'); ?></dd>
            <dt class="col-4">Emergency phone</dt>
            <dd class="col-8"><?php echo e($user->family_emergency_phone ?? '—'); ?></dd>
          </dl>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
          <h2 class="h6 mb-1">Linked Children</h2>
          <span class="small text-muted"><?php echo e($familyChildren->count()); ?> children · <?php echo e($familyCompletedTests); ?> completed tests</span>
        </div>
        <div class="card-body small">
          <?php if($familyChildren->isEmpty()): ?>
            <p class="text-muted mb-0">No students are currently linked to this family account.</p>
          <?php else: ?>
            <?php
              $teachersByStudent = $familyChildrenTeachers->groupBy('student_id');
            ?>
            <ul class="list-group list-group-flush">
              <?php $__currentLoopData = $familyChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item px-0">
                  <div class="d-flex justify-content-between">
                    <span><?php echo e($child->last_name); ?>, <?php echo e($child->first_name); ?></span>
                    <span class="text-muted">DOB: <?php echo e($child->date_of_birth); ?></span>
                  </div>
                  <?php $teachers = $teachersByStudent->get($child->student_id, collect()); ?>
                  <?php if($teachers->isNotEmpty()): ?>
                    <div class="text-muted small mt-1">
                      Teachers:
                      <?php echo e($teachers->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->teacher_username)->implode(', ')); ?>

                    </div>
                  <?php endif; ?>
                </li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Admin Account</h2>
        </div>
        <div class="card-body small">
          <p class="text-muted mb-0">This is an administrator account used for system configuration and oversight.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\users_show.blade.php ENDPATH**/ ?>