@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back</a>
  </div>
</div>

@php
  $totalQuestions = \App\Models\Domain::with('questions')->get()->sum(fn($d)=>$d->questions->count());
  $answeredCount = $test->responses->count();
  $progressPct = $totalQuestions ? round(($answeredCount / max(1,$totalQuestions)) * 100) : null;
@endphp

@if($progressPct !== null)
  <div class="progress mb-3" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: {{ $progressPct }}%" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: {{ $answeredCount }} / {{ $totalQuestions }} ({{ $progressPct }}%)</div>
@endif

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <div class="text-muted">Domain</div>
        <h2 class="h5 mb-0">{{ $domain->name }}</h2>
      </div>
      <span class="badge bg-primary">Question {{ $index + 1 }}</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <p class="fs-5">{{ $question->text }}</p>
    @if($question->display_text)
      <p><strong>Display Text:</strong> {{ $question->display_text }}</p>
    @endif

    <form method="post" action="{{ route('teacher.tests.question.submit', [$test->test_id, $domain->domain_id, $index]) }}" class="mt-3">
      @csrf
      <div class="btn-group" role="group" aria-label="Answer">
        <input type="radio" class="btn-check" name="answer" id="answerYes" value="yes" required>
        <label class="btn btn-outline-success" for="answerYes">Yes</label>

        <input type="radio" class="btn-check" name="answer" id="answerNo" value="no">
        <label class="btn btn-outline-danger" for="answerNo">No</label>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Next</button>
      </div>
    </form>
    <div class="mt-3 d-flex gap-2">
      <form method="post" action="{{ route('teacher.tests.pause', $test->test_id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Pause</button>
      </form>
      <form method="post" action="{{ route('teacher.tests.cancel', $test->test_id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">Cancel</button>
      </form>
    </div>
  </div>
</div>
@endsection
