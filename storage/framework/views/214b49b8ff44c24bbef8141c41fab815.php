

<?php $__env->startSection('content'); ?>
<style>
  .admin-page-title { margin-bottom: 0.15rem; }
  .admin-page-intro { font-size: 0.9rem; }
  .admin-alert-card h2.h6 { font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; }
  .admin-filter-toggle { font-size: 0.8rem; }
  .admin-filter-label { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; }
  .admin-table-caption { font-size: 0.8rem; color: #6B7280; margin-bottom: 0.35rem; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 admin-page-title">Students</h1>
    <p class="text-muted admin-page-intro mb-0">Overview of all enrolled children, their families, and assessment status.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.students.create')); ?>" class="btn btn-primary btn-sm">Add New Student</a>
    <a href="<?php echo e(route('admin.students.export')); ?>" class="btn btn-outline-secondary btn-sm">Export List</a>
  </div>
</div>


<div class="row g-3 mb-3">
  <div class="col-12 col-lg-6">
    <div class="card h-100 admin-alert-card">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h2 class="h6 mb-1">Overdue Assessments</h2>
          <p class="text-muted small mb-0">Students whose assessment windows have closed without completion.</p>
        </div>
      </div>
      <div class="card-body small">
        <?php $overdueCount = $alerts['overdue']->count(); ?>
        <p class="mb-1">
          <span class="fw-semibold"><?php echo e($overdueCount); ?></span>
          <span class="text-muted">student<?php echo e($overdueCount === 1 ? '' : 's'); ?> with overdue assessment periods.</span>

        <div class="row g-3">
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-2 h-100">
              <div class="text-muted small mb-1">No assigned teacher</div>
              <div class="h5 mb-0"><?php echo e($noTeacherCount); ?></div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-2 h-100">
              <div class="text-muted small mb-1">Missing family score</div>
              <div class="h5 mb-0"><?php echo e($missingFamilyCount); ?></div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-2 h-100">
              <div class="text-muted small mb-1">Scheduled, no tests</div>
              <div class="h5 mb-0"><?php echo e($scheduledNoTestsCount); ?></div>
            </div>
          </div>
        </div>

        <p class="text-muted small mt-3 mb-0">Use the Students and Assessments pages for full lists when you need to drill into individual cases.</p>
      </div>
    </div>
  </div>
</div>


<div class="card mb-3">
  <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
    <div>
      <div class="admin-filter-label">Filters</div>
      <p class="text-muted small mb-1">Narrow down students by section, age, teachers, and assessment status.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm admin-filter-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#studentsFilter" aria-expanded="true" aria-controls="studentsFilter">
      Show / Hide filters
    </button>
  </div>
  <div id="studentsFilter" class="collapse show">
    <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Section</label>
        <select name="section_id" class="form-select form-select-sm">
          <option value="">All sections</option>
          <?php $__currentLoopData = $sectionOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sec->section_id); ?>" <?php echo e((string)request('section_id') === (string)$sec->section_id ? 'selected' : ''); ?>>
              <?php echo e($sec->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Student name</label>
        <input type="text" name="student_name" value="<?php echo e(request('student_name')); ?>" class="form-control form-control-sm" placeholder="Search student">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label form-label-sm">Family name</label>
        <input type="text" name="family_name" value="<?php echo e(request('family_name')); ?>" class="form-control form-control-sm" placeholder="Search family">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age min</label>
        <input type="number" name="age_min" value="<?php echo e(request('age_min')); ?>" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age max</label>
        <input type="number" name="age_max" value="<?php echo e(request('age_max')); ?>" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-12 col-md-2">
        <label class="form-label form-label-sm">Assigned teacher</label>
        <select name="teacher_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $teacherOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($t->user_id); ?>" <?php echo e((string)request('teacher_id') === (string)$t->user_id ? 'selected' : ''); ?>>
              <?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Assessment status</label>
        <select name="assessment_status" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="overdue" <?php echo e(request('assessment_status') === 'overdue' ? 'selected' : ''); ?>>Overdue</option>
        </select>
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Interpretation</label>
        <select name="interpretation" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="advanced" <?php echo e(request('interpretation') === 'advanced' ? 'selected' : ''); ?>>Advanced</option>
          <option value="average" <?php echo e(request('interpretation') === 'average' ? 'selected' : ''); ?>>Average</option>
          <option value="needs_retest" <?php echo e(request('interpretation') === 'needs_retest' ? 'selected' : ''); ?>>Needs Retest</option>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="with_completed_tests" id="with_completed_tests" value="1" <?php echo e(request()->boolean('with_completed_tests') ? 'checked' : ''); ?>>
          <label class="form-check-label" for="with_completed_tests">With completed tests</label>
        </div>
        <div class="form-check form-check-sm">
          <input class="form-check-input" type="checkbox" name="without_completed_tests" id="without_completed_tests" value="1" <?php echo e(request()->boolean('without_completed_tests') ? 'checked' : ''); ?>>
          <label class="form-check-label" for="without_completed_tests">Without completed tests</label>
        </div>
      </div>
      <div class="col-12 col-md-2 mt-2 mt-md-0 text-md-end">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
      </div>
    </form>
    </div>
  </div>
</div>


<form method="post" action="<?php echo e(route('admin.students.bulk_assign_teacher')); ?>">
  <?php echo csrf_field(); ?>
  <div class="card mb-2">
    <div class="card-body py-2 d-flex flex-wrap gap-2 align-items-center">
      <div class="small fw-semibold">Bulk actions:</div>
      <div>
        <select name="teacher_id" class="form-select form-select-sm d-inline-block" style="min-width: 220px;">
          <option value="">Assign teacher...</option>
          <?php $__currentLoopData = $teacherOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($t->user_id); ?>"><?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)</option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Apply to selected</button>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <div class="admin-table-caption">Showing <?php echo e($students->count()); ?> of <?php echo e($students->total()); ?> students (paginated).</div>
        <table class="table table-sm mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:32px;"><input type="checkbox" onclick="document.querySelectorAll('.student-check').forEach(cb => cb.checked = this.checked);"></th>
              <th style="width:52px;">Photo</th>
              <th>Section</th>
              <th>Student</th>
              <th>Age</th>
              <th>Family</th>
              <th>Teachers</th>
              <th>Status</th>
              <th>Latest Score</th>
              <th>Interpretation</th>
              <th>Last Updated</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><input type="checkbox" class="student-check" name="student_ids[]" value="<?php echo e($s->student_id); ?>"></td>
                <td>
                  <?php if($s->feature_path): ?>
                    <img src="<?php echo e(asset('storage/' . $s->feature_path)); ?>" alt="" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                  <?php else: ?>
                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:36px;height:36px;font-size:0.8rem;">
                      <?php echo e(strtoupper(substr($s->first_name, 0, 1))); ?>

                    </div>
                  <?php endif; ?>
                </td>
                <td><?php echo e($s->section_name ?? '—'); ?></td>
                <td><?php echo e($s->last_name); ?>, <?php echo e($s->first_name); ?></td>
                <td><?php echo e($s->computed_age_years !== null ? $s->computed_age_years . ' yrs' : '—'); ?></td>
                <td><?php echo e($s->family_name ?? '—'); ?></td>
                <td>
                  <?php $teachers = $s->computed_teachers; ?>
                  <?php if($teachers->isEmpty()): ?>
                    <span class="text-muted">None</span>
                  <?php else: ?>
                    <span>
                      <?php echo e($teachers->take(2)->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->username)->implode(', ')); ?>

                      <?php if($teachers->count() > 2): ?>
                        <span class="text-muted">+<?php echo e($teachers->count() - 2); ?> more</span>
                      <?php endif; ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php $status = $s->computed_status; ?>
                  <?php if($status === 'Overdue'): ?>
                    <span class="badge bg-danger">Overdue</span>
                  <?php elseif($status === 'Ongoing'): ?>
                    <span class="badge bg-warning text-dark">Ongoing</span>
                  <?php elseif($status === 'Completed'): ?>
                    <span class="badge bg-success">Completed</span>
                  <?php elseif($status === 'Scheduled'): ?>
                    <span class="badge bg-info text-dark">Scheduled</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">No assessment</span>
                  <?php endif; ?>
                </td>
                <td><?php echo e($s->computed_latest_score ?? '—'); ?></td>
                <td><?php echo e($s->computed_latest_interpretation ?? '—'); ?></td>
                <td><?php echo e(optional($s->updated_at)->format('Y-m-d')); ?></td>
                <td class="text-end">
                  <div class="btn-group btn-group-sm" role="group">
                    <a href="<?php echo e(route('admin.students.show', $s->student_id)); ?>" class="btn btn-outline-secondary">View</a>
                    <a href="<?php echo e(route('admin.students.edit', $s->student_id)); ?>" class="btn btn-outline-secondary">Edit</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="11" class="text-center text-muted py-3">No students found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="p-2">
        <?php echo e($students->links()); ?>

      </div>
    </div>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/admin/students.blade.php ENDPATH**/ ?>