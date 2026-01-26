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
	<form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}" style="margin-bottom:8px">
		@csrf
		<input type="date" name="hire_date" value="{{ $teacher->hire_date }}">
		<select name="status">
			<option value="active" @selected($teacher->status==='active')>Active</option>
			<option value="inactive" @selected($teacher->status==='inactive')>Inactive</option>
		</select>
		<button type="submit">Update</button>
	</form>
	<form method="POST" action="{{ route('admin.teachers.delete', $teacher->id) }}" onsubmit="return confirm('Delete this teacher profile?')" style="margin-bottom:12px">
		@csrf
		<button type="submit">Delete</button>
	</form>
	<p>Assigned Students:</p>
	<ul>
		@foreach($teacher->students as $s)
			<li>{{ $s->name }} (role: {{ $s->pivot->role }}, since: {{ $s->pivot->assigned_at }})</li>
		@endforeach
	</ul>
	<form method="POST" action="{{ route('admin.teachers.assign', ['teacher' => $teacher->id]) }}">
		@csrf
		<p>Assign Student:
			<select name="student_id" required>
				@foreach($allStudents as $stu)
					<option value="{{ $stu->id }}">{{ $stu->name }}</option>
				@endforeach
			</select>
		</p>
		<p>Role:
			<select name="role">
				<option value="homeroom">Homeroom</option>
				<option value="specialist">Specialist</option>
				<option value="others">Others</option>
			</select>
		</p>
		<p><button type="submit">Assign</button></p>
	</form>
	<hr>
@endforeach
@endsection
