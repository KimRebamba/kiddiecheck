

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Teacher Dashboard</h1>
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
?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-primary fs-2 mb-2">👥</div>
          <h6 class="text-muted">Assigned Students</h6>
        </div>
        <div class="display-4 fs-1 fw-bold"><?php echo e($students->count()); ?></div>
        <div class="text-muted small">Active in your sections</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-warning fs-2 mb-2">⏱️</div>
          <h6 class="text-muted">In-Progress Tests</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-warning"><?php echo e($inProgress->count()); ?></div>
        <div class="text-muted small">Tests currently active</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-success fs-2 mb-2">✅</div>
          <h6 class="text-muted">Eligible Now</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-success"><?php echo e($eligibleNow->count()); ?></div>
        <div class="text-muted small">Ready for new tests</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card border-0 shadow-sm">
      <div class="card-body text-center">
        <div class="mb-3">
          <div class="text-info fs-2 mb-2">📊</div>
          <h6 class="text-muted">Completed Tests</h6>
        </div>
        <div class="display-4 fs-1 fw-bold text-info"><?php echo e($recentCompleted->count()); ?></div>
        <div class="text-muted small">Last 30 days</div>
      </div>
    </div>
  </div>
</div>

<!-- Assigned Students Table -->
<div class="row g-3">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assigned Students</h5>
        <span class="badge bg-primary rounded-pill"><?php echo e($students->count()); ?></span>
      </div>
      <div class="card-body p-0">
        <?php if($students->isEmpty()): ?>
          <div class="text-center text-muted py-4">
            <div class="fs-1 mb-2">📚</div>
            <div class="h6">No students assigned</div>
            <p class="text-muted">Students will appear here once they are assigned to your sections.</p>
          </div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th><i class="fas fa-user me-2"></i> Student</th>
                  <th><i class="fas fa-birthday-cake me-2"></i> Age</th>
                  <th><i class="fas fa-calendar me-2"></i> Latest Test</th>
                  <th><i class="fas fa-info-circle me-2"></i> Status</th>
                  <th class="text-center"><i class="fas fa-cogs me-2"></i> Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php 
                    $st = $status[$s->student_id] ?? null;
                    $latest = $st['latest_teacher'] ?? null;
                    $dob = is_string($s->date_of_birth) ? \Carbon\Carbon::parse($s->date_of_birth) : $s->date_of_birth;
                    $age = $dob ? (int)$dob->diffInYears(now()) : 'N/A';
                  ?>
                  <tr>
                    <td>
                      <a href="<?php echo e(route('teacher.student', $s->student_id)); ?>" class="text-decoration-none d-flex align-items-center">
                        <div class="avatar bg-primary text-white me-2">
                          <?php echo e(strtoupper(substr($s->first_name, 0, 1))); ?>

                        </div>
                        <div>
                          <strong><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?></strong>
                        <br>
                          <small class="text-muted"><?php echo e($age); ?> years old</small>
                        </div>
                      </a>
                    </td>
                    <td class="text-center"><?php echo e($age); ?></td>
                    <td>
                      <?php if($latest): ?>
                        <?php echo e($latest->test_date->format('M d, Y')); ?><br>
                        <small class="badge bg-<?php echo e($latest->status === 'completed' ? 'success' : ($latest->status === 'in_progress' ? 'warning' : 'secondary')); ?> rounded-pill"><?php echo e(ucfirst($latest->status)); ?></small>
                      <?php else: ?>
                        <span class="text-muted">No tests</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($st['in_progress']): ?>
                        <span class="badge bg-warning rounded-pill">In Progress</span>
                      <?php elseif($st['eligible']): ?>
                        <span class="badge bg-success rounded-pill">Eligible</span>
                      <?php else: ?>
                        <span class="badge bg-secondary rounded-pill">Not Eligible</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="<?php echo e(route('teacher.student', $s->student_id)); ?>" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-eye me-1"></i> View
                        </a>
                        <?php if($st['in_progress']): ?>
                          <a href="<?php echo e(route('teacher.tests.question', [$st['in_progress']->test_id, \App\Models\Domain::orderBy('domain_id')->first()->domain_id ?? 1, 0])); ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-play me-1"></i> Continue
                          </a>
                        <?php elseif($st['eligible']): ?>
                          <?php 
                            $availablePeriod = $s->assessmentPeriods()
                                ->where('status', '!=', 'overdue')
                                ->where('status', '!=', 'completed')
                                ->first();
                          ?>
                          <?php if($availablePeriod): ?>
                            <form action="<?php echo e(route('teacher.tests.start', $s->student_id)); ?>" method="POST" class="d-inline">
                              <?php echo csrf_field(); ?>
                              <input type="hidden" name="period_id" value="<?php echo e($availablePeriod->period_id); ?>">
                              <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Start Test
                              </button>
                            </form>
                          <?php else: ?>
                            <span class="text-muted small">No periods available</span>
                          <?php endif; ?>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<!-- In-Progress Tests -->
<?php if($inProgress->count() > 0): ?>
  <div class="col-12 col-lg-6">
        <div class="card-header">
          <h5 class="mb-0">In-Progress Assessments</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Started</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $inProgress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($ip['student']->first_name); ?> <?php echo e($ip['student']->last_name); ?></td>
                    <td><?php echo e($ip['test']->test_date->format('M d, Y')); ?></td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="<?php echo e(route('teacher.tests.question', [$ip['test']->test_id, \App\Models\Domain::orderBy('domain_id')->first()->domain_id ?? 1, 0])); ?>" class="btn btn-sm btn-outline-warning">Resume</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Eligible Students -->
  <?php if($eligibleNow->count() > 0): ?>
    <div class="col-12 col-lg-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Eligible for Testing</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Last Test</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $eligibleNow; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php 
                    $st = $status[$s->student_id] ?? null;
                    $latest = $st['latest_teacher'] ?? null;
                  ?>
                  <tr>
                    <td><?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?></td>
                    <td><?php echo e($latest ? $latest->test_date->format('M d, Y') : 'N/A'); ?></td>
                    <td>
                      <?php
                        $availablePeriod = $s->assessmentPeriods()
                            ->where('status', '!=', 'overdue')
                            ->where('status', '!=', 'completed')
                            ->first();
                      ?>
                      <?php if($availablePeriod): ?>
                        <form action="<?php echo e(route('teacher.tests.start', $s->student_id)); ?>" method="POST">
                          <?php echo csrf_field(); ?>
                          <input type="hidden" name="period_id" value="<?php echo e($availablePeriod->period_id); ?>">
                          <button type="submit" class="btn btn-sm btn-primary">Start Test</button>
                        </form>
                      <?php else: ?>
                        <span class="text-muted small">No available periods</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Recent Completed Tests -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Recent Completed Tests</h5>
      </div>
      <div class="card-body p-0">
        <?php if($recentCompleted->isEmpty()): ?>
          <div class="p-3 text-muted">No recent completed tests.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Student</th>
                  <th>Date</th>
                  <th>Score</th>
                  <th>Interpretation</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $recentCompleted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $standardScore = $t->standardScore;
                  ?>
                  <tr>
                    <td><?php echo e($t->student->first_name); ?> <?php echo e($t->student->last_name); ?></td>
                    <td><?php echo e($t->test_date->format('M d, Y')); ?></td>
                    <td><?php echo e($standardScore ? $standardScore->standard_score : 'N/A'); ?></td>
                    <td><?php echo e($standardScore ? $standardScore->interpretation : 'N/A'); ?></td>
                    <td>
                      <a href="<?php echo e(route('teacher.tests.result', $t->test_id)); ?>" class="btn btn-sm btn-outline-primary">View</a>
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
    margin-bottom: 1.5rem;
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/dashboard.blade.php ENDPATH**/ ?>