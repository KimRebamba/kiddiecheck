@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <h3>Reports</h3>
      <p class="text-muted">View assessment reports for completed tests</p>
    </div>
  </div>

  @if($tests->isEmpty())
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-muted mb-0">No completed tests yet</p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Completed Tests</h5>
          </div>
          <div class="card-body">
            <table class="table table-sm table-hover">
              <thead>
                <tr class="table-light">
                  <th>Student Name</th>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tests as $test)
                  <tr>
                    <td>{{ $test->student->first_name }} {{ $test->student->last_name }}</td>
                    <td>Period {{ $test->assessmentPeriod->index }}</td>
                    <td>{{ $test->test_date->format('M d, Y') }}</td>
                    <td><span class="badge bg-success">{{ ucfirst($test->status) }}</span></td>
                    <td>
                      <a href="{{ route('teacher.reports.detail', [$test->student_id, $test->assessment_period_id, $test->id]) }}" class="btn btn-sm btn-primary">View Details</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
