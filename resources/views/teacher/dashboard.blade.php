@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <!-- Teacher Profile Summary -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <h4>Welcome, {{ Auth::user()->name }}</h4>
        <p class="text-muted mb-0">Teacher Profile Summary</p>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="row mb-4">
    <div class="col-md-3 mb-3">
      <div class="card text-center">
        <div class="card-body">
          <h2 class="text-primary">{{ $totalStudents }}</h2>
          <p class="text-muted mb-0">Assigned Students</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center">
        <div class="card-body">
          <h2 class="text-warning">{{ $upcomingAssessments->count() }}</h2>
          <p class="text-muted mb-0">Upcoming Assessments</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center">
        <div class="card-body">
          <h2 class="text-danger">{{ $overdueAssessments->count() }}</h2>
          <p class="text-muted mb-0">Overdue Assessments</p>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="card text-center">
        <div class="card-body">
          <h2 class="text-success">{{ $recentlyCompleted->count() }}</h2>
          <p class="text-muted mb-0">Recently Completed</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Assessments Table -->
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Upcoming Assessments</h5>
        </div>
        <div class="card-body">
          @if($upcomingAssessments->isEmpty())
            <p class="text-muted mb-0">No upcoming assessments.</p>
          @else
            <table class="table table-sm table-hover mb-0">
              <thead>
                <tr class="table-light">
                  <th>Student Name</th>
                  <th>Period</th>
                  <th>Due Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($upcomingAssessments as $assessment)
                  <tr>
                    <td>{{ $assessment->student->first_name }} {{ $assessment->student->last_name }}</td>
                    <td>Period {{ $assessment->index }}</td>
                    <td>{{ $assessment->ends_at->format('M d, Y') }}</td>
                    <td><span class="badge bg-warning">{{ ucfirst($assessment->status) }}</span></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Overdue Assessments Table -->
  @if($overdueAssessments->isNotEmpty())
  <div class="row mb-4">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Overdue Assessments</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr class="table-light">
                <th>Student Name</th>
                <th>Period</th>
                <th>Due Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($overdueAssessments as $assessment)
                <tr>
                  <td>{{ $assessment->student->first_name }} {{ $assessment->student->last_name }}</td>
                  <td>Period {{ $assessment->index }}</td>
                  <td>{{ $assessment->ends_at->format('M d, Y') }}</td>
                  <td><span class="badge bg-danger">{{ ucfirst($assessment->status) }}</span></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Recently Completed Tests Table -->
  @if($recentlyCompleted->isNotEmpty())
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Recently Completed Tests</h5>
        </div>
        <div class="card-body">
          <table class="table table-sm table-hover mb-0">
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
              @foreach($recentlyCompleted as $test)
                <tr>
                  <td>{{ $test->student->first_name }} {{ $test->student->last_name }}</td>
                  <td>Period {{ $test->assessmentPeriod->index }}</td>
                  <td>{{ $test->test_date->format('M d, Y') }}</td>
                  <td><span class="badge bg-success">{{ ucfirst($test->status) }}</span></td>
                  <td>
                    <a href="{{ route('teacher.tests.result', $test->id) }}" class="btn btn-sm btn-primary">View</a>
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
