@extends('admin.layout')

@section('content')
<h1>Teachers</h1>

<h2>Add Teacher Profile</h2>
<form method="POST" action="{{ route('admin.teachers.store') }}">
	@csrf
	<p>User:
		<select name="user_id" required>
			@foreach($teacherUsers as $tu)
				<option value="{{ $tu->id }}">{{ $tu->name }} ({{ $tu->email }})</option>
			@endforeach
		</select>
	</p>
	<p>Hire Date: <input type="date" name="hire_date"></p>
	<p>Status:
		<select name="status" required>
			<option value="active">Active</option>
			<option value="inactive">Inactive</option>
		</select>
	</p>
	<p><button type="submit">Save</button></p>
</form>

<h2>All Teachers</h2>
@foreach($teachers as $teacher)
	<h3>{{ optional($teacher->user)->name }} ({{ optional($teacher->user)->email }})</h3>
	<p>Hire Date: {{ $teacher->hire_date }} | Status: {{ $teacher->status }}</p>
	<p>Assigned Students:</p>
	<ul>
		@foreach($teacher->students as $s)
			<li>{{ $s->name }} (role: {{ $s->pivot->role }}, since: {{ $s->pivot->assigned_at }})</li>
		@endforeach
	</ul>
	<hr>
@endforeach
@endsection
