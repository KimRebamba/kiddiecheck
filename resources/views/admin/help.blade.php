@extends('admin.layout')

@section('content')
<h1>Help</h1>
<p>This admin area manages KiddieCheck data:</p>
<ul>
	<li>Users: all accounts with roles (family, teacher, admin).</li>
	<li>Families: one account per family; students belong to families.</li>
	<li>Teachers: linked 1:1 to a user; assigned to students.</li>
	<li>Sections: class sections that group students.</li>
	<li>Reports: monthly tests, responses, and domain scores.</li>
	<li>Profile: current user details; update name/profile path.</li>
</ul>
<p>Navigation: use the header links to open each section. Forms are simple and functional; submit to create or update records.</p>
@endsection
