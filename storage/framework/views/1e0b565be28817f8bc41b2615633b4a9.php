

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Reports - <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.reports')); ?>" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h5 class="mb-0">Assessment Period</h5>
  </div>
  <div class="card-body">
    <p class="mb-1"><strong>Period:</strong> <?php echo e($period->description); ?></p>
    <p class="mb-1"><strong>Dates:</strong> <?php echo e($period->start_date->format('M d, Y')); ?> - <?php echo e($period->end_date->format('M d, Y')); ?></p>
    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-<?php echo e($period->status === 'completed' ? 'success' : ($period->status === 'overdue' ? 'danger' : 'info')); ?>"><?php echo e(ucfirst($period->status)); ?></span></p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Finalized Tests in this Period</h5>
  </div>
  <div class="card-body">
    <?php if($tests->isEmpty()): ?>
      <p class="text-muted mb-0">No finalized tests for this period.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover table-sm align-middle">
          <thead>
            <tr>
              <th>Test Date</th>
              <th>Status</th>
              <th>Standard Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $score = $test->standardScore; ?>
              <tr>
                <td><?php echo e($test->test_date->format('M d, Y')); ?></td>
                <td><span class="badge bg-success"><?php echo e(ucfirst($test->status)); ?></span></td>
                <td>
                  <?php if($score): ?>
                    <strong><?php echo e($score->standard_score); ?></strong>
                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($score): ?>
                    <?php echo e($score->interpretation); ?>

                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="<?php echo e(route('teacher.reports.detail', [$student->student_id, $period->period_id, $test->test_id])); ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\report_show.blade.php ENDPATH**/ ?>