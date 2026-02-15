

<?php $__env->startSection('content'); ?>
  <h1 class="h4 mb-3">Scales</h1>
  <p class="text-muted mb-2">Manage assessment scales and view ECCD data.</p>
  <a href="<?php echo e(route('admin.eccd')); ?>" class="btn btn-outline-primary btn-sm">Open ECCD Scale Explorer</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views/admin/scales.blade.php ENDPATH**/ ?>