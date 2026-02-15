@extends('admin.layout')

@section('content')
<style>
  .dashboard-title {
    font-weight: 600;
  }
  .summary-card {
    border-radius: 0.75rem;
  }
  .summary-card .card-title {
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: .04em;
  }
  .summary-card .display-6 {
    font-weight: 600;
  }
  .status-chip {
    border-radius: 999px;
    padding: 0.35rem 0.75rem;
    font-size: 0.85rem;
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="h3 dashboard-title mb-1">Admin Dashboard</h1>
    <p class="text-muted mb-0">High-level overview of school assessments and activity.</p>
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
  
  <div class="col-12 col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Assessment Status Overview</h2>
        <p class="text-muted small mb-0">Monitor scheduled, ongoing, completed, and overdue assessments.</p>
      </div>
      <div class="card-body">
        <div class="row text-center g-3">
          <div class="col-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Scheduled Assessments</div>
              <div class="h4 mb-0">{{ $scheduledAssessments }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Ongoing Tests</div>
              <div class="h4 mb-0">{{ $ongoingTests }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Completed Tests</div>
              <div class="h4 mb-0">{{ $completedTests }}</div>
            </div>
          </div>
          <div class="col-6">
            <div class="border rounded-3 p-3 h-100">
              <div class="text-muted small mb-1">Overdue Assessments</div>
              <div class="h4 mb-0 text-danger">{{ $overdueAssessments }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div class="col-12 col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Development Summary Snapshot</h2>
        <p class="text-muted small mb-0">Aggregate outcomes across completed assessment periods.</p>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-3 h-100 text-center">
              <div class="text-muted small mb-1">Advanced Development</div>
              <div class="h4 mb-0 text-success">{{ $developmentSnapshot['advanced'] }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-3 h-100 text-center">
              <div class="text-muted small mb-1">Average Development</div>
              <div class="h4 mb-0">{{ $developmentSnapshot['average'] }}</div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="border rounded-3 p-3 h-100 text-center">
              <div class="text-muted small mb-1">Needs Monitoring / Retest</div>
              <div class="h4 mb-0 text-warning">{{ $developmentSnapshot['monitor'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  
  <div class="col-12 col-lg-6">
    <div class="card h-100 shadow-sm">
      <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
        <div>
          <h2 class="h6 mb-1">Recent Activity</h2>
          <p class="text-muted small mb-0">Latest teacher, family, and system events.</p>
        </div>
      </div>
      <div class="card-body">
        <ul class="list-group list-group-flush small">
          @forelse($recentActivities as $item)
            <li class="list-group-item px-0 d-flex justify-content-between align-items-start">
              <span>{{ $item['label'] }}</span>
              <span class="text-muted ms-3">{{ \Illuminate\Support\Carbon::parse($item['time'])->diffForHumans() }}</span>
            </li>
          @empty
            <li class="list-group-item px-0 text-muted">No recent activity to display.</li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>

  
  <div class="col-12 col-lg-6">
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




