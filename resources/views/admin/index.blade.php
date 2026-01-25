@extends('admin.layout')

@section('content')
<h1>Admin Dashboard</h1>
<ul>
	<li>Users: {{ $userCount }}</li>
	<li>Families: {{ $familyCount }}</li>
	<li>Teachers: {{ $teacherCount }}</li>
	<li>Students: {{ $studentCount }}</li>
	<li>Domains: {{ $domainCount }}</li>
	<li>Questions: {{ $questionCount }}</li>
	<li>Tests: {{ $testCount }}</li>
</ul>
<p>Use the header links to manage data.</p>
@endsection
