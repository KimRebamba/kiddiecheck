@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">Family Details</h1>
</div>

<div class="row">
  <!-- Family Information -->
  <div class="col-12 col-lg-4 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-home me-2"></i>Family Information
        </h5>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Family Name</label>
          <div class="fw-bold">{{ $family->family_name }}</div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Address</label>
          <div>{{ $family->home_address }}</div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Emergency Contact</label>
          <div>{{ $family->emergency_contact }}</div>
        </div>
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Emergency Phone</label>
          <div>{{ $family->emergency_phone }}</div>
        </div>
        @if($family->user)
        <div class="mb-3">
          <label class="text-muted small fw-semibold">Account User</label>
          <div>{{ $family->user->username }} ({{ $family->user->email }})</div>
        </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Students in this Family -->
  <div class="col-12 col-lg-8 mb-4">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-users me-2"></i>Students in this Family
        </h5>
      </div>
      <div class="card-body p-0">
        @if($family->students->isNotEmpty())
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Age</th>
                  <th>Section</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($family->students as $student)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                          {{ strtoupper(substr($student->first_name, 0, 1)) }}
                        </div>
                        <div>
                          <div class="fw-semibold">{{ $student->first_name }} {{ $student->last_name }}</div>
                          <div class="text-muted small">{{ $student->date_of_birth->format('Y-m-d') }}</div>
                        </div>
                      </div>
                    </td>
                    <td>{{ $student->date_of_birth->age }} years</td>
                    <td>
                      <span class="badge bg-primary">{{ $student->section->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ route('teacher.reports.show', ['student' => $student->student_id, 'period' => 1]) }}" class="btn btn-sm btn-outline-primary">
                          <i class="fas fa-file-alt me-1"></i>Reports
                        </a>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-5">
            <i class="fas fa-users text-muted fs-1 mb-3"></i>
            <h6 class="text-muted">No students assigned to this family</h6>
            <p class="text-muted small">Students will appear here when they are assigned to this family.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card border-0 shadow-sm">
      <div class="card-header">
        <h5 class="card-title mb-0">
          <i class="fas fa-chart-line me-2"></i>Assessment Summary
        </h5>
      </div>
      <div class="card-body">
        <div class="row text-center">
          <div class="col-12 col-md-3 mb-3">
            <div class="text-primary fs-2 mb-2">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <h6 class="text-muted fw-semibold">Total Tests</h6>
            <div class="display-4 fs-1 fw-bold text-primary">
              {{ $family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'completed')->count(); }) }}
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-success fs-2 mb-2">
              <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="text-muted fw-semibold">Completed</h6>
            <div class="display-4 fs-1 fw-bold text-success">
              {{ $family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'finalized')->count(); }) }}
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-warning fs-2 mb-2">
              <i class="fas fa-clock"></i>
            </div>
            <h6 class="text-muted fw-semibold">In Progress</h6>
            <div class="display-4 fs-1 fw-bold text-warning">
              {{ $family->students->sum(function($student) { return \App\Models\Test::where('student_id', $student->student_id)->where('status', 'in_progress')->count(); }) }}
            </div>
          </div>
          <div class="col-12 col-md-3 mb-3">
            <div class="text-info fs-2 mb-2">
              <i class="fas fa-calendar-check"></i>
            </div>
            <h6 class="text-muted fw-semibold">Upcoming</h6>
            <div class="display-4 fs-1 fw-bold text-info">
              {{ $family->students->sum(function($student) { return \App\Models\AssessmentPeriod::where('student_id', $student->student_id)->whereNotIn('status', ['completed', 'overdue'])->count(); }) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="{{ route('teacher.family') }}" class="btn btn-outline-secondary">
  <i class="fas fa-arrow-left me-2"></i>Back to Families
</a>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--teacher-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
  }
</style>
@endsection
