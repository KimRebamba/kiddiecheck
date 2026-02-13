@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Report</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.reports') }}" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
  </div>
</div>

<div class="row g-3">
  <!-- Student & Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
            @php
              $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
              $testDate = is_string($test->test_date) ? \Carbon\Carbon::parse($test->test_date) : $test->test_date;
            @endphp
            <p><strong>Date of Birth:</strong> {{ $dob->format('M d, Y') }}</p>
            <p><strong>Age at Test:</strong> {{ $dob->diffInYears($testDate) }} years</p>
          </div>
          <div class="col-md-6">
            <p><strong>Assessment Period:</strong> {{ $period->description }}</p>
            <p><strong>Period Dates:</strong> {{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}</p>
            <p><strong>Test Date:</strong> {{ $test->test_date->format('M d, Y') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- A. Test Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
        <h5 class="mb-0">Test Summary</h5>
      </div>
      <div class="card-body">
        @php
          $standardScore = $test->standardScore;
        @endphp

        @if($test->domainScores && $test->domainScores->count() > 0)
          <h6 class="mb-3">Domain Scores</h6>
          <div class="table-responsive mb-3">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th style="width: 40%;">Visual</th>
                </tr>
              </thead>
              <tbody>
                @foreach($test->domainScores as $domainScore)
                  <tr>
                    <td><strong>{{ $domainScore->domain->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $domainScore->raw_score ?? 'N/A' }}</td>
                    <td>{{ $domainScore->scaled_score ?? 'N/A' }}</td>
                    <td>
                      @if($domainScore->scaled_score)
                        <div class="progress" style="height: 20px;">
                          @php
                            $percentage = min(100, ($domainScore->scaled_score / 19) * 100);
                          @endphp
                          <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $domainScore->scaled_score }}
                          </div>
                        </div>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        @if($standardScore)
          <hr>
          <div class="row">
            <div class="col-md-6">
              <p class="mb-2"><strong>Sum of Scaled Scores:</strong> {{ $standardScore->sum_scaled_scores }}</p>
              <p class="mb-2"><strong>Standard Score:</strong> {{ $standardScore->standard_score }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-2"><strong>Interpretation:</strong> <span class="badge bg-info">{{ $standardScore->interpretation }}</span></p>
            </div>
          </div>
        @else
          <p class="text-muted">Standard score not yet calculated.</p>
        @endif
      </div>
    </div>
  </div>

  <!-- B. Period Summary (if period is completed) -->
  @if($period->status === 'completed')
    <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-primary" style="background-color: rgba(231, 122, 116, 0.2) !important;">
          <h5 class="mb-0">Period Summary</h5>
        </div>
        <div class="card-body">
          @php
            $periodSummary = \App\Models\PeriodSummaryScore::where('period_id', $period->period_id)->first();
          @endphp

          @if($periodSummary)
            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher's Avg Score:</strong><br>
                  {{ $periodSummary->teachers_standard_score_avg ?? 'N/A' }}
                </p>
                <p class="mb-2">
                  <strong>Family Score:</strong><br>
                  {{ $periodSummary->family_standard_score ?? 'Not provided' }}
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Weighted Final Score:</strong><br>
                  <strong>{{ $periodSummary->final_standard_score ?? 'N/A' }}</strong>
                </p>
                <p class="mb-2">
                  <strong>Final Interpretation:</strong><br>
                  <span class="badge bg-success">{{ $periodSummary->final_interpretation ?? 'N/A' }}</span>
                </p>
              </div>
            </div>

            <hr>

            <div class="row">
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher Discrepancy:</strong><br>
                  <span class="badge bg-warning">{{ ucfirst($periodSummary->teacher_discrepancy ?? 'none') }}</span>
                </p>
              </div>
              <div class="col-md-6">
                <p class="mb-2">
                  <strong>Teacher-Family Discrepancy:</strong><br>
                  <span class="badge bg-warning">{{ ucfirst($periodSummary->teacher_family_discrepancy ?? 'none') }}</span>
                </p>
              </div>
            </div>
          @else
            <p class="text-muted">Period summary not available yet.</p>
          @endif
        </div>
      </div>
    </div>
  @endif

  <!-- Test Notes -->
  @if($test->notes)
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Notes</h5>
        </div>
        <div class="card-body">
          <p>{{ $test->notes }}</p>
        </div>
      </div>
    </div>
  @endif

  <!-- Actions -->
  <div class="col-md-12">
    <a href="{{ route('teacher.reports') }}" class="btn btn-outline-secondary">Back to Reports</a>
    <!-- PDF download can be added here later -->
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .card-header {
    border-bottom: 2px solid rgba(231, 122, 116, 0.3);
  }
</style>
@endsection
