

<?php $__env->startSection('content'); ?>
<?php $isEdit = $mode === 'edit'; ?>

<h1 class="h4 mb-3"><?php echo e($isEdit ? 'Edit Student' : 'Add New Student'); ?></h1>

<form method="post" action="<?php echo e($isEdit ? route('admin.students.update', $student->student_id) : route('admin.students.store')); ?>">
  <?php echo csrf_field(); ?>
  <?php if($isEdit): ?>
    <?php echo method_field('PUT'); ?>
  <?php endif; ?>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <label class="form-label">First name</label>
      <input type="text" name="first_name" value="<?php echo e(old('first_name', $student->first_name ?? '')); ?>" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Last name</label>
      <input type="text" name="last_name" value="<?php echo e(old('last_name', $student->last_name ?? '')); ?>" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Date of birth</label>
      <input type="date" name="date_of_birth" value="<?php echo e(old('date_of_birth', isset($student->date_of_birth) ? substr($student->date_of_birth, 0, 10) : '')); ?>" class="form-control form-control-sm" required>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-6">
      <label class="form-label">Family</label>
      <select name="family_id" class="form-select form-select-sm" required>
        <option value="">Select family...</option>
        <?php $__currentLoopData = $families; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($f->user_id); ?>" <?php echo e((string)old('family_id', $student->family_id ?? '') === (string)$f->user_id ? 'selected' : ''); ?>>
            <?php echo e($f->family_name); ?> (<?php echo e($f->email); ?>)
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Photo path (optional)</label>
      <input type="text" name="feature_path" value="<?php echo e(old('feature_path', $student->feature_path ?? '')); ?>" class="form-control form-control-sm">
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary btn-sm"><?php echo e($isEdit ? 'Save Changes' : 'Create Student'); ?></button>
    <a href="<?php echo e(route('admin.students')); ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\students_form.blade.php ENDPATH**/ ?>