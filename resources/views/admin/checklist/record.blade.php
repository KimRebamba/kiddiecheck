@extends('admin.layout')

@section('content')
<h1>ECCD Checklist: Child's Record</h1>

<h2>Sociodemographic Profile</h2>
<p>Student: {{ $student->name }} | Gender: {{ $student->gender }} | DOB: {{ $student->dob }}</p>
<p>Family: {{ $student->family?->name }} | Section: {{ $student->section?->name }}</p>
<p>Handedness: {{ $student->handedness }} | Studying: {{ $student->is_studying ? 'Yes' : 'No' }} | School: {{ $student->school_name ?? 'N/A' }}</p>

@php
  $completed = $tests->filter(function($t){ return $t->status === 'completed'; });
@endphp

<h2>Assessments</h2>
@if($completed->isEmpty())
  <p class="text-muted">No completed assessments yet.</p>
@else
  <ul>
  @foreach($completed as $i => $test)
    @php $label = match($i){0=>'1st',1=>'2nd',2=>'3rd',default=>($i+1).'th'}; @endphp
    <li>
      {{ $label }} Assessment — Date: {{ $test->test_date }} | Age (months): {{ $test->age_months ?? 'n/a' }} | Examiner: {{ $test->examiner_name ?? $test->observer?->name ?? 'n/a' }}
    </li>
  @endforeach
  </ul>
@endif

@foreach($domains as $domain)
  <h2>{{ $domain->name }}</h2>
  <table border="1">
    <tr>
      <th>Item</th>
      <th>Material/Procedure</th>
@foreach($completed as $i => $test)
      @php $label = match($i){0=>'1st',1=>'2nd',2=>'3rd',default=>($i+1).'th'}; @endphp
      <th>{{ $label }} Eval</th>
@endforeach
      <th>Comments</th>
    </tr>
@foreach($domain->questions as $idx => $q)
    @php
      $rowResponses = $completed->map(function($t){ return $t->responses->keyBy('question_id'); });
    @endphp
    <tr>
      <td>{{ $idx+1 }}. {{ $q->question_text }}</td>
      <td>
        @if($q->materials) <div><strong>Materials:</strong> {{ $q->materials }}</div> @endif
        @if($q->procedure) <div><strong>Procedure:</strong> {{ $q->procedure }}</div> @endif
      </td>
@foreach($completed as $ti => $t)
      @php $resp = $rowResponses[$ti][$q->id] ?? null; @endphp
      <td>{{ ($resp && ($resp->score ?? 0) > 0) ? '✔' : '-' }}</td>
@endforeach
      <td>
        @foreach($completed as $ti => $t)
          @php $resp = $rowResponses[$ti][$q->id] ?? null; @endphp
          @if($resp && $resp->comment)
            @php $label = match($ti){0=>'1st',1=>'2nd',2=>'3rd',default=>($ti+1).'th'}; @endphp
            <div><em>{{ $label }}:</em> {{ $resp->comment }}</div>
          @endif
        @endforeach
      </td>
    </tr>
@endforeach
  </table>
@endforeach

<h2>Domain Scores</h2>
<table border="1">
  <tr>
    <th>Domain</th>
@foreach($completed as $i => $test)
    @php $label = match($i){0=>'1st',1=>'2nd',2=>'3rd',default=>($i+1).'th'}; @endphp
    <th>{{ $label }} Raw</th>
    <th>{{ $label }} Scaled</th>
    <th>{{ $label }} Interp</th>
@endforeach
  </tr>
@foreach($domains as $domain)
  <tr>
    <td>{{ $domain->name }}</td>
@foreach($completed as $i => $test)
    @php $sum = $summaries[$test->id] ?? null; $d = $sum['domains'][$domain->id] ?? null; @endphp
    <td>{{ $d['raw'] ?? '' }}</td>
    <td>
      @php
        $v = $d['scaled'] ?? null; $max = config('eccd.scaled_score_max', 19);
        $sv = $v !== null ? ($v > $max ? \App\Services\EccdScoring::percentageToScaled((float)$v) : (int)$v) : null;
      @endphp
      {{ $sv ?? '' }}
    </td>
    <td>
      @php $sc = $sv ? \App\Services\EccdScoring::classifyScaled((int)$sv) : null; @endphp
      {{ $sc ?? '' }}
    </td>
@endforeach
  </tr>
@endforeach
</table>

<h2>Score Summary</h2>
<table border="1">
  <tr>
    <th>Assessment</th>
    <th>Age (months)</th>
    <th>Sum of Scaled Scores</th>
    <th>Standard Score</th>
    <th>Interpretation</th>
  </tr>
@foreach($completed as $i => $test)
  @php $sum = $summaries[$test->id] ?? null; @endphp
  <tr>
    @php $label = match($i){0=>'1st',1=>'2nd',2=>'3rd',default=>($i+1).'th'}; @endphp
    <td>{{ $label }}</td>
    <td>{{ $test->age_months ?? '' }}</td>
    <td>{{ $sum['sumScaled'] ?? '' }}</td>
    <td>{{ $sum['standardScore'] ?? '' }}</td>
    <td>
      @php $ss = $sum['standardScore'] ?? null; $ssCat = $ss ? \App\Services\EccdScoring::classifyStandard((int)$ss) : null; @endphp
      {{ $ssCat ?? '' }}
    </td>
  </tr>
@endforeach
  @php $agg = \App\Services\EccdScoring::aggregate($completed, $domains); @endphp
  <tr>
    <td><strong>Aggregated</strong></td>
    <td>—</td>
    <td>{{ $agg['sumScaled'] ?? '' }}</td>
    <td>{{ $agg['standardScore'] ?? '' }}</td>
    <td>
      @php $as = $agg['standardScore'] ?? null; $asCat = $as ? \App\Services\EccdScoring::classifyStandard((int)$as) : null; @endphp
      {{ $asCat ?? '' }}
    </td>
  </tr>
</table>

@php
  $byRole = \App\Services\EccdScoring::aggregateByRole($completed, $domains);
  $disc = \App\Services\EccdScoring::analyzeDiscrepancies($byRole['teacher'], $byRole['family'], $domains);
@endphp

<h2>Teacher vs Family Discrepancies (All Assessments)</h2>
<p>
  Teacher Standard: {{ $disc['overall']['teacher'] ?? '—' }} |
  Family Standard: {{ $disc['overall']['family'] ?? '—' }} |
  Δ: {{ $disc['overall']['delta'] ?? '—' }}
  @if(data_get($disc,'overall.flag')) | ⚠️ Significant overall discrepancy @endif
</p>
<table border="1">
  <tr>
    <th>Domain</th>
    <th>Teacher Avg (1–19)</th>
    <th>Family Avg (1–19)</th>
    <th>Δ (Abs)</th>
    <th>Flag</th>
  </tr>
  @foreach($disc['domains'] as $row)
    <tr>
      <td>{{ $row['domain'] }}</td>
      <td>{{ $row['teacher'] !== null ? number_format($row['teacher'],2) : '—' }}</td>
      <td>{{ $row['family'] !== null ? number_format($row['family'],2) : '—' }}</td>
      <td>{{ $row['delta'] !== null ? number_format($row['delta'],2) : '—' }}</td>
      <td>
        @if($row['flag'])
          ⚠️ {{ $row['direction'] === 'teacher_lower' ? 'Teacher lower' : ($row['direction'] === 'teacher_higher' ? 'Teacher higher' : 'Discrepancy') }}
        @else
          —
        @endif
      </td>
    </tr>
  @endforeach
  @if(empty($disc['domains']))
    <tr><td colspan="5">No domain discrepancies computed.</td></tr>
  @endif
  </table>

@endsection
