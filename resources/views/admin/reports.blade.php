@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h1 class="h4 mb-1">Reports</h1>
    <p class="text-muted mb-0">High-level insights, risk detection, and consistency monitoring.</p>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.reports.export', ['format' => 'excel'] + request()->query()) }}" class="btn btn-outline-secondary btn-sm">Export Red Flags (Excel)</a>
    <a href="{{ route('admin.reports.export', ['format' => 'pdf'] + request()->query()) }}" class="btn btn-outline-secondary btn-sm">Export Summary (PDF)</a>
  </div>
</div>

{{-- Global filters --}}
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age min (months)</label>
        <input type="number" name="age_min_months" value="{{ $filters['age_min_months'] }}" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-2">
        <label class="form-label form-label-sm">Age max (months)</label>
        <input type="number" name="age_max_months" value="{{ $filters['age_max_months'] }}" class="form-control form-control-sm" min="0">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Assessment period</label>
        <select name="period_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($periodOptions as $p)
            <option value="{{ $p->period_id }}" {{ (string)$filters['period_id'] === (string)$p->period_id ? 'selected' : '' }}>{{ $p->description }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Teacher</label>
        <select name="teacher_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($teacherOptions as $t)
            <option value="{{ $t->user_id }}" {{ (string)$filters['teacher_id'] === (string)$t->user_id ? 'selected' : '' }}>
              {{ $t->last_name }}, {{ $t->first_name }} ({{ $t->username }})
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Domain</label>
        <select name="domain_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($domainOptions as $d)
            <option value="{{ $d->domain_id }}" {{ (string)$filters['domain_id'] === (string)$d->domain_id ? 'selected' : '' }}>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Interpretation</label>
        <select name="interpretation" class="form-select form-select-sm">
          <option value="">All</option>
          <option value="advanced" {{ $filters['interpretation'] === 'advanced' ? 'selected' : '' }}>Advanced</option>
          <option value="average" {{ $filters['interpretation'] === 'average' ? 'selected' : '' }}>Average</option>
          <option value="retest" {{ $filters['interpretation'] === 'retest' ? 'selected' : '' }}>Re-test</option>
        </select>
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label form-label-sm">Scale version</label>
        <select name="scale_version_id" class="form-select form-select-sm">
          <option value="">All</option>
          @foreach($scaleVersions as $sv)
            <option value="{{ $sv->scale_version_id }}" {{ (string)$filters['scale_version_id'] === (string)$sv->scale_version_id ? 'selected' : '' }}>{{ $sv->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-12 col-md-3 mt-2 mt-md-0 text-md-end">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Apply Filters</button>
      </div>
    </form>
  </div>
</div>

<div class="row g-3 mb-3">
  {{-- 1. Student Development Overview --}}
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
              <div class="h5 mb-0">{{ $totalStudents }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Completed periods</div>
              <div class="h5 mb-0">{{ $totalCompletedPeriods }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Advanced</div>
              <div class="h5 mb-0 text-success">{{ $studentInterpretationCounts['advanced'] }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Average</div>
              <div class="h5 mb-0 text-warning">{{ $studentInterpretationCounts['average'] }}</div>
            </div>
          </div>
          <div class="col-6 col-md-2">
            <div class="border rounded p-2 h-100">
              <div class="text-muted small">Re-test (6 mo)</div>
              <div class="h5 mb-0 text-danger">{{ $studentInterpretationCounts['retest'] }}</div>
            </div>
          </div>
        </div>
        <div class="border rounded p-2 mb-2 bg-light">
          <span class="small text-muted">Students with major discrepancies:</span>
          <span class="fw-semibold text-danger">{{ $majorDiscrepancyStudentsCount }}</span>
        </div>

        <h3 class="h6 mt-3">Recently completed assessments</h3>
        @if($recentCompletedAssessments->isEmpty())
          <p class="text-muted mb-0 small">No completed assessments in the current filter.</p>
        @else
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
                @foreach($recentCompletedAssessments as $r)
                  <tr>
                    <td>{{ $r->last_name }}, {{ $r->first_name }}</td>
                    <td>{{ $r->family_name ?? '—' }}</td>
                    <td>{{ $r->period_description }}</td>
                    <td>{{ $r->end_date }}</td>
                    <td>{{ $r->final_interpretation ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    {{-- 3. Domain Performance Report --}}
    <div class="card">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Domain Performance (school-wide)</h2>
        <p class="text-muted small mb-0">Average raw and scaled scores grouped by age range.</p>
      </div>
      <div class="card-body small">
        @if(empty($domainPerformance))
          <p class="text-muted mb-0">No domain score data available for the current filters.</p>
        @else
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
                @foreach($domainPerformance as $dp)
                  @foreach($dp['age_buckets'] as $ageLabel => $bucket)
                    @php
                      $count = $bucket['count'];
                      $avgRaw = $count ? $bucket['sum_raw'] / $count : null;
                      $avgScaled = $count ? $bucket['sum_scaled'] / $count : null;
                    @endphp
                    <tr>
                      <td>{{ $dp['domain_name'] }}</td>
                      <td>{{ $ageLabel }}</td>
                      <td>{{ $avgRaw !== null ? number_format($avgRaw, 1) : '—' }}</td>
                      <td>{{ $avgScaled !== null ? number_format($avgScaled, 1) : '—' }}</td>
                      <td>{{ $count }}</td>
                    </tr>
                  @endforeach
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="mt-2 small">
            <span class="text-muted">Strongest domain:</span>
            <span class="fw-semibold">{{ $strongestDomain ?? '—' }}</span>
            <span class="ms-3 text-muted">Weakest domain:</span>
            <span class="fw-semibold">{{ $weakestDomain ?? '—' }}</span>
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Right column: teacher & comparison reports --}}
  <div class="col-12 col-xl-5">
    {{-- 4. Teacher Consistency Report --}}
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Teacher Consistency</h2>
        <p class="text-muted small mb-0">Identifies strict/lenient or misaligned teachers.</p>
      </div>
      <div class="card-body small">
        @if(empty($teacherConsistency))
          <p class="text-muted mb-0">No completed teacher assessments for the current filters.</p>
        @else
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
                @foreach($teacherConsistency as $t)
                  <tr>
                    <td>{{ $t->username }}</td>
                    <td>{{ $t->completed_assessments }}</td>
                    <td>{{ $t->avg_standard_score !== null ? number_format($t->avg_standard_score, 1) : '—' }}</td>
                    <td>{{ number_format($t->discrepancy_with_teachers_rate * 100, 0) }}%</td>
                    <td>{{ number_format($t->discrepancy_with_families_rate * 100, 0) }}%</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>

    {{-- 5. Teacher vs Family Comparison --}}
    <div class="card mb-3">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Teacher vs Family Comparison</h2>
      </div>
      <div class="card-body small">
        @if($teacherFamilyComparison['avg_teacher_score'] === null)
          <p class="text-muted mb-0">Not enough paired teacher/family scores for this filter.</p>
        @else
          <dl class="row mb-2">
            <dt class="col-6">Avg teacher score</dt>
            <dd class="col-6">{{ number_format($teacherFamilyComparison['avg_teacher_score'], 1) }}</dd>
            <dt class="col-6">Avg family score</dt>
            <dd class="col-6">{{ number_format($teacherFamilyComparison['avg_family_score'], 1) }}</dd>
          </dl>
          <dl class="row mb-0">
            <dt class="col-6">Minor discrepancies</dt>
            <dd class="col-6">{{ number_format($teacherFamilyComparison['pct_minor_discrepancy'] * 100, 0) }}%</dd>
            <dt class="col-6">Major discrepancies</dt>
            <dd class="col-6 text-danger">{{ number_format($teacherFamilyComparison['pct_major_discrepancy'] * 100, 0) }}%</dd>
          </dl>
        @endif
      </div>
    </div>

    {{-- 7. Scale Version Usage --}}
    <div class="card">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Scale Version Usage</h2>
      </div>
      <div class="card-body small">
        @if($scaleVersions->isEmpty())
          <p class="text-muted mb-0">No scale versions configured.</p>
        @else
          <ul class="list-group list-group-flush mb-0">
            @foreach($scaleVersions as $sv)
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span>{{ $sv->name }}</span>
                <span class="text-muted">{{ $scaleUsage[$sv->scale_version_id] ?? 0 }} assessments</span>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-3">
  {{-- 6. Assessment Monitoring Report --}}
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Monitoring</h2>
        <p class="text-muted small mb-0">Overdue periods, missing assessments, and in-progress tests.</p>
      </div>
      <div class="card-body small">
        <h3 class="h6">Overdue assessment periods</h3>
        @if($monitorOverdue->isEmpty())
          <p class="text-muted">None.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($monitorOverdue as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }} (ended {{ $a->end_date }})</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Students missing teacher assessments</h3>
        @if($monitorMissingTeacher->isEmpty())
          <p class="text-muted">None.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($monitorMissingTeacher as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Students missing family assessments</h3>
        @if($monitorMissingFamily->isEmpty())
          <p class="text-muted">None.</p>
        @else
          <ul class="list-group list-group-flush mb-2">
            @foreach($monitorMissingFamily as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }}</li>
            @endforeach
          </ul>
        @endif

        <h3 class="h6 mt-3">Tests still in progress</h3>
        @if($monitorInProgressTests->isEmpty())
          <p class="text-muted mb-0">No tests in progress for the current filters.</p>
        @else
          <ul class="list-group list-group-flush mb-0">
            @foreach($monitorInProgressTests as $a)
              <li class="list-group-item px-0">{{ $a->student_name }} · {{ $a->period_description }} (since {{ $a->test_date }})</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>

  {{-- 8. Red Flag Report --}}
  <div class="col-12 col-xl-6">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <h2 class="h6 mb-1">Red Flag Report</h2>
        <span class="badge bg-danger">High priority</span>
      </div>
      <div class="card-body small">
        @if($redFlags->isEmpty())
          <p class="text-muted mb-0">No high-priority red flags under the current filters.</p>
        @else
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
                @foreach($redFlags as $r)
                  <tr>
                    <td>{{ $r->last_name }}, {{ $r->first_name }}</td>
                    <td>{{ $r->family_name ?? '—' }}</td>
                    <td>{{ $r->period_description }}</td>
                    <td class="text-nowrap">{{ $r->final_standard_score ?? '—' }}</td>
                    <td>{{ $r->final_interpretation ?? '—' }}</td>
                    <td>
                      @if($r->teacher_discrepancy === 'major' || $r->teacher_family_discrepancy === 'major')
                        <span class="badge bg-danger">Major</span>
                      @elseif($r->teacher_discrepancy === 'minor' || $r->teacher_family_discrepancy === 'minor')
                        <span class="badge bg-warning text-dark">Minor</span>
                      @else
                        <span class="badge bg-success">None</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
