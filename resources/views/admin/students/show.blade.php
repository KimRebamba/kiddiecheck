@extends('admin.layout')

@section('content')
<h1>Student</h1>
@if($student)
  <p>ID: {{ $student->id }}</p>
  <p>Name: {{ $student->name }}</p>
  <p>Gender: {{ $student->gender }}</p>
  <p>Status: {{ $student->status }}</p>
  <p>Section: {{ $student->section?->name }}</p>
  <p>Family: {{ $student->family?->name }}</p>
  <p>Teachers: {{ $student->teachers->pluck('user.name')->join(', ') }}</p>
  <p>Tags: {{ $student->tags->pluck('tag_type')->join(', ') }}</p>
  <p>Tests: {{ $student->tests->count() }}</p>
  <p><a href="{{ route('admin.students.record', $student->id) }}">View ECCD Record</a></p>
@endif
@endsection
