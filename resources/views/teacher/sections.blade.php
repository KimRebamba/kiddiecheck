@extends('teacher.layout')

@section('content')
<div class="container mt-4">
  <div class="row mb-4">
    <div class="col-md-12">
      <h3>Sections</h3>
      <p class="text-muted">View your assigned students by section</p>
    </div>
  </div>

  @if($sections->isEmpty())
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body text-center">
            <p class="text-muted mb-0">No sections assigned yet</p>
          </div>
        </div>
      </div>
    </div>
  @else
    <div class="row">
      @foreach($sections as $section)
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">{{ $section->name }}</h5>
            </div>
            <div class="card-body">
              <p class="text-muted">{{ $section->description }}</p>
              <p><strong>Students:</strong> {{ $section->students()->count() }}</p>
              <div class="mt-3">
                <a href="{{ route('teacher.sections.show', $section->id) }}" class="btn btn-sm btn-primary">View Students</a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
