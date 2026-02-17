@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Sections</h1>
</div>

@if($sections->isEmpty())
  <div class="alert alert-info" role="alert">
    No sections with assigned students.
  </div>
@else
  <div class="row g-3">
    @foreach($sections as $section)
      <div class="col-md-6 col-lg-4">
        <div class="card" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#sectionModal{{ $section->section_id }}">
          <div class="card-body">
            <h5 class="card-title">{{ $section->name }}</h5>
            <p class="card-text text-muted">
              <strong>Students:</strong> {{ $section->students->count() }}
            </p>
            @if($section->description)
              <p class="card-text" style="font-size: 0.85rem;">{{ Str::limit($section->description, 60) }}</p>
            @endif
            <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();" data-bs-toggle="modal" data-bs-target="#sectionModal{{ $section->section_id }}">
              View Students
            </button>
          </div>
        </div>
      </div>

      <!-- Section Modal with Students Table -->
      <div class="modal fade" id="sectionModal{{ $section->section_id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ $section->name }}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              @if($section->description)
                <p class="text-muted mb-3">{{ $section->description }}</p>
              @endif

              @if($section->students->isEmpty())
                <p class="text-muted">No students in this section assigned to you.</p>
              @else
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Last Score</th>
                        <th>Eligible</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($section->students as $student)
                        <tr>
                          <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                          <td>{{ $student->age ?? 'N/A' }}</td>
                          <td>{{ $student->last_standard_score ?? 'No score' }}</td>
                          <td>
                            @if($student->eligible)
                              <span class="badge bg-success">Yes</span>
                            @else
                              <span class="badge bg-secondary">No</span>
                            @endif
                          </td>
                          <td style="white-space: nowrap;">
                            <div class="btn-group btn-group-sm" role="group">
                              <a href="{{ route('teacher.student', $student->student_id) }}" class="btn btn-outline-secondary" title="View Student">View</a>
                              
                              @if($student->eligible)
                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#startTestModal{{ $student->student_id }}" title="Start Test">Test</button>
                              @endif

                              <a href="{{ route('teacher.reports') }}" class="btn btn-outline-info" title="View Reports">Report</a>
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Start Test Modals for each student -->
      @foreach($section->students as $student)
        @if($student->eligible)
          <div class="modal fade" id="startTestModal{{ $student->student_id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Start Assessment</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
                  
                  @php
                    $eligiblePeriods = $student->assessmentPeriods()
                      ->where('status', '!=', 'completed')
                      ->where('end_date', '>=', now())
                      ->get();
                  @endphp

                  @if($eligiblePeriods->isEmpty())
                    <p class="text-warning">No active assessment periods found for this student.</p>
                  @else
                    <label class="form-label">Select Assessment Period:</label>
                    <form action="{{ route('teacher.tests.start', $student->student_id) }}" method="POST">
                      @csrf
                      <select name="period_id" class="form-select mb-3" required>
                        <option value="">-- Select Period --</option>
                        @foreach($eligiblePeriods as $period)
                          <option value="{{ $period->period_id }}">
                            {{ $period->description }} ({{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }})
                          </option>
                        @endforeach
                      </select>
                      <button type="submit" class="btn btn-primary w-100">Start Assessment</button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
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
  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
</style>
@endsection
