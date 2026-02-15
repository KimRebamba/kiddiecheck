

<?php $__env->startSection('content'); ?>
  <div class="row mb-3">
    <div class="col-12">
      <h1 class="h4 mb-1">Admin Profile</h1>
      <p class="text-muted mb-0">Manage your personal account details and security.</p>
    </div>
  </div>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following issues:</div>
      <ul class="mb-0 small">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-lg-4">
      
      <div class="card mb-3">
        <div class="card-body text-center">
          <?php if($user->profile_path): ?>
            <img src="<?php echo e(asset($user->profile_path)); ?>" alt="Profile" class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;">
          <?php else: ?>
            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:96px;height:96px;font-size:2rem;">
              <?php echo e(strtoupper(substr($user->username ?? 'A', 0, 1))); ?>

            </div>
          <?php endif; ?>

          <h2 class="h6 mb-1"><?php echo e($user->username); ?></h2>
          <p class="text-muted small mb-2">Administrator</p>
          <p class="text-muted small mb-3"><?php echo e($user->email); ?></p>

          <a href="#profile-edit" class="btn btn-outline-primary btn-sm">Change profile picture</a>
        </div>
      </div>

      
      <div class="card">
        <div class="card-header py-2">
          <span class="fw-semibold small">System Role</span>
        </div>
        <div class="card-body small">
          <p class="mb-1"><span class="fw-semibold">Role:</span> Administrator</p>
          <p class="text-muted mb-0">
            Administrators manage users, assessments, and system configuration.
          </p>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      
      <div class="card mb-3">
        <div class="card-header py-2">
          <span class="fw-semibold small">Account Information</span>
        </div>
        <div class="card-body small">
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Username</div>
            <div class="col-sm-8"><?php echo e($user->username); ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Email address</div>
            <div class="col-sm-8"><?php echo e($user->email); ?></div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Role</div>
            <div class="col-sm-8">Administrator</div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Account created</div>
            <div class="col-sm-8"><?php echo e(optional($user->created_at)->format('M d, Y H:i')); ?></div>
          </div>
          <div class="row mb-0">
            <div class="col-sm-4 text-muted">Last updated</div>
            <div class="col-sm-8"><?php echo e(optional($user->updated_at)->format('M d, Y H:i')); ?></div>
          </div>
        </div>
      </div>

      
      <div class="card mb-3" id="profile-edit">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
          <span class="fw-semibold small">Editable Profile Details</span>
        </div>
        <div class="card-body">
          <form method="post" action="<?php echo e(route('admin.profile.update')); ?>" enctype="multipart/form-data" class="small">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Email address</label>
              <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" value="<?php echo e(old('username', $user->username)); ?>" class="form-control form-control-sm" required>
              <div class="form-text">Your login username. Changing this will not log you out.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Profile picture</label>
              <input type="file" name="profile_image" accept="image/*" class="form-control form-control-sm">
              <div class="form-text">Optional. JPG or PNG, up to 2 MB.</div>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="card mb-3">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
          <span class="fw-semibold small">Security</span>
        </div>
        <div class="card-body">
          <form method="post" action="<?php echo e(route('admin.profile.password')); ?>" class="small">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Current password</label>
              <input type="password" name="current_password" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">New password</label>
              <input type="password" name="password" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm new password</label>
              <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
            </div>
            <p class="text-muted small mb-3">For security reasons, your password is never displayed.</p>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-outline-primary btn-sm">Change password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/admin/profile.blade.php ENDPATH**/ ?>