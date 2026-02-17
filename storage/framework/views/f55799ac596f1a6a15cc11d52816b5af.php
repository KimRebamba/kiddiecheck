

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Sections</h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.sections.create')); ?>" class="btn btn-primary">Add Section</a>
  </div>
</div>

<?php if($sections->isEmpty()): ?>
  <div class="alert alert-info" role="alert">
    No sections with assigned students.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo e($section->name); ?></h5>
            <p class="card-text text-muted">
              <strong>Students:</strong> <?php echo e($section->student_count); ?>

            </p>
            <?php if($section->description): ?>
              <p class="card-text" style="font-size: 0.85rem;"><?php echo e(Str::limit($section->description, 60)); ?></p>
            <?php endif; ?>
            <div class="btn-group btn-group-sm" role="group">
              <a href="<?php echo e(route('teacher.sections.show', $section->section_id)); ?>" class="btn btn-outline-primary">View</a>
              <a href="<?php echo e(route('teacher.sections.edit', $section->section_id)); ?>" class="btn btn-outline-secondary">Edit</a>
              <form action="<?php echo e(route('teacher.sections.destroy', $section->section_id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-outline-danger">Delete</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s;
  }
  .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/sections.blade.php ENDPATH**/ ?>