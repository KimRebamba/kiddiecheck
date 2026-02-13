

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Results - <?php echo e($test->student->first_name); ?> <?php echo e($test->student->last_name); ?></h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.index')); ?>" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <p class="text-muted mb-1">Test Date</p>
            <p class="fw-semibold"><?php echo e($test->test_date->format('M d, Y')); ?></p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Period</p>
            <p class="fw-semibold"><?php echo e(optional($test->assessmentPeriod)->description ?? 'N/A'); ?></p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Status</p>
            <p>
              <span class="badge bg-<?php echo e($test->status === 'finalized' ? 'success' : 
                ($test->status === 'completed' ? 'info' : 'warning')); ?>">
                <?php echo e(ucfirst($test->status)); ?>

              </span>
            </p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="fw-semibold"><?php echo e($standardScore ?? 'Not calculated'); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Domain Scores -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Domain Scores</h5>
      </div>
      <div class="card-body p-0">
        <?php if($test->domainScores->isEmpty()): ?>
          <p class="p-3 text-muted">No domain scores yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead class="table-light">
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th>Progress</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $test->domainScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $score): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><strong><?php echo e($score->domain->name ?? 'Unknown'); ?></strong></td>
                    <td><?php echo e($score->raw_score ?? 'N/A'); ?></td>
                    <td><?php echo e($score->scaled_score ?? 'N/A'); ?></td>
                    <td>
                      <?php if($score->scaled_score): ?>
                        <div class="progress" style="height: 20px;">
                          <?php
                            $percentage = min(100, ($score->scaled_score / 19) * 100);
                          ?>
                          <div class="progress-bar" role="progressbar" style="width: <?php echo e($percentage); ?>%;" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo e($score->scaled_score); ?>

                          </div>
                        </div>
                      <?php endif; ?>
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

  <!-- Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Summary</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="text-muted mb-1">Sum of Scaled Scores</p>
            <p class="display-6"><?php echo e($sumScaled); ?></p>
          </div>
          <div class="col-md-6">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="display-6"><?php echo e($standardScore ?? 'N/A'); ?></p>
          </div>
        </div>
        <?php if($interpretation): ?>
          <p class="mt-3">
            <strong>Interpretation:</strong>
            <span class="badge bg-info"><?php echo e($interpretation); ?></span>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="col-md-12">
    <div class="d-flex gap-2">
      <?php if($test->status === 'completed'): ?>
        <form action="<?php echo e(route('teacher.tests.finalize', $test->test_id)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-success">Finalize Test</button>
        </form>
        <form action="<?php echo e(route('teacher.tests.cancel', $test->test_id)); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-outline-danger">Cancel Test</button>
        </form>
      <?php elseif($test->status === 'finalized'): ?>
        <span class="badge bg-success" style="padding: 0.5rem 1rem;">Test Finalized</span>
      <?php endif; ?>

      <a href="<?php echo e(route('teacher.index')); ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
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

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/test_result.blade.php ENDPATH**/ ?>