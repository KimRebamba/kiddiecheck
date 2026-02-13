@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Reports</h1>
</div>

<div class="row g-3">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Select Assessment to Review</h5>
      </div>
      <div class="card-body">
        @if($tests->isEmpty())
          <p class="text-muted">No completed assessments to review.</p>
        @else
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Status</th>
                  <th>Standard Score</th>
                  <th>Interpretation</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tests as $test)
                  @php
                    $standardScore = $test->standardScore;
                  @endphp
                  <tr>
                    <td>
                      <strong>{{ $test->student->first_name }} {{ $test->student->last_name }}</strong>
                    </td>
                    <td>{{ $test->assessmentPeriod->description }}</td>
                    <td>{{ $test->test_date->format('M d, Y') }}</td>
                    <td>
                      <span class="badge bg-success">{{ ucfirst($test->status) }}</span>
                    </td>
                    <td>
                      @if($standardScore)
                        <strong>{{ $standardScore->standard_score }}</strong>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if($standardScore)
                        {{ $standardScore->interpretation }}
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <a href="{{ route('teacher.reports.detail', [$test->student_id, $test->period_id, $test->test_id]) }}" class="btn btn-sm btn-outline-primary">
                        View Details
                      </a>
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

<style>
  .table-hover tbody tr:hover {
    background-color: rgba(231, 122, 116, 0.1);
  }
</style>
@endsection
