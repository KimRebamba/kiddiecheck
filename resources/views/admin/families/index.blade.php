
@extends('admin.layout')

@section('content')
<h1>Families</h1>

<h2>Create Family</h2>
<form method="POST" action="{{ route('admin.families.store') }}">
	@csrf
	<p>User:
		<select name="user_id" required>
			@foreach($familyUsers as $fu)
				<option value="{{ $fu->id }}">{{ $fu->name }} ({{ $fu->email }})</option>
			@endforeach
		</select>
	</p>
	<p>Name: <input type="text" name="name" required></p>
	<p>Home Address: <input type="text" name="home_address" required></p>
	<p><button type="submit">Create</button></p>
</form>

<h2>All Families</h2>
@foreach($families as $family)
	<h3>#{{ $family->id }} {{ $family->name }}</h3>
	<p>User: {{ optional($family->user)->name }} ({{ optional($family->user)->email }})</p>
	<p>Home: {{ $family->home_address }}</p>
	<form method="POST" action="{{ route('admin.families.update', $family->id) }}" style="margin-bottom:8px">
		@csrf
		<input type="text" name="name" value="{{ $family->name }}" placeholder="Name" required>
		<input type="text" name="home_address" value="{{ $family->home_address }}" placeholder="Home Address" required>
		<button type="submit">Update</button>
	</form>
	<form method="POST" action="{{ route('admin.families.delete', $family->id) }}" onsubmit="return confirm('Delete this family? Students must be removed first.')" style="margin-bottom:12px">
		@csrf
		<button type="submit">Delete</button>
	</form>
	<p>Students:</p>
	<ul>
		@foreach($family->students as $student)
			<li>{{ $student->name }} ({{ $student->gender }}) - {{ $student->enrollment_date }} - {{ $student->status }}</li>
		@endforeach
	</ul>
	<form method="POST" action="{{ route('admin.families.assign', ['family' => $family->id]) }}">
		@csrf
		<p>Assign Student to this Family:
			<select name="student_id" required>
				@foreach($allStudents as $stu)
					<option value="{{ $stu->id }}">{{ $stu->name }}</option>
				@endforeach
			</select>
		</p>
		<p><button type="submit">Assign</button></p>
	</form>
	<hr>
@endforeach
@endsection