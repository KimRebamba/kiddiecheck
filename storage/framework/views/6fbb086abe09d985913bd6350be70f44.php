<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0"><?php echo e($section->name); ?></h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.sections')); ?>" class="btn btn-outline-secondary">Back</a>
    <a href="<?php echo e(route('teacher.sections.edit', $section->section_id)); ?>" class="btn btn-outline-primary">Edit Section</a>
  </div>
</div>

<div class="row g-3">
  <!-- Section Information -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Section Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> <?php echo e($section->name); ?></p>
        <p class="mb-2"><strong>Total Students:</strong> <?php echo e($students->count()); ?></p>
      </div>
    </div>
  </div>

  <!-- Students -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assigned Students</h5>
      </div>
      <div class="card-body">
        <?php if($students->isEmpty()): ?>
          <p class="text-muted">No students assigned to this section.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Eligible for Test</th>
                  <th>Last Standard Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>
                      <a href="<?php echo e(route('teacher.student', $student->student_id)); ?>" class="text-decoration-none">
                        <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?>

                      </a>
                    </td>
                    <td><?php echo e($student->age ?? 'N/A'); ?> years</td>
                    <td>
                      <?php if($student->eligible): ?>
                        <span class="badge bg-success">Yes</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">No</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo e($student->last_standard_score ?? 'No score'); ?></td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="<?php echo e(route('teacher.student', $student->student_id)); ?>" class="btn btn-outline-secondary">View</a>
                        
                        <?php if($student->eligible): ?>
                          <?php
                            $availablePeriod = DB::table('assessment_periods')
                                ->where('student_id', $student->student_id)
                                ->where('status', '!=', 'overdue')
                                ->where('status', '!=', 'completed')
                                ->first();
                          ?>
                          <?php if($availablePeriod): ?>
                            <form action="<?php echo e(route('teacher.tests.start', $student->student_id)); ?>" method="POST" style="display: inline;">
                              <?php echo csrf_field(); ?>
                              <input type="hidden" name="period_id" value="<?php echo e($availablePeriod->period_id); ?>">
                              <button type="submit" class="btn btn-outline-primary">Start Test</button>
                            </form>
                          <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Delete Section Button -->
                        <?php if($section->student_count == 0): ?>
                          <form action="<?php echo e(route('teacher.sections.destroy', $section->section_id)); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-outline-danger">Delete Section</button>
                          </form>
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
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/sections_show.blade.php ENDPATH**/ ?>