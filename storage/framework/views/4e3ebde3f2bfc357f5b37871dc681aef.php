

<?php $__env->startSection('content'); ?>
<?php
  $isEdit = $mode === 'edit';
?>

<h1 class="h4 mb-3"><?php echo e($isEdit ? 'Edit User' : 'Create New User'); ?></h1>

<form method="post" action="<?php echo e($isEdit ? route('admin.users.update', $user->user_id) : route('admin.users.store')); ?>">
  <?php echo csrf_field(); ?>
  <?php if($isEdit): ?>
    <?php echo method_field('PUT'); ?>
  <?php endif; ?>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <label class="form-label">Username</label>
      <input type="text" name="username" value="<?php echo e(old('username', $user->username ?? '')); ?>" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Email</label>
      <input type="email" name="email" value="<?php echo e(old('email', $user->email ?? '')); ?>" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Role</label>
      <select name="role" id="role" class="form-select form-select-sm" required>
        <?php $role = old('role', $user->role ?? 'teacher'); ?>
        <option value="admin" <?php echo e($role === 'admin' ? 'selected' : ''); ?>>Admin</option>
        <option value="teacher" <?php echo e($role === 'teacher' ? 'selected' : ''); ?>>Teacher</option>
        <option value="family" <?php echo e($role === 'family' ? 'selected' : ''); ?>>Family</option>
      </select>
      <?php if($isEdit): ?>
        <div class="form-text">Changing role may impact linked records.</div>
        <div class="form-check mt-1">
          <input class="form-check-input" type="checkbox" name="confirm_role_change" id="confirm_role_change" value="1">
          <label class="form-check-label" for="confirm_role_change">I understand changing the role can affect assignments.</label>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <label class="form-label">Profile image path (optional)</label>
      <input type="text" name="profile_path" value="<?php echo e(old('profile_path', $user->profile_path ?? '')); ?>" class="form-control form-control-sm">
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Password <?php echo e($isEdit ? '(leave blank to keep current)' : ''); ?></label>
      <input type="password" name="password" class="form-control form-control-sm" <?php echo e($isEdit ? '' : 'required'); ?>>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="password_confirmation" class="form-control form-control-sm" <?php echo e($isEdit ? '' : 'required'); ?>>
    </div>
  </div>

  <hr>

  <h2 class="h6 mb-2">Role-specific Details</h2>

  <div id="teacher-fields" class="mb-3">
    <h3 class="h6">Teacher Information</h3>
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">First name</label>
        <input type="text" name="teacher_first_name" value="<?php echo e(old('teacher_first_name', $user->teacher_first_name ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Last name</label>
        <input type="text" name="teacher_last_name" value="<?php echo e(old('teacher_last_name', $user->teacher_last_name ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Home address</label>
        <input type="text" name="teacher_home_address" value="<?php echo e(old('teacher_home_address', $user->teacher_home_address ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Phone number</label>
        <input type="text" name="teacher_phone_number" value="<?php echo e(old('teacher_phone_number', $user->teacher_phone_number ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Hire date</label>
        <input type="date" name="teacher_hire_date" value="<?php echo e(old('teacher_hire_date', isset($user->teacher_hire_date) ? substr($user->teacher_hire_date, 0, 10) : '')); ?>" class="form-control form-control-sm">
      </div>
    </div>
  </div>

  <div id="family-fields" class="mb-3">
    <h3 class="h6">Family Information</h3>
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Family name</label>
        <input type="text" name="family_name" value="<?php echo e(old('family_name', $user->family_name ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Address</label>
        <input type="text" name="family_home_address" value="<?php echo e(old('family_home_address', $user->family_home_address ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Emergency contact</label>
        <input type="text" name="family_emergency_contact" value="<?php echo e(old('family_emergency_contact', $user->family_emergency_contact ?? '')); ?>" class="form-control form-control-sm">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Emergency phone</label>
        <input type="text" name="family_emergency_phone" value="<?php echo e(old('family_emergency_phone', $user->family_emergency_phone ?? '')); ?>" class="form-control form-control-sm">
      </div>
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary btn-sm"><?php echo e($isEdit ? 'Save Changes' : 'Create User'); ?></button>
    <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
  </div>
</form>

<script>
  (function() {
    function toggleRoleFields() {
      var role = document.getElementById('role').value;
      var teacher = document.getElementById('teacher-fields');
      var family = document.getElementById('family-fields');
      teacher.style.display = (role === 'teacher') ? 'block' : 'none';
      family.style.display = (role === 'family') ? 'block' : 'none';
    }
    document.getElementById('role').addEventListener('change', toggleRoleFields);
    toggleRoleFields();
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\users_form.blade.php ENDPATH**/ ?>