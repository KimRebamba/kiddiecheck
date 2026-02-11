@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Test Result – {{ $test->student->name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ url()->previous() }}">Back</a>
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
        <div class="text-muted">Observer</div>
        <div class="fw-semibold">{{ optional($test->observer)->name }} ({{ optional($test->observer)->role }})</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Status</div>
        <span class="badge bg-{{ in_array($test->status,['finalized','completed']) ? 'success' : ($test->status==='in_progress' ? 'warning' : ($test->status==='archived' ? 'secondary' : ($test->status==='terminated' ? 'danger' : 'secondary'))) }}">{{ ucfirst(str_replace('_',' ', $test->status)) }}</span>
        @if($test->termination_reason)
          <div class="small text-muted">{{ $test->termination_reason }}</div>
        @endif
      </div>
      <div class="col-6 col-md-3">
        <div class="text-muted">Standard Score</div>
        <div class="fw-semibold">{{ $standardScore ?? '—' }}</div>
        @if($standardScore)
          <div class="small text-muted">{{ \App\Services\EccdScoring::classifyStandard((int)$standardScore) }}</div>
        @endif
      </div>
    </div>
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
  <div class="card-header bg-light">Window Aggregates (Period)</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="text-muted">Combined Sum Scaled</div>
        <div class="fw-semibold">{{ number_format($aggregates['combined']['sumScaled'] ?? 0, 2) }}</div>
      </div>
      <div class="col-12 col-md-4">
        <div class="text-muted">Combined Standard</div>
        <div class="fw-semibold">{{ $aggregates['combined']['standardScore'] ?? '—' }}</div>
      </div>
      <div class="col-12 col-md-4">
        <div class="text-muted">Flags</div>
        <div>
          @if($familyOnly)
            <span class="badge bg-info">Family-only</span>
          @endif
          @if($allNA)
            <span class="badge bg-warning">All N/A</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header bg-light">Discrepancy Analysis</div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Domain</th>
            <th>Teacher Avg</th>
            <th>Family Avg</th>
            <th>Δ</th>
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
                  <span class="text-warning">⚠️</span>
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
@endsection
