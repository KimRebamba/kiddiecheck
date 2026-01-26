@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Teacher Test Result - {{ $test->student->name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back to Dashboard</a>
  </div>
  </div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-6 col-md-3">
        <div class="text-muted">Test Date</div>
        <div class="fw-semibold">{{ $test->test_date }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Status</div>
        <span class="badge bg-{{ $test->status === 'completed' ? 'success' : ($test->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ ucfirst(str_replace('_',' ', $test->status)) }}</span>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Sum Scaled</div>
        <div class="fw-semibold">{{ number_format($sumScaled,2) }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Standard Score</div>
        <div class="fw-semibold">{{ $standardScore ?? '—' }}</div>
      </div>
    </div>
    @if($standardScore)
      @php $ssCat = \App\Services\EccdScoring::classifyStandard((int)$standardScore); @endphp
      <div class="mt-2 text-muted">Interpretation: {{ $ssCat ?? '—' }}</div>
    @endif
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">Domain Scores</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Domain</th>
            <th>Raw</th>
            <th>Scaled (1–19)</th>
            <th>Interpretation</th>
            <th>Based (count)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($domains as $d)
            @php $s = $test->scores->firstWhere('domain_id', $d->id); @endphp
            <tr>
              <td>{{ $d->name }}</td>
              <td>{{ optional($s)->raw_score ?? '—' }}</td>
              <td>
                @php $v = optional($s)->scaled_score; $max = config('eccd.scaled_score_max', 19); @endphp
                {{ $v !== null ? ($v > $max ? \App\Services\EccdScoring::percentageToScaled((float)$v) : $v) : '—' }}
              </td>
              <td>
                @php
                  $sv = $v !== null ? ($v > $max ? \App\Services\EccdScoring::percentageToScaled((float)$v) : (int)$v) : null;
                  $sc = $sv ? \App\Services\EccdScoring::classifyScaled((int)$sv) : null;
                @endphp
                {{ $sc ?? '—' }}
              </td>
              <td>{{ optional($s)->scaled_score_based ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">Six-Month Window Summary</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="text-muted">Window</div>
        <div class="fw-semibold">{{ $windowStart->toDateString() }} → {{ $windowEnd->toDateString() }}</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="text-muted">Combined Sum Scaled</div>
        <div class="fw-semibold">{{ number_format($aggregates['combined']['sumScaled'] ?? 0, 2) }}</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="text-muted">Combined Standard</div>
        <div class="fw-semibold">{{ $aggregates['combined']['standardScore'] ?? '—' }}</div>
      </div>
    </div>
    @php $combStd = $aggregates['combined']['standardScore'] ?? null; $combCat = $combStd ? \App\Services\EccdScoring::classifyStandard((int)$combStd) : null; @endphp
    @if($combCat)
      <div class="mt-2 text-muted">Interpretation: {{ $combCat }}</div>
    @endif
  </div>
</div>

<div class="card mb-3">
  <div class="card-header bg-light">Discrepancy Analysis</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Domain</th>
            <th>Teacher Avg (1–19)</th>
            <th>Family Avg (1–19)</th>
            <th>Δ (Abs)</th>
            <th>Flag</th>
          </tr>
        </thead>
        <tbody>
          @foreach($discrepancies['domains'] as $did => $row)
            <tr>
              <td>{{ $row['domain'] }}</td>
              <td>{{ $row['teacher'] !== null ? number_format($row['teacher'],2) : '—' }}</td>
              <td>{{ $row['family'] !== null ? number_format($row['family'],2) : '—' }}</td>
              <td>{{ $row['delta'] !== null ? number_format($row['delta'],2) : '—' }}</td>
              <td>
                @if($row['flag'])
                  <span class="text-warning">⚠️ {{ $row['direction'] === 'teacher_lower' ? 'Teacher lower' : ($row['direction'] === 'teacher_higher' ? 'Teacher higher' : 'Discrepancy') }}</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="text-muted">Overall Teacher Standard</div>
        <div class="fw-semibold">{{ $discrepancies['overall']['teacher'] ?? '—' }}</div>
      </div>
      <div class="col-12 col-md-4">
        <div class="text-muted">Overall Family Standard</div>
        <div class="fw-semibold">{{ $discrepancies['overall']['family'] ?? '—' }}</div>
      </div>
      <div class="col-12 col-md-4">
        <div class="text-muted">Δ</div>
        <div class="fw-semibold">{{ $discrepancies['overall']['delta'] ?? '—' }}</div>
      </div>
    </div>
    @if(data_get($discrepancies,'overall.flag'))
      <div class="mt-2 text-warning">⚠️ Significant overall discrepancy</div>
    @endif
  </div>
</div>

<div class="card">
  <div class="card-header bg-light">Actions</div>
  <div class="card-body d-flex flex-wrap gap-2">
    <form method="POST" action="{{ route('teacher.tests.finalize', $test->id) }}">
      @csrf
      <button type="submit" class="btn btn-success">Finalize (Mark Completed)</button>
    </form>
    <form method="POST" action="{{ route('teacher.tests.incomplete', $test->id) }}">
      @csrf
      <button type="submit" class="btn btn-outline-secondary">Mark Incomplete</button>
    </form>
    <form method="POST" action="{{ route('teacher.tests.cancel', $test->id) }}">
      @csrf
      <button type="submit" class="btn btn-outline-danger">Cancel Test</button>
    </form>
    <form method="POST" action="{{ route('teacher.tests.terminate', $test->id) }}">
      @csrf
      <button type="submit" class="btn btn-outline-warning">Terminate Test</button>
    </form>
  </div>
</div>
@endsection
