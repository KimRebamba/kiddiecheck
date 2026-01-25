@extends('layouts.app')

@section('content')
<h1>Teacher Dashboard</h1>
<p>Assigned Children: {{ $students->count() }}</p>

<table border="1" cellpadding="6">
  <tr>
    <th>Child</th>
    <th>Latest Teacher Test</th>
    <th>Eligible (>= 6 months)</th>
    <th>Actions</th>
  </tr>
  @foreach($students as $s)
    @php $st = $status[$s->id] ?? null; $latest = $st['latest_teacher'] ?? null; @endphp
    <tr>
      <td><a href="{{ route('teacher.student', $s->id) }}">{{ $s->name }}</a></td>
      <td>
        @if($latest)
          {{ $latest->test_date }} ({{ $latest->status }})
        @else
          none
        @endif
      </td>
      <td>{{ ($st['eligible'] ?? false) ? 'Yes' : 'No' }}</td>
      <td>
        @if(($st['eligible'] ?? false))
          <form method="post" action="{{ route('teacher.tests.start', $s->id) }}">
            @csrf
            <button type="submit">Start Test</button>
          </form>
        @else
          <span>Next test after 6 months</span>
        @endif
      </td>
    </tr>
  @endforeach
</table>
@endsection
