@extends('admin.layout')

@section('content')
<h1>Sections</h1>

<h2>Create Section</h2>
<form method="POST" action="{{ route('admin.sections.store') }}">
	@csrf
	<p>Name: <input type="text" name="name" required></p>
	<p>Description: <textarea name="description"></textarea></p>
	<p><button type="submit">Create Section</button></p>
</form>

<h2>All Sections</h2>
<ul>
	@foreach($sections as $s)
		<li>
			#{{ $s->id }} {{ $s->name }} ({{ $s->students_count }} students)
			— <a href="{{ route('admin.sections.students', $s->id) }}">View Students</a>
		</li>
	@endforeach
	</ul>
@endsection
