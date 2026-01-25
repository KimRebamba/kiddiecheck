@extends('layouts.app')

@section('content')
<h1>Test for {{ $test->student->name }}</h1>
<h2>Domain: {{ $domain->name }}</h2>

<p><strong>Question {{ $index + 1 }}</strong>: {{ $question->question_text }}</p>
@if($question->instructions)
  <p><em>Instructions:</em> {{ $question->instructions }}</p>
@endif
@if($question->materials)
  <p><em>Materials:</em> {{ $question->materials }}</p>
@endif
@if($question->procedure)
  <p><em>Procedure:</em> {{ $question->procedure }}</p>
@endif

<form method="post" action="{{ route('family.tests.question.submit', [$test->id, $domain->id, $index]) }}">
  @csrf
  <label><input type="radio" name="answer" value="yes" required> Yes</label>
  <label><input type="radio" name="answer" value="no"> No</label>
  <label><input type="radio" name="answer" value="na"> N/A</label>
  <div>
    <button type="submit">Next</button>
  </div>
</form>
@endsection
