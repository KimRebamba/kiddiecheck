@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <a href="{{ route('teacher.sections') }}" class="btn btn-sm btn-secondary mb-2">← Back to Sections</a>
      <h3>{{ $section->name }}</h3>
      <p class="text-muted">Manage students in this section</p>
    </div>
  </div>

  @if($section->students->isEmpty())
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-muted mb-0">No students assigned to this section</p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Students in {{ $section->name }}</h5>
          </div>
          <div class="card-body">
            <table class="table table-sm table-hover">
              <thead>
                <tr class="table-light">
                  <th>Student Name</th>
                  <th>Age</th>
                  <th>Last Standard Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($section->students as $student)
                  <tr>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ $student->age }} years</td>
                    <td>
                      @if($student->last_standard_score)
                        {{ number_format($student->last_standard_score, 2) }}
                      @else
                        <span class="text-muted">No data</span>
                      @endif
                    </td>
                    <td>
                      <a href="#" class="btn btn-sm btn-info">View</a>
                      @if($student->eligible)
                        <form method="post" action="{{ route('teacher.tests.start', $student->id) }}" style="display: inline;">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success">Start Test</button>
                        </form>
                      @else
                        <button class="btn btn-sm btn-secondary" disabled>Not Eligible</button>
                      @endif
                      <a href="{{ route('teacher.reports.show', [$student->id, $student->assessmentPeriods->first()->id ?? 0]) }}" class="btn btn-sm btn-primary">View Report</a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection
