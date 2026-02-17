@extends('teacher.layout')

@section('content')
<div class="d-flex align-items-center mb-4">
  <h1 class="h3 mb-0">Edit Section</h1>
  <div class="ms-auto">
    <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Section Information</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('teacher.sections.update', $section->section_id) }}" method="POST">
          @csrf
          @method('PUT')
          
          <div class="mb-3">
            <label for="name" class="form-label">Section Name *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $section->name) }}" required maxlength="255">
            @error('name')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Section</button>
            <a href="{{ route('teacher.sections') }}" class="btn btn-outline-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
@endsection
