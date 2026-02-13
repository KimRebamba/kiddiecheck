@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Families</h1>
</div>

@if($families->isEmpty())
  <div class="alert alert-info" role="alert">
    No families found with assigned students.
  </div>
@else
  <div class="row g-3">
    @foreach($families as $family)
      <div class="col-md-6 col-lg-4">
        <div class="card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#familyModal{{ $family->user_id }}">
          <div class="card-body">
            <h5 class="card-title">{{ $family->family_name }}</h5>
            <p class="card-text text-muted mb-2">
              <strong>Students:</strong> {{ $family->students->count() }}
            </p>
            <p class="card-text" style="font-size: 0.9rem;">
              <strong>Address:</strong> {{ $family->home_address }}<br>
              <strong>Emergency:</strong> {{ $family->emergency_contact }}<br>
              <strong>Phone:</strong> {{ $family->emergency_phone }}
            </p>
            <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#familyModal{{ $family->user_id }}">
              View Details
            </button>
          </div>
        </div>
      </div>

      <!-- Family Modal -->
      <div class="modal fade" id="familyModal{{ $family->user_id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ $family->family_name }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <!-- Family Info -->
              <div class="mb-4">
                <h6 class="mb-3">Family Information</h6>
                <div class="row g-2" style="font-size: 0.95rem;">
                  <div class="col-md-6">
                    <p><strong>Family Name:</strong><br>{{ $family->family_name }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Address:</strong><br>{{ $family->home_address }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Emergency Contact:</strong><br>{{ $family->emergency_contact }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Emergency Phone:</strong><br>{{ $family->emergency_phone }}</p>
                  </div>
                </div>
              </div>

              <hr>

              <!-- Assigned Students -->
              <div>
                <h6 class="mb-3">Assigned Students</h6>
                @if($family->students->isEmpty())
                  <p class="text-muted">No students assigned.</p>
                @else
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Age</th>
                          <th>Section</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($family->students as $student)
                          @php
                            $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
                            $age = $dob ? (int)$dob->diffInYears(now()) : 'N/A';
                          @endphp
                          <tr>
                            <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td>{{ $age }}</td>
                            <td>{{ optional($student->section)->name ?? 'N/A' }}</td>
                            <td>
                              <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-xs btn-outline-secondary" style="font-size: 0.8rem;">View</a>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <a href="{{ route('teacher.reports') }}" class="btn btn-primary">View Reports</a>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

<style>
  .modal-content {
    border-radius: 12px;
  }
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s;
  }
  .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }
  .btn-xs {
    padding: 0.25rem 0.5rem;
  }
</style>
@endsection
