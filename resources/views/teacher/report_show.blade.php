@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <a href="{{ route('teacher.reports') }}" class="btn btn-sm btn-secondary mb-2">← Back to Reports</a>
      <h3>{{ $student->first_name }} {{ $student->last_name }} - Period {{ $period->index }}</h3>
      <p class="text-muted">Assessment Results</p>
    </div>
  </div>

  @if($tests->isEmpty())
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-muted mb-0">No finalized tests for this period</p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      @foreach($tests as $test)
        <div class="col-md-12 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">Test from {{ $test->test_date->format('M d, Y') }}</h5>
            </div>
            <div class="card-body">
              <p><strong>Status:</strong> <span class="badge bg-success">{{ ucfirst($test->status) }}</span></p>
              <div class="mt-3">
                <a href="{{ route('teacher.reports.detail', [$student->id, $period->id, $test->id]) }}" class="btn btn-sm btn-primary">View Full Report</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
