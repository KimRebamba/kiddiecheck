

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Reports</h1>
    <p class="text-muted mb-0">High-level insights, risk detection, and consistency monitoring.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="<?php echo e(route('admin.reports.export', ['format' => 'excel'] + request()->query())); ?>" class="btn btn-outline-secondary btn-sm">Export Red Flags (Excel)</a>
    <a href="<?php echo e(route('admin.reports.export', ['format' => 'pdf'] + request()->query())); ?>" class="btn btn-outline-secondary btn-sm">Export Summary (PDF)</a>
  </div>
</div>


<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age min (months)</label>
        <input type="number" name="age_min_months" value="<?php echo e($filters['age_min_months']); ?>" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age max (months)</label>
        <input type="number" name="age_max_months" value="<?php echo e($filters['age_max_months']); ?>" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Assessment period</label>
        <select name="period_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $periodOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($p->period_id); ?>" <?php echo e((string)$filters['period_id'] === (string)$p->period_id ? 'selected' : ''); ?>><?php echo e($p->description); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Teacher</label>
        <select name="teacher_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $teacherOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($t->user_id); ?>" <?php echo e((string)$filters['teacher_id'] === (string)$t->user_id ? 'selected' : ''); ?>>
              <?php echo e($t->last_name); ?>, <?php echo e($t->first_name); ?> (<?php echo e($t->username); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Domain</label>
        <select name="domain_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $domainOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($d->domain_id); ?>" <?php echo e((string)$filters['domain_id'] === (string)$d->domain_id ? 'selected' : ''); ?>><?php echo e($d->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Interpretation</label>
        <select name="interpretation" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="advanced" <?php echo e($filters['interpretation'] === 'advanced' ? 'selected' : ''); ?>>Advanced</option>
          <option value="average" <?php echo e($filters['interpretation'] === 'average' ? 'selected' : ''); ?>>Average</option>
          <option value="retest" <?php echo e($filters['interpretation'] === 'retest' ? 'selected' : ''); ?>>Re-test</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Scale version</label>
        <select name="scale_version_id" class="form-select form-select-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $scaleVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sv->scale_version_id); ?>" <?php echo e((string)$filters['scale_version_id'] === (string)$sv->scale_version_id ? 'selected' : ''); ?>><?php echo e($sv->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-12 col-md-3 mt-2 mt-md-0 text-md-end">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  
  <div class="col-12 col-xl-7">
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Student Development Overview</h2>
      </div>
      <div class="card-body">
        <div class="row g-3 mb-2">
          <div class="col-6 col-md-3">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Total students</div>
              <div class="h5 mb-0"><?php echo e($totalStudents); ?></div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Completed periods</div>
              <div class="h5 mb-0"><?php echo e($totalCompletedPeriods); ?></div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Advanced</div>
              <div class="h5 mb-0 text-success"><?php echo e($studentInterpretationCounts['advanced']); ?></div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Average</div>
              <div class="h5 mb-0 text-warning"><?php echo e($studentInterpretationCounts['average']); ?></div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Re-test (6 mo)</div>
              <div class="h5 mb-0 text-danger"><?php echo e($studentInterpretationCounts['retest']); ?></div>
            </div>
          </div>
        </div>
        <div class="border rounded p-2 mb-2 bg-light">
          <span class="small text-muted">Students with major discrepancies:</span>
          <span class="fw-semibold text-danger"><?php echo e($majorDiscrepancyStudentsCount); ?></span>
        </div>

        <h3 class="h6 mt-3">Recently completed assessments</h3>
        <?php if($recentCompletedAssessments->isEmpty()): ?>
          <p class="text-muted mb-0 small">No completed assessments in the current filter.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0 small align-middle">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Family</th>
                  <th>Period</th>
                  <th>Completed</th>
                  <th>Final interpretation</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $recentCompletedAssessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($r->last_name); ?>, <?php echo e($r->first_name); ?></td>
                    <td><?php echo e($r->family_name ?? '—'); ?></td>
                    <td><?php echo e($r->period_description); ?></td>
                    <td><?php echo e($r->end_date); ?></td>
                    <td><?php echo e($r->final_interpretation ?? '—'); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="card">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Domain Performance (school-wide)</h2>
        <p class="text-muted small mb-0">Average raw and scaled scores grouped by age range.</p>
      </div>
      <div class="card-body small">
        <?php if(empty($domainPerformance)): ?>
          <p class="text-muted mb-0">No domain score data available for the current filters.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Age range</th>
                  <th>Avg raw</th>
                  <th>Avg scaled</th>
                  <th>N</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $domainPerformance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $__currentLoopData = $dp['age_buckets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ageLabel => $bucket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $count = $bucket['count'];
                      $avgRaw = $count ? $bucket['sum_raw'] / $count : null;
                      $avgScaled = $count ? $bucket['sum_scaled'] / $count : null;
                    ?>
                    <tr>
                      <td><?php echo e($dp['domain_name']); ?></td>
                      <td><?php echo e($ageLabel); ?></td>
                      <td><?php echo e($avgRaw !== null ? number_format($avgRaw, 1) : '—'); ?></td>
                      <td><?php echo e($avgScaled !== null ? number_format($avgScaled, 1) : '—'); ?></td>
                      <td><?php echo e($count); ?></td>
                    </tr>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
          <div class="mt-2 small">
            <span class="text-muted">Strongest domain:</span>
            <span class="fw-semibold"><?php echo e($strongestDomain ?? '—'); ?></span>
            <span class="ms-3 text-muted">Weakest domain:</span>
            <span class="fw-semibold"><?php echo e($weakestDomain ?? '—'); ?></span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  
  <div class="col-12 col-xl-5">
    
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Teacher Consistency</h2>
        <p class="text-muted small mb-0">Identifies strict/lenient or misaligned teachers.</p>
      </div>
      <div class="card-body small">
        <?php if(empty($teacherConsistency)): ?>
          <p class="text-muted mb-0">No completed teacher assessments for the current filters.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th>Teacher</th>
                  <th>Completed</th>
                  <th>Avg score</th>
                  <th>Disc. vs teachers</th>
                  <th>Disc. vs families</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $teacherConsistency; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($t->username); ?></td>
                    <td><?php echo e($t->completed_assessments); ?></td>
                    <td><?php echo e($t->avg_standard_score !== null ? number_format($t->avg_standard_score, 1) : '—'); ?></td>
                    <td><?php echo e(number_format($t->discrepancy_with_teachers_rate * 100, 0)); ?>%</td>
                    <td><?php echo e(number_format($t->discrepancy_with_families_rate * 100, 0)); ?>%</td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Teacher vs Family Comparison</h2>
      </div>
      <div class="card-body small">
        <?php if($teacherFamilyComparison['avg_teacher_score'] === null): ?>
          <p class="text-muted mb-0">Not enough paired teacher/family scores for this filter.</p>
        <?php else: ?>
          <dl class="row mb-2">
            <dt class="col-6">Avg teacher score</dt>
            <dd class="col-6"><?php echo e(number_format($teacherFamilyComparison['avg_teacher_score'], 1)); ?></dd>
            <dt class="col-6">Avg family score</dt>
            <dd class="col-6"><?php echo e(number_format($teacherFamilyComparison['avg_family_score'], 1)); ?></dd>
          </dl>
          <dl class="row mb-0">
            <dt class="col-6">Minor discrepancies</dt>
            <dd class="col-6"><?php echo e(number_format($teacherFamilyComparison['pct_minor_discrepancy'] * 100, 0)); ?>%</dd>
            <dt class="col-6">Major discrepancies</dt>
            <dd class="col-6 text-danger"><?php echo e(number_format($teacherFamilyComparison['pct_major_discrepancy'] * 100, 0)); ?>%</dd>
          </dl>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="card">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Scale Version Usage</h2>
      </div>
      <div class="card-body small">
        <?php if($scaleVersions->isEmpty()): ?>
          <p class="text-muted mb-0">No scale versions configured.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-0">
            <?php $__currentLoopData = $scaleVersions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span><?php echo e($sv->name); ?></span>
                <span class="text-muted"><?php echo e($scaleUsage[$sv->scale_version_id] ?? 0); ?> assessments</span>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Monitoring</h2>
        <p class="text-muted small mb-0">Overdue periods, missing assessments, and in-progress tests.</p>
      </div>
      <div class="card-body small">
        <h3 class="h6">Overdue assessment periods</h3>
        <?php if($monitorOverdue->isEmpty()): ?>
          <p class="text-muted">None.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $monitorOverdue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?> (ended <?php echo e($a->end_date); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Students missing teacher assessments</h3>
        <?php if($monitorMissingTeacher->isEmpty()): ?>
          <p class="text-muted">None.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $monitorMissingTeacher; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Students missing family assessments</h3>
        <?php if($monitorMissingFamily->isEmpty()): ?>
          <p class="text-muted">None.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-2">
            <?php $__currentLoopData = $monitorMissingFamily; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>

        <h3 class="h6 mt-3">Tests still in progress</h3>
        <?php if($monitorInProgressTests->isEmpty()): ?>
          <p class="text-muted mb-0">No tests in progress for the current filters.</p>
        <?php else: ?>
          <ul class="list-group list-group-flush mb-0">
            <?php $__currentLoopData = $monitorInProgressTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item px-0"><?php echo e($a->student_name); ?> · <?php echo e($a->period_description); ?> (since <?php echo e($a->test_date); ?>)</li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Red Flag Report</h2>
        <span class="badge bg-danger">High priority</span>
      </div>
      <div class="card-body small">
        <?php if($redFlags->isEmpty()): ?>
          <p class="text-muted mb-0">No high-priority red flags under the current filters.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Family</th>
                  <th>Period</th>
                  <th>Final score</th>
                  <th>Interpretation</th>
                  <th>Discrepancy</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $redFlags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td><?php echo e($r->last_name); ?>, <?php echo e($r->first_name); ?></td>
                    <td><?php echo e($r->family_name ?? '—'); ?></td>
                    <td><?php echo e($r->period_description); ?></td>
                    <td class="text-nowrap"><?php echo e($r->final_standard_score ?? '—'); ?></td>
                    <td><?php echo e($r->final_interpretation ?? '—'); ?></td>
                    <td>
                      <?php if($r->teacher_discrepancy === 'major' || $r->teacher_family_discrepancy === 'major'): ?>
                        <span class="badge bg-danger">Major</span>
                      <?php elseif($r->teacher_discrepancy === 'minor' || $r->teacher_family_discrepancy === 'minor'): ?>
                        <span class="badge bg-warning text-dark">Minor</span>
                      <?php else: ?>
                        <span class="badge bg-success">None</span>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kim\Desktop\laravel\kiddiecheck\resources\views/admin/reports.blade.php ENDPATH**/ ?>