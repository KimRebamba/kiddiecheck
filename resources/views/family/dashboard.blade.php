@extends('layouts.app')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h3 mb-0">Family Dashboard</h1>
  <span class="ms-3 text-muted">Children: {{ $students->count() }}</span>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('index') }}">Home</a>
  </div>
  </div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th>Child</th>
            <th>Latest Test</th>
            <th>This Month</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        @foreach($students as $s)
          <tr>
            <td><a href="{{ route('family.child', $s->id) }}">{{ $s->name }}</a></td>
            <td>
              @if($status[$s->id]['latest'])
                {{ $status[$s->id]['latest']->test_date }} ({{ $status[$s->id]['latest']->status }})
              @else
                <span class="text-muted">none</span>
              @endif
            </td>
            <td>
              @if($status[$s->id]['family_has_test_this_month'])
                <span class="badge bg-success">Yes</span>
              @else
                <span class="badge bg-secondary">No</span>
              @endif
            </td>
            <td class="d-flex gap-2">
              @if(!$status[$s->id]['family_has_test_this_month'])
                <form method="post" action="{{ route('family.tests.start', $s->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-primary">Start Test ({{ $currentMonth }})</button>
                </form>
              @else
                @php $t = $status[$s->id]['latest_family']; @endphp
                @if($t && $t->status === 'in_progress')
                  <a class="btn btn-sm btn-warning" href="{{ route('family.tests.question', [$t->id, \App\Models\Domain::orderBy('id')->first()->id, 0]) }}">Continue</a>
                @elseif($t && $t->status === 'completed')
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('family.tests.result', $t->id) }}">View Result</a>
                @else
                  <span class="text-muted">No active test</span>
                @endif
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
