

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">ECCD Overview</h1>
</div>

<?php if(session('success')): ?>
  <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
  <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php if($students->isEmpty()): ?>
  <div class="card">
    <div class="card-body">
      <p class="mb-0 text-muted">No assigned students with finalized ECCD assessments yet.</p>
    </div>
  </div>
<?php else: ?>
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Student ECCD Summary</h5>
      <?php
        $user = $teacher;
        $teacherProfile = $user->teacher ?? null;
        $displayName = $teacherProfile && ($teacherProfile->first_name || $teacherProfile->last_name)
          ? trim(($teacherProfile->first_name ?? '').' '.($teacherProfile->last_name ?? ''))
          : ($user->username ?? $user->email ?? '');
      ?>
      <span class="text-muted small">Teacher: <?php echo e($displayName); ?></span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Student</th>
              <th>Section</th>
              <th>Latest Period</th>
              <th>Latest Test Date</th>
              <th>Standard Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $latestTest = $student->tests->sortByDesc('test_date')->first();
                $standardScore = $latestTest?->standardScore;
                $latestPeriod = $latestTest?->assessmentPeriod;
              ?>
              <tr>
                <td>
                  <strong><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></strong><br>
                  <small class="text-muted">ID: <?php echo e($student->student_id); ?></small>
                </td>
                <td>
                  <?php if($student->section): ?>
                    <?php echo e($student->section->name); ?>

                  <?php else: ?>
                    <span class="text-muted">Unassigned</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($latestPeriod): ?>
                    <?php echo e($latestPeriod->description); ?>

                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($latestTest): ?>
                    <?php echo e($latestTest->test_date->format('M d, Y')); ?>

                  <?php else: ?>
                    <span class="text-muted">N/A</span>
                  <?php endif; ?>
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
                  <?php if($latestPeriod && $latestTest): ?>
                    <a href="<?php echo e(route('teacher.reports.detail', [$student->student_id, $latestPeriod->period_id, $latestTest->test_id])); ?>" class="btn btn-sm btn-outline-primary">
                      View Details
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">No finalized test</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $tests = $student->tests->sortBy('test_date');
    ?>
    <?php if($tests->isNotEmpty()): ?>
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></h5>
              <small class="text-muted">Longitudinal ECCD Scores</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Standard Score</th>
                  <th>Interpretation</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $score = $test->standardScore; ?>
                  <tr>
                    <td><?php echo e($test->assessmentPeriod?->description ?? 'N/A'); ?></td>
                    <td><?php echo e($test->test_date?->format('M d, Y') ?? 'N/A'); ?></td>
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
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\eccd.blade.php ENDPATH**/ ?>