@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back</a>
  </div>
</div>

@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($progressPct !== null)
  <div class="progress mb-2" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: {{ $progressPct }}%" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: {{ $answeredCount }} / {{ $totalQuestions }} ({{ $progressPct }}%)</div>
@endif

<form method="post" action="{{ route('teacher.tests.form.submit', $test->test_id) }}">
  @csrf

  @foreach($domains as $domain)
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="mb-0">{{ $domain->name }}</h5>
      </div>
      <div class="card-body p-0">
        @if($domain->questions->isEmpty())
          <p class="p-3 text-muted">No questions in this domain.</p>
        @else
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 60%;">Question</th>
                <th style="width: 40%;">Answer</th>
              </tr>
            </thead>
            <tbody>
              @foreach($domain->questions as $q)
                @php
                  $existingAnswer = $existing[$q->question_id] ?? null;
                @endphp
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $q->text }}</div>
                    @if($q->display_text)
                      <div class="text-muted small">{{ $q->display_text }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group" role="group" aria-label="Answer">
                      <input type="radio" class="btn-check" name="q_{{ $q->question_id }}" id="q{{ $q->question_id }}_yes" value="yes" {{ $existingAnswer === 'yes' ? 'checked' : '' }}>
                      <label class="btn btn-outline-success btn-sm" for="q{{ $q->question_id }}_yes">Yes</label>

                      <input type="radio" class="btn-check" name="q_{{ $q->question_id }}" id="q{{ $q->question_id }}_no" value="no" {{ $existingAnswer === 'no' ? 'checked' : '' }}>
                      <label class="btn btn-outline-danger btn-sm" for="q{{ $q->question_id }}_no">No</label>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  @endforeach

  <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Save &amp; View Result</button>
    <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
@endsection
