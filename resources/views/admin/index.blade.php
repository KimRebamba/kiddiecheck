@extends('admin.layout')

@section('content')
<style>
  .dash-wrap { padding: 14px; }
  .dash-top { margin-bottom: 50px; text-align: center; }
  .dash-top .hello { font-weight:400; font-size:18px; color:#cfc9f0; }
  .dash-top .date { color: var(--text); font-size:45px; font-weight:800; }

  .grid { display:grid; gap:12px; grid-template-columns: repeat(3, minmax(0, 1fr));}
  .grid-table { display:grid; gap:12px; grid-template-columns: repeat(2, minmax(0, 1fr));}
  @media (max-width: 1100px) { .grid { grid-template-columns: repeat(3, 1fr); } }
  @media (max-width: 768px) { .grid { grid-template-columns: repeat(2, 1fr); } }
  .cardy { background: #5855df; color: var(--text); border-radius: 18px; padding: 16px; }
  .cardy .label { font-size: 23px; color: white; font-weight:700; }
  .cardy .value { font-size: 36px; font-weight: 800;     text-align: end;}
  .cardy.alt { background: #55df85ac; }
  .cardy.alt1 { background: #df9a55a8; }
  .cardy.alt2 { background: #df55d78c; }
  .cardy.alt3 { background: #df5555c2; }
  .cardy.alt4 { background: #df557dbd; }
  .cardy.alt5 { background: #df55d78c; }
  .text-muted {
    --bs-text-opacity: 1;
    color: white !important;
}

ul.pagination{
  margin: 0;
}
p.small.text-muted{
  margin: 0;
}

  .actions { display:flex; flex-wrap:wrap; gap:8px; margin: 16px 0 8px; }
  .act { background: var(--hover); color: var(--text); border: none; border-radius: 999px; padding: 10px 16px; text-decoration:none; font-weight:700;}
  .act:hover { opacity: 0.95; }
  .section-title { font-weight:800; color: var(--text); margin: 18px 0 10px; font-size:20px; }
  .table-card { background: #fff; border-radius: 14px; }
  .table-card .card-header { border-bottom: 1px solid #eee; padding: 10px 12px; color:#4a3e87; font-weight:600; }
  .table-card .table { margin-bottom:0; }
  
  .footer-links { display:flex; flex-wrap:wrap; gap:16px; margin-top: 50px; color: #5c59ef;     justify-content: center;}
  .footer-links a { color:#5c59ef; text-decoration:none; font-weight:600; }
  .footer-links a:hover { text-decoration:underline; }
  .subnote { font-size: 17px; color:#d6d1f3; font-style: italic;font-weight:480; }
  .yellow{
	color: #f9d976 !important;
  }


  .table-card {
    background: #5855df;
    border-radius: 14px;
}

.table {
    --bs-table-color-type: initial;
    --bs-table-bg-type: initial;
    --bs-table-color-state: initial;
    --bs-table-bg-state: initial;
    --bs-table-color: white;
    --bs-table-bg: none;
    --bs-table-border-color: var(--bs-border-color);
    --bs-table-accent-bg: transparent;
    --bs-table-striped-color: var(--bs-emphasis-color);
    --bs-table-striped-bg: rgba(var(--bs-emphasis-color-rgb), 0.05);
    --bs-table-active-color: var(--bs-emphasis-color);
    --bs-table-active-bg: rgba(var(--bs-emphasis-color-rgb), 0.1);
    --bs-table-hover-color: var(--bs-emphasis-color);
    --bs-table-hover-bg: rgba(var(--bs-emphasis-color-rgb), 0.075);
    width: 100%;
    margin-bottom: 1rem;
    vertical-align: top;
    border-color: var(--bs-table-border-color);
}
.table>thead {
    vertical-align: middle;
}
table {
  border-collapse: collapse;
}

th, td {
  border: none !important; /* This removes the lines */
}

tr{
	padding-top:5px !important;
}

.table-card .card-header {
    border-bottom: 1px solid #eee;
    padding: 10px 12px;
    color: white;
    font-weight: 600;
	font-size:20px;
}

.pagination {
    --bs-pagination-padding-x: 0.75rem;
    --bs-pagination-padding-y: 0.375rem;
    --bs-pagination-font-size: 1rem;
    --bs-pagination-color: #f9d976;;
    --bs-pagination-bg: none;
    --bs-pagination-border-width: #f9d976;
    --bs-pagination-border-color: #f9d976;
    --bs-pagination-border-radius: var(--bs-border-radius);

    --bs-pagination-hover-color: #4747b9;
    --bs-pagination-hover-bg: #f9d976;
    --bs-pagination-hover-border-color: var(--bs-border-color);
    
    --bs-pagination-focus-color: #4747b9;
    --bs-pagination-focus-bg: #f9d976;

    --bs-pagination-active-color: #f9d976;
    --bs-pagination-active-bg: #4747b9;
    --bs-pagination-active-border-color: #f9d976;
    
    --bs-pagination-disabled-color: #f9d976;
    --bs-pagination-disabled-bg: #4747b9;
    --bs-pagination-disabled-border-color: var(--bs-border-color);
    display: flex;
    padding-left: 0;
    list-style: none;
}

.john{
  margin-top: 20px;
}
</style>

<div class="dash-wrap">
  <div class="dash-top">
    <div class="hello">Hello, Admin! Today is …</div>
    <div class="date">{{ $today }}</div>
  </div>

  <!-- Top counters -->
  <div class="grid" style="margin-bottom: 10px;">
    <div class="cardy"><div class="label"><span class="yellow">#</span> Students</div><div class="value">{{ $studentCount }}</div></div>
    <div class="cardy"><div class="label"><span class="yellow">#</span> Teachers</div><div class="value">{{ $teacherCount }}</div></div>
    <div class="cardy"><div class="label"><span class="yellow">#</span> Families</div><div class="value">{{ $familyCount }}</div></div>
  </div>

  <div class="section-title">Test Summaries</div>
  <div class="grid">
    <div class="cardy alt"><div class="label"><span class="yellow">#</span> Completed</div><div class="value">{{ $completedCount }}</div></div>
    <div class="cardy alt1"><div class="label"><span class="yellow">#</span> In Progress</div><div class="value">{{ $inProgressCount }}</div></div>
    <div class="cardy alt2"><div class="label"><span class="yellow">#</span> Students Eligible for Test <span class="subnote">(For Teachers)</span></div>
	<div class="value">{{ $eligibleTeacherCount }}</div></div>
    <div class="cardy alt3"><div class="label"><span class="yellow">#</span> Canceled</div><div class="value">{{ $cancelledCount }}</div></div>
    <div class="cardy alt4"><div class="label"><span class="yellow">#</span> Terminated</div><div class="value">{{ $terminatedCount }}</div></div>
    <div class="cardy alt5"><div class="label"><span class="yellow">#</span> Students Eligible for Test <span class="subnote">(For Families)</span></div>
	<div class="value">{{ $eligibleFamilyCount }}</div></div>
  </div>

  <div class="section-title">Recent Activities</div>

  <div class="grid-table">
    <div class="table-card">
      <div class="card-header"><span class="yellow"><i class="bi bi-database"></i></span> Tests</div>
      <div class="card-body px-2 py-2">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr class ="yellow">
                <th>Test ID</th>
                <th>Student Name</th>
                <th>Test Date</th>
                <th>Assessment Period</th>
                <th>Examiner Role</th>
                <th>Progress</th>
                <th>Status</th>
                <th>Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($testsTable as $row)
                <tr>
                  <td>{{ $row['id'] }}</td>
                  <td>{{ $row['student'] ?? '—' }}</td>
                  <td>{{ $row['date_fmt'] ?? '—' }}</td>
                  <td>{{ $row['period_str'] ?? '—' }}</td>
                  <td>{{ $row['examiner_role'] ?? '—' }}</td>
                  <td>{{ $row['progress'] }}%</td>
                  <td>{{ ucfirst(str_replace('_',' ', $row['status'])) }}</td>
                  <td class="flex">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.tests.result', $row['id']) }}">View</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="8" class="text-muted">No test records</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-2 py-2 john">{{ $testsTable->links() }}</div>
      </div>
    </div>

    <div class="table-card">
      <div class="card-header"><span class="yellow"><i class="bi bi-database"></i></span> Students</div>
      <div class="card-body px-2 py-2">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Section Name</th>
                <th>Last Test</th>
                <th>Tests Left</th>
                <th>Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($studentsTable as $row)
                <tr>
                  <td>{{ $row['id'] }}</td>
                  <td>{{ $row['name'] }}</td>
                  <td>{{ $row['section'] ?? '—' }}</td>
                  <td>{{ $row['last_test_fmt'] ?? 'None' }}</td>
                  <td>{{ $row['tests_left'] }}</td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.students.show', $row['id']) }}">View</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-muted">No student records</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-2 py-2 john">{{ $studentsTable->links() }}</div>
      </div>
    </div>

    <div class="table-card">
      <div class="card-header"><span class="yellow"><i class="bi bi-database"></i></span> Teachers</div>
      <div class="card-body px-2 py-3">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Teacher ID</th>
                <th>Teacher Name</th>
                <th>Name (User)</th>
                <th>Assigned Students</th>
                <th>Tests Done</th>
                <th>Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($teachersTable as $row)
                <tr>
                  <td>{{ $row['id'] }}</td>
                  <td>{{ $row['teacher_name'] }}</td>
                  <td>{{ $row['user_name'] ?? '—' }}</td>
                  <td>{{ $row['assigned_students'] }}</td>
                  <td>{{ $row['tests_done'] }}</td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.teachers') }}">View</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-muted">No teacher records</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-2 py-2 john">{{ $teachersTable->links() }}</div>
      </div>
    </div>

    <div class="table-card">
      <div class="card-header"><span class="yellow"><i class="bi bi-database"></i></span> Families</div>
      <div class="card-body  px-2 py-3">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr>
                <th>Family ID</th>
                <th>Family Name</th>
                <th>Num of Children</th>
                <th>Last Test</th>
                <th>Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($familiesTable as $row)
                <tr>
                  <td>{{ $row['id'] }}</td>
                  <td>{{ $row['name'] }}</td>
                  <td>{{ $row['children'] }}</td>
                  <td>{{ $row['last_test_fmt'] ?? 'None' }}</td>
                  <td>
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.families') }}">View</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-muted">No family records</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-2 py-2 john">{{ $familiesTable->links() }}</div>
      </div>
    </div>

    <div class="table-card">
      <div class="card-header"><span class="yellow"><i class="bi bi-database"></i></span> Users</div>
      <div class="card-body px-2 py-3">
        <div class="table-responsive">
          <table class="table table-sm table-hover mb-0">
            <thead>
              <tr style=" padding: .3rem .25rem; vertical-align: middle;">
                <th>User ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Quick Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($usersTable as $u)
                <tr>
                  <td>{{ $u->id }}</td>
                  <td>{{ $u->name }}</td>
                  <td>{{ $u->email }}</td>
                  <td>{{ $u->role }}</td>
                  <td class="d-flex gap-2">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users') }}">View</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.users') }}">Edit</a>
                 
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-muted">No user records</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="px-2 py-2 john">{{ $usersTable->links() }}</div>
      </div>
    </div>
  </div>

  <div class="section-title">Quick Actions</div>
  <div class="actions">
    <a class="act" href="{{ route('admin.users') }}"><span class="yellow">+</span> Add User</a>
    <a class="act" href="{{ route('admin.sections') }}"><span class="yellow">+</span> Add Student</a>
    <a class="act" href="{{ route('admin.teachers') }}"><span class="yellow">+</span> Add Teacher</a>
    <a class="act" href="{{ route('admin.families') }}"><span class="yellow">+</span> Add Family</a>
    <a class="act" href="{{ route('admin.sections') }}"><span class="yellow">+</span> Add Section</a>
  </div>


  <div class="footer-links">
    <span>Powered by <span style="color: #e4c66c">Kiddie ✓</span></span>
    <a href="https://github.com/" target="_blank" rel="noopener">Github</a>
    <a href="{{ route('admin.reports') }}">ECCD Resources</a>
    <a href="{{ route('admin.reports') }}">Disclaimer</a>
    <a href="{{ route('admin.reports') }}">Research Paper</a>
  </div>
</div>
@endsection
