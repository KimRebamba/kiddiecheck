

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - <?php echo e($test->student->first_name); ?> <?php echo e($test->student->last_name); ?></h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('teacher.index')); ?>">Back</a>
  </div>
</div>

<?php if(session('error')): ?>
  <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php if(session('success')): ?>
  <div class="alert alert-success"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<?php if($progressPct !== null): ?>
  <div class="progress mb-2" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: <?php echo e($progressPct); ?>%" aria-valuenow="<?php echo e($progressPct); ?>" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: <?php echo e($answeredCount); ?> / <?php echo e($totalQuestions); ?> (<?php echo e($progressPct); ?>%)</div>
<?php endif; ?>

<form method="post" action="<?php echo e(route('teacher.tests.form.submit', $test->test_id)); ?>">
  <?php echo csrf_field(); ?>

  <?php $__currentLoopData = $domains; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $domain): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="mb-0"><?php echo e($domain->name); ?></h5>
      </div>
      <div class="card-body p-0">
        <?php if($domain->questions->isEmpty()): ?>
          <p class="p-3 text-muted">No questions in this domain.</p>
        <?php else: ?>
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 60%;">Question</th>
                <th style="width: 40%;">Answer</th>
              </tr>
            </thead>
            <tbody>
              <?php $__currentLoopData = $domain->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $existingAnswer = $existing[$q->question_id] ?? null;
                ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?php echo e($q->text); ?></div>
                    <?php if($q->display_text): ?>
                      <div class="text-muted small"><?php echo e($q->display_text); ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="btn-group" role="group" aria-label="Answer">
                      <input type="radio" class="btn-check" name="q_<?php echo e($q->question_id); ?>" id="q<?php echo e($q->question_id); ?>_yes" value="yes" <?php echo e($existingAnswer === 'yes' ? 'checked' : ''); ?>>
                      <label class="btn btn-outline-success btn-sm" for="q<?php echo e($q->question_id); ?>_yes">Yes</label>

                      <input type="radio" class="btn-check" name="q_<?php echo e($q->question_id); ?>" id="q<?php echo e($q->question_id); ?>_no" value="no" <?php echo e($existingAnswer === 'no' ? 'checked' : ''); ?>>
                      <label class="btn btn-outline-danger btn-sm" for="q<?php echo e($q->question_id); ?>_no">No</label>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Save &amp; View Result</button>
    <a href="<?php echo e(route('teacher.index')); ?>" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\test_form.blade.php ENDPATH**/ ?>