@extends('layouts.app')

@section('content')
<h1>{{ $student->name }}</h1>
<p>Gender: {{ $student->gender }} | DOB: {{ $student->dob }} | Section: {{ $student->section->name ?? 'N/A' }}</p>
<p>Emergency Contact: {{ $student->emergency_contact ?? 'N/A' }}</p>

<h2>Recent Tests</h2>
@if($tests->isEmpty())
  <p>No tests yet.</p>
@else
  <table border="1" cellpadding="6">
    <tr>
      <th>Date</th>
      <th>Status</th>
      <th>Sum Scaled</th>
      <th>Standard Score</th>
      <th>Action</th>
    </tr>
    @foreach($tests as $t)
      @php $sum = $summary[$t->id]['sumScaled'] ?? null; $ss = $summary[$t->id]['standardScore'] ?? null; @endphp
      <tr>
        <td>{{ $t->test_date }}</td>
        <td>{{ $t->status }}</td>
        <td>{{ $sum ?? '—' }}</td>
        <td>{{ $ss ?? '—' }}</td>
        <td><a href="{{ route('family.tests.result', $t->id) }}">View</a></td>
      </tr>
    @endforeach
  </table>
@endif

<h2>Domain Performance (Last 3 and 6 months)</h2>
<p>Simple trend using available tests; based on reference scale idea.</p>
<table border="1" cellpadding="6">
  <tr>
    <th>Domain</th>
    <th>Last Test</th>
    <th>Avg (Last 3)</th>
    <th>Avg (Last 6)</th>
  </tr>
  @foreach($domains as $d)
    @php
      $last = $tests->first()?->scores->firstWhere('domain_id', $d->id)?->scaled_score;
      $avg3 = round($tests->take(3)->map(fn($t) => optional($t->scores->firstWhere('domain_id',$d->id))->scaled_score)->filter()->avg() ?? 0, 2);
      $avg6 = round($tests->take(6)->map(fn($t) => optional($t->scores->firstWhere('domain_id',$d->id))->scaled_score)->filter()->avg() ?? 0, 2);
    @endphp
    <tr>
      <td>{{ $d->name }}</td>
      <td>{{ $last ?? '—' }}</td>
      <td>{{ $avg3 }}</td>
      <td>{{ $avg6 }}</td>
    </tr>
  @endforeach
</table>
@endsection
