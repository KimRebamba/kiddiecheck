@extends('admin.layout')

@section('content')
<h1>Domains & Questions</h1>

@foreach($domains as $domain)
	<section>
		<h2>{{ $domain->name }}</h2>
		@if($domain->description)
			<p>{{ $domain->description }}</p>
		@endif
		<p>Total Questions: {{ $domain->questions->count() }}</p>

		@php
			$static = $domain->questions->where('type', 'static');
			$interactive = $domain->questions->where('type', 'interactive');
		@endphp

		<h3>Static ({{ $static->count() }})</h3>
		@if($static->isEmpty())
			<p>None</p>
		@else
			<ol>
				@foreach($static as $q)
					<li>
						<strong>{{ $q->question_text }}</strong>
						@if($q->instructions)
							<div><em>Instructions:</em> {{ $q->instructions }}</div>
						@endif
						@if($q->materials)
							<div><em>Materials:</em> {{ $q->materials }}</div>
						@endif
						@if($q->procedure)
							<div><em>Procedure:</em> {{ $q->procedure }}</div>
						@endif
					</li>
				@endforeach
			</ol>
		@endif

		<h3>Interactive ({{ $interactive->count() }})</h3>
		@if($interactive->isEmpty())
			<p>None</p>
		@else
			<ol>
				@foreach($interactive as $q)
					<li>
						<strong>{{ $q->question_text }}</strong>
						@if($q->instructions)
							<div><em>Instructions:</em> {{ $q->instructions }}</div>
						@endif
						@if($q->materials)
							<div><em>Materials:</em> {{ $q->materials }}</div>
						@endif
						@if($q->procedure)
							<div><em>Procedure:</em> {{ $q->procedure }}</div>
						@endif
					</li>
				@endforeach
			</ol>
		@endif

		<hr>
	</section>
@endforeach

@endsection
