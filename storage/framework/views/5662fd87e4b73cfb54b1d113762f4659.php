

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.sections')); ?>" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Student Information -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Student Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></p>
        <?php
          $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
        ?>
        <p class="mb-2"><strong>Date of Birth:</strong> <?php echo e($dob ? $dob->format('M d, Y') : 'N/A'); ?></p>
        <p class="mb-2"><strong>Age:</strong> <?php echo e($student->age ?? 'N/A'); ?> years</p>
        <p class="mb-2"><strong>Section:</strong> <?php echo e(optional($student->section)->name ?? 'N/A'); ?></p>
        <p class="mb-2"><strong>Family:</strong> <?php echo e(optional($student->family)->family_name ?? 'N/A'); ?></p>
      </div>
    </div>
  </div>

  <!-- Test Status -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Status</h5>
      </div>
      <div class="card-body">
        <p class="mb-2">
          <strong>Eligible for Test:</strong><br>
          <?php if($student->eligible): ?>
            <span class="badge bg-success">Yes</span>
          <?php else: ?>
            <span class="badge bg-secondary">No</span>
          <?php endif; ?>
        </p>
        <p class="mb-2">
          <strong>Last Standard Score:</strong><br>
          <?php echo e($student->last_standard_score ?? 'No score'); ?>

        </p>
      </div>
    </div>
  </div>

  <!-- Assessment Periods -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Periods</h5>
      </div>
      <div class="card-body">
        <?php if($student->assessmentPeriods->isEmpty()): ?>
          <p class="text-muted">No assessment periods.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Dates</th>
                  <th>Status</th>
                  <th>Tests</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $student->assessmentPeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $tests = $period->tests()->where('examiner_id', auth()->id())->get();
                  ?>
                  <tr>
                    <td><strong><?php echo e($period->description); ?></strong></td>
                    <td><?php echo e($period->start_date->format('M d')); ?> - <?php echo e($period->end_date->format('M d, Y')); ?></td>
                    <td>
                      <span class="badge bg-<?php echo e($period->status === 'completed' ? 'success' : ($period->status === 'overdue' ? 'danger' : 'info')); ?>">
                        <?php echo e(ucfirst($period->status)); ?>

                      </span>
                    </td>
                    <td><?php echo e($tests->count()); ?></td>
                    <td>
                      <?php if($student->eligible && $period->status !== 'completed'): ?>
                        <form action="<?php echo e(route('teacher.tests.start', $student->student_id)); ?>" method="POST" style="display: inline;">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="period_id" value="<?php echo e($period->period_id); ?>">
                          <button type="submit" class="btn btn-sm btn-outline-primary">Start Test</button>
                        </form>
                      <?php else: ?>
                        <span class="text-muted small">Not eligible</span>
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

  <!-- Previous Tests -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Test History</h5>
      </div>
      <div class="card-body">
        <?php
          $teacherTests = $student->tests()
            ->where('examiner_id', auth()->id())
            ->orderBy('test_date', 'desc')
            ->get();
        ?>
        
        <?php if($teacherTests->isEmpty()): ?>
          <p class="text-muted">No tests yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Period</th>
                  <th>Status</th>
                  <th>Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $teacherTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $standardScore = $test->standardScore;
                  ?>
                  <tr>
                    <td><?php echo e($test->test_date->format('M d, Y')); ?></td>
                    <td><?php echo e(optional($test->assessmentPeriod)->description ?? 'N/A'); ?></td>
                    <td>
                      <span class="badge bg-<?php echo e($test->status === 'finalized' ? 'success' : 
                        ($test->status === 'completed' ? 'info' : 
                        ($test->status === 'canceled' ? 'danger' : 'warning'))); ?>">
                        <?php echo e(ucfirst($test->status)); ?>

                      </span>
                    </td>
                    <td><?php echo e($standardScore ? $standardScore->standard_score : 'N/A'); ?></td>
                    <td>
                      <a href="<?php echo e(route('teacher.reports.detail', [$student->student_id, $test->period_id, $test->test_id])); ?>" class="btn btn-xs btn-outline-secondary" style="font-size: 0.8rem;">View</a>
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
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .btn-xs {
    padding: 0.25rem 0.5rem;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views/teacher/student.blade.php ENDPATH**/ ?>