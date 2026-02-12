@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <h3>Families</h3>
      <p class="text-muted">View families and their assigned students</p>
    </div>
  </div>

  @if($families->isEmpty())
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-muted mb-0">No families assigned yet</p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      @foreach($families as $family)
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">{{ $family->user->name }}</h5>
            </div>
            <div class="card-body">
              <p><strong>Address:</strong> {{ $family->user->profile_path ?? 'N/A' }}</p>
              <p><strong>Students Assigned:</strong> {{ $family->students->count() }}</p>
              <div class="mt-3">
                <a href="{{ route('teacher.family.show', $family->id) }}" class="btn btn-sm btn-primary">View Details</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
