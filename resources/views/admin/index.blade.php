@extends('admin.layout')

@section('content')
<div class="d-flex align-items-center mb-3">
	<h1 class="h3 mb-0">Admin Dashboard</h1>
	<div class="ms-auto">
		<a class="btn btn-sm btn-outline-secondary" href="{{ route('index') }}">Home</a>
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Users</div><div class="display-6">{{ $userCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Families</div><div class="display-6">{{ $familyCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Teachers</div><div class="display-6">{{ $teacherCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Students</div><div class="display-6">{{ $studentCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Domains</div><div class="display-6">{{ $domainCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Questions</div><div class="display-6">{{ $questionCount }}</div></div></div>
	</div>
	<div class="col-6 col-md-3">
		<div class="card text-center"><div class="card-body"><div class="fw-semibold">Tests</div><div class="display-6">{{ $testCount }}</div></div></div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<h2 class="h5">Manage</h2>
		<div class="d-flex flex-wrap gap-2">
			<a class="btn btn-primary" href="{{ route('admin.users') }}">Users</a>
			<a class="btn btn-primary" href="{{ route('admin.families') }}">Families</a>
			<a class="btn btn-primary" href="{{ route('admin.sections') }}">Sections</a>
			<a class="btn btn-primary" href="{{ route('admin.teachers') }}">Teachers</a>
			<a class="btn btn-outline-primary" href="{{ route('admin.domains') }}">Domains</a>
			<a class="btn btn-outline-primary" href="{{ route('admin.reports') }}">Reports</a>
		</div>
	</div>
</div>

<div class="row g-3 mt-3">
	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header bg-light">In-Progress Tests</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-sm table-hover mb-0">
						<thead>
							<tr>
								<th>Child</th>
								<th>Observer</th>
								<th>Date</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($inProgressTests as $t)
								<tr>
									<td><a href="{{ route('admin.students.show', $t->student_id) }}">{{ $t->student->name }}</a></td>
									<td>{{ optional($t->observer)->name }} ({{ optional($t->observer)->role }})</td>
									<td>{{ $t->test_date }}</td>
									<td class="d-flex gap-2">
										<span class="text-muted">In progress</span>
										<form method="post" action="{{ route('admin.tests.delete', $t->id) }}">
											@csrf
											<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
										</form>
									</td>
								</tr>
							@empty
								<tr><td colspan="4" class="text-muted">No in-progress tests</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header bg-light">Scheduled (Pending) Tests</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-sm table-hover mb-0">
						<thead>
							<tr>
								<th>Child</th>
								<th>Window Start</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							@forelse($pendingTests as $t)
								<tr>
									<td><a href="{{ route('admin.students.show', $t->student_id) }}">{{ $t->student->name }}</a></td>
									<td>{{ $t->test_date }}</td>
									<td><span class="badge bg-secondary">Pending</span></td>
								</tr>
							@empty
								<tr><td colspan="3" class="text-muted">No pending tests</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row g-3 mt-3">
	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header bg-light">Recent Completed Tests</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-sm table-hover mb-0">
						<thead>
							<tr>
								<th>Child</th>
								<th>Observer</th>
								<th>Date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@forelse($recentCompleted as $t)
								<tr>
									<td><a href="{{ route('admin.students.show', $t->student_id) }}">{{ $t->student->name }}</a></td>
									<td>{{ optional($t->observer)->name }} ({{ optional($t->observer)->role }})</td>
									<td>{{ $t->test_date }}</td>
									<td><a class="btn btn-sm btn-outline-primary" href="{{ optional($t->observer)->role === 'teacher' ? route('teacher.tests.result', $t->id) : route('family.tests.result', $t->id) }}">View Result</a></td>
								</tr>
							@empty
								<tr><td colspan="4" class="text-muted">No recent results</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12 col-lg-6">
		<div class="card">
			<div class="card-header bg-light">Unassigned Students</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-sm table-hover mb-0">
						<thead>
							<tr>
								<th>Child</th>
								<th>Family</th>
								<th>Section</th>
								<th>Manage</th>
							</tr>
						</thead>
						<tbody>
							@forelse($unassignedStudents as $s)
								<tr>
									<td><a href="{{ route('admin.students.show', $s->id) }}">{{ $s->name }}</a></td>
									<td>{{ optional($s->family)->name ?? '—' }}</td>
									<td>{{ optional($s->section)->name ?? '—' }}</td>
									<td><a class="btn btn-sm btn-primary" href="{{ route('admin.teachers') }}">Assign Teacher</a></td>
								</tr>
							@empty
								<tr><td colspan="4" class="text-muted">All students are assigned</td></tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row g-3 mt-3">
	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header bg-light">Families</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					@forelse($familiesSummary as $f)
						<li class="list-group-item d-flex justify-content-between align-items-center">
							{{ $f->name }}
							<span class="badge bg-primary">{{ $f->students_count }} students</span>
						</li>
					@empty
						<li class="list-group-item text-muted">No families</li>
					@endforelse
				</ul>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header bg-light">Teachers</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					@forelse($teachersSummary as $t)
						<li class="list-group-item d-flex justify-content-between align-items-center">
							{{ optional($t->user)->name ?? 'Teacher #'.$t->id }}
							<span class="badge bg-primary">{{ $t->students_count }} students</span>
						</li>
					@empty
						<li class="list-group-item text-muted">No teachers</li>
					@endforelse
				</ul>
			</div>
		</div>
	</div>
	<div class="col-12 col-lg-4">
		<div class="card">
			<div class="card-header bg-light">Sections</div>
			<div class="card-body p-0">
				<ul class="list-group list-group-flush">
					@forelse($sectionsSummary as $sec)
						<li class="list-group-item d-flex justify-content-between align-items-center">
							{{ $sec->name }}
							<span class="badge bg-primary">{{ $sec->students_count }} students</span>
						</li>
					@empty
						<li class="list-group-item text-muted">No sections</li>
					@endforelse
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection
