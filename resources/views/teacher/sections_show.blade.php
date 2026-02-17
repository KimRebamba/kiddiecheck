@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">{{ $section->name }}</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Back</a>
    <a href="{{ route('teacher.sections.edit', $section->section_id) }}" class="btn btn-outline-primary">Edit Section</a>
  </div>
</div>

<div class="row g-3">
  <!-- Section Information -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Section Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> {{ $section->name }}</p>
        <p class="mb-2"><strong>Total Students:</strong> {{ $students->count() }}</p>
      </div>
    </div>
  </div>

  <!-- Students -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assigned Students</h5>
      </div>
      <div class="card-body">
        @if($students->isEmpty())
          <p class="text-muted">No students assigned to this section.</p>
        @else
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Eligible for Test</th>
                  <th>Last Standard Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $student)
                  <tr>
                    <td>
                      <a href="{{ route('teacher.student', $student->student_id) }}" class="text-decoration-none">
                        {{ $student->first_name }} {{ $student->last_name }}
                      </a>
                    </td>
                    <td>{{ $student->age ?? 'N/A' }} years</td>
                    <td>
                      @if($student->eligible)
                        <span class="badge bg-success">Yes</span>
                      @else
                        <span class="badge bg-secondary">No</span>
                      @endif
                    </td>
                    <td>{{ $student->last_standard_score ?? 'No score' }}</td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-outline-secondary">View</a>
                        
                        @if($student->eligible)
                          @php
                            $availablePeriod = DB::table('assessment_periods')
                                ->where('student_id', $student->student_id)
                                ->where('status', '!=', 'overdue')
                                ->where('status', '!=', 'completed')
                                ->first();
                          @endphp
                          @if($availablePeriod)
                            <form action="{{ route('teacher.tests.start', $student->student_id) }}" method="POST" style="display: inline;">
                              @csrf
                              <input type="hidden" name="period_id" value="{{ $availablePeriod->period_id }}">
                              <button type="submit" class="btn btn-outline-primary">Start Test</button>
                            </form>
                          @endif
                        @endif
                        
                        <!-- Delete Section Button -->
                        @if($section->student_count == 0)
                          <form action="{{ route('teacher.sections.destroy', $section->section_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">Delete Section</button>
                          </form>
                        @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
</style>
@endsection
