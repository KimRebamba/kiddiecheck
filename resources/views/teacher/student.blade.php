@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">{{ $student->first_name }} {{ $student->last_name }}</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.sections') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Student Information -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Student Information</h5>
      </div>
      <div class="card-body">
        <p class="mb-2"><strong>Name:</strong> {{ $student->first_name }} {{ $student->last_name }}</p>
        @php
          $dob = is_string($student->date_of_birth) ? \Carbon\Carbon::parse($student->date_of_birth) : $student->date_of_birth;
        @endphp
        <p class="mb-2"><strong>Date of Birth:</strong> {{ $dob ? $dob->format('M d, Y') : 'N/A' }}</p>
        <p class="mb-2"><strong>Age:</strong> {{ $student->age ?? 'N/A' }} years</p>
        <p class="mb-2"><strong>Section:</strong> {{ optional($student->section)->name ?? 'N/A' }}</p>
        <p class="mb-2"><strong>Family:</strong> {{ optional($student->family)->family_name ?? 'N/A' }}</p>
      </div>
    </div>
  </div>

  <!-- Test Status -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Status</h5>
      </div>
      <div class="card-body">
        <p class="mb-2">
          <strong>Eligible for Test:</strong><br>
          @if($student->eligible)
            <span class="badge bg-success">Yes</span>
          @else
            <span class="badge bg-secondary">No</span>
          @endif
        </p>
        <p class="mb-2">
          <strong>Last Standard Score:</strong><br>
          {{ $student->last_standard_score ?? 'No score' }}
        </p>
      </div>
    </div>
  </div>

  <!-- Assessment Periods -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Periods</h5>
      </div>
      <div class="card-body">
        @if($student->assessmentPeriods->isEmpty())
          <p class="text-muted">No assessment periods.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Dates</th>
                  <th>Status</th>
                  <th>Tests</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($student->assessmentPeriods as $period)
                  @php
                    $tests = $period->tests()->where('examiner_id', auth()->id())->get();
                  @endphp
                  <tr>
                    <td><strong>{{ $period->description }}</strong></td>
                    <td>{{ $period->start_date->format('M d') }} - {{ $period->end_date->format('M d, Y') }}</td>
                    <td>
                      <span class="badge bg-{{ $period->status === 'completed' ? 'success' : ($period->status === 'overdue' ? 'danger' : 'info') }}">
                        {{ ucfirst($period->status) }}
                      </span>
                    </td>
                    <td>{{ $tests->count() }}</td>
                    <td>
                      @if($student->eligible && $period->status !== 'completed' && $period->status !== 'overdue')
                        <form action="{{ route('teacher.tests.start', $student->student_id) }}" method="POST" style="display: inline;">
                          @csrf
                          <input type="hidden" name="period_id" value="{{ $period->period_id }}">
                          <button type="submit" class="btn btn-sm btn-outline-primary">Start Test</button>
                        </form>
                      @else
                        <span class="text-muted small">
                          @if($period->status === 'overdue')
                            Period overdue
                          @elseif($period->status === 'completed')
                            Period completed
                          @else
                            Not eligible
                          @endif
                        </span>
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

  <!-- Previous Tests -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Test History</h5>
      </div>
      <div class="card-body">
        @php
          $teacherTests = $student->tests()
            ->where('examiner_id', auth()->id())
            ->orderBy('test_date', 'desc')
            ->get();
        @endphp
        
        @if($teacherTests->isEmpty())
          <p class="text-muted">No tests yet.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Period</th>
                  <th>Status</th>
                  <th>Score</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($teacherTests as $test)
                  @php
                    $standardScore = $test->standardScore;
                  @endphp
                  <tr>
                    <td>{{ $test->test_date->format('M d, Y') }}</td>
                    <td>{{ optional($test->assessmentPeriod)->description ?? 'N/A' }}</td>
                    <td>
                      <span class="badge bg-{{ 
                        $test->status === 'finalized' ? 'success' : 
                        ($test->status === 'completed' ? 'info' : 
                        ($test->status === 'canceled' ? 'danger' : 'warning'))
                      }}">
                        {{ ucfirst($test->status) }}
                      </span>
                    </td>
                    <td>{{ $standardScore ? $standardScore->standard_score : 'N/A' }}</td>
                    <td>
                      <a href="{{ route('teacher.reports.detail', [$student->student_id, $test->period_id, $test->test_id]) }}" class="btn btn-xs btn-outline-secondary" style="font-size: 0.8rem;">View</a>
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
  .btn-xs {
    padding: 0.25rem 0.5rem;
  }
</style>
@endsection
