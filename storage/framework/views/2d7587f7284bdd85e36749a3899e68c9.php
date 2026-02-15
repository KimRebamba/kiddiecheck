

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    <?php if($student->feature_path): ?>
      <img src="<?php echo e(asset($student->feature_path)); ?>" alt="" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
    <?php else: ?>
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:64px;height:64px;font-size:1.25rem;">
        <?php echo e(strtoupper(substr($student->first_name, 0, 1))); ?>

      </div>
    <?php endif; ?>
    <div>
      <h1 class="h4 mb-1"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></h1>
      <div class="text-muted small">DOB: <?php echo e($student->date_of_birth); ?> · Age: <?php echo e($ageYears !== null ? $ageYears . ' yrs' : '—'); ?></div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.students.edit', $student->student_id)); ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Basic Information</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Full name</dt>
          <dd class="col-7"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></dd>
          <dt class="col-5">Date of birth</dt>
          <dd class="col-7"><?php echo e($student->date_of_birth); ?></dd>
          <dt class="col-5">Age</dt>
          <dd class="col-7"><?php echo e($ageYears !== null ? $ageYears . ' years' : '—'); ?></dd>
          <dt class="col-5">Created</dt>
          <dd class="col-7"><?php echo e(optional($student->created_at)->format('Y-m-d H:i')); ?></dd>
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
          <dd class="col-7"><?php echo e($student->family_name ?? '—'); ?></dd>
          <dt class="col-5">Address</dt>
          <dd class="col-7"><?php echo e($student->family_home_address ?? '—'); ?></dd>
          <dt class="col-5">Emergency contact</dt>
          <dd class="col-7"><?php echo e($student->emergency_contact ?? '—'); ?></dd>
          <dt class="col-5">Emergency phone</dt>
          <dd class="col-7"><?php echo e($student->emergency_phone ?? '—'); ?></dd>
        </dl>
        <form method="post" action="<?php echo e(route('admin.students.transfer_family', $student->student_id)); ?>" class="mt-3">
          <?php echo csrf_field(); ?>
          <label class="form-label">Transfer to another family</label>
          <select name="family_id" class="form-select form-select-sm mb-2">
            <?php $__currentLoopData = \Illuminate\Support\Facades\DB::table('families as f')->join('users as u','f.user_id','=','u.user_id')->orderBy('f.family_name')->get(['f.user_id','f.family_name','u.email']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($f->user_id); ?>" <?php echo e((string)$student->family_id === (string)$f->user_id ? 'selected' : ''); ?>><?php echo e($f->family_name); ?> (<?php echo e($f->email); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button type="submit" class="btn btn-outline-secondary btn-sm">Update Family</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Assigned Teachers</h2>
      </div>
      <div class="card-body small">
        <?php if($teachers->isEmpty()): ?>
          <p class="text-muted mb-2">No teachers currently assigned.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span><?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)</span>
                <form method="post" action="<?php echo e(route('admin.students.remove_teacher', [$student->student_id, $t->user_id])); ?>" onsubmit="return confirm('Remove this teacher from the student?');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-link btn-sm text-danger p-0">Remove</button>
                </form>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
        <form method="post" action="<?php echo e(route('admin.students.assign_teacher', $student->student_id)); ?>">
          <?php echo csrf_field(); ?>
          <label class="form-label">Assign teacher</label>
          <select name="teacher_id" class="form-select form-select-sm mb-2">
            <option value="">Select...</option>
            <?php $__currentLoopData = $allTeacherOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($t->user_id); ?>"><?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button type="submit" class="btn btn-outline-secondary btn-sm">Assign</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Timeline</h2>
      </div>
      <div class="card-body small">
        <?php if($periods->isEmpty()): ?>
          <p class="text-muted mb-0">No assessment periods found for this student.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Status</th>
                  <th>Teacher avg</th>
                  <th>Family score</th>
                  <th>Final score</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($p->description); ?></td>
                    <td><?php echo e($p->start_date); ?></td>
                    <td><?php echo e($p->end_date); ?></td>
                    <td>
                      <?php if($p->status === 'overdue'): ?>
                        <span class="badge bg-danger">Overdue</span>
                      <?php elseif($p->status === 'completed'): ?>
                        <span class="badge bg-success">Completed</span>
                      <?php else: ?>
                        <span class="badge bg-info text-dark">Scheduled</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo e($p->teachers_standard_score_avg ?? '—'); ?></td>
                    <td><?php echo e($p->family_standard_score ?? '—'); ?></td>
                    <td><?php echo e($p->final_standard_score ?? '—'); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Scores & Discrepancy Summary</h2>
      </div>
      <div class="card-body small">
        <?php if($discrepancySummaries->isEmpty()): ?>
          <p class="text-muted mb-0">No summary scores have been computed yet.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Final score</th>
                  <th>Interpretation</th>
                  <th>Teacher disc.</th>
                  <th>Teacher–family</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $discrepancySummaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($p->description); ?></td>
                    <td><?php echo e($p->final_standard_score ?? '—'); ?></td>
                    <td><?php echo e($p->final_interpretation ?? '—'); ?></td>
                    <td>
                      <?php if($p->teacher_discrepancy === 'major'): ?>
                        <span class="badge bg-danger">Major</span>
                      <?php elseif($p->teacher_discrepancy === 'minor'): ?>
                        <span class="badge bg-warning text-dark">Minor</span>
                      <?php elseif($p->teacher_discrepancy === 'none'): ?>
                        <span class="badge bg-success">None</span>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($p->teacher_family_discrepancy === 'major'): ?>
                        <span class="badge bg-danger">Major</span>
                      <?php elseif($p->teacher_family_discrepancy === 'minor'): ?>
                        <span class="badge bg-warning text-dark">Minor</span>
                      <?php elseif($p->teacher_family_discrepancy === 'none'): ?>
                        <span class="badge bg-success">None</span>
                      <?php else: ?>
                        —
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <hr>
        <h3 class="h6">Latest Domain Scores (most recent completed test)</h3>
        <?php if($domainScores->isEmpty()): ?>
          <p class="text-muted mb-0">No domain scores available yet.</p>
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

<div class="card mb-3">
  <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-1">Tests Overview</h2>
  </div>
  <div class="card-body small">
    <?php if($tests->isEmpty()): ?>
      <p class="text-muted mb-0">No tests have been recorded for this student.</p>
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
              <th>Pictures</th>
              <th class="text-end">Actions</th>
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
                <td><?php echo e($picturesCountByTest[$t->test_id] ?? 0); ?></td>
                <td class="text-end">
                  <form method="post" action="<?php echo e(route('admin.tests.cancel', $t->test_id)); ?>" class="d-inline" onsubmit="return confirm('Cancel this test? This is for invalid/erroneous tests only.');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-secondary btn-sm" <?php echo e(in_array($t->status, ['canceled','finalized']) ? 'disabled' : ''); ?>>Cancel</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\admin\students_show.blade.php ENDPATH**/ ?>