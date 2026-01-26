
@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Families</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Dashboard</a>
	</div>
</div>

<div class="card mb-3">
	<div class="card-body">
		<form class="row g-2" method="get" action="{{ route('admin.families') }}">
			<div class="col-12 col-md-3">
				<label class="form-label">Family</label>
				<input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Search name">
			</div>
			<div class="col-12 col-md-3">
				<label class="form-label">User</label>
				<input type="text" name="user" value="{{ $filters['user'] ?? '' }}" class="form-control form-control-sm" placeholder="Search user/email">
			</div>
			<div class="col-12 col-md-3">
				<label class="form-label">Has</label>
				<select name="has" class="form-select form-select-sm">
					<option value="">All</option>
					<option value="students" {{ ($filters['has'] ?? '') === 'students' ? 'selected' : '' }}>With students</option>
					<option value="none" {{ ($filters['has'] ?? '') === 'none' ? 'selected' : '' }}>No students</option>
				</select>
			</div>
			<div class="col-12 col-md-3 d-flex align-items-end gap-2">
				<button class="btn btn-sm btn-primary" type="submit">Filter</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.families') }}">Reset</a>
				<a class="btn btn-sm btn-outline-success" href="{{ route('admin.families.export', request()->query()) }}">Export CSV</a>
			</div>
		</form>
	</div>
</div>

<div class="card mb-3">
	<div class="card-header bg-light">Create Family</div>
	<div class="card-body">
		<form class="row g-2" method="POST" action="{{ route('admin.families.store') }}">
			@csrf
			<div class="col-12 col-md-4">
				<label class="form-label">User</label>
				<select name="user_id" class="form-select form-select-sm" required>
					@foreach($familyUsers as $fu)
						<option value="{{ $fu->id }}">{{ $fu->name }} ({{ $fu->email }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-12 col-md-4">
				<label class="form-label">Family Name</label>
				<input type="text" name="name" class="form-control form-control-sm" required>
			</div>
			<div class="col-12 col-md-4">
				<label class="form-label">Home Address</label>
				<input type="text" name="home_address" class="form-control form-control-sm" required>
			</div>
			<div class="col-12">
				<button class="btn btn-primary btn-sm" type="submit">Create</button>
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
						<th>Family</th>
						<th>User</th>
						<th>Email</th>
						<th>Home</th>
						<th>Students</th>
						<th>Assign</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
					@forelse($families as $family)
						<tr>
							<td>{{ $family->id }}</td>
							<td>
								<form method="POST" action="{{ route('admin.families.update', $family->id) }}" class="d-flex gap-2 align-items-center">
									@csrf
									<input type="text" name="name" value="{{ $family->name }}" class="form-control form-control-sm" style="min-width:160px">
									<input type="text" name="home_address" value="{{ $family->home_address }}" class="form-control form-control-sm" style="min-width:200px">
									<button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
								</form>
							</td>
							<td>{{ optional($family->user)->name }}</td>
							<td>{{ optional($family->user)->email }}</td>
							<td>{{ $family->home_address }}</td>
							<td><span class="badge bg-primary">{{ $family->students_count }}</span></td>
							<td>
								<form method="POST" action="{{ route('admin.families.assign', ['family' => $family->id]) }}" class="d-flex gap-2">
									@csrf
									<select name="student_id" class="form-select form-select-sm" required style="min-width:160px">
										@foreach($allStudents as $stu)
											<option value="{{ $stu->id }}">{{ $stu->name }}</option>
										@endforeach
									</select>
									<button class="btn btn-sm btn-outline-success" type="submit">Assign</button>
								</form>
							</td>
							<td class="d-flex gap-2">
								<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users') }}">User</a>
								<form method="POST" action="{{ route('admin.families.delete', $family->id) }}" onsubmit="return confirm('Delete this family? Students must be removed first.')">
									@csrf
									<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
								</form>
							</td>
						</tr>
						<tr>
							<td colspan="8">
								<div class="small text-muted">Students:</div>
								<ul class="small mb-2">
									@forelse($family->students as $student)
										<li>{{ $student->name }} ({{ $student->gender }}) – {{ $student->enrollment_date }} – {{ $student->status }}</li>
									@empty
										<li class="text-muted">No students</li>
									@endforelse
								</ul>
							</td>
						</tr>
					@empty
						<tr><td colspan="8" class="text-muted">No families found</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer">{{ $families->links() }}</div>
</div>
@endsection