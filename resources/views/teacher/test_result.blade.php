@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Assessment Results - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row g-3">
  <!-- Test Info -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <p class="text-muted mb-1">Test Date</p>
            <p class="fw-semibold">{{ $test->test_date->format('M d, Y') }}</p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Period</p>
            <p class="fw-semibold">{{ optional($test->assessmentPeriod)->description ?? 'N/A' }}</p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Status</p>
            <p>
              <span class="badge bg-{{ 
                $test->status === 'finalized' ? 'success' : 
                ($test->status === 'completed' ? 'info' : 'warning')
              }}">
                {{ ucfirst($test->status) }}
              </span>
            </p>
          </div>
          <div class="col-md-3">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="fw-semibold">{{ $standardScore ?? 'Not calculated' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Domain Scores -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Domain Scores</h5>
      </div>
      <div class="card-body p-0">
        @if($test->domainScores->isEmpty())
          <p class="p-3 text-muted">No domain scores yet.</p>
        @else
          <div class="table-responsive">
            <table class="table table-sm">
              <thead class="table-light">
                <tr>
                  <th>Domain</th>
                  <th>Raw Score</th>
                  <th>Scaled Score</th>
                  <th>Progress</th>
                </tr>
              </thead>
              <tbody>
                @foreach($test->domainScores as $score)
                  <tr>
                    <td><strong>{{ $score->domain->name ?? 'Unknown' }}</strong></td>
                    <td>{{ $score->raw_score ?? 'N/A' }}</td>
                    <td>{{ $score->scaled_score ?? 'N/A' }}</td>
                    <td>
                      @if($score->scaled_score)
                        <div class="progress" style="height: 20px;">
                          @php
                            $percentage = min(100, ($score->scaled_score / 19) * 100);
                          @endphp
                          <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $score->scaled_score }}
                          </div>
                        </div>
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

  <!-- Summary -->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Assessment Summary</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <p class="text-muted mb-1">Sum of Scaled Scores</p>
            <p class="display-6">{{ $sumScaled }}</p>
          </div>
          <div class="col-md-6">
            <p class="text-muted mb-1">Standard Score</p>
            <p class="display-6">{{ $standardScore ?? 'N/A' }}</p>
          </div>
        </div>
        @if($interpretation)
          <p class="mt-3">
            <strong>Interpretation:</strong>
            <span class="badge bg-info">{{ $interpretation }}</span>
          </p>
        @endif
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="col-md-12">
    <div class="d-flex gap-2">
      @if($test->status === 'completed')
        <form action="{{ route('teacher.tests.finalize', $test->test_id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-success">Finalize Test</button>
        </form>
        <form action="{{ route('teacher.tests.cancel', $test->test_id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-outline-danger">Cancel Test</button>
        </form>
      @elseif($test->status === 'finalized')
        <span class="badge bg-success" style="padding: 0.5rem 1rem;">Test Finalized</span>
      @endif

      <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
@endsection
