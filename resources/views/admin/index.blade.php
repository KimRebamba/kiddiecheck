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

 
@endsection
