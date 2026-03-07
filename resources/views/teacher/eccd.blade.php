@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0 fw-bold">ECCD Overview</h1>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($students->isEmpty())
  <div class="card">
    <div class="card-body">
      <p class="mb-0 text-muted">No assigned students with finalized ECCD assessments yet.</p>
    </div>
  </div>
@else
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Student ECCD Summary</h5>
      @php
        $user = $teacher;
        $teacherProfile = $user->teacher ?? null;
        $displayName = $teacherProfile && ($teacherProfile->first_name || $teacherProfile->last_name)
          ? trim(($teacherProfile->first_name ?? '').' '.($teacherProfile->last_name ?? ''))
          : ($user->username ?? $user->email ?? '');
      @endphp
      <span class="text-muted small">Teacher: {{ $displayName }}</span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Student</th>
              <th>Section</th>
              <th>Latest Period</th>
              <th>Latest Test Date</th>
              <th>Standard Score</th>
              <th>Interpretation</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $student)
              @php
                $latestTest = $student->tests->sortByDesc('test_date')->first();
                $standardScore = $latestTest?->standardScore;
                $latestPeriod = $latestTest?->assessmentPeriod;
              @endphp
              <tr>
                <td>
                  <strong>{{ $student->first_name }} {{ $student->last_name }}</strong><br>
                  <small class="text-muted">ID: {{ $student->student_id }}</small>
                </td>
                <td>
                  @if($student->section)
                    {{ $student->section->name }}
                  @else
                    <span class="text-muted">Unassigned</span>
                  @endif
                </td>
                <td>
                  @if($latestPeriod)
                    {{ $latestPeriod->description }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($latestTest)
                    {{ $latestTest->test_date->format('M d, Y') }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($standardScore)
                    <strong>{{ $standardScore->standard_score }}</strong>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($standardScore)
                    {{ $standardScore->interpretation }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($latestPeriod && $latestTest)
                    <a href="{{ route('teacher.reports.detail', [$student->student_id, $latestPeriod->period_id, $latestTest->test_id]) }}" class="btn btn-sm btn-outline-primary">
                      View Details
                    </a>
                  @else
                    <span class="text-muted small">No finalized test</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @foreach($students as $student)
    @php
      $tests = $student->tests->sortBy('test_date');
    @endphp
    @if($tests->isNotEmpty())
      <div class="card mb-4">
        <div class="card-header">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">{{ $student->first_name }} {{ $student->last_name }}</h5>
              <small class="text-muted">Longitudinal ECCD Scores</small>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
              <thead>
                <tr>
                  <th>Period</th>
                  <th>Test Date</th>
                  <th>Standard Score</th>
                  <th>Interpretation</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tests as $test)
                  @php $score = $test->standardScore; @endphp
                  <tr>
                    <td>{{ $test->assessmentPeriod?->description ?? 'N/A' }}</td>
                    <td>{{ $test->test_date?->format('M d, Y') ?? 'N/A' }}</td>
                    <td>
                      @if($score)
                        <strong>{{ $score->standard_score }}</strong>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      @if($score)
                        {{ $score->interpretation }}
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  @endforeach
@endif
@endsection
