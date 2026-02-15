

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - <?php echo e($test->student->first_name); ?> <?php echo e($test->student->last_name); ?></h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('teacher.index')); ?>">Back</a>
  </div>
</div>

<?php
  $totalQuestions = \App\Models\Domain::with('questions')->get()->sum(fn($d)=>$d->questions->count());
  $answeredCount = $test->responses->count();
  $progressPct = $totalQuestions ? round(($answeredCount / max(1,$totalQuestions)) * 100) : null;
?>

<?php if($progressPct !== null): ?>
  <div class="progress mb-3" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: <?php echo e($progressPct); ?>%" aria-valuenow="<?php echo e($progressPct); ?>" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: <?php echo e($answeredCount); ?> / <?php echo e($totalQuestions); ?> (<?php echo e($progressPct); ?>%)</div>
<?php endif; ?>

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <div class="text-muted">Domain</div>
        <h2 class="h5 mb-0"><?php echo e($domain->name); ?></h2>
      </div>
      <span class="badge bg-primary">Question <?php echo e($index + 1); ?></span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <p class="fs-5"><?php echo e($question->text); ?></p>
    <?php if($question->display_text): ?>
      <p><strong>Display Text:</strong> <?php echo e($question->display_text); ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo e(route('teacher.tests.question.submit', [$test->test_id, $domain->domain_id, $index])); ?>" class="mt-3">
      <?php echo csrf_field(); ?>
      <div class="btn-group" role="group" aria-label="Answer">
        <input type="radio" class="btn-check" name="answer" id="answerYes" value="yes" required>
        <label class="btn btn-outline-success" for="answerYes">Yes</label>

        <input type="radio" class="btn-check" name="answer" id="answerNo" value="no">
        <label class="btn btn-outline-danger" for="answerNo">No</label>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Next</button>
      </div>
    </form>
    <div class="mt-3 d-flex gap-2">
      <form method="post" action="<?php echo e(route('teacher.tests.pause', $test->test_id)); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-outline-secondary">Pause</button>
      </form>
      <form method="post" action="<?php echo e(route('teacher.tests.cancel', $test->test_id)); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-outline-danger">Cancel</button>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views\teacher\test_question.blade.php ENDPATH**/ ?>