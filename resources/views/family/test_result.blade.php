@extends('family.layout')

@section('content')
  <div class="row mb-3">
    <div class="col">
      <h2 class="mb-1">Results for {{ $test->student->name }}</h2>
      <div class="text-muted">Date: {{ $test->test_date }} • {{ in_array($test->status,['finalized','completed']) ? 'Completed' : ucfirst($test->status) }}</div>
      <div class="mt-2">Observer: {{ optional($test->observer)->name }} ({{ optional($test->observer)->role }})</div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col">
      @php $ssCat = $standardScore ? \App\Services\EccdScoring::classifyStandard((int)$standardScore) : null; @endphp
      <div class="card border-primary">
        <div class="card-body">
          <h5 class="card-title">Overall Summary</h5>
          <p class="mb-1">Standard Score: <strong>{{ $standardScore ?? '—' }}</strong></p>
          <p class="mb-0">Interpretation: <strong>{{ $ssCat ?? 'Not available' }}</strong></p>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    @foreach($domains as $d)
      @php
        $s = $test->scores->firstWhere('domain_id', $d->id);
        $v = optional($s)->scaled_score;
        $max = config('eccd.scaled_score_max', 19);
        $sv = $v !== null ? ($v > $max ? \App\Services\EccdScoring::percentageToScaled((float)$v) : (int)$v) : null;
        $sc = $sv ? \App\Services\EccdScoring::classifyScaled((int)$sv) : null;
      @endphp
      <div class="col-12 col-md-6">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ $d->name }}</h5>
            <p class="mb-1">Scaled Score: <strong>{{ $sv ?? '—' }}</strong></p>
            <p class="mb-0">Interpretation: <strong>{{ $sc ?? 'Not available' }}</strong></p>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="row mb-4">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Six-Month Window</h5>
          <p class="text-muted mb-2">{{ $windowStart->toDateString() }} to {{ $windowEnd->toDateString() }}</p>
          @php $combStd = $aggregates['combined']['standardScore'] ?? null; $combCat = $combStd ? \App\Services\EccdScoring::classifyStandard((int)$combStd) : null; @endphp
          <p class="mb-1">Combined Standard Score: <strong>{{ $combStd ?? '—' }}</strong></p>
          <p class="mb-0">Interpretation: <strong>{{ $combCat ?? 'Not available' }}</strong></p>
          @if($familyOnly)
            <div class="alert alert-info mt-2" role="alert">
              Teacher assessments not submitted for this period.
            </div>
          @endif
          @if($allNA)
            <div class="alert alert-warning mt-2" role="alert">
              Completed – All N/A. Interpretation is limited due to no applicable items.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Family vs Teacher (Awareness)</h5>
          <p class="mb-3">These comparisons use completed tests only and highlight where family and teacher observations differ.</p>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Domain</th>
                  <th>Teacher Avg</th>
                  <th>Family Avg</th>
                  <th>Difference</th>
                </tr>
              </thead>
              <tbody>
                @foreach($discrepancies['domains'] as $did => $row)
                  <tr>
                    <td>{{ $row['domain'] }}</td>
                    <td>{{ $row['teacher'] !== null ? number_format($row['teacher'],2) : '—' }}</td>
                    <td>{{ $row['family'] !== null ? number_format($row['family'],2) : '—' }}</td>
                    <td>{{ $row['delta'] !== null ? number_format($row['delta'],2) : '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if(data_get($discrepancies,'overall.flag'))
            <div class="alert alert-warning mt-2" role="alert">
              Notable overall difference between family and teacher results.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <a class="btn btn-outline-secondary" href="{{ route('family.index') }}">Back to Dashboard</a>
    </div>
  </div>
@endsection
