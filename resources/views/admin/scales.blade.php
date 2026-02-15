@extends('admin.layout')

@section('content')
  <h1 class="h4 mb-3">Scales</h1>
  <p class="text-muted mb-2">Manage assessment scales and view ECCD data.</p>
  <a href="{{ route('admin.eccd') }}" class="btn btn-outline-primary btn-sm">Open ECCD Scale Explorer</a>
@endsection
