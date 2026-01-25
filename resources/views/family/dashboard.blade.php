@extends('layouts.app')

@section('content')
<h1>Family Dashboard</h1>
<p>Children: {{ $students->count() }}</p>

<table border="1" cellpadding="6">
  <tr>
    <th>Child</th>
    <th>Latest Test</th>
    <th>This Month</th>
    <th>Actions</th>
  </tr>
  @foreach($students as $s)
    <tr>
      <td><a href="{{ route('family.child', $s->id) }}">{{ $s->name }}</a></td>
      <td>
        @if($status[$s->id]['latest'])
          {{ $status[$s->id]['latest']->test_date }} ({{ $status[$s->id]['latest']->status }})
        @else
          none
        @endif
      </td>
      <td>{{ $status[$s->id]['has_test_this_month'] ? 'Yes' : 'No' }}</td>
      <td>
        @if(!$status[$s->id]['has_test_this_month'])
          <form method="post" action="{{ route('family.tests.start', $s->id) }}">
            @csrf
            <button type="submit">Start Test ({{ $currentMonth }})</button>
          </form>
        @else
          @php $t = $status[$s->id]['latest']; @endphp
          @if($t && $t->status !== 'completed')
            <a href="{{ route('family.tests.question', [$t->id, \App\Models\Domain::orderBy('id')->first()->id, 0]) }}">Continue</a>
          @else
            <a href="{{ route('family.tests.result', $t->id) }}">View Result</a>
          @endif
        @endif
      </td>
    </tr>
  @endforeach
</table>
@endsection
