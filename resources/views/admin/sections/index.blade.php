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
			<form method="POST" action="{{ route('admin.sections.update', $s->id) }}" style="display:inline-block; margin-left:8px">
				@csrf
				<input type="text" name="name" value="{{ $s->name }}" style="width:140px">
				<input type="text" name="description" value="{{ $s->description }}" style="width:220px">
				<button type="submit">Update</button>
			</form>
			<form method="POST" action="{{ route('admin.sections.delete', $s->id) }}" style="display:inline-block; margin-left:6px" onsubmit="return confirm('Delete this section? It must have no students.')">
				@csrf
				<button type="submit">Delete</button>
			</form>
		</li>
	@endforeach
	</ul>
@endsection
