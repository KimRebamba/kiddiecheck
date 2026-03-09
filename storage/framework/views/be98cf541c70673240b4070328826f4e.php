

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
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --teal:        #2EC4B6;
  --teal-soft:   #C8F4F1;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --lemon:       #F9C74F;
  --lemon-soft:  #FFF6CC;
  --peach:       #FF9A76;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE HEADER ── */
.h4.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.45rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.student-subhead {
  font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-top: 1px;
}

/* ── BACK / CANCEL BUTTONS ── */
.btn-ghost-back {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.8rem;
  background: white; color: var(--text-muted);
  border: 1.5px solid #E8E0F0; border-radius: 10px;
  padding: 6px 14px; text-decoration: none; display: inline-flex; align-items: center;
  transition: all 0.18s;
}
.btn-ghost-back:hover { background: var(--violet-soft); color: var(--violet); border-color: var(--violet-soft); }
.btn-ghost-cancel {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.85rem;
  background: #F0E8FF; color: var(--text-muted);
  border: none; border-radius: 10px; padding: 8px 18px;
  text-decoration: none; display: inline-flex; align-items: center;
  transition: all 0.18s;
}
.btn-ghost-cancel:hover { background: var(--violet-soft); color: var(--violet); }

/* ── ALERTS ── */
.alert-custom {
  border-radius: 10px; padding: 11px 15px;
  font-size: 0.86rem; font-weight: 700; line-height: 1.5;
}
.alert-danger-custom  { background: var(--coral-soft);  border-left: 4px solid var(--coral);  color: #a0203a; }
.alert-success-custom { background: var(--mint-soft);   border-left: 4px solid var(--mint);   color: #1a6640; }

/* ── PROGRESS ── */
.progress-wrap { background: white; border-radius: var(--radius); padding: 14px 18px; box-shadow: var(--shadow); }
.progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.progress-label  { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); }
.progress-count  { font-size: 0.78rem; font-weight: 700; color: var(--violet); }
.progress-track  { height: 8px; background: #F0E8FF; border-radius: 10px; overflow: hidden; }
.progress-fill   { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--violet), var(--coral)); transition: width 0.6s ease; }

/* ── CARDS ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  overflow: hidden;
  animation: fadeUp 0.35s ease both;
  transition: box-shadow 0.2s;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

/* ── CARD HEADERS ── */
.card-header {
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 13px 18px !important;
  display: flex; align-items: center; gap: 10px;
}
.card-header h5 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important;
  margin: 0 !important;
}
/* cycle domain dot colors */
.card:nth-child(7n+1) .domain-dot { background: var(--violet); }
.card:nth-child(7n+2) .domain-dot { background: var(--teal); }
.card:nth-child(7n+3) .domain-dot { background: var(--coral); }
.card:nth-child(7n+4) .domain-dot { background: var(--mint); }
.card:nth-child(7n+5) .domain-dot { background: var(--lemon); }
.card:nth-child(7n+6) .domain-dot { background: var(--peach); }
.card:nth-child(7n+7) .domain-dot { background: var(--teal); }
.domain-dot {
  width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}

/* ── TABLE ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.7rem !important; font-weight: 800 !important;
  text-transform: uppercase; letter-spacing: 0.07em;
  color: var(--text-muted) !important; background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 10px 16px !important;
}
.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }
.table tbody tr:last-child { border-bottom: none !important; }
.table tbody td { padding: 13px 16px !important; vertical-align: middle !important; border: none !important; }
.q-row:hover { background: #FDFBFF !important; }
.q-answered  { background: #FDFBFF; }

.q-text { font-weight: 800; font-size: 0.88rem; color: var(--text); line-height: 1.5; }
.q-sub  { font-size: 0.76rem; color: var(--text-muted); font-weight: 600; margin-top: 2px; }
.text-muted-italic { font-size: 0.85rem; color: var(--text-muted); font-style: italic; font-weight: 600; }

/* ── YES / NO BUTTONS ── */
.btn-answer {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important; font-size: 0.78rem !important;
  padding: 5px 14px !important; border-radius: 0 !important;
  transition: all 0.15s !important;
}
.btn-group .btn-answer:first-of-type { border-radius: 9px 0 0 9px !important; }
.btn-group .btn-answer:last-of-type  { border-radius: 0 9px 9px 0 !important; }

/* Yes — mint */
.btn-yes {
  color: var(--mint) !important;
  border: 1.5px solid var(--mint-soft) !important;
  background: white !important;
}
.btn-yes:hover { background: var(--mint-soft) !important; }
.btn-check:checked + .btn-yes {
  background: var(--mint) !important;
  color: white !important;
  border-color: var(--mint) !important;
  box-shadow: 0 2px 8px rgba(82,194,123,0.3) !important;
}

/* No — coral */
.btn-no {
  color: var(--coral) !important;
  border: 1.5px solid var(--coral-soft) !important;
  background: white !important;
}
.btn-no:hover { background: var(--coral-soft) !important; }
.btn-check:checked + .btn-no {
  background: var(--coral) !important;
  color: white !important;
  border-color: var(--coral) !important;
  box-shadow: 0 2px 8px rgba(255,107,138,0.3) !important;
}

/* ── SAVE BUTTON ── */
.btn-primary-grad {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.88rem;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  color: white; border: none; border-radius: 10px; padding: 9px 22px;
  box-shadow: 0 3px 12px rgba(132,94,194,0.28);
  display: inline-flex; align-items: center; transition: all 0.18s;
  cursor: pointer;
}
.btn-primary-grad:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(132,94,194,0.38); color: white; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/test_form.blade.php ENDPATH**/ ?>