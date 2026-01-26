@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Users</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Dashboard</a>
	</div>
</div>

<div class="card mb-3">
	<div class="card-body">
		<form class="row g-2" method="get" action="{{ route('admin.users') }}">
			<div class="col-12 col-md-3">
				<label class="form-label">Name</label>
				<input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Search name">
			</div>
			<div class="col-12 col-md-3">
				<label class="form-label">Email</label>
				<input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="form-control form-control-sm" placeholder="Search email">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">Role</label>
				<select name="role" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach(['family','teacher','admin'] as $role)
						<option value="{{ $role }}" {{ ($filters['role'] ?? '') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">Status</label>
				<select name="status" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach(['active','inactive'] as $st)
						<option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-12 col-md-2 d-flex align-items-end gap-2">
				<button class="btn btn-sm btn-primary" type="submit">Filter</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users') }}">Reset</a>
				<a class="btn btn-sm btn-outline-success" href="{{ route('admin.users.export', request()->query()) }}">Export CSV</a>
			</div>
		</form>
	</div>
</div>

<div class="card mb-3">
	<div class="card-header bg-light">Create User</div>
	<div class="card-body">
		@if($errors->any())
			<div class="alert alert-danger" role="alert">
				<strong>There were problems with your submission:</strong>
				<ul class="mb-0">
					@foreach($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		<form class="row g-2" method="POST" action="{{ route('admin.users.store') }}">
			@csrf
			<div class="col-12 col-md-3"><label class="form-label">Name</label><input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required></div>
			<div class="col-12 col-md-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required></div>
			<div class="col-12 col-md-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control form-control-sm" required></div>
			<div class="col-6 col-md-2"><label class="form-label">Role</label><select name="role" class="form-select form-select-sm" required><option value="family" @selected(old('role')==='family')>Family</option><option value="teacher" @selected(old('role')==='teacher')>Teacher</option><option value="admin" @selected(old('role')==='admin')>Admin</option></select></div>
			<div class="col-6 col-md-2"><label class="form-label">Status</label><select name="status" class="form-select form-select-sm" required><option value="active" @selected(old('status','active')==='active')>Active</option><option value="inactive" @selected(old('status')==='inactive')>Inactive</option></select></div>
			<div class="col-12"><label class="form-label">Profile Path</label><input type="text" name="profile_path" value="{{ old('profile_path') }}" class="form-control form-control-sm" placeholder="/storage/public/avatar.jpg"></div>
			<div class="col-12"><button class="btn btn-primary btn-sm" type="submit">Create</button></div>
		</form>
	</div>
</div>

<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-sm table-hover mb-0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Role</th>
						<th>Status</th>
						<th>Profile</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					@forelse($users as $u)
						<tr>
							<td>{{ $u->id }}</td>
							<td colspan="5">
								<form method="POST" action="{{ route('admin.users.update', $u->id) }}" class="row g-2 align-items-center">
									@csrf
									<div class="col-12 col-md-3"><input type="text" name="name" value="{{ $u->name }}" class="form-control form-control-sm"></div>
									<div class="col-12 col-md-3"><input type="email" name="email" value="{{ $u->email }}" class="form-control form-control-sm"></div>
									<div class="col-6 col-md-2"><select name="role" class="form-select form-select-sm"><option value="family" @selected($u->role==='family')>Family</option><option value="teacher" @selected($u->role==='teacher')>Teacher</option><option value="admin" @selected($u->role==='admin')>Admin</option></select></div>
									<div class="col-6 col-md-2"><select name="status" class="form-select form-select-sm"><option value="active" @selected($u->status==='active')>Active</option><option value="inactive" @selected($u->status==='inactive')>Inactive</option></select></div>
									<div class="col-12"><input type="text" name="profile_path" value="{{ $u->profile_path }}" class="form-control form-control-sm" placeholder="/storage/public/avatar.jpg"></div>
									<div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Save</button></div>
								</form>
							</td>
							<td class="d-flex gap-2">
								<form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('Delete this user?')">
									@csrf
									<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr><td colspan="7" class="text-muted">No users found</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer">{{ $users->links() }}</div>
</div>
@endsection
