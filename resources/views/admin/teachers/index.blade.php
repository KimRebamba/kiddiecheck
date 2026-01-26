@extends('admin.layout')

@section('content')

<form method="POST" action="{{ route('admin.teachers.store') }}">
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
					<input type="number" name="min_students" value="{{ $filters['min_students'] ?? '' }}" class="form-control form-control-sm" min="0">
				</div>
				<div class="col-6 col-md-2">
					<label class="form-label">Max Students</label>
					<input type="number" name="max_students" value="{{ $filters['max_students'] ?? '' }}" class="form-control form-control-sm" min="0">
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
				<div class="col-12 col-md-4"><label class="form-label">Name</label><input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" required></div>
				<div class="col-12 col-md-4"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" required></div>
				<div class="col-12 col-md-4"><label class="form-label">Status</label><select name="status" class="form-select form-select-sm" required><option value="active" @selected(old('status','active')==='active')>Active</option><option value="inactive" @selected(old('status')==='inactive')>Inactive</option></select></div>
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
							<th>Status</th>
							<th>Students</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						@forelse($teachers as $t)
							<tr>
								<td>{{ $t->id }}</td>
								<td colspan="4">
									<form method="POST" action="{{ route('admin.teachers.update', $t->id) }}" class="row g-2 align-items-center">
										@csrf
										<div class="col-12 col-md-3"><input type="text" name="name" value="{{ $t->name }}" class="form-control form-control-sm"></div>
										<div class="col-12 col-md-3"><input type="email" name="email" value="{{ $t->email }}" class="form-control form-control-sm"></div>
										<div class="col-6 col-md-2"><select name="status" class="form-select form-select-sm"><option value="active" @selected($t->status==='active')>Active</option><option value="inactive" @selected($t->status==='inactive')>Inactive</option></select></div>
										<div class="col-6 col-md-2">
											<input type="number" value="{{ $t->students_count ?? 0 }}" class="form-control form-control-sm" disabled>
											<div class="form-text">Assigned students</div>
										</div>
										<div class="col-12"><button class="btn btn-sm btn-outline-primary" type="submit">Save</button></div>
									</form>
								</td>
								<td class="d-flex gap-2">
									<form method="POST" action="{{ route('admin.teachers.delete', $t->id) }}" onsubmit="return confirm('Delete this teacher? This cannot be undone.')">
										@csrf
										<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
									</form>
								</td>
							</tr>
						@empty
							<tr><td colspan="6" class="text-muted">No teachers found</td></tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
		<div class="card-footer">{{ $teachers->links() }}</div>
	</div>
	@endsection
