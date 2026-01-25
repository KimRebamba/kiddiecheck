@extends('admin.layout')

@section('content')
<h1>Section: {{ $section->name }}</h1>
<p>{{ $section->description }}</p>

<h2>Students</h2>
<h3>Add Student to this Section</h3>
<form method="POST" action="{{ route('admin.sections.students.store', $section->id) }}">
	@csrf
	<p>Family:
		<select name="family_id" required>
			@foreach($families as $f)
				<option value="{{ $f->id }}">{{ $f->name }} (user: {{ $f->user?->email }})</option>
			@endforeach
		</select>
	</p>
	<p>Name: <input type="text" name="name" required></p>
	<p>DOB: <input type="date" name="dob" required></p>
	<p>Emergency Contact: <input type="text" name="emergency_contact"></p>
	<p>Gender:
		<select name="gender" required>
			<option value="male">Male</option>
			<option value="female">Female</option>
			<option value="other">Other</option>
		</select>
	</p>
	<p>Enrollment Date: <input type="date" name="enrollment_date" required></p>
	<p>Status:
		<select name="status" required>
			<option value="active">Active</option>
			<option value="transferred">Transferred</option>
			<option value="graduated">Graduated</option>
		</select>
	</p>
	<p>Profile Path: <input type="text" name="profile_path" placeholder="/storage/public/student.jpg"></p>
	<p>Notes: <textarea name="notes"></textarea></p>
	<p><button type="submit">Add Student</button></p>
</form>

<ul>
	@foreach($section->students as $c)
		<li>
			#{{ $c->id }} {{ $c->name }} ({{ $c->gender }}) status={{ $c->status }}
			— <a href="{{ route('admin.students.show', $c->id) }}">Open student</a>
		</li>
	@endforeach
</ul>
@endsection
