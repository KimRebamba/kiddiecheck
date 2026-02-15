

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Report</h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.reports')); ?>" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
  </div>
</div>

<div class="row g-3">
  <!-- Student & Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Student:</strong> <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></p>
            <?php
              $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
              $testDate = is_string($test->test_date) ? \Carbon\Carbon::parse($test->test_date) : $test->test_date;
            ?>
            <p><strong>Date of Birth:</strong> <?php echo e($dob->format('M d, Y')); ?></p>
            <p><strong>Age at Test:</strong> <?php echo e($dob->diffInYears($testDate)); ?> years</p>
          </div>
          <div class="col-md-6">
            <p><strong>Assessment Period:</strong> <?php echo e($period->description); ?></p>
            <p><strong>Period Dates:</strong> <?php echo e($period->start_date->format('M d, Y')); ?> - <?php echo e($period->end_date->format('M d, Y')); ?></p>
            <p><strong>Test Date:</strong> <?php echo e($test->test_date->format('M d, Y')); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- A. Test Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Test Summary</h5>
      </div>
      <div class="card-body">
        <?php
          $standardScore = $test->standardScore;
        ?>

        <?php if($test->domainScores && $test->domainScores->count() > 0): ?>
          <h6 class="mb-3">Domain Scores</h6>
          <div class="table-responsive mb-3">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th style="width: 40%;">Visual</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $test->domainScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domainScore): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><strong><?php echo e($domainScore->domain->name ?? 'Unknown'); ?></strong></td>
                    <td><?php echo e($domainScore->raw_score ?? 'N/A'); ?></td>
                    <td><?php echo e($domainScore->scaled_score ?? 'N/A'); ?></td>
                    <td>
                      <?php if($domainScore->scaled_score): ?>
                        <div class="progress" style="height: 20px;">
                          <?php
                            $percentage = min(100, ($domainScore->scaled_score / 19) * 100);
                          ?>
                          <div class="progress-bar" role="progressbar" style="width: <?php echo e($percentage); ?>%;" aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100">
                            <?php echo e($domainScore->scaled_score); ?>

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

        <?php if($standardScore): ?>
          <hr>
          <div class="row">
            <div class="col-md-6">
              <p class="mb-2"><strong>Sum of Scaled Scores:</strong> <?php echo e($standardScore->sum_scaled_scores); ?></p>
              <p class="mb-2"><strong>Standard Score:</strong> <?php echo e($standardScore->standard_score); ?></p>
            </div>
            <div class="col-md-6">
              <p class="mb-2"><strong>Interpretation:</strong> <span class="badge bg-info"><?php echo e($standardScore->interpretation); ?></span></p>
            </div>
          </div>
        <?php else: ?>
          <p class="text-muted">Standard score not yet calculated.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- B. Period Summary (if period is completed) -->
  <?php if($period->status === 'completed'): ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
          <h5 class="mb-0">Period Summary</h5>
        </div>
        <div class="card-body">
          <?php
            $periodSummary = \App\Models\PeriodSummaryScore::where('period_id', $period->period_id)->first();
          ?>

          <?php if($periodSummary): ?>
            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher's Avg Score:</strong><br>
                  <?php echo e($periodSummary->teachers_standard_score_avg ?? 'N/A'); ?>

                </p>
                <p class="mb-2">
                  <strong>Family Score:</strong><br>
                  <?php echo e($periodSummary->family_standard_score ?? 'Not provided'); ?>

                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Weighted Final Score:</strong><br>
                  <strong><?php echo e($periodSummary->final_standard_score ?? 'N/A'); ?></strong>
                </p>
                <p class="mb-2">
                  <strong>Final Interpretation:</strong><br>
                  <span class="badge bg-success"><?php echo e($periodSummary->final_interpretation ?? 'N/A'); ?></span>
                </p>
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher Discrepancy:</strong><br>
                  <span class="badge bg-warning"><?php echo e(ucfirst($periodSummary->teacher_discrepancy ?? 'none')); ?></span>
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher-Family Discrepancy:</strong><br>
                  <span class="badge bg-warning"><?php echo e(ucfirst($periodSummary->teacher_family_discrepancy ?? 'none')); ?></span>
                </p>
              </div>
            </div>
          <?php else: ?>
            <p class="text-muted">Period summary not available yet.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Test Notes -->
  <?php if($test->notes): ?>
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Notes</h5>
        </div>
        <div class="card-body">
          <p><?php echo e($test->notes); ?></p>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Actions -->
  <div class="col-md-12">
    <a href="<?php echo e(route('teacher.reports')); ?>" class="btn btn-outline-secondary">Back to Reports</a>
    <!-- PDF download can be added here later -->
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .card-header {
    border-bottom: 2px solid rgba(231, 122, 116, 0.3);
  }
</style>
<?php $__env->stopSection(); ?>

<<<<<<<< Updated upstream:storage/framework/views/06e13da22ab6c9ebf6377069edde8297.php
<<<<<<< Updated upstream
=======
<<<<<<<< Updated upstream:storage/framework/views/06e13da22ab6c9ebf6377069edde8297.php
>>>>>>> Stashed changes
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\report_detail.blade.php ENDPATH**/ ?>
========
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views\teacher\report_detail.blade.php ENDPATH**/ ?>
>>>>>>>> Stashed changes:storage/framework/views/10af1cfb588a4fd8b8e71075d99e07f8.php
<<<<<<< Updated upstream
=======
========
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xamppkiddiecheck\htdocs\kiddiecheck\resources\views\teacher\report_detail.blade.php ENDPATH**/ ?>
>>>>>>>> Stashed changes:storage/framework/views/10af1cfb588a4fd8b8e71075d99e07f8.php
>>>>>>> Stashed changes
