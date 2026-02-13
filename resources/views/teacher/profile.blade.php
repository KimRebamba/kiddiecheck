@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Profile Settings</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Teacher Profile Card -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Personal Information</h5>
      </div>
      <div class="card-body">
        @php
          $user = Auth::user();
          $teacher = $user->teacher ?? null;
        @endphp
        
        <p class="mb-2">
          <strong>Name:</strong><br>
          {{ $user->username ?? 'N/A' }}
        </p>
        <p class="mb-2">
          <strong>Email:</strong><br>
          {{ $user->email ?? 'N/A' }}
        </p>
        <p class="mb-2">
          <strong>Role:</strong><br>
          <span class="badge bg-primary">{{ ucfirst($user->role) }}</span>
        </p>

        @if($teacher)
          <hr>
          <p class="mb-2">
            <strong>First Name:</strong><br>
            {{ $teacher->first_name ?? 'N/A' }}
          </p>
          <p class="mb-2">
            <strong>Last Name:</strong><br>
            {{ $teacher->last_name ?? 'N/A' }}
          </p>
          <p class="mb-2">
            <strong>Home Address:</strong><br>
            {{ $teacher->home_address ?? 'N/A' }}
          </p>
          <p class="mb-2">
            <strong>Phone Number:</strong><br>
            {{ $teacher->phone_number ?? 'N/A' }}
          </p>
          @php
            $hireDate = is_string($teacher->hire_date) ? \Carbon\Carbon::parse($teacher->hire_date) : $teacher->hire_date;
          @endphp
          <p class="mb-2">
            <strong>Hire Date:</strong><br>
            {{ $hireDate ? $hireDate->format('M d, Y') : 'N/A' }}
          </p>
        @endif
      </div>
    </div>
  </div>

  <!-- Statistics -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Statistics</h5>
      </div>
      <div class="card-body">
        @php
          $teacher = Auth::user()->teacher;
          $assignedStudents = $teacher ? $teacher->students()->count() : 0;
          $userId = Auth::user()->id ?? Auth::user()->user_id;
          $totalTests = \App\Models\Test::where('examiner_id', $userId)->count();
          $completedTests = \App\Models\Test::where('examiner_id', $userId)->whereIn('status', ['completed', 'finalized'])->count();
          $inProgressTests = \App\Models\Test::where('examiner_id', $userId)->where('status', 'in_progress')->count();
        @endphp

        <div class="row g-3">
          <div class="col-6">
            <p class="text-muted mb-1">Assigned Students</p>
            <p class="display-6">{{ $assignedStudents }}</p>
          </div>
          <div class="col-6">
            <p class="text-muted mb-1">Total Tests</p>
            <p class="display-6">{{ $totalTests }}</p>
          </div>
          <div class="col-6">
            <p class="text-muted mb-1">Completed Tests</p>
            <p class="display-6">{{ $completedTests }}</p>
          </div>
          <div class="col-6">
            <p class="text-muted mb-1">In Progress</p>
            <p class="display-6">{{ $inProgressTests }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Account Settings -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Account Settings</h5>
      </div>
      <div class="card-body">
        <div class="alert alert-info" role="alert">
          <strong>Note:</strong> To change your password or email address, please contact your administrator.
        </div>

        <p class="text-muted">Account management options will be available in future updates.</p>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="col-md-12">
    <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
      @csrf
      <button type="submit" class="btn btn-outline-danger">Logout</button>
    </form>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
  }
</style>
@endsection
