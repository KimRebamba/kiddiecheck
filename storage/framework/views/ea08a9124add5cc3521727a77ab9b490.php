<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Teacher Dashboard</h1>
</div>

<?php
  $inProgress = collect($students)->map(function($s) use ($status) {
    $st = $status[$s->student_id] ?? null;
    return ($st && ($st['in_progress'] ?? null)) ? ['student' => $s, 'test' => $st['in_progress']] : null;
  })->filter();
  $eligibleNow = collect($students)->filter(function($s) use ($status) {
    $st = $status[$s->student_id] ?? null;
    return $st && ($st['eligible'] ?? false);
  });
  $recentCompleted = \App\Models\Test::with(['student'])
    ->where('examiner_id', auth()->id())
    ->where('status', 'completed')
    ->orderByDesc('test_date')
    ->limit(10)
    ->get();
  
  // Get assessment periods for upcoming and overdue assessments
  $upcomingAssessments = \App\Models\AssessmentPeriod::with(['student'])
    ->whereHas('student.teachers', function($q) {
      $q->where('user_id', auth()->id());
    })
    ->where('status', 'scheduled')
    ->where('end_date', '>=', now())
    ->orderBy('end_date')
    ->get();
    
  $overdueAssessments = \App\Models\AssessmentPeriod::with(['student'])
    ->whereHas('student.teachers', function($q) {
      $q->where('user_id', auth()->id());
    })
    ->where('status', 'scheduled')
    ->where('end_date', '<', now())
    ->orderBy('end_date')
    ->get();
?>

<!-- Teacher Profile Summary -->
<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
            <?php echo e(strtoupper(substr(auth()->user()->username, 0, 1))); ?>

          </div>
          <div>
            <h5 class="mb-1"><?php echo e(auth()->user()->username); ?></h5>
            <p class="text-muted mb-0"><?php echo e(ucfirst(auth()->user()->role)); ?> Account</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-primary fs-2 mb-2">
            <i class="fas fa-users"></i>
          </div>
          <h6 class="text-muted fw-semibold">Assigned Students</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-primary"><?php echo e($students->count()); ?></div>
        <div class="text-muted small">Active in your sections</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-info fs-2 mb-2">
            <i class="fas fa-calendar-check"></i>
          </div>
          <h6 class="text-muted fw-semibold">Upcoming Assessments</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-info"><?php echo e($upcomingAssessments->count()); ?></div>
        <div class="text-muted small">Status = scheduled</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-danger fs-2 mb-2">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h6 class="text-muted fw-semibold">Overdue Assessments</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-danger"><?php echo e($overdueAssessments->count()); ?></div>
        <div class="text-muted small">Past due date</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-success fs-2 mb-2">
            <i class="fas fa-check-circle"></i>
          </div>
          <h6 class="text-muted fw-semibold">Recently Completed</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-success"><?php echo e(count($recentCompleted)); ?></div>
        <div class="text-muted small">Last 30 days</div>
      </div>
    </div>
  </div>
</div>

<!-- Upcoming Assessments Table -->
<?php if($upcomingAssessments->isNotEmpty()): ?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-calendar-check me-2"></i>Upcoming Assessments
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Period</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $upcomingAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($assessment->student->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($assessment->student->first_name); ?> <?php echo e($assessment->student->last_name); ?></div>
                    <div class="text-muted small">Age: <?php echo e($assessment->student->date_of_birth->age); ?> years</div>
                  </div>
                </div>
              </td>
              <td><?php echo e($assessment->description); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($assessment->end_date)->format('M j, Y')); ?></td>
              <td><span class="badge bg-info"><?php echo e($assessment->status); ?></span></td>
              <td>
                <a href="<?php echo e(route('teacher.tests.start', $assessment->student->student_id)); ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-play me-1"></i>Start Test
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Overdue Assessments -->
<?php if($overdueAssessments->isNotEmpty()): ?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-exclamation-triangle me-2"></i>Overdue Assessments
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Period</th>
            <th>Due Date</th>
            <th>Days Overdue</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $overdueAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($assessment->student->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($assessment->student->first_name); ?> <?php echo e($assessment->student->last_name); ?></div>
                    <div class="text-muted small">Age: <?php echo e($assessment->student->date_of_birth->age); ?> years</div>
                  </div>
                </div>
              </td>
              <td><?php echo e($assessment->description); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($assessment->end_date)->format('M j, Y')); ?></td>
              <td><span class="badge bg-danger"><?php echo e(\Carbon\Carbon::parse($assessment->end_date)->diffInDays(now())); ?> days</span></td>
              <td>
                <a href="<?php echo e(route('teacher.tests.start', $assessment->student->student_id)); ?>" class="btn btn-sm btn-danger">
                  <i class="fas fa-exclamation me-1"></i>Start Now
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Students Table -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-users me-2"></i>Assigned Students
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Age</th>
            <th>Section</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($student->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></div>
                    <div class="text-muted small"><?php echo e($student->date_of_birth->format('Y-m-d')); ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo e($student->date_of_birth->age); ?> years</td>
              <td>
                <span class="badge bg-primary"><?php echo e($student->section->name ?? 'N/A'); ?></span>
              </td>
              <td>
                <?php
                  $st = $status[$student->student_id] ?? null;
                  $statusClass = '';
                  $statusText = 'No Status';
                  if ($st) {
                      if ($st['in_progress']) {
                          $statusClass = 'bg-warning';
                          $statusText = 'In Progress';
                      } elseif ($st['eligible']) {
                          $statusClass = 'bg-info';
                          $statusText = 'Eligible';
                      } elseif (isset($st['completed']) && $st['completed']) {
                          $statusClass = 'bg-success';
                          $statusText = 'Completed';
                      }
                  }
                ?>
                <span class="badge <?php echo e($statusClass); ?> <?php echo e($statusText == 'No Status' ? 'no-status' : ''); ?>"><?php echo e($statusText); ?></span>
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="<?php echo e(route('teacher.student', $student->student_id)); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>View
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- In-Progress Tests -->
<?php if($inProgress->isNotEmpty()): ?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-clock me-2"></i>In-Progress Tests
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Test Date</th>
            <th>Progress</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $inProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($item['student']->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($item['student']->first_name); ?> <?php echo e($item['student']->last_name); ?></div>
                    <div class="text-muted small">Age: <?php echo e($item['student']->date_of_birth->age); ?> years</div>
                  </div>
                </div>
              </td>
              <td><?php echo e($item['test']->test_date->format('M j, Y')); ?></td>
              <td>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-warning" style="width: 60%"></div>
                </div>
                <div class="text-muted small mt-1">60% Complete</div>
              </td>
              <td>
                <a href="<?php echo e(route('teacher.tests.question', ['test' => $item['test']->test_id, 'domain' => 1, 'index' => 0])); ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-play me-1"></i>Continue
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Eligible Students -->
<?php if($eligibleNow->isNotEmpty()): ?>
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-user-check me-2"></i>Eligible Students
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Age</th>
            <th>Section</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $eligibleNow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($student->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></div>
                    <div class="text-muted small"><?php echo e($student->date_of_birth->format('Y-m-d')); ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo e($student->date_of_birth->age); ?> years</td>
              <td>
                <span class="badge bg-info"><?php echo e($student->section->name ?? 'N/A'); ?></span>
              </td>
              <td>
                <a href="<?php echo e(route('teacher.tests.start', $student->student_id)); ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus me-1"></i>Start Test
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Recent Completed Tests -->
<?php if($recentCompleted->isNotEmpty()): ?>
<div class="card border-0 shadow-sm">
  <div class="card-header">
    <h5 class="card-title mb-0">
      <i class="fas fa-check-circle me-2"></i>Recent Completed Tests
    </h5>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student</th>
            <th>Test Date</th>
            <th>Score</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $recentCompleted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <div class="avatar me-3">
                    <?php echo e(strtoupper(substr($test->student->first_name, 0, 1))); ?>

                  </div>
                  <div>
                    <div class="fw-semibold"><?php echo e($test->student->first_name); ?> <?php echo e($test->student->last_name); ?></div>
                    <div class="text-muted small"><?php echo e($test->student->date_of_birth->format('Y-m-d')); ?></div>
                  </div>
                </div>
              </td>
              <td><?php echo e($test->test_date->format('M j, Y')); ?></td>
              <td>
                <div class="fw-bold text-success">85/100</div>
              </td>
              <td>
                <span class="badge bg-success">Completed</span>
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="<?php echo e(route('teacher.tests.result', $test->test_id)); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>View
                  </a>
                  <a href="<?php echo e(route('teacher.reports.show', ['student' => $test->student_id, 'period' => $test->assessment_period_id])); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-alt me-1"></i>Report
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--teacher-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/dashboard.blade.php ENDPATH**/ ?>