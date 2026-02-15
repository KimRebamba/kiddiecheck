@extends('admin.layout')

@section('content')
  <div class="row mb-3">
    <div class="col-12">
      <h1 class="h4 mb-1">Admin Profile</h1>
      <p class="text-muted mb-0">Manage your personal account details and security.</p>
    </div>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Please fix the following issues:</div>
      <ul class="mb-0 small">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">
    <div class="col-lg-4">
      {{-- Profile picture and basic account info --}}
      <div class="card mb-3">
        <div class="card-body text-center">
          @if($user->profile_path)
            <img src="{{ asset($user->profile_path) }}" alt="Profile" class="rounded-circle mb-3" style="width:96px;height:96px;object-fit:cover;">
          @else
            <div class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width:96px;height:96px;font-size:2rem;">
              {{ strtoupper(substr($user->username ?? 'A', 0, 1)) }}
            </div>
          @endif

          <h2 class="h6 mb-1">{{ $user->username }}</h2>
          <p class="text-muted small mb-2">Administrator</p>
          <p class="text-muted small mb-3">{{ $user->email }}</p>

          <a href="#profile-edit" class="btn btn-outline-primary btn-sm">Change profile picture</a>
        </div>
      </div>

      {{-- System role information (read-only) --}}
      <div class="card">
        <div class="card-header py-2">
          <span class="fw-semibold small">System Role</span>
        </div>
        <div class="card-body small">
          <p class="mb-1"><span class="fw-semibold">Role:</span> Administrator</p>
          <p class="text-muted mb-0">
            Administrators manage users, assessments, and system configuration.
          </p>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      {{-- Account information (read-only) --}}
      <div class="card mb-3">
        <div class="card-header py-2">
          <span class="fw-semibold small">Account Information</span>
        </div>
        <div class="card-body small">
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Username</div>
            <div class="col-sm-8">{{ $user->username }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Email address</div>
            <div class="col-sm-8">{{ $user->email }}</div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Role</div>
            <div class="col-sm-8">Administrator</div>
          </div>
          <div class="row mb-2">
            <div class="col-sm-4 text-muted">Account created</div>
            <div class="col-sm-8">{{ optional($user->created_at)->format('M d, Y H:i') }}</div>
          </div>
          <div class="row mb-0">
            <div class="col-sm-4 text-muted">Last updated</div>
            <div class="col-sm-8">{{ optional($user->updated_at)->format('M d, Y H:i') }}</div>
          </div>
        </div>
      </div>

      {{-- Editable profile details --}}
      <div class="card mb-3" id="profile-edit">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
          <span class="fw-semibold small">Editable Profile Details</span>
        </div>
        <div class="card-body">
          <form method="post" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="small">
            @csrf
            <div class="mb-3">
              <label class="form-label">Email address</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control form-control-sm" required>
              <div class="form-text">Your login username. Changing this will not log you out.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Profile picture</label>
              <input type="file" name="profile_image" accept="image/*" class="form-control form-control-sm">
              <div class="form-text">Optional. JPG or PNG, up to 2 MB.</div>
            </div>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Security section --}}
      <div class="card mb-3">
        <div class="card-header py-2 d-flex justify-content-between align-items-center">
          <span class="fw-semibold small">Security</span>
        </div>
        <div class="card-body">
          <form method="post" action="{{ route('admin.profile.password') }}" class="small">
            @csrf
            <div class="mb-3">
              <label class="form-label">Current password</label>
              <input type="password" name="current_password" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">New password</label>
              <input type="password" name="password" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm new password</label>
              <input type="password" name="password_confirmation" class="form-control form-control-sm" required>
            </div>
            <p class="text-muted small mb-3">For security reasons, your password is never displayed.</p>
            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-outline-primary btn-sm">Change password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
