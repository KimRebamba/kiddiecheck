@extends('admin.layout')

@section('content')
<h1>ECCD Checklist: Child's Record</h1>

<h2>Sociodemographic Profile</h2>
<p>Student: {{ $student->name }} | Gender: {{ $student->gender }} | DOB: {{ $student->dob }}</p>
<p>Family: {{ $student->family?->name }} | Section: {{ $student->section?->name }}</p>
<p>Handedness: {{ $student->handedness }} | Studying: {{ $student->is_studying ? 'Yes' : 'No' }} | School: {{ $student->school_name ?? 'N/A' }}</p>

<h2>Assessments</h2>
<ul>
@foreach($tests as $i => $test)
  <li>
    {{ ['1st','2nd','3rd'][$i] }} Assessment — Date: {{ $test->test_date }} | Age (months): {{ $test->age_months ?? 'n/a' }} | Examiner: {{ $test->examiner_name ?? $test->observer?->name ?? 'n/a' }}
  </li>
@endforeach
</ul>

@foreach($domains as $domain)
  <h2>{{ $domain->name }}</h2>
  <table border="1">
    <tr>
      <th>Item</th>
      <th>Material/Procedure</th>
@foreach($tests as $i => $test)
      <th>{{ ['1st','2nd','3rd'][$i] }} Eval</th>
@endforeach
      <th>Comments</th>
    </tr>
@foreach($domain->questions as $idx => $q)
    @php
      $rowResponses = $tests->map(function($t){ return $t->responses->keyBy('question_id'); });
    @endphp
    <tr>
      <td>{{ $idx+1 }}. {{ $q->question_text }}</td>
      <td>
        @if($q->materials) <div><strong>Materials:</strong> {{ $q->materials }}</div> @endif
        @if($q->procedure) <div><strong>Procedure:</strong> {{ $q->procedure }}</div> @endif
      </td>
@foreach($tests as $ti => $t)
      @php $resp = $rowResponses[$ti][$q->id] ?? null; @endphp
      <td>{{ ($resp && ($resp->score ?? 0) > 0) ? '✔' : '-' }}</td>
@endforeach
      <td>
        @foreach($tests as $ti => $t)
          @php $resp = $rowResponses[$ti][$q->id] ?? null; @endphp
          @if($resp && $resp->comment)
            <div><em>{{ ['1st','2nd','3rd'][$ti] }}:</em> {{ $resp->comment }}</div>
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
@foreach($tests as $i => $test)
    <th>{{ ['1st','2nd','3rd'][$i] }} Raw</th>
    <th>{{ ['1st','2nd','3rd'][$i] }} Scaled</th>
@endforeach
  </tr>
@foreach($domains as $domain)
  <tr>
    <td>{{ $domain->name }}</td>
@foreach($tests as $i => $test)
    @php $sum = $summaries[$test->id] ?? null; $d = $sum['domains'][$domain->id] ?? null; @endphp
    <td>{{ $d['raw'] ?? '' }}</td>
    <td>{{ $d['scaled'] ?? '' }}</td>
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
  </tr>
@foreach($tests as $i => $test)
  @php $sum = $summaries[$test->id] ?? null; @endphp
  <tr>
    <td>{{ ['1st','2nd','3rd'][$i] }}</td>
    <td>{{ $test->age_months ?? '' }}</td>
    <td>{{ $sum['sumScaled'] ?? '' }}</td>
    <td>{{ $sum['standardScore'] ?? '' }}</td>
  </tr>
@endforeach
</table>

@endsection
