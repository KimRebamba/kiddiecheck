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

.hero {
    background: linear-gradient(135deg, #FF9A5C 0%, #FF6B35 100%);
    border-radius: 24px;
    padding: 26px 28px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 18px;
}

.hero-avatar {
    width: 64px;
    height: 64px;
    border-radius: 999px;
    background: rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
}

.hero-name {
    font-size: 22px;
    font-weight: 800;
    color: #FFFFFF;
}

.hero-sub {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
    margin-top: 2px;
}

.back-btn {
    margin-left: auto;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,0.18);
    color: #FFFFFF;
    font-size: 13px;
    text-decoration: none;
}

.card {
    background: #FFFFFF;
    border-radius: 18px;
    box-shadow: 0 14px 30px rgba(15,23,42,0.08);
    padding: 18px 20px 20px;
    margin-bottom: 16px;
}

.card-title {
    font-weight: 800;
    font-size: 15px;
    margin-bottom: 12px;
}

.c-orange { color: #EA580C; }
.c-green  { color: #15803D; }

.g3, .g2 {
    display: grid;
    gap: 12px 16px;
}

.g3 { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
.g2 { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }

.f .fl {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748B;
}

.f .fv {
    font-size: 14px;
    font-weight: 600;
    color: #0F172A;
}
</style>

@php
    $dob = \Carbon\Carbon::parse($student->date_of_birth);
    $age = $dob->diff(now());
@endphp

<div class="profile-page">

    {{-- Hero --}}
    <div class="hero">
        <div class="hero-avatar">👶</div>
        <div>
            <div class="hero-name">{{ $student->first_name }} {{ $student->last_name }}</div>
            <div class="hero-sub">
                Section: {{ optional($student->section)->name ?? '—' }}
                &nbsp;·&nbsp;
                Age: {{ $age->y }} yrs, {{ $age->m }} mos
            </div>
        </div>
        <a href="{{ route('family.index') }}" class="back-btn">← Back</a>
    </div>

    {{-- Basic Child Info --}}
    <div class="card">
        <div class="card-title c-orange">👤 Child Information</div>
        <div class="g3">
            <div class="f"><span class="fl">First Name</span><div class="fv">{{ $student->first_name }}</div></div>
            <div class="f"><span class="fl">Last Name</span><div class="fv">{{ $student->last_name }}</div></div>
            <div class="f"><span class="fl">Date of Birth</span><div class="fv">{{ $dob->format('F j, Y') }}</div></div>
        </div>
    </div>

    {{-- Family & Emergency --}}
    <div class="card">
        <div class="card-title c-green">🏠 Family & Emergency Contact</div>
        <div class="g2">
            <div class="f"><span class="fl">Family Name</span><div class="fv">{{ optional($student->family)->family_name ?? '—' }}</div></div>
            <div class="f"><span class="fl">Home Address</span><div class="fv">{{ optional($student->family)->home_address ?? '—' }}</div></div>
            <div class="f"><span class="fl">Emergency Contact</span><div class="fv">{{ optional($student->family)->emergency_contact ?? '—' }}</div></div>
            <div class="f"><span class="fl">Emergency Phone</span><div class="fv">{{ optional($student->family)->emergency_phone ?? '—' }}</div></div>
        </div>
    </div>

</div>

@endsection