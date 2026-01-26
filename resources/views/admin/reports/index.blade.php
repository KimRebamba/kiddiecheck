@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Reports</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Dashboard</a>
	</div>
	</div>

<div class="card mb-3">
	<div class="card-body">
		<form class="row g-2" method="get" action="{{ route('admin.reports') }}">
			<div class="col-12 col-md-2">
				<label class="form-label">Status</label>
				<select name="status" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach(['pending','in_progress','completed','incomplete','cancelled','terminated'] as $st)
						<option value="{{ $st }}" {{ ($filters['status'] ?? '') === $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-12 col-md-2">
				<label class="form-label">Role</label>
				<select name="role" class="form-select form-select-sm">
					<option value="">All</option>
					@foreach(['teacher','family'] as $role)
						<option value="{{ $role }}" {{ ($filters['role'] ?? '') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-12 col-md-3">
				<label class="form-label">Student</label>
				<input type="text" name="student" value="{{ $filters['student'] ?? '' }}" class="form-control form-control-sm" placeholder="Search by name">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">From</label>
				<input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">To</label>
				<input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control form-control-sm">
			</div>
			<div class="col-12 col-md-1 d-flex align-items-end gap-2">
				<button class="btn btn-sm btn-primary" type="submit">Filter</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.reports') }}">Reset</a>
			</div>
		</form>
		<div class="mt-2">
			<a class="btn btn-sm btn-outline-success" href="{{ route('admin.reports.export', request()->query()) }}">Export CSV</a>
		</div>
	</div>
	</div>

<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-sm table-hover mb-0">
				<thead>
					<tr>
						<th>ID</th>
						<th>Student</th>
						<th>Family</th>
						<th>Observer</th>
						<th>Role</th>
						<th>Date</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				@forelse($tests as $t)
					<tr>
						<td>{{ $t->id }}</td>
						<td><a href="{{ route('admin.students.show', $t->student_id) }}">{{ optional($t->student)->name }}</a></td>
						<td>{{ optional(optional($t->student)->family)->name }}</td>
						<td>{{ optional($t->observer)->name }}</td>
						<td>{{ optional($t->observer)->role }}</td>
						<td>{{ $t->test_date }}</td>
						<td><span class="badge bg-{{ $t->status === 'completed' ? 'success' : ($t->status === 'in_progress' ? 'warning' : 'secondary') }}">{{ ucfirst(str_replace('_',' ', $t->status)) }}</span></td>
						<td class="d-flex gap-2">
							@if($t->status === 'in_progress')
								<span class="text-muted">In progress</span>
							@endif
							@if($t->status === 'completed')
								<a class="btn btn-sm btn-outline-primary" href="{{ optional($t->observer)->role === 'teacher' ? route('teacher.tests.result', $t->id) : route('family.tests.result', $t->id) }}">View Result</a>
							@endif
							<form method="post" action="{{ route('admin.tests.delete', $t->id) }}" onsubmit="return confirm('Delete test #{{ $t->id }}? This cannot be undone.')">
								@csrf
								<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
							</form>
						</td>
					</tr>
				@empty
					<tr><td colspan="8" class="text-muted">No tests found</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer">
		{{ $tests->links() }}
	</div>
</div>
@endsection
