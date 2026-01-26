@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Sections</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.index') }}">Dashboard</a>
	</div>
</div>

<div class="card mb-3">
	<div class="card-body">
		<form class="row g-2" method="get" action="{{ route('admin.sections') }}">
			<div class="col-12 col-md-4">
				<label class="form-label">Name</label>
				<input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="form-control form-control-sm" placeholder="Search name">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">Min Students</label>
				<input type="number" name="min" value="{{ $filters['min'] ?? '' }}" class="form-control form-control-sm" min="0">
			</div>
			<div class="col-6 col-md-2">
				<label class="form-label">Max Students</label>
				<input type="number" name="max" value="{{ $filters['max'] ?? '' }}" class="form-control form-control-sm" min="0">
			</div>
			<div class="col-12 col-md-4 d-flex align-items-end gap-2">
				<button class="btn btn-sm btn-primary" type="submit">Filter</button>
				<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.sections') }}">Reset</a>
				<a class="btn btn-sm btn-outline-success" href="{{ route('admin.sections.export', request()->query()) }}">Export CSV</a>
			</div>
		</form>
	</div>
</div>

<div class="card mb-3">
	<div class="card-header bg-light">Create Section</div>
	<div class="card-body">
		<form class="row g-2" method="POST" action="{{ route('admin.sections.store') }}">
			@csrf
			<div class="col-12 col-md-4">
				<label class="form-label">Name</label>
				<input type="text" name="name" class="form-control form-control-sm" required>
			</div>
			<div class="col-12 col-md-8">
				<label class="form-label">Description</label>
				<input type="text" name="description" class="form-control form-control-sm">
			</div>
			<div class="col-12">
				<button class="btn btn-primary btn-sm" type="submit">Create Section</button>
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
						<th>Description</th>
						<th>Students</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
					@forelse($sections as $s)
						<tr>
							<td>{{ $s->id }}</td>
							<td>
								<form method="POST" action="{{ route('admin.sections.update', $s->id) }}" class="d-flex gap-2 align-items-center">
									@csrf
									<input type="text" name="name" value="{{ $s->name }}" class="form-control form-control-sm" style="min-width:160px">
									<input type="text" name="description" value="{{ $s->description }}" class="form-control form-control-sm" style="min-width:220px">
									<button class="btn btn-sm btn-outline-primary" type="submit">Update</button>
								</form>
							</td>
							<td>{{ $s->description }}</td>
							<td><span class="badge bg-primary">{{ $s->students_count }}</span></td>
							<td class="d-flex gap-2">
								<a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.sections.students', $s->id) }}">View Students</a>
								<form method="POST" action="{{ route('admin.sections.delete', $s->id) }}" onsubmit="return confirm('Delete this section? It must have no students.')">
									@csrf
									<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
								</form>
							</td>
						</tr>
					@empty
						<tr><td colspan="5" class="text-muted">No sections found</td></tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
	<div class="card-footer">{{ $sections->links() }}</div>
</div>
@endsection
