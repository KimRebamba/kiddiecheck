@extends('admin.layout')

@section('content')
@php $isEdit = $mode === 'edit'; @endphp

<h1 class="h4 mb-3">{{ $isEdit ? 'Edit Student' : 'Add New Student' }}</h1>

<form method="post" action="{{ $isEdit ? route('admin.students.update', $student->student_id) : route('admin.students.store') }}">
  @csrf
  @if($isEdit)
    @method('PUT')
  @endif

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <label class="form-label">First name</label>
      <input type="text" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Last name</label>
      <input type="text" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" class="form-control form-control-sm" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Date of birth</label>
      <input type="date" name="date_of_birth" value="{{ old('date_of_birth', isset($student->date_of_birth) ? substr($student->date_of_birth, 0, 10) : '') }}" class="form-control form-control-sm" required>
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
      <label class="form-label">Section</label>
      <select name="section_id" class="form-select form-select-sm" required>
        <option value="">Select section...</option>
        @foreach($sections as $sec)
          <option value="{{ $sec->section_id }}" {{ (string)old('section_id', $student->section_id ?? '') === (string)$sec->section_id ? 'selected' : '' }}>
            {{ $sec->name }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Family</label>
      <select name="family_id" class="form-select form-select-sm" required>
        <option value="">Select family...</option>
        @foreach($families as $f)
          <option value="{{ $f->user_id }}" {{ (string)old('family_id', $student->family_id ?? '') === (string)$f->user_id ? 'selected' : '' }}>
            {{ $f->family_name }} ({{ $f->email }})
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Photo path (optional)</label>
      <input type="text" name="feature_path" value="{{ old('feature_path', $student->feature_path ?? '') }}" class="form-control form-control-sm">
    </div>
  </div>

  <div class="mt-3">
    <button type="submit" class="btn btn-primary btn-sm">{{ $isEdit ? 'Save Changes' : 'Create Student' }}</button>
    <a href="{{ route('admin.students') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
  </div>
</form>
@endsection
