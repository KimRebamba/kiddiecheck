@extends('admin.layout')

@section('content')
<style>
  .dashboard-title { font-weight: 800; letter-spacing: -0.03em; }
  .dashboard-subtitle { font-size: 0.9rem; }
  .summary-card { border-radius: 1.1rem; border: 1px solid #E5E7EB; box-shadow: 0 6px 18px rgba(15,23,42,0.06); }
  .summary-card .card-title { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .09em; color: #6B7280; }
  .summary-card .display-6 { font-weight: 700; color: #111827; }
  .status-chip { border-radius: 999px; padding: 0.35rem 0.75rem; font-size: 0.8rem; }
  .dashboard-section-title { font-size: 0.85rem; letter-spacing: .08em; text-transform: uppercase; color: #6B7280; }
  .dashboard-section-subtitle { font-size: 0.85rem; color: #9CA3AF; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 dashboard-title mb-1">Admin Dashboard</h1>
    <p class="text-muted dashboard-subtitle mb-0">High-level overview of assessments, users, and students across the school.</p>
  </div>
</div>


<div class="row g-3 mb-4">
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Total Students</div>
        <div class="display-6">{{ $totalStudents }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Total Families</div>
        <div class="display-6">{{ $totalFamilies }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Total Teachers</div>
        <div class="display-6">{{ $totalTeachers }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Active Assessment Periods</div>
        <div class="display-6">{{ $activeAssessmentPeriods }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Tests In Progress</div>
        <div class="display-6">{{ $testsInProgress }}</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4 col-xl-2">
    <div class="card summary-card shadow-sm h-100">
      <div class="card-body">
        <div class="card-title text-muted mb-1">Completed This Month</div>
        <div class="display-6">{{ $completedAssessmentsThisMonth }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Status Overview</h2>
        <p class="text-muted small mb-0">Scheduled, ongoing, completed, and overdue assessments at a glance.</p>
      </div>
      <div class="card-body">
        <div class="row text-center g-3">
          <div class="col-6 col-md-3">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Scheduled</div>
              <div class="h4 mb-0">{{ $scheduledAssessments }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Ongoing</div>
              <div class="h4 mb-0">{{ $ongoingTests }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Completed</div>
              <div class="h4 mb-0">{{ $completedTests }}</div>
            </div>
          </div>
          <div class="col-6 col-md-3">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Overdue</div>
              <div class="h4 mb-0 text-danger">{{ $overdueAssessments }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <div class="col-12">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Students Requiring Attention</h2>
        <p class="text-muted small mb-0">Automatically generated alerts needing administrative review.</p>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush small">
          @forelse($studentsRequiringAttention as $item)
            <li class="list-group-item px-0 d-flex flex-column flex-sm-row justify-content-between align-items-start">
              <div class="me-sm-3">
                <div class="fw-semibold">{{ $item['student'] }}</div>
                <div class="text-muted">{{ $item['detail'] }}</div>
              </div>
              <span class="badge bg-danger-subtle text-danger mt-2 mt-sm-0 align-self-start status-chip border border-danger-subtle">
                {{ $item['type'] }}
              </span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted">No students currently flagged for attention.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>


<div class="card shadow-sm mb-4">
  <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
    <h2 class="h6 mb-1">Quick Actions</h2>
    {{-- <p class="text-muted small mb-0">Start key workflows directly from the dashboard.</p> --}}
  </div>
  <div class="card-body">
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">Create User</a>
      <a href="#" class="btn btn-outline-primary btn-sm">Register Teacher</a>
      <a href="#" class="btn btn-outline-primary btn-sm">Register Family</a>
      <a href="#" class="btn btn-outline-primary btn-sm">Add Student</a>
    </div>
  </div>
</div>

{{-- <div class="text-end">
  <a href="{{ route('admin.eccd') }}" class="btn btn-link btn-sm">View ECCD data</a>
</div> --}}

@endsection




