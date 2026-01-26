@extends('admin.layout')

@section('content')
<h1>Reports</h1>

@foreach($tests as $t)
	<h3>Test #{{ $t->id }} | Student: {{ $t->student->name }} | Date: {{ $t->test_date }} | Status: {{ $t->status }}</h3>
	<p>Observer: {{ optional($t->observer)->name }} ({{ optional($t->observer)->role }})</p>
	<form method="post" action="{{ route('admin.tests.delete', $t->id) }}" style="display:inline-block">
		@csrf
		<button class="btn btn-sm btn-outline-danger" type="submit">Delete Test</button>
	</form>
	<p>Responses:</p>
	<ul>
		@foreach($t->responses as $r)
			<li>Q{{ $r->question_id }}: {{ optional($r->question)->question_text }} — Score: {{ $r->score }} — {{ $r->comment }}</li>
		@endforeach
	</ul>
	<p>Domain Scores:</p>
	<ul>
		@foreach($t->scores as $s)
			<li>{{ optional($s->domain)->name }} — Raw: {{ $s->raw_score }} — Scaled: {{ $s->scaled_score }} (base {{ $s->scaled_score_based }})</li>
		@endforeach
	</ul>
	<p>Pictures:</p>
	<ul>
		@foreach($t->pictures as $p)
			<li>File: {{ $p->file_path }} (Q: {{ $p->question_id }})</li>
		@endforeach
	</ul>
	<hr>
@endforeach
@endsection
