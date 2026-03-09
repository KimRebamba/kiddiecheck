

<?php $__env->startSection('content'); ?>

<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Sections</h1>
  <div class="ms-auto">
    <a href="<?php echo e(route('teacher.sections.create')); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> Add Section
    </a>
  </div>
</div>

<?php if($sections->isEmpty()): ?>
  <div class="empty-state">
    <div class="empty-icon">🗂️</div>
    <h5>No Sections Yet</h5>
    <p>You don't have any sections with assigned students yet.</p>
    <a href="<?php echo e(route('teacher.sections.create')); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> Create Your First Section
    </a>
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 col-lg-4">
        <div class="section-card card">
          <div class="card-body">

            <div class="section-card-top">
              <div class="section-avatar">
                <?php echo e(strtoupper(substr($section->name, 0, 1))); ?>

              </div>
              <div class="section-meta">
                <h5 class="card-title mb-0"><?php echo e($section->name); ?></h5>
                <span class="student-count">
                  <i class="fas fa-user-friends me-1"></i>
                  <?php echo e($section->student_count); ?> <?php echo e(Str::plural('student', $section->student_count)); ?>

                </span>
              </div>
            </div>

            <div class="section-card-actions">
              <a href="<?php echo e(route('teacher.sections.show', $section->section_id)); ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye me-1"></i>View
              </a>
              <?php if($section->student_count == 0): ?>
                <form action="<?php echo e(route('teacher.sections.destroy', $section->section_id)); ?>" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash me-1"></i>Delete
                  </button>
                </form>
              <?php endif; ?>
            </div>

            <?php if($section->student_count > 0): ?>
              <div class="no-delete-note">
                <i class="fas fa-lock me-1"></i>Sections with students cannot be deleted.
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

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
  --sky:         #4EA8DE;
  --sky-soft:    #D6EEFF;
  --peach:       #FF9A76;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE TITLE ── */
.h3.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.7rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* ── ADD SECTION BUTTON ── */
.btn-primary {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important;
  border-radius: 10px !important;
  background: linear-gradient(135deg, var(--violet), var(--coral)) !important;
  border: none !important;
  box-shadow: 0 3px 10px rgba(132,94,194,0.25) !important;
  transition: all 0.18s !important;
  font-size: 0.85rem !important;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(132,94,194,0.35) !important; }

/* ── SECTION CARDS ── */
.section-card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  transition: transform 0.2s, box-shadow 0.2s;
  overflow: hidden;
  animation: fadeUp 0.4s ease both;
  background: white !important;
}
.section-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 32px rgba(100,60,160,0.14) !important;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(14px); } to { opacity:1; transform:translateY(0); } }

/* stagger by position */
.col-md-6:nth-child(1) .section-card { animation-delay: 0.05s; }
.col-md-6:nth-child(2) .section-card { animation-delay: 0.10s; }
.col-md-6:nth-child(3) .section-card { animation-delay: 0.15s; }
.col-md-6:nth-child(4) .section-card { animation-delay: 0.20s; }
.col-md-6:nth-child(5) .section-card { animation-delay: 0.25s; }
.col-md-6:nth-child(6) .section-card { animation-delay: 0.30s; }

/* colored top accent bar cycling through palette */
.section-card::before {
  content: '';
  display: block;
  height: 5px;
  width: 100%;
}
.col-md-6:nth-child(6n+1) .section-card::before { background: linear-gradient(90deg, var(--violet), var(--coral)); }
.col-md-6:nth-child(6n+2) .section-card::before { background: linear-gradient(90deg, var(--teal),   var(--sky));   }
.col-md-6:nth-child(6n+3) .section-card::before { background: linear-gradient(90deg, var(--coral),  var(--peach)); }
.col-md-6:nth-child(6n+4) .section-card::before { background: linear-gradient(90deg, var(--mint),   var(--teal));  }
.col-md-6:nth-child(6n+5) .section-card::before { background: linear-gradient(90deg, var(--lemon),  var(--peach)); }
.col-md-6:nth-child(6n+6) .section-card::before { background: linear-gradient(90deg, var(--sky),    var(--violet));}

/* ── CARD TOP ROW ── */
.section-card-top {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 16px;
}
.section-avatar {
  width: 48px; height: 48px;
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  font-family: 'Baloo 2', cursive;
  font-size: 1.3rem; font-weight: 800;
  color: white;
  flex-shrink: 0;
}
/* cycle avatar colors */
.col-md-6:nth-child(6n+1) .section-avatar { background: linear-gradient(135deg, var(--violet), var(--coral)); }
.col-md-6:nth-child(6n+2) .section-avatar { background: linear-gradient(135deg, var(--teal),   var(--sky));   }
.col-md-6:nth-child(6n+3) .section-avatar { background: linear-gradient(135deg, var(--coral),  var(--peach)); }
.col-md-6:nth-child(6n+4) .section-avatar { background: linear-gradient(135deg, var(--mint),   var(--teal));  }
.col-md-6:nth-child(6n+5) .section-avatar { background: linear-gradient(135deg, var(--lemon),  var(--peach)); }
.col-md-6:nth-child(6n+6) .section-avatar { background: linear-gradient(135deg, var(--sky),    var(--violet));}

.card-title {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1rem !important;
  font-weight: 800 !important;
  color: var(--text) !important;
}
.student-count {
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--text-muted);
  display: block;
  margin-top: 2px;
}

/* ── CARD ACTIONS ── */
.section-card-actions {
  display: flex;
  gap: 8px;
  align-items: center;
}
.btn-sm {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important;
  border-radius: 9px !important;
  font-size: 0.76rem !important;
  padding: 5px 13px !important;
  transition: all 0.18s !important;
}
.btn-outline-primary {
  color: var(--violet) !important;
  border: 1.5px solid var(--violet-soft) !important;
  background: white !important;
}
.btn-outline-primary:hover { background: var(--violet-soft) !important; }

.btn-outline-danger {
  color: var(--coral) !important;
  border: 1.5px solid var(--coral-soft) !important;
  background: white !important;
}
.btn-outline-danger:hover { background: var(--coral-soft) !important; }

/* ── NO DELETE NOTE ── */
.no-delete-note {
  margin-top: 10px;
  font-size: 0.73rem;
  font-weight: 700;
  color: var(--text-muted);
  font-style: italic;
  display: flex;
  align-items: center;
  gap: 4px;
}
.no-delete-note .fa-lock { color: var(--lemon); }

/* ── EMPTY STATE ── */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  animation: fadeUp 0.4s ease;
}
.empty-icon { font-size: 3rem; margin-bottom: 12px; }
.empty-state h5 {
  font-family: 'Baloo 2', cursive;
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 6px;
}
.empty-state p {
  color: var(--text-muted);
  font-size: 0.9rem;
  font-weight: 600;
  margin-bottom: 20px;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('teacher.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Sedriel Navasca\Desktop\SAAD\Kiddiecheck\kiddiecheck\resources\views/teacher/sections.blade.php ENDPATH**/ ?>