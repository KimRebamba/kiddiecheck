@extends('layouts.app')

@section('content')
<h1>Result for {{ $test->student->name }} ({{ $test->test_date }})</h1>
<p>Status: {{ $test->status }} | Sum Scaled: {{ number_format($sumScaled,2) }} | Standard Score: {{ $standardScore ?? '—' }}</p>

<table border="1" cellpadding="6">
  <tr>
    <th>Domain</th>
    <th>Raw</th>
    <th>Scaled (%)</th>
    <th>Based (count)</th>
  </tr>
  @foreach($domains as $d)
    @php $s = $test->scores->firstWhere('domain_id', $d->id); @endphp
    <tr>
      <td>{{ $d->name }}</td>
      <td>{{ optional($s)->raw_score ?? '—' }}</td>
      <td>{{ optional($s)->scaled_score ?? '—' }}</td>
      <td>{{ optional($s)->scaled_score_based ?? '—' }}</td>
    </tr>
  @endforeach
</table>

<p><a href="{{ route('family.index') }}">Back to Dashboard</a></p>
@endsection
