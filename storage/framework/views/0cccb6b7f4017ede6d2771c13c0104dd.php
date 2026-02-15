

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Reports</h1>
</div>

<div class="row g-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Select Assessment to Review</h5>
      </div>
      <div class="card-body">
        <?php if($tests->isEmpty()): ?>
          <p class="text-muted">No completed assessments to review.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Status</th>
                  <th>Standard Score</th>
                  <th>Interpretation</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $standardScore = $test->standardScore;
                  ?>
                  <tr>
                    <td>
                      <strong><?php echo e($test->student->first_name); ?> <?php echo e($test->student->last_name); ?></strong>
                    </td>
                    <td><?php echo e($test->assessmentPeriod->description); ?></td>
                    <td><?php echo e($test->test_date->format('M d, Y')); ?></td>
                    <td>
                      <span class="badge bg-success"><?php echo e(ucfirst($test->status)); ?></span>
                    </td>
                    <td>
                      <?php if($standardScore): ?>
                        <strong><?php echo e($standardScore->standard_score); ?></strong>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($standardScore): ?>
                        <?php echo e($standardScore->interpretation); ?>

                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <a href="<?php echo e(route('teacher.reports.detail', [$test->student_id, $test->period_id, $test->test_id])); ?>" class="btn btn-sm btn-outline-primary">
                        View Details
                      </a>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(231, 122, 116, 0.1);
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\reports.blade.php ENDPATH**/ ?>