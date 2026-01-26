@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Admin Dashboard</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('index') }}">Home</a>
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Users</div><div class="display-6">{{ $userCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Families</div><div class="display-6">{{ $familyCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Teachers</div><div class="display-6">{{ $teacherCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Students</div><div class="display-6">{{ $studentCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Domains</div><div class="display-6">{{ $domainCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Questions</div><div class="display-6">{{ $questionCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Tests</div><div class="display-6">{{ $testCount }}</div></div></div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<h2 class="h5">Manage</h2>
		<div class="d-flex flex-wrap gap-2">
			<a class="btn btn-primary" href="{{ route('admin.users') }}">Users</a>
			<a class="btn btn-primary" href="{{ route('admin.families') }}">Families</a>
			<a class="btn btn-primary" href="{{ route('admin.sections') }}">Sections</a>
			<a class="btn btn-primary" href="{{ route('admin.teachers') }}">Teachers</a>
			<a class="btn btn-outline-primary" href="{{ route('admin.domains') }}">Domains</a>
			<a class="btn btn-outline-primary" href="{{ route('admin.reports') }}">Reports</a>
		</div>
	</div>
</div>
@endsection
