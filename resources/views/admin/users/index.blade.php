@extends('admin.layout')

@section('content')
<h1>Users</h1>

<h2>Create User</h2>
<form method="POST" action="{{ route('admin.users.store') }}">
	@csrf
	<p>Name: <input type="text" name="name" required></p>
	<p>Email: <input type="email" name="email" required></p>
	<p>Password: <input type="password" name="password" required></p>
	<p>Role:
		<select name="role" required>
			<option value="family">Family</option>
			<option value="teacher">Teacher</option>
			<option value="admin">Admin</option>
		</select>
	</p>
	<p>Status:
		<select name="status" required>
			<option value="active">Active</option>
			<option value="inactive">Inactive</option>
		</select>
	</p>
	<p>Profile Path: <input type="text" name="profile_path" placeholder="/storage/public/avatar.jpg"></p>
	<p><button type="submit">Create</button></p>
</form>

<h2>All Users</h2>
<table border="1" cellpadding="6" cellspacing="0">
	<thead>
		<tr>
			<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Profile</th>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $u)
			<tr>
				<td>{{ $u->id }}</td>
				<td>{{ $u->name }}</td>
				<td>{{ $u->email }}</td>
				<td>{{ $u->role }}</td>
				<td>{{ $u->status }}</td>
				<td>{{ $u->profile_path }}</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endsection
