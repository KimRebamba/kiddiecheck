@extends('admin.layout')

@section('content')
<h1>Users</h1>

<h2>Create User</h2>
@if($errors->any())
  <div>
    <strong>There were problems with your submission:</strong>
    <ul>
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
<form method="POST" action="{{ route('admin.users.store') }}">
	@csrf
	<p>Name: <input type="text" name="name" value="{{ old('name') }}" required></p>
	<p>Email: <input type="email" name="email" value="{{ old('email') }}" required></p>
	<p>Password: <input type="password" name="password" required></p>
	<p>Role:
		<select name="role" required>
			<option value="family" @selected(old('role')==='family')>Family</option>
			<option value="teacher" @selected(old('role')==='teacher')>Teacher</option>
			<option value="admin" @selected(old('role')==='admin')>Admin</option>
		</select>
	</p>
	<p>Status:
		<select name="status" required>
			<option value="active" @selected(old('status','active')==='active')>Active</option>
			<option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
		</select>
	</p>
	<p>Profile Path: <input type="text" name="profile_path" value="{{ old('profile_path') }}" placeholder="/storage/public/avatar.jpg"></p>
	<p><button type="submit">Create</button></p>
</form>

<h2>All Users</h2>
<table border="1" cellpadding="6" cellspacing="0">
	<thead>
		<tr>
			<th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Profile</th><th>Actions</th>
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
				<td>
					<form method="POST" action="{{ route('admin.users.update', $u->id) }}" style="display:inline-block">
						@csrf
						<input type="text" name="name" value="{{ $u->name }}" style="width:120px">
						<input type="email" name="email" value="{{ $u->email }}" style="width:160px">
						<select name="role">
							<option value="family" @selected($u->role==='family')>Family</option>
							<option value="teacher" @selected($u->role==='teacher')>Teacher</option>
							<option value="admin" @selected($u->role==='admin')>Admin</option>
						</select>
						<select name="status">
							<option value="active" @selected($u->status==='active')>Active</option>
							<option value="inactive" @selected($u->status==='inactive')>Inactive</option>
						</select>
						<input type="text" name="profile_path" value="{{ $u->profile_path }}" placeholder="/storage/public/avatar.jpg" style="width:160px">
						<button type="submit">Save</button>
					</form>
					<form method="POST" action="{{ route('admin.users.delete', $u->id) }}" style="display:inline-block" onsubmit="return confirm('Delete this user?')">
						@csrf
						<button type="submit">Delete</button>
					</form>
				</td>
			</tr>
		@endforeach
	</tbody>
</table>
@endsection
