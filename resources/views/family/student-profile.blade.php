@extends('family.layout')

@section('title', 'Student Profile')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Baloo+2:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

.profile-page {
    max-width: 900px;
    margin: 0 auto;
    padding: 28px 20px 60px;
    font-family: 'Nunito', sans-serif;
    color: #1E293B;
}

/* Hero */
.hero {
    background: linear-gradient(135deg, #FF9A5C 0%, #FF6B35 100%);
    border-radius: 24px;
    padding: 26px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 18px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(255,107,53,0.28);
}
.hero::after {
    content: '';
    position: absolute; top: -50px; right: -50px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.1);
    pointer-events: none;
}
.hero-avatar {
    width: 68px; height: 68px; border-radius: 50%;
    background: rgba(255,255,255,0.22);
    border: 3px solid rgba(255,255,255,0.45);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; flex-shrink: 0;
}
.hero-name {
    font-family: 'Baloo 2', cursive;
    font-weight: 800; font-size: 1.6rem; color: #fff; line-height: 1.2;
}
.hero-sub { color: rgba(255,255,255,0.85); font-size: 0.88rem; font-weight: 600; margin-top: 3px; }
.back-btn {
    margin-left: auto; flex-shrink: 0;
    background: rgba(255,255,255,0.22);
    color: #fff; border: 2px solid rgba(255,255,255,0.4);
    border-radius: 100px; padding: 8px 18px;
    font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.85rem;
    text-decoration: none; transition: background 0.15s;
}
.back-btn:hover { background: rgba(255,255,255,0.38); }

/* Card */
.card {
    background: #fff;
    border-radius: 20px;
    padding: 22px 26px;
    margin-bottom: 14px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
}
.card-title {
    font-family: 'Baloo 2', cursive;
    font-weight: 800; font-size: 0.8rem;
    text-transform: uppercase; letter-spacing: 0.07em;
    padding-bottom: 12px; margin-bottom: 16px;
    border-bottom: 2px dashed #F1F5F9;
    display: flex; align-items: center; gap: 7px;
}
.c-orange { color: #FF6B35; }
.c-blue   { color: #3B82F6; }
.c-green  { color: #10B981; }
.c-pink   { color: #EC4899; }

/* Grids */
.g2 { display: grid; grid-template-columns: 1fr 1fr;           gap: 14px; }
.g3 { display: grid; grid-template-columns: 1fr 1fr 1fr;       gap: 14px; }
.g4 { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr;   gap: 12px; }
.mt { margin-top: 16px; }

/* Field */
.f  { display: flex; flex-direction: column; gap: 3px; }
.fl { font-size: 0.7rem; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.06em; }
.fv { font-size: 0.93rem; font-weight: 700; color: #1E293B; padding: 7px 0 5px; border-bottom: 2px solid #F1F5F9; min-height: 32px; }
.fv.na { color: #CBD5E1; font-style: italic; font-weight: 400; }

/* Checkboxes */
.checks   { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 6px; }
.chk-item { display: flex; align-items: center; gap: 6px; font-size: 0.87rem; font-weight: 700; color: #475569; }
.box      { width: 22px; height: 22px; border-radius: 6px; border: 2.5px solid #E2E8F0; background: #F8FAFC; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0; }
.box.on   { background: #FFF7ED; border-color: #FF9A5C; color: #FF6B35; font-weight: 900; }
</style>

<div class="profile-page">

@php
    $dob = \Carbon\Carbon::parse($student->date_of_birth);
    $age = $dob->diff(now());
    $v   = fn($x) => empty($x) ? false : true;   // has value?
    $is  = fn($field, $opt) => strtolower($student->$field ?? '') === strtolower($opt);
@endphp

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-avatar">👶</div>
        <div>
            <div class="hero-name">{{ $student->first_name }} {{ $student->last_name }}</div>
            <div class="hero-sub">
                Section: {{ $student->section->name ?? '—' }}
                &nbsp;·&nbsp;
                Age: {{ $age->y }} yrs, {{ $age->m }} mos
            </div>
        </div>
        <a href="{{ route('family.index') }}" class="back-btn">← Back</a>
    </div>

    {{-- Child Info --}}
    <div class="card">
        <div class="card-title c-orange">👤 Child Information</div>
        <div class="g3">
            <div class="f"><span class="fl">First Name</span><div class="fv">{{ $student->first_name }}</div></div>
            <div class="f"><span class="fl">Last Name</span><div class="fv">{{ $student->last_name }}</div></div>
            <div class="f"><span class="fl">Date of Birth</span><div class="fv">{{ $dob->format('F j, Y') }}</div></div>
        </div>
        <div class="g3 mt">
            <div class="f">
                <span class="fl">Gender</span>
                <div class="fv {{ $v($student->gender ?? null) ? '' : 'na' }}">{{ $student->gender ?? 'Not specified' }}</div>
            </div>
            <div class="f">
                <span class="fl">Birth Order</span>
                <div class="fv {{ $v($student->birth_order ?? null) ? '' : 'na' }}">{{ $student->birth_order ?? '—' }}</div>
            </div>
            <div class="f">
                <span class="fl">No. of Siblings</span>
                <div class="fv {{ $v($student->siblings_count ?? null) ? '' : 'na' }}">{{ $student->siblings_count ?? '—' }}</div>
            </div>
        </div>

        <div class="f mt">
            <span class="fl">Child's Handedness</span>
            <div class="checks">
                @foreach(['Right','Left','Both','Not yet established'] as $h)
                <div class="chk-item">
                    <div class="box {{ $is('handedness', $h) ? 'on' : '' }}">{{ $is('handedness', $h) ? '✓' : '' }}</div>
                    {{ $h }}
                </div>
                @endforeach
            </div>
        </div>

        <div class="f mt">
            <span class="fl">Is the child presently studying?</span>
            <div class="checks">
                <div class="chk-item">
                    <div class="box {{ ($student->is_studying ?? false) ? 'on' : '' }}">{{ ($student->is_studying ?? false) ? '✓' : '' }}</div> Yes
                </div>
                <div class="chk-item">
                    <div class="box {{ !($student->is_studying ?? false) ? 'on' : '' }}">{{ !($student->is_studying ?? false) ? '✓' : '' }}</div> No
                </div>
            </div>
            @if(!empty($student->school_name))
            <div class="f mt">
                <span class="fl">School / Learning Center / Day Care</span>
                <div class="fv">{{ $student->school_name }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Address --}}
    <div class="card">
        <div class="card-title c-blue">📍 Address</div>
        <div class="g4">
            <div class="f"><span class="fl">Barangay</span><div class="fv {{ $v($student->barangay ?? null) ? '' : 'na' }}">{{ $student->barangay ?? '—' }}</div></div>
            <div class="f"><span class="fl">Municipality/City</span><div class="fv {{ $v($student->city ?? null) ? '' : 'na' }}">{{ $student->city ?? '—' }}</div></div>
            <div class="f"><span class="fl">Province</span><div class="fv {{ $v($student->province ?? null) ? '' : 'na' }}">{{ $student->province ?? '—' }}</div></div>
            <div class="f"><span class="fl">Region</span><div class="fv {{ $v($student->region ?? null) ? '' : 'na' }}">{{ $student->region ?? '—' }}</div></div>
        </div>
    </div>

    {{-- Father --}}
    <div class="card">
        <div class="card-title c-blue">👨 Father's Information</div>
        <div class="g2">
            <div class="f"><span class="fl">Father's Name</span><div class="fv {{ $v($student->father_name ?? null) ? '' : 'na' }}">{{ $student->father_name ?? '—' }}</div></div>
            <div class="f"><span class="fl">Age</span><div class="fv {{ $v($student->father_age ?? null) ? '' : 'na' }}">{{ $student->father_age ?? '—' }}</div></div>
            <div class="f"><span class="fl">Occupation</span><div class="fv {{ $v($student->father_occupation ?? null) ? '' : 'na' }}">{{ $student->father_occupation ?? '—' }}</div></div>
            <div class="f"><span class="fl">Educational Attainment</span><div class="fv {{ $v($student->father_education ?? null) ? '' : 'na' }}">{{ $student->father_education ?? '—' }}</div></div>
        </div>
    </div>

    {{-- Mother --}}
    <div class="card">
        <div class="card-title c-pink">👩 Mother's Information</div>
        <div class="g2">
            <div class="f"><span class="fl">Mother's Name</span><div class="fv {{ $v($student->mother_name ?? null) ? '' : 'na' }}">{{ $student->mother_name ?? '—' }}</div></div>
            <div class="f"><span class="fl">Age</span><div class="fv {{ $v($student->mother_age ?? null) ? '' : 'na' }}">{{ $student->mother_age ?? '—' }}</div></div>
            <div class="f"><span class="fl">Occupation</span><div class="fv {{ $v($student->mother_occupation ?? null) ? '' : 'na' }}">{{ $student->mother_occupation ?? '—' }}</div></div>
            <div class="f"><span class="fl">Educational Attainment</span><div class="fv {{ $v($student->mother_education ?? null) ? '' : 'na' }}">{{ $student->mother_education ?? '—' }}</div></div>
        </div>
    </div>

    {{-- Family & Emergency --}}
    <div class="card">
        <div class="card-title c-green">🏠 Family & Emergency Contact</div>
        <div class="g2">
            <div class="f"><span class="fl">Family Name</span><div class="fv {{ $v($student->family->family_name ?? null) ? '' : 'na' }}">{{ $student->family->family_name ?? '—' }}</div></div>
            <div class="f"><span class="fl">Home Address</span><div class="fv {{ $v($student->family->home_address ?? null) ? '' : 'na' }}">{{ $student->family->home_address ?? '—' }}</div></div>
            <div class="f"><span class="fl">Emergency Contact</span><div class="fv {{ $v($student->family->emergency_contact ?? null) ? '' : 'na' }}">{{ $student->family->emergency_contact ?? '—' }}</div></div>
            <div class="f"><span class="fl">Emergency Phone</span><div class="fv {{ $v($student->family->emergency_phone ?? null) ? '' : 'na' }}">{{ $student->family->emergency_phone ?? '—' }}</div></div>
        </div>
    </div>

</div>

@endsection