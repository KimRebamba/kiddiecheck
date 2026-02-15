

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    <?php if($period->student_feature_path): ?>
      <img src="<?php echo e(asset($period->student_feature_path)); ?>" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
    <?php else: ?>
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.25rem;">
        <?php echo e(strtoupper(substr($period->student_first_name, 0, 1))); ?>

      </div>
    <?php endif; ?>
    <div>
      <h1 class="h4 mb-1"><?php echo e($period->student_first_name); ?> <?php echo e($period->student_last_name); ?></h1>
      <div class="text-muted small">
        Period: <?php echo e($period->description); ?> · <?php echo e($period->start_date); ?> to <?php echo e($period->end_date); ?>

      </div>
      <div class="text-muted small">
        Age during assessment: <?php echo e($ageYearsAtStart !== null ? $ageYearsAtStart . ' years' : '—'); ?>

      </div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.assessments')); ?>" class="btn btn-outline-secondary btn-sm">Back to Assessments</a>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Student & Period Info</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Student</dt>
          <dd class="col-7"><?php echo e($period->student_first_name); ?> <?php echo e($period->student_last_name); ?></dd>
          <dt class="col-5">DOB</dt>
          <dd class="col-7"><?php echo e($period->date_of_birth); ?></dd>
          <dt class="col-5">Period</dt>
          <dd class="col-7"><?php echo e($period->description); ?></dd>
          <dt class="col-5">Dates</dt>
          <dd class="col-7"><?php echo e($period->start_date); ?> – <?php echo e($period->end_date); ?></dd>
          <dt class="col-5">Status</dt>
          <dd class="col-7 text-capitalize"><?php echo e($period->status); ?></dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Family</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Family name</dt>
          <dd class="col-7"><?php echo e($period->family_name ?? '—'); ?></dd>
          <dt class="col-5">Address</dt>
          <dd class="col-7"><?php echo e($period->family_home_address ?? '—'); ?></dd>
          <dt class="col-5">Emergency contact</dt>
          <dd class="col-7"><?php echo e($period->emergency_contact ?? '—'); ?></dd>
          <dt class="col-5">Emergency phone</dt>
          <dd class="col-7"><?php echo e($period->emergency_phone ?? '—'); ?></dd>
        </dl>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assigned Teachers & Examiners</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Assigned teachers</h3>
        <?php if($teachers->isEmpty()): ?>
          <p class="text-muted mb-2">No teachers assigned.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Examiners (tests)</h3>
        <?php if($examiners->isEmpty()): ?>
          <p class="text-muted mb-0">No tests recorded.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-0">
            <?php $__currentLoopData = $examiners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($e->username); ?> <span class="text-muted">(<?php echo e($e->role); ?>)</span></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Test Progress</h2>
      </div>
      <div class="card-body small">
        <?php if($tests->isEmpty()): ?>
          <p class="text-muted mb-0">No tests have been started for this period.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th>Examiner</th>
                  <th>Role</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Notes</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $tests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($t->examiner_username ?? '—'); ?></td>
                    <td class="text-capitalize"><?php echo e($t->examiner_role ?? '—'); ?></td>
                    <td><?php echo e($t->test_date); ?></td>
                    <td><?php echo e(ucfirst($t->status)); ?></td>
                    <td><?php echo e($t->notes ? 'Yes' : 'No'); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card h-100 mb-3 mb-xl-2">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Domain Results Summary</h2>
      </div>
      <div class="card-body small">
        <?php if($domainScores->isEmpty()): ?>
          <p class="text-muted mb-0">No domain scores computed yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Raw</th>
                  <th>Scaled</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $domainScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($d->domain_name); ?></td>
                    <td><?php echo e($d->raw_score); ?></td>
                    <td><?php echo e($d->scaled_score); ?></td>
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

<div class="row g-3 mb-3">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Standard Score Results</h2>
      </div>
      <div class="card-body small">
        <h3 class="h6">Individual teacher standard scores</h3>
        <?php if($teacherStandardScores->isEmpty()): ?>
          <p class="text-muted">No teacher standard scores recorded.</p>
        <?php else: ?>
          <div class="table-responsive mb-2">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Teacher</th>
                  <th>Standard score</th>
                  <th>Interpretation</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $teacherStandardScores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($ts->teacher_username); ?></td>
                    <td><?php echo e($ts->standard_score); ?></td>
                    <td><?php echo e($ts->interpretation); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <h3 class="h6 mt-3">Summary</h3>
        <?php if($summary): ?>
          <dl class="row mb-0">
            <dt class="col-6 col-md-5">Teachers average score</dt>
            <dd class="col-6 col-md-7"><?php echo e($summary->teachers_standard_score_avg ?? '—'); ?></dd>
            <dt class="col-6 col-md-5">Family standard score</dt>
            <dd class="col-6 col-md-7"><?php echo e($summary->family_standard_score ?? '—'); ?></dd>
            <dt class="col-6 col-md-5">Final standard score</dt>
            <dd class="col-6 col-md-7"><?php echo e($summary->final_standard_score ?? '—'); ?></dd>
            <dt class="col-6 col-md-5">Final interpretation</dt>
            <dd class="col-6 col-md-7"><?php echo e($summary->final_interpretation ?? '—'); ?></dd>
          </dl>
        <?php else: ?>
          <p class="text-muted mb-0">No summary scores computed yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card h-100 mb-3 mb-xl-2">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Discrepancy Monitoring</h2>
      </div>
      <div class="card-body small">
        <?php if(!$summary): ?>
          <p class="text-muted mb-0">No discrepancy data yet.</p>
        <?php else: ?>
          <div class="mb-2">
            <div class="text-muted">Teacher vs teacher discrepancy</div>
            <?php $td = $summary->teacher_discrepancy; ?>
            <?php if($td === 'major'): ?>
              <span class="badge bg-danger">Major</span>
            <?php elseif($td === 'minor'): ?>
              <span class="badge bg-warning text-dark">Minor</span>
            <?php elseif($td === 'none'): ?>
              <span class="badge bg-success">None</span>
            <?php else: ?>
              <span class="badge bg-secondary">Unknown</span>
            <?php endif; ?>
          </div>
          <div class="mb-2">
            <div class="text-muted">Teacher vs family discrepancy</div>
            <?php $tfd = $summary->teacher_family_discrepancy; ?>
            <?php if($tfd === 'major'): ?>
              <span class="badge bg-danger">Major</span>
            <?php elseif($tfd === 'minor'): ?>
              <span class="badge bg-warning text-dark">Minor</span>
            <?php elseif($tfd === 'none'): ?>
              <span class="badge bg-success">None</span>
            <?php else: ?>
              <span class="badge bg-secondary">Unknown</span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Admin Actions</h2>
      </div>
      <div class="card-body small">
        <form method="post" action="<?php echo e(route('admin.assessments.extend', $period->period_id)); ?>" class="mb-2">
          <?php echo csrf_field(); ?>
          <label class="form-label">Extend deadline</label>
          <div class="input-group input-group-sm mb-1">
            <input type="date" name="end_date" value="<?php echo e($period->end_date); ?>" class="form-control form-control-sm">
            <button class="btn btn-outline-secondary" type="submit">Update</button>
          </div>
          <div class="form-text">Admins may only adjust deadlines, not responses.</div>
        </form>

        <form method="post" action="<?php echo e(route('admin.assessments.close', $period->period_id)); ?>" class="mb-2" onsubmit="return confirm('Mark this assessment period as closed?');">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Mark period as closed</button>
        </form>

        <form method="post" action="<?php echo e(route('admin.assessments.recompute', $period->period_id)); ?>" class="mb-2">
          <?php echo csrf_field(); ?>
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Recompute scores</button>
        </form>

        <a href="<?php echo e(route('admin.assessments.export', $period->period_id)); ?>" class="btn btn-outline-secondary btn-sm w-100 mb-2">Export assessment report (PDF)</a>

        <form method="post" action="<?php echo e(route('admin.assessments.notify', $period->period_id)); ?>" class="mb-2">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="target" value="teachers">
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Send notification to teachers</button>
        </form>

        <form method="post" action="<?php echo e(route('admin.assessments.notify', $period->period_id)); ?>">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="target" value="family">
          <button type="submit" class="btn btn-outline-secondary btn-sm w-100">Send notification to family</button>
        </form>

        <p class="text-muted mt-2 mb-0">
          Admins cannot edit answers or scores here. This page is for monitoring, oversight, and coordination.
        </p>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\assessments_show.blade.php ENDPATH**/ ?>