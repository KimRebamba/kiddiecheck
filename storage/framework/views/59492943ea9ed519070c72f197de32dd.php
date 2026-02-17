

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Sections</h1>
</div>

<?php if($sections->isEmpty()): ?>
  <div class="alert alert-info" role="alert">
    No sections with assigned students.
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-lg-4">
        <div class="card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sectionModal<?php echo e($section->section_id); ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo e($section->name); ?></h5>
            <p class="card-text text-muted">
              <strong>Students:</strong> <?php echo e($section->students->count()); ?>

            </p>
            <?php if($section->description): ?>
              <p class="card-text" style="font-size: 0.85rem;"><?php echo e(Str::limit($section->description, 60)); ?></p>
            <?php endif; ?>
            <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#sectionModal<?php echo e($section->section_id); ?>">
              View Students
            </button>
          </div>
        </div>
      </div>

      <!-- Section Modal with Students Table -->
      <div class="modal fade" id="sectionModal<?php echo e($section->section_id); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo e($section->name); ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <?php if($section->description): ?>
                <p class="text-muted mb-3"><?php echo e($section->description); ?></p>
              <?php endif; ?>

              <?php if($section->students->isEmpty()): ?>
                <p class="text-muted">No students in this section assigned to you.</p>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Last Score</th>
                        <th>Eligible</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $__currentLoopData = $section->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                          <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                          <td><?php echo e($student->age ?? 'N/A'); ?></td>
                          <td><?php echo e($student->last_standard_score ?? 'No score'); ?></td>
                          <td>
                            <?php if($student->eligible): ?>
                              <span class="badge bg-success">Yes</span>
                            <?php else: ?>
                              <span class="badge bg-secondary">No</span>
                            <?php endif; ?>
                          </td>
                          <td style="white-space: nowrap;">
                            <div class="btn-group btn-group-sm" role="group">
                              <a href="<?php echo e(route('teacher.student', $student->student_id)); ?>" class="btn btn-outline-secondary" title="View Student">View</a>
                              
                              <?php if($student->eligible): ?>
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#startTestModal<?php echo e($student->student_id); ?>" title="Start Test">Test</button>
                              <?php endif; ?>

                              <a href="<?php echo e(route('teacher.reports')); ?>" class="btn btn-outline-info" title="View Reports">Report</a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Start Test Modals for each student -->
      <?php $__currentLoopData = $section->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($student->eligible): ?>
          <div class="modal fade" id="startTestModal<?php echo e($student->student_id); ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Start Assessment</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Student:</strong> <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></p>
                  
                  <?php
                    $eligiblePeriods = $student->assessmentPeriods()
                      ->where('status', '!=', 'completed')
                      ->where('end_date', '>=', now())
                      ->get();
                  ?>

                  <?php if($eligiblePeriods->isEmpty()): ?>
                    <p class="text-warning">No active assessment periods found for this student.</p>
                  <?php else: ?>
                    <label class="form-label">Select Assessment Period:</label>
                    <form action="<?php echo e(route('teacher.tests.start', $student->student_id)); ?>" method="POST">
                      <?php echo csrf_field(); ?>
                      <select name="period_id" class="form-select mb-3" required>
                        <option value="">-- Select Period --</option>
                        <?php $__currentLoopData = $eligiblePeriods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <option value="<?php echo e($period->period_id); ?>">
                            <?php echo e($period->description); ?> (<?php echo e($period->start_date->format('M d')); ?> - <?php echo e($period->end_date->format('M d, Y')); ?>)
                          </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </select>
                      <button type="submit" class="btn btn-primary w-100">Start Assessment</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

<style>
  .modal-content {
    border-radius: 12px;
  }
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s;
  }
  .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views/teacher/sections.blade.php ENDPATH**/ ?>