

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Assessments</h1>
    <p class="text-muted mb-0">Monitor assessment periods, progress, and scoring health.</p>
  </div>
</div>


<div class="row g-3 mb-3">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Total Periods</div>
        <div class="h5 mb-0"><?php echo e($totalPeriods); ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Scheduled</div>
        <div class="h5 mb-0"><?php echo e($scheduledPeriods); ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Ongoing (tests)</div>
        <div class="h5 mb-0"><?php echo e($ongoingAssessments); ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Completed</div>
        <div class="h5 mb-0"><?php echo e($completedPeriods); ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Overdue</div>
        <div class="h5 mb-0 text-danger"><?php echo e($overduePeriods); ?></div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="card shadow-sm h-100">
      <div class="card-body py-2">
        <div class="text-muted small">Tests awaiting finalization</div>
        <div class="h5 mb-0"><?php echo e($testsAwaitingFinalization); ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-9">
    
    <div class="card mb-3">
      <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-end">
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Status</label>
            <select name="status" class="form-select form-select-sm">
              <option value="">All</option>
              <option value="scheduled" <?php echo e(request('status') === 'scheduled' ? 'selected' : ''); ?>>Scheduled</option>
              <option value="ongoing" <?php echo e(request('status') === 'ongoing' ? 'selected' : ''); ?>>Ongoing</option>
              <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
              <option value="overdue" <?php echo e(request('status') === 'overdue' ? 'selected' : ''); ?>>Overdue</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Teacher</label>
            <select name="teacher_id" class="form-select form-select-sm">
              <option value="">All</option>
              <?php $__currentLoopData = $teacherOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($t->user_id); ?>" <?php echo e((string)request('teacher_id') === (string)$t->user_id ? 'selected' : ''); ?>>
                  <?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)
                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Student name</label>
            <input type="text" name="student_name" value="<?php echo e(request('student_name')); ?>" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Family name</label>
            <input type="text" name="family_name" value="<?php echo e(request('family_name')); ?>" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Start from</label>
            <input type="date" name="start_from" value="<?php echo e(request('start_from')); ?>" class="form-control form-control-sm">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label form-label-sm">Start to</label>
            <input type="date" name="start_to" value="<?php echo e(request('start_to')); ?>" class="form-control form-control-sm">
          </div>
          <div class="col-12 col-md-3">
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="with_discrepancies" id="with_discrepancies" value="1" <?php echo e(request()->boolean('with_discrepancies') ? 'checked' : ''); ?>>
              <label class="form-check-label" for="with_discrepancies">With discrepancies only</label>
            </div>
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="missing_teacher_test" id="missing_teacher_test" value="1" <?php echo e(request()->boolean('missing_teacher_test') ? 'checked' : ''); ?>>
              <label class="form-check-label" for="missing_teacher_test">Missing teacher test</label>
            </div>
            <div class="form-check form-check-sm">
              <input class="form-check-input" type="checkbox" name="missing_family_test" id="missing_family_test" value="1" <?php echo e(request()->boolean('missing_family_test') ? 'checked' : ''); ?>>
              <label class="form-check-label" for="missing_family_test">Missing family test</label>
            </div>
          </div>
          <div class="col-12 col-md-3 mt-2 mt-md-0 text-md-end">
            <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
          </div>
        </form>
      </div>
    </div>

    
    <div class="card">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Family</th>
                <th>Teachers</th>
                <th>Period</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Teacher tests</th>
                <th>Family test</th>
                <th>Final score</th>
                <th>Last activity</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($p->student_last_name); ?>, <?php echo e($p->student_first_name); ?></td>
                  <td><?php echo e($p->family_name ?? '—'); ?></td>
                  <td>
                    <?php $assigned = $p->assigned_teachers; ?>
                    <?php if($assigned->isEmpty()): ?>
                      <span class="text-muted">None</span>
                    <?php else: ?>
                      <span>
                        <?php echo e($assigned->take(2)->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->username)->implode(', ')); ?>

                        <?php if($assigned->count() > 2): ?>
                          <span class="text-muted">+<?php echo e($assigned->count() - 2); ?> more</span>
                        <?php endif; ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($p->description); ?></td>
                  <td><?php echo e($p->start_date); ?></td>
                  <td><?php echo e($p->end_date); ?></td>
                  <td>
                    <?php $status = $p->computed_status; ?>
                    <?php if($status === 'overdue'): ?>
                      <span class="badge bg-danger">Overdue</span>
                    <?php elseif($status === 'completed'): ?>
                      <span class="badge bg-success">Completed</span>
                    <?php elseif($status === 'ongoing'): ?>
                      <span class="badge bg-warning text-dark">Ongoing</span>
                    <?php elseif($status === 'scheduled'): ?>
                      <span class="badge bg-info text-dark">Scheduled</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Other</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($p->teacher_progress_label); ?></td>
                  <td><?php echo e($p->family_status_label); ?></td>
                  <td>
                    <?php if($p->final_score_status === 'Computed'): ?>
                      <span class="badge bg-success">Computed</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Not computed</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($p->last_activity ? $p->last_activity->format('Y-m-d') : '—'); ?></td>
                  <td class="text-end">
                    <a href="<?php echo e(route('admin.assessments.show', $p->period_id)); ?>" class="btn btn-outline-secondary btn-sm">View</a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="12" class="text-center text-muted py-3">No assessment periods found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="p-2">
          <?php echo e($periods->links()); ?>

        </div>
      </div>
    </div>
  </div>

  
  <div class="col-12 col-xl-3">
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Alerts & Problems</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Overdue assessment periods</h3>
        <?php if($alerts['overdue']->isEmpty()): ?>
          <p class="text-muted">None.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $alerts['overdue']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?> (ended <?php echo e($a->end_date); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Missing teacher evaluations</h3>
        <?php if($alerts['missing_teacher']->isEmpty()): ?>
          <p class="text-muted">All periods have at least one completed teacher test.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $alerts['missing_teacher']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Family test not completed</h3>
        <?php if($alerts['missing_family']->isEmpty()): ?>
          <p class="text-muted">All periods have a completed family test.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $alerts['missing_family']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Major scoring discrepancies</h3>
        <?php if($alerts['major_discrepancy']->isEmpty()): ?>
          <p class="text-muted">No major discrepancies detected.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $alerts['major_discrepancy']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0">
                <?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?>

              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Tests stuck in progress</h3>
        <?php if($alerts['stuck_tests']->isEmpty()): ?>
          <p class="text-muted mb-0">No long-running in-progress tests.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-0">
            <?php $__currentLoopData = $alerts['stuck_tests']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?> (since <?php echo e($a->test_date); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\assessments.blade.php ENDPATH**/ ?>