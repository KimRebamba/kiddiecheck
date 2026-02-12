@extends('teacher.layout')

@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header" style="background-color: #e77a74; color: white;">
          <h4 class="mb-0">Profile Settings</h4>
        </div>
        <div class="card-body p-4">
          
          <!-- Profile Information -->
          <div class="mb-5">
            <h5 class="mb-3" style="color: #e77a74;">Personal Information</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">First Name</label>
                  <p class="form-control-plaintext fw-bold">{{ $teacher->teacher?->first_name ?? 'N/A' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">Last Name</label>
                  <p class="form-control-plaintext fw-bold">{{ $teacher->teacher?->last_name ?? 'N/A' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">Email</label>
                  <p class="form-control-plaintext">{{ $teacher->email }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">Phone</label>
                  <p class="form-control-plaintext">{{ $teacher->teacher?->phone_number ?? 'N/A' }}</p>
                </div>
              </div>
              <div class="col-12">
                <div class="form-group">
                  <label class="form-label text-muted small">Home Address</label>
                  <p class="form-control-plaintext">{{ $teacher->teacher?->home_address ?? 'N/A' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">Hire Date</label>
                  <p class="form-control-plaintext">{{ $teacher->teacher?->hire_date ? \Carbon\Carbon::parse($teacher->teacher->hire_date)->format('M d, Y') : 'N/A' }}</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="form-label text-muted small">Status</label>
                  <p class="form-control-plaintext">
                    <span class="badge bg-success">Active</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Settings -->
          <div class="mb-5">
            <h5 class="mb-3" style="color: #e77a74;">Account Settings</h5>
            <div class="row g-3">
              <div class="col-12">
                <div class="form-group">
                  <label class="form-label text-muted small">Username</label>
                  <p class="form-control-plaintext">{{ $teacher->username }}</p>
                </div>
              </div>
              <div class="col-12">
                <div class="alert alert-info small mb-0">
                  <i class="bi bi-info-circle"></i> Contact your administrator to update profile information.
                </div>
              </div>
            </div>
          </div>

          <!-- Security -->
          <div class="mb-5">
            <h5 class="mb-3" style="color: #e77a74;">Security</h5>
            <div class="row g-3">
              <div class="col-12">
                <a href="#changePassword" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse">
                  Change Password
                </a>
                <div id="changePassword" class="collapse mt-3">
                  <div class="alert alert-warning small">
                    Password change functionality will be available soon.
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Danger Zone -->
          <div>
            <h5 class="mb-3" style="color: #dc3545;">Danger Zone</h5>
            <div class="d-flex gap-2">
              <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </button>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .form-control-plaintext {
    border: none;
    padding: 0;
    color: #333;
  }
</style>
@endsection
