@extends('teacher.layout')



@section('content')

<div class="d-flex align-items-center mb-4">

  <h1 class="h3 mb-0">Sections</h1>

  <div class="ms-auto">

    <a href="{{ route('teacher.sections.create') }}" class="btn btn-primary">Add Section</a>

  </div>

</div>



@if($sections->isEmpty())

  <div class="alert alert-info" role="alert">

    No sections with assigned students.

  </div>

@else

  <div class="row g-3">

    @foreach($sections as $section)

      <div class="col-md-6 col-lg-4">

        <div class="card">

          <div class="card-body">

            <h5 class="card-title">{{ $section->name }}</h5>

            <p class="card-text text-muted">

              <strong>Students:</strong> {{ $section->student_count }}

            </p>

            <!-- Description field removed - not present in database schema -->

            <div class="mt-3">

            <div class="btn-group btn-group-sm" role="group">
              <a href="{{ route('teacher.sections.show', $section->section_id) }}" class="btn btn-outline-primary">View</a>

              <a href="{{ route('teacher.sections.edit', $section->section_id) }}" class="btn btn-outline-secondary">Edit</a>

              <form action="{{ route('teacher.sections.destroy', $section->section_id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this section?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Delete</button>
              </form>
            </div>
          </div>

          </div>

        </div>

      </div>

    @endforeach

  </div>

@endif



<style>

  .card {

    border: none;

    box-shadow: 0 2px 8px rgba(0,0,0,0.1);

    transition: box-shadow 0.3s;

  }

  .card:hover {

    box-shadow: 0 4px 12px rgba(0,0,0,0.15);

  }

  .btn-group-sm .btn {

    padding: 0.25rem 0.5rem;

    font-size: 0.75rem;

  }

</style>

@endsection

