<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Create Section</h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.sections')); ?>" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Section Information</h5>
      </div>
      <div class="card-body">
        <form action="<?php echo e(route('teacher.sections.store')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          
          <div class="mb-3">
            <label for="name" class="form-label">Section Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo e(old('name')); ?>" required maxlength="255">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <div class="text-danger small mt-1"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Section</button>
            <a href="<?php echo e(route('teacher.sections')); ?>" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/sections_create.blade.php ENDPATH**/ ?>