@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <a href="{{ route('teacher.reports.show', [$student->id, $period->id]) }}" class="btn btn-sm btn-secondary mb-2">← Back to Period Summary</a>
      <h3>{{ $student->first_name }} {{ $student->last_name }} - Test Report</h3>
      <p class="text-muted">Period {{ $period->index }} | Test Date: {{ $test->test_date->format('M d, Y') }}</p>
    </div>
  </div>

  <!-- Test Summary -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">A. Test Summary</h5>
        </div>
        <div class="card-body">
          @if($test->domainScores->isEmpty())
            <p class="text-muted mb-0">No domain scores recorded</p>
          @else
            <table class="table table-sm">
              <thead>
                <tr class="table-light">
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                </tr>
              </thead>
              <tbody>
                @foreach($test->domainScores as $score)
                  <tr>
                    <td>{{ $score->domain->name ?? 'N/A' }}</td>
                    <td>{{ $score->raw_score }}</td>
                    <td>{{ $score->scaled_score }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>

            @if($test->standardScore)
              <hr>
              <p class="mb-2"><strong>Sum of Scaled Scores:</strong> {{ $test->standardScore->sum_scaled_scores }}</p>
              <p class="mb-2"><strong>Standard Score:</strong> {{ $test->standardScore->standard_score }}</p>
              <p class="mb-0"><strong>Interpretation:</strong> {{ $test->standardScore->interpretation }}</p>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Period Summary (if available) -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">B. Period Summary</h5>
        </div>
        <div class="card-body">
          <p class="text-muted mb-0">Summary data will be calculated when all tests for this period are finalized</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
