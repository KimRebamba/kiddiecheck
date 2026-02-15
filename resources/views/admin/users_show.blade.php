@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div class="d-flex align-items-center gap-3">
    @if($user->profile_path)
      <img src="{{ asset($user->profile_path) }}" alt="Profile" class="rounded-circle" style="width:56px;height:56px;object-fit:cover;">
    @else
      <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:1.25rem;">
        {{ strtoupper(substr($user->username, 0, 1)) }}
      </div>
    @endif
    <div>
      <h1 class="h4 mb-1">{{ $user->username }}</h1>
      <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="badge bg-primary text-capitalize">{{ $user->role }}</span>
        @php $status = $user->status ?? 'active'; @endphp
        @if($status === 'disabled')
          <span class="badge bg-secondary">Disabled</span>
        @elseif($status === 'reset_required')
          <span class="badge bg-warning text-dark">Reset Required</span>
        @else
          <span class="badge bg-success">Active</span>
        @endif
      </div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
    <form method="post" action="{{ route('admin.users.status', $user->user_id) }}" class="d-inline">
      @csrf
      <input type="hidden" name="status" value="{{ ($status === 'disabled') ? 'active' : 'disabled' }}">
      <button type="submit" class="btn btn-outline-secondary btn-sm">{{ $status === 'disabled' ? 'Enable' : 'Disable' }}</button>
    </form>
    <form method="post" action="{{ route('admin.users.reset_password', $user->user_id) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-outline-secondary btn-sm">Reset Password</button>
    </form>
    <form method="post" action="{{ route('admin.users.force_reset', $user->user_id) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-outline-secondary btn-sm">Force Reset on Next Login</button>
    </form>
    <form method="post" action="{{ route('admin.users.resend_notification', $user->user_id) }}" class="d-inline">
      @csrf
      <button type="submit" class="btn btn-outline-secondary btn-sm">Resend Notification</button>
    </form>
    <form method="post" action="{{ route('admin.users.destroy', $user->user_id) }}" class="d-inline" onsubmit="return confirm('Deleting this user may remove important linked data. Prefer disabling the account instead. Continue?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger btn-sm">Delete</button>
    </form>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="card h-100">
      <div class="card-header bg-white border-0 pb-0">
        <h2 class="h6 mb-1">Account Information</h2>
      </div>
      <div class="card-body small">
        <dl class="row mb-0">
          <dt class="col-5">Username</dt>
          <dd class="col-7">{{ $user->username }}</dd>

          <dt class="col-5">Email</dt>
          <dd class="col-7">{{ $user->email }}</dd>

          <dt class="col-5">Role</dt>
          <dd class="col-7 text-capitalize">{{ $user->role }}</dd>

          <dt class="col-5">Created</dt>
          <dd class="col-7">{{ optional($user->created_at)->format('Y-m-d H:i') }}</dd>

          <dt class="col-5">Last Updated</dt>
          <dd class="col-7">{{ optional($user->updated_at)->format('Y-m-d H:i') }}</dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-8">
    @if($user->role === 'teacher')
      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Teacher Details</h2>
        </div>
        <div class="card-body small">
          <dl class="row mb-0">
            <dt class="col-4">First name</dt>
            <dd class="col-8">{{ $user->teacher_first_name ?? '—' }}</dd>
            <dt class="col-4">Last name</dt>
            <dd class="col-8">{{ $user->teacher_last_name ?? '—' }}</dd>
            <dt class="col-4">Home address</dt>
            <dd class="col-8">{{ $user->teacher_home_address ?? '—' }}</dd>
            <dt class="col-4">Phone number</dt>
            <dd class="col-8">{{ $user->teacher_phone_number ?? '—' }}</dd>
            <dt class="col-4">Hire date</dt>
            <dd class="col-8">{{ $user->teacher_hire_date ?? '—' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
          <h2 class="h6 mb-1">Assigned Students</h2>
          <span class="small text-muted">{{ $teacherStudents->count() }} students · {{ $teacherAssessmentsCount }} assessments</span>
        </div>
        <div class="card-body small">
          @if($teacherStudents->isEmpty())
            <p class="text-muted mb-0">No students are currently assigned to this teacher.</p>
          @else
            <ul class="list-group list-group-flush">
              @foreach($teacherStudents as $s)
                <li class="list-group-item px-0 d-flex justify-content-between">
                  <span>{{ $s->last_name }}, {{ $s->first_name }}</span>
                  <span class="text-muted">DOB: {{ $s->date_of_birth }}</span>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    @elseif($user->role === 'family')
      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Family Details</h2>
        </div>
        <div class="card-body small">
          <dl class="row mb-0">
            <dt class="col-4">Family name</dt>
            <dd class="col-8">{{ $user->family_name ?? '—' }}</dd>
            <dt class="col-4">Address</dt>
            <dd class="col-8">{{ $user->family_home_address ?? '—' }}</dd>
            <dt class="col-4">Emergency contact</dt>
            <dd class="col-8">{{ $user->family_emergency_contact ?? '—' }}</dd>
            <dt class="col-4">Emergency phone</dt>
            <dd class="col-8">{{ $user->family_emergency_phone ?? '—' }}</dd>
          </dl>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header bg-white border-0 pb-0 d-flex justify-content-between align-items-center">
          <h2 class="h6 mb-1">Linked Children</h2>
          <span class="small text-muted">{{ $familyChildren->count() }} children · {{ $familyCompletedTests }} completed tests</span>
        </div>
        <div class="card-body small">
          @if($familyChildren->isEmpty())
            <p class="text-muted mb-0">No students are currently linked to this family account.</p>
          @else
            @php
              $teachersByStudent = $familyChildrenTeachers->groupBy('student_id');
            @endphp
            <ul class="list-group list-group-flush">
              @foreach($familyChildren as $child)
                <li class="list-group-item px-0">
                  <div class="d-flex justify-content-between">
                    <span>{{ $child->last_name }}, {{ $child->first_name }}</span>
                    <span class="text-muted">DOB: {{ $child->date_of_birth }}</span>
                  </div>
                  @php $teachers = $teachersByStudent->get($child->student_id, collect()); @endphp
                  @if($teachers->isNotEmpty())
                    <div class="text-muted small mt-1">
                      Teachers:
                      {{ $teachers->map(fn($t) => trim(($t->first_name ?? '').' '.($t->last_name ?? '')) ?: $t->teacher_username)->implode(', ') }}
                    </div>
                  @endif
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </div>
    @else
      <div class="card">
        <div class="card-header bg-white border-0 pb-0">
          <h2 class="h6 mb-1">Admin Account</h2>
        </div>
        <div class="card-body small">
          <p class="text-muted mb-0">This is an administrator account used for system configuration and oversight.</p>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
