

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
  $recentCompleted = \App\Models\Test::with(['student','standardScore'])
    ->where('examiner_id', auth()->id())
    ->whereIn('status', ['completed', 'finalized'])
    ->where('test_date', '>=', now()->subDays(30))
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
                <?php
                  $tests = \App\Models\Test::where('student_id', $assessment->student->student_id)
                    ->where('period_id', $assessment->period_id)
                    ->where('examiner_id', auth()->id())
                    ->get();
                  $inProgressTest = $tests->firstWhere('status', 'in_progress');
                  $completedTest = $tests->firstWhere('status', 'completed');
                  $finalizedTest = $tests->firstWhere('status', 'finalized');
                  $viewableTest = $finalizedTest ?: $completedTest;
                  $st = $status[$assessment->student->student_id] ?? null;
                  $eligible = $st['eligible'] ?? false;
                ?>
                <?php if($inProgressTest): ?>
                  <a href="<?php echo e(route('teacher.tests.form', $inProgressTest->test_id)); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-play me-1"></i>Continue Test
                  </a>
                <?php elseif($viewableTest): ?>
                  <a href="<?php echo e(route('teacher.tests.result', $viewableTest->test_id)); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-eye me-1"></i>View Result
                  </a>
                <?php elseif(!$eligible): ?>
                  <span class="text-muted small">Not eligible for new test yet.</span>
                <?php else: ?>
                  <form action="<?php echo e(route('teacher.tests.start', $assessment->student->student_id)); ?>" method="POST" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="period_id" value="<?php echo e($assessment->period_id); ?>">
                    <button type="submit" class="btn btn-sm btn-primary">
                      <i class="fas fa-play me-1"></i>Start Test
                    </button>
                  </form>
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
                <span class="text-muted small">Period overdue. New tests cannot be started.</span>
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
                <?php
                  $totalQuestions = \DB::table('questions')
                    ->join('domains', 'questions.domain_id', '=', 'domains.domain_id')
                    ->count();
                  $answered = \DB::table('test_responses')
                    ->where('test_id', $item['test']->test_id)
                    ->count();
                  $pct = $totalQuestions ? round(($answered / max(1,$totalQuestions)) * 100) : 0;
                ?>
                <div class="progress" style="height: 6px;">
                  <div class="progress-bar bg-warning" style="width: <?php echo e($pct); ?>%"></div>
                </div>
                <div class="text-muted small mt-1"><?php echo e($answered); ?> / <?php echo e($totalQuestions); ?> (<?php echo e($pct); ?>%)</div>
              </td>
              <td>
                <a href="<?php echo e(route('teacher.tests.form', $item['test']->test_id)); ?>" class="btn btn-sm btn-primary">
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
                <?php
                  $activeTest = \App\Models\Test::where('student_id', $student->student_id)
                    ->where('examiner_id', auth()->id())
                    ->whereHas('assessmentPeriod', function ($q) {
                      $q->where('status', 'scheduled');
                    })
                    ->orderByDesc('test_date')
                    ->first();
                ?>
                <?php if($activeTest && $activeTest->status === 'in_progress'): ?>
                  <a href="<?php echo e(route('teacher.tests.form', $activeTest->test_id)); ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-play me-1"></i>Continue Test
                  </a>
                <?php elseif($activeTest && in_array($activeTest->status, ['completed', 'finalized'])): ?>
                  <a href="<?php echo e(route('teacher.tests.result', $activeTest->test_id)); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-eye me-1"></i>View Result
                  </a>
                <?php else: ?>
                  <?php
                    $availablePeriod = \App\Models\AssessmentPeriod::where('student_id', $student->student_id)
                        ->where('status', '!=', 'overdue')
                        ->where('status', '!=', 'completed')
                        ->where('end_date', '>=', now()->startOfDay())
                        ->first();
                  ?>
                  <?php if($availablePeriod): ?>
                    <form action="<?php echo e(route('teacher.tests.start', $student->student_id)); ?>" method="POST" style="display:inline;">
                      <?php echo csrf_field(); ?>
                      <input type="hidden" name="period_id" value="<?php echo e($availablePeriod->period_id); ?>">
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>Start Test
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small">No available assessment period.</span>
                  <?php endif; ?>
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
                <?php
                  $score = optional($test->standardScore)->standard_score;
                ?>
                <div class="fw-bold <?php echo e($score !== null ? 'text-success' : 'text-muted'); ?>">
                  <?php echo e($score !== null ? $score : 'Not scored'); ?>

                </div>
              </td>
              <td>
                <?php
                  $statusLabel = ucfirst($test->status);
                  $badgeClass = $test->status === 'finalized' ? 'bg-success' : 'bg-info';
                ?>
                <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($statusLabel); ?></span>
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a href="<?php echo e(route('teacher.tests.result', $test->test_id)); ?>" class="btn btn-sm btn-outline-primary">
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
<?php endif; ?>



<style>
/* ── FONTS ── */
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

/* ── TOKENS ── */
:root {
  --violet:       #845EC2;
  --violet-soft:  #EDE4FF;
  --violet-bg:    #F8F4FF;
  --teal:         #2EC4B6;
  --teal-soft:    #C8F4F1;
  --coral:        #FF6B8A;
  --coral-soft:   #FFE0E8;
  --mint:         #52C27B;
  --mint-soft:    #D4F5E2;
  --lemon:        #F9C74F;
  --lemon-soft:   #FFF6CC;
  --sky:          #4EA8DE;
  --sky-soft:     #D6EEFF;
  --peach:        #FF9A76;
  --text:         #2D2040;
  --text-muted:   #8A7A99;
  --radius:       14px;
  --shadow:       0 4px 20px rgba(100,60,160,0.09);
}

/* ── BASE ── */
body { font-family: 'Nunito', sans-serif !important; color: var(--text); background: var(--violet-bg); }

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── CARDS ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  margin-bottom: 1.5rem;
  transition: transform 0.2s, box-shadow 0.2s;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
}
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(100,60,160,0.13) !important; }
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

/* ── PROFILE CARD ── */
.row.g-4.mb-4:first-of-type .card {
  background: linear-gradient(135deg, #EDE4FF 0%, #D6EEFF 100%) !important;
  border-left: 4px solid var(--violet) !important;
}
.row.g-4.mb-4:first-of-type .card h5 {
  font-family: 'Baloo 2', cursive;
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--text);
}
.row.g-4.mb-4:first-of-type .avatar {
  width: 60px !important; height: 60px !important;
  font-size: 1.4rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral)) !important;
  border-radius: 50% !important;
  box-shadow: 0 4px 14px rgba(132,94,194,0.3);
}

/* ── STAT CARDS ── */
.row.g-4.mb-4:nth-of-type(2) .card {
  text-align: center;
  position: relative;
  overflow: hidden;
}
.row.g-4.mb-4:nth-of-type(2) .card::before {
  content: '';
  position: absolute;
  top: -20px; right: -20px;
  width: 80px; height: 80px;
  border-radius: 50%;
  opacity: 0.10;
}
.row.g-4.mb-4:nth-of-type(2) .col-12:nth-child(1) .card::before { background: var(--violet); }
.row.g-4.mb-4:nth-of-type(2) .col-12:nth-child(2) .card::before { background: var(--teal); }
.row.g-4.mb-4:nth-of-type(2) .col-12:nth-child(3) .card::before { background: var(--coral); }
.row.g-4.mb-4:nth-of-type(2) .col-12:nth-child(4) .card::before { background: var(--mint); }

.row.g-4.mb-4:nth-of-type(2) .display-4 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 2.6rem !important;
  font-weight: 800 !important;
  line-height: 1;
}
.row.g-4.mb-4:nth-of-type(2) h6.text-muted {
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 800 !important;
}

/* recolor stat icons */
.text-primary.fs-2 { color: var(--violet) !important; }
.text-info.fs-2    { color: var(--teal)   !important; }
.text-danger.fs-2  { color: var(--coral)  !important; }
.text-success.fs-2 { color: var(--mint)   !important; }
.text-primary.fs-1.fw-bold { color: var(--violet) !important; }
.text-info.fs-1.fw-bold    { color: var(--teal)   !important; }
.text-danger.fs-1.fw-bold  { color: var(--coral)  !important; }
.text-success.fs-1.fw-bold { color: var(--mint)   !important; }

/* ── CARD HEADERS ── */
.card-header {
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 14px 20px !important;
}
.card-header .card-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important;
  font-weight: 700 !important;
  color: var(--text) !important;
}
.card-header .fas.fa-calendar-check { color: var(--teal); }
.card-header .fas.fa-exclamation-triangle { color: var(--coral); }
.card-header .fas.fa-users           { color: var(--violet); }
.card-header .fas.fa-clock           { color: var(--lemon); }
.card-header .fas.fa-user-check      { color: var(--sky); }
.card-header .fas.fa-check-circle    { color: var(--mint); }

/* ── TABLES ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.72rem !important;
  font-weight: 800 !important;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--text-muted) !important;
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 11px 16px !important;
  white-space: nowrap;
}
.table tbody tr {
  border-bottom: 1px solid #F9F5FF !important;
  transition: background 0.15s;
}
.table tbody tr:last-child { border-bottom: none !important; }
.table-hover tbody tr:hover { background: #FDFBFF !important; }
.table tbody td {
  padding: 13px 16px !important;
  font-size: 0.875rem;
  vertical-align: middle !important;
  border: none !important;
}

/* ── AVATARS (table rows) ── */
.avatar {
  border-radius: 50% !important;
  font-weight: 900 !important;
  color: white !important;
  flex-shrink: 0;
}
/* cycle gradient colors by nth-child */
tbody tr:nth-child(5n+1) .avatar { background: linear-gradient(135deg, var(--coral), var(--peach)) !important; }
tbody tr:nth-child(5n+2) .avatar { background: linear-gradient(135deg, var(--teal),  var(--sky))   !important; }
tbody tr:nth-child(5n+3) .avatar { background: linear-gradient(135deg, var(--violet),var(--coral)) !important; }
tbody tr:nth-child(5n+4) .avatar { background: linear-gradient(135deg, var(--mint),  var(--teal))  !important; }
tbody tr:nth-child(5n+5) .avatar { background: linear-gradient(135deg, var(--lemon), var(--peach)) !important; }

.fw-semibold { font-weight: 800 !important; font-size: 0.88rem; color: var(--text); }
.text-muted.small { font-size: 0.75rem !important; font-weight: 600; color: var(--text-muted) !important; }

/* ── BADGES ── */
.badge {
  font-size: 0.72rem !important;
  font-weight: 800 !important;
  padding: 4px 12px !important;
  border-radius: 20px !important;
}
.badge.bg-primary   { background: var(--violet-soft) !important; color: #5a3e8a !important; }
.badge.bg-info      { background: var(--sky-soft)    !important; color: #2260a0 !important; }
.badge.bg-warning   { background: var(--lemon-soft)  !important; color: #9a6800 !important; }
.badge.bg-success   { background: var(--mint-soft)   !important; color: #2a7a50 !important; }
.badge.bg-danger    { background: var(--coral-soft)  !important; color: #c0294a !important; }
.badge.no-status    { background: #F0E8FF             !important; color: var(--text-muted)  !important; }

/* ── BUTTONS ── */
.btn {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important;
  border-radius: 10px !important;
  transition: all 0.18s !important;
  font-size: 0.78rem !important;
}
.btn-primary, .btn-sm.btn-primary {
  background: linear-gradient(135deg, var(--violet), var(--coral)) !important;
  border: none !important;
  color: white !important;
  box-shadow: 0 3px 10px rgba(132,94,194,0.25) !important;
}
.btn-primary:hover, .btn-sm.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 5px 16px rgba(132,94,194,0.35) !important;
}
.btn-outline-primary, .btn-sm.btn-outline-primary {
  background: white !important;
  color: var(--violet) !important;
  border: 1.5px solid var(--violet-soft) !important;
}
.btn-outline-primary:hover, .btn-sm.btn-outline-primary:hover {
  background: var(--violet-soft) !important;
}
.btn-outline-secondary, .btn-sm.btn-outline-secondary {
  background: white !important;
  color: var(--text-muted) !important;
  border: 1.5px solid #E8E0F0 !important;
}

/* ── PROGRESS BAR ── */
.progress {
  background: #F0E8FF !important;
  border-radius: 10px !important;
  height: 7px !important;
}
.progress-bar {
  background: linear-gradient(90deg, var(--violet), var(--coral)) !important;
  border-radius: 10px !important;
}
.progress-bar.bg-warning {
  background: linear-gradient(90deg, var(--lemon), var(--peach)) !important;
}

/* ── SCORE VALUE IN COMPLETED TABLE ── */
.fw-bold.text-success {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.2rem !important;
  color: var(--mint) !important;
}

/* ── "NOT ELIGIBLE" MUTED TEXT ── */
.text-muted.small:not(.student-sub) {
  font-style: italic;
}

/* ── STAGGER CARD ANIMATIONS ── */
.card:nth-child(1) { animation-delay: 0.05s; }
.card:nth-child(2) { animation-delay: 0.10s; }
.card:nth-child(3) { animation-delay: 0.15s; }
.card:nth-child(4) { animation-delay: 0.20s; }
.card:nth-child(5) { animation-delay: 0.25s; }
.card:nth-child(6) { animation-delay: 0.30s; }
.card:nth-child(7) { animation-delay: 0.35s; }

/* ── PAGE FADE IN ── */
.container-fluid, .container {
  animation: fadeDown 0.35s ease;
}
@keyframes fadeDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/dashboard.blade.php ENDPATH**/ ?>