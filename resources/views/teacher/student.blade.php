@extends('layouts.app')

@section('content')
<h1>{{ $student->name }}</h1>
<p>Gender: {{ $student->gender }} | DOB: {{ $student->dob }} | Section: {{ $student->section->name ?? 'N/A' }}</p>
<p>Emergency Contact: {{ $student->emergency_contact ?? 'N/A' }}</p>

<h2>All Tests</h2>
@if($tests->isEmpty())
  <p>No tests yet.</p>
@else
  <table border="1" cellpadding="6">
    <tr>
      <th>Date</th>
      <th>By</th>
      <th>Status</th>
      <th>Sum Scaled</th>
      <th>Standard Score</th>
      <th>Action</th>
    </tr>
    @foreach($tests as $t)
      @php $sum = $t->scores->sum('scaled_score'); $by = $t->observer?->role; @endphp
      <tr>
        <td>{{ $t->test_date }}</td>
        <td>{{ $by ?? '—' }}</td>
        <td>{{ $t->status }}</td>
        <td>{{ $sum ? number_format($sum,2) : '—' }}</td>
        <td>{{ \App\Services\EccdScoring::deriveStandardScore((float)$sum) ?? '—' }}</td>
        <td><a href="{{ $by==='teacher' ? route('teacher.tests.result',$t->id) : route('family.tests.result',$t->id) }}">View</a></td>
      </tr>
    @endforeach
  </table>
@endif

<h2>Domain Performance (6 / 12 / 18 months)</h2>
<table border="1" cellpadding="6">
  <tr>
    <th>Domain</th>
    <th>Teacher Avg (6m)</th>
    <th>Teacher Avg (12m)</th>
    <th>Teacher Avg (18m)</th>
    <th>Family Avg (6m)</th>
  </tr>
  @foreach($domains as $d)
    <tr>
      <td>{{ $d->name }}</td>
      <td>{{ $avg($teacherTests, 6, $d->id) }}</td>
      <td>{{ $avg($teacherTests, 12, $d->id) }}</td>
      <td>{{ $avg($teacherTests, 18, $d->id) }}</td>
      <td>{{ $avg($familyTests, 6, $d->id) }}</td>
    </tr>
  @endforeach
</table>

<form method="post" action="{{ route('teacher.tests.start', $student->id) }}">
  @csrf
  <button type="submit">Start New Test (if eligible)</button>
</form>
@endsection
