@extends('admin.layout')

@section('content')
<h1>Profile</h1>
@if($user)
	<p>ID: {{ $user->id }}</p>
	<p>Name: {{ $user->name }}</p>
	<p>Email: {{ $user->email }}</p>
	<p>Role: {{ $user->role }}</p>
	<p>Status: {{ $user->status }}</p>
	<p>Profile: {{ $user->profile_path }}</p>
	<hr>
	<h2>Update Profile</h2>
	<form method="POST" action="{{ route('admin.profile.update') }}">
		@csrf
		<p>Name: <input type="text" name="name" value="{{ $user->name }}" required></p>
		<p>Profile Path: <input type="text" name="profile_path" value="{{ $user->profile_path }}"></p>
		<p><button type="submit">Update</button></p>
	</form>
@else
	<p>No authenticated user.</p>
@endif
@endsection
