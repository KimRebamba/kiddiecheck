@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Reports - {{ $student->first_name }} {{ $student->last_name }}</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.reports') }}" class="btn btn-sm btn-outline-secondary">Back to Reports</a>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header">
    <h5 class="mb-0">Assessment Period</h5>
  </div>
  <div class="card-body">
    <p class="mb-1"><strong>Period:</strong> {{ $period->description }}</p>
    <p class="mb-1"><strong>Dates:</strong> {{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }}</p>
    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-{{ $period->status === 'completed' ? 'success' : ($period->status === 'overdue' ? 'danger' : 'info') }}">{{ ucfirst($period->status) }}</span></p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Finalized Tests in this Period</h5>
  </div>
  <div class="card-body">
    @if($tests->isEmpty())
      <p class="text-muted mb-0">No finalized tests for this period.</p>
    @else
      <div class="table-responsive">
        <table class="table table-hover table-sm align-middle">
          <thead>
            <tr>
              <th>Test Date</th>
              <th>Status</th>
              <th>Standard Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tests as $test)
              @php $score = $test->standardScore; @endphp
              <tr>
                <td>{{ $test->test_date->format('M d, Y') }}</td>
                <td><span class="badge bg-success">{{ ucfirst($test->status) }}</span></td>
                <td>
                  @if($score)
                    <strong>{{ $score->standard_score }}</strong>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($score)
                    {{ $score->interpretation }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('teacher.reports.detail', [$student->student_id, $period->period_id, $test->test_id]) }}" class="btn btn-sm btn-outline-primary">View Details</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
@endsection
