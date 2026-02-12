@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <a href="{{ route('teacher.family') }}" class="btn btn-sm btn-secondary mb-2">← Back to Families</a>
      <h3>{{ $family->user->name }}</h3>
      <p class="text-muted">Family Information & Assigned Students</p>
    </div>
  </div>

  <!-- Family Info -->
  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Family Information</h5>
        </div>
        <div class="card-body">
          <p><strong>Family Name:</strong> {{ $family->user->name }}</p>
          <p><strong>Address:</strong> {{ $family->user->profile_path ?? 'N/A' }}</p>
          <p><strong>Emergency Contact:</strong> N/A</p>
          <p><strong>Emergency Phone:</strong> N/A</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Students Assigned -->
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Assigned Students</h5>
        </div>
        <div class="card-body">
          @if($family->students->isEmpty())
            <p class="text-muted mb-0">No students assigned to this family</p>
          @else
            <table class="table table-sm table-hover">
              <thead>
                <tr class="table-light">
                  <th>Student Name</th>
                  <th>Age</th>
                  <th>Family</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($family->students as $student)
                  <tr>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ $student->date_of_birth ? $student->date_of_birth->diffInYears(now()) : 'N/A' }} years</td>
                    <td>{{ $student->family->user->name ?? 'N/A' }}</td>
                    <td>
                      <a href="{{ route('teacher.reports.show', [$student->id, $student->assessmentPeriods->first()->id ?? 0]) }}" class="btn btn-sm btn-primary">View Reports</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
