@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Teachers</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Dashboard</a>
	</div>
</div>

<div class="card mb-3">
	<div class="card-body">
		<form class="row g-2" method="get" action="{{ route('admin.teachers') }}">
			<div class="col-12 col-md-3">
				<label class="form-label">Name</label>
				<input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Search name">
			</div>
			<div class="col-12 col-md-3">
				<label class="form-label">Email</label>
				<input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="form-control form-control-sm" placeholder="Search email">
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
			<div class="col-6 col-md-2">
				<label class="form-label">Min Students</label>
				<input type="number" name="min" value="{{ $filters['min'] ?? '' }}" class="form-control form-control-sm" min="0">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">Max Students</label>
				<input type="number" name="max" value="{{ $filters['max'] ?? '' }}" class="form-control form-control-sm" min="0">
			</div>
			<div class="col-12 col-md-2 d-flex align-items-end gap-2">
				<button class="btn btn-sm btn-primary" type="submit">Filter</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.teachers') }}">Reset</a>
				<a class="btn btn-sm btn-outline-success" href="{{ route('admin.teachers.export', request()->query()) }}">Export CSV</a>
			</div>
		</form>
	</div>
	</div>

<div class="card mb-3">
	<div class="card-header bg-light">Create Teacher</div>
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
		<form class="row g-2" method="POST" action="{{ route('admin.teachers.store') }}">
			@csrf
			<div class="col-12 col-md-4">
				<label class="form-label">User (role: teacher)</label>
				<select name="user_id" class="form-select form-select-sm" required>
					@forelse($teacherUsers as $u)
						<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
					@empty
						<option disabled>— No teacher users. Create one in Users —</option>
					@endforelse
				</select>
			</div>
			<div class="col-6 col-md-3">
				<label class="form-label">Hire Date</label>
				<input type="date" name="hire_date" class="form-control form-control-sm">
			</div>
			<div class="col-6 col-md-3">
				<label class="form-label">Status</label>
				<select name="status" class="form-select form-select-sm" required>
					<option value="active">Active</option>
					<option value="inactive">Inactive</option>
				</select>
			</div>
			<div class="col-12 d-flex gap-2 align-items-end">
				<button class="btn btn-primary btn-sm" type="submit">Create</button>
				<a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.users') }}">Manage Users</a>
			</div>
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
						<th>Status</th>
						<th>Students</th>
						<th>Assign Student</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
				@forelse($teachers as $t)
					<tr>
						<td>{{ $t->id }}</td>
						<td>{{ optional($t->user)->name ?? '—' }}</td>
						<td>{{ optional($t->user)->email ?? '—' }}</td>
						<td>
							<form method="POST" action="{{ route('admin.teachers.update', $t->id) }}" class="d-flex gap-2">
								@csrf
								<select name="status" class="form-select form-select-sm" style="max-width:140px">
									<option value="active" @selected($t->status==='active')>Active</option>
									<option value="inactive" @selected($t->status==='inactive')>Inactive</option>
								</select>
								<input type="date" name="hire_date" value="{{ $t->hire_date }}" class="form-control form-control-sm" style="max-width:160px">
								<button class="btn btn-sm btn-outline-primary" type="submit">Save</button>
							</form>
						</td>
						<td><span class="badge bg-primary">{{ $t->students_count }}</span></td>
						<td>
							<form method="POST" action="{{ route('admin.teachers.assign', $t->id) }}" class="d-flex gap-2">
								@csrf
								<select name="student_id" class="form-select form-select-sm" required style="min-width:160px">
									@foreach($allStudents as $stu)
										<option value="{{ $stu->id }}">{{ $stu->name }}</option>
									@endforeach
								</select>
								<select name="role" class="form-select form-select-sm" style="max-width:140px">
									<option value="homeroom">Homeroom</option>
									<option value="specialist">Specialist</option>
									<option value="others">Others</option>
								</select>
								<button class="btn btn-sm btn-outline-success" type="submit">Assign</button>
							</form>
						</td>
						<td class="d-flex gap-2">
							<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users') }}">User</a>
							<form method="POST" action="{{ route('admin.teachers.delete', $t->id) }}" onsubmit="return confirm('Delete this teacher? This removes assignments too.')">
								@csrf
								<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
							</form>
						</td>
					</tr>
					<tr>
						<td colspan="7">
							<div class="small text-muted">Students:</div>
							<ul class="small mb-2">
								@forelse($t->students as $s)
									<li>{{ $s->name }} ({{ $s->status }})</li>
								@empty
									<li class="text-muted">No students</li>
								@endforelse
							</ul>
						</td>
					</tr>
				@empty
					<tr><td colspan="7" class="text-muted">No teachers found</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer">{{ $teachers->links() }}</div>
</div>
@endsection
