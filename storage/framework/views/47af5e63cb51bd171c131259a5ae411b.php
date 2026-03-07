

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Family Details</h1>
</div>

<div class="row">
  <!-- Family Information -->
  <div class="col-12 col-lg-4 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-home me-2"></i>Family Information
        </h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Family Name</label>
          <div class="fw-bold"><?php echo e($family->family_name); ?></div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Address</label>
          <div><?php echo e($family->home_address); ?></div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Emergency Contact</label>
          <div><?php echo e($family->emergency_contact); ?></div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Emergency Phone</label>
          <div><?php echo e($family->emergency_phone); ?></div>
        </div>
        <?php if($family->user): ?>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Account User</label>
          <div><?php echo e($family->user->username); ?> (<?php echo e($family->user->email); ?>)</div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Students in this Family -->
  <div class="col-12 col-lg-8 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-users me-2"></i>Students in this Family
        </h5>
      </div>
      <div class="card-body p-0">
        <?php if($family->students->isNotEmpty()): ?>
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
                <?php $__currentLoopData = $family->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                      <div class="btn-group" role="group">
                        <a href="<?php echo e(route('teacher.student', $student->student_id)); ?>" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="<?php echo e(route('teacher.reports')); ?>" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-file-alt me-1"></i>Reports
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class="fas fa-users text-muted fs-1 mb-3"></i>
            <h6 class="text-muted">No students assigned to this family</h6>
            <p class="text-muted small">Students will appear here when they are assigned to this family.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-chart-line me-2"></i>Assessment Summary
        </h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-12 col-md-3 mb-3">
            <div class="text-primary fs-2 mb-2">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <h6 class="text-muted fw-semibold">Total Tests</h6>
            <div class="display-4 fs-1 fw-bold text-primary">
              <?php echo e($family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'completed')->count(); })); ?>

            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-success fs-2 mb-2">
              <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="text-muted fw-semibold">Completed</h6>
            <div class="display-4 fs-1 fw-bold text-success">
              <?php echo e($family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'finalized')->count(); })); ?>

            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-warning fs-2 mb-2">
              <i class="fas fa-clock"></i>
            </div>
            <h6 class="text-muted fw-semibold">In Progress</h6>
            <div class="display-4 fs-1 fw-bold text-warning">
              <?php echo e($family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'in_progress')->count(); })); ?>

            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-info fs-2 mb-2">
              <i class="fas fa-calendar-check"></i>
            </div>
            <h6 class="text-muted fw-semibold">Upcoming</h6>
            <div class="display-4 fs-1 fw-bold text-info">
              <?php echo e($family->students->sum(function($student) { return \App\Models\AssessmentPeriod::where('student_id', $student->student_id)->whereNotIn('status', ['completed', 'overdue'])->count(); })); ?>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="<?php echo e(route('teacher.family')); ?>" class="btn btn-outline-secondary">
  <i class="fas fa-arrow-left me-2"></i>Back to Families
</a>

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

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\family_show.blade.php ENDPATH**/ ?>