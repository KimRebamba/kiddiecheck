@extends('family.layout')

@section('title', 'Our Family')

@section('content')
<style>
    :root {
        --font: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    body { background: #fffbf0; font-family: var(--font); }

    .fp-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.2rem 5rem;
    }

    .fp-banner {
        background: linear-gradient(135deg, #ff8fab 0%, #ffca3a 60%, #8ac926 100%);
        border-radius: 32px;
        padding: 2rem 2rem 2.4rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 6px 28px rgba(255,143,171,0.28);
    }

    .fp-banner::before {
        content: ' ';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.12);
        border-radius: 50%;
        pointer-events: none;
    }

    .fp-banner::after {
        content: ' ';
        position: absolute;
        bottom: -50px;
        left: 30%;
        width: 160px;
        height: 160px;
        background: rgba(255,255,255,0.10);
        border-radius: 50%;
        pointer-events: none;
    }

    .fp-banner-inner {
        position: relative;
        z-index: 1;
    }

    .fp-greeting {
        font-size: 1.9rem;
        font-weight: 900;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 0.3rem;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .fp-sub {
        font-size: 0.88rem;
        color: rgba(255,255,255,0.9);
        font-weight: 700;
        margin-bottom: 1.2rem;
    }

    .fp-pills {
        display: flex;
        gap: 0.7rem;
        flex-wrap: wrap;
    }

    .fp-pill {
        background: rgba(255,255,255,0.26);
        border: 2px solid rgba(255,255,255,0.48);
        border-radius: 50px;
        padding: 0.38rem 1rem;
        font-size: 0.78rem;
        font-weight: 800;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .fp-section-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.2rem;
    }

    .fp-section-icon {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .fp-section-title {
        font-size: 1rem;
        font-weight: 800;
        color: #333;
        letter-spacing: -0.01em;
    }

    .children-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.2rem;
    }

    .child-card {
        background: white;
        border-radius: 24px;
        padding: 1.6rem 1.2rem 1.4rem;
        text-align: center;
        text-decoration: none;
        color: inherit;
        display: block;
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        animation: popIn 0.4s cubic-bezier(.34,1.56,.64,1) both;
    }

    .child-card:hover {
        transform: translateY(-7px) scale(1.02);
        box-shadow: 0 16px 36px rgba(0,0,0,0.11);
        text-decoration: none;
        color: inherit;
    }

    .child-card.c0:hover { border-color: #ff8fab; }
    .child-card.c1:hover { border-color: #ffca3a; }
    .child-card.c2:hover { border-color: #8ac926; }
    .child-card.c3:hover { border-color: #ff8fab; }
    .child-card.c4:hover { border-color: #ffca3a; }
    .child-card.c5:hover { border-color: #8ac926; }

    .card-top {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 52px;
        border-radius: 22px 22px 0 0;
        pointer-events: none;
    }

    .c0 .card-top { background: linear-gradient(90deg, #ff8fab, #ffb3c6); }
    .c1 .card-top { background: linear-gradient(90deg, #ffca3a, #ffe08a); }
    .c2 .card-top { background: linear-gradient(90deg, #8ac926, #b5e550); }
    .c3 .card-top { background: linear-gradient(90deg, #ff8fab, #ffca3a); }
    .c4 .card-top { background: linear-gradient(90deg, #ffca3a, #8ac926); }
    .c5 .card-top { background: linear-gradient(90deg, #8ac926, #ff8fab); }

    .child-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        margin: 0.6rem auto 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.3rem;
        background: white;
        border: 4px solid white;
        box-shadow: 0 4px 16px rgba(0,0,0,0.10);
        overflow: hidden;
        position: relative;
        z-index: 1;
        transition: transform 0.22s;
    }

    .child-card:hover .child-avatar {
        transform: scale(1.1) rotate(-3deg);
    }

    .child-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .child-name {
        font-size: 0.98rem;
        font-weight: 900;
        color: #222;
        margin-bottom: 0.2rem;
        letter-spacing: -0.01em;
    }

    .child-age {
        font-size: 0.72rem;
        color: #aaa;
        font-weight: 700;
        margin-bottom: 0.9rem;
    }

    .child-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.28rem;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 0.26rem 0.75rem;
        border-radius: 50px;
        margin-bottom: 0.9rem;
    }

    .badge-done     { background: #e8faf0; color: #1a7a45; border: 1.5px solid #a7f3c5; }
    .badge-inprog   { background: #fffbe6; color: #a0660a; border: 1.5px solid #fde68a; }
    .badge-open     { background: #fff0f3; color: #c0185a; border: 1.5px solid #ffb3c6; }
    .badge-noperiod { background: #f3f4f6; color: #888;    border: 1.5px solid #e0e0e0; }

    .prog-wrap { padding: 0 0.2rem; }

    .prog-track {
        height: 8px;
        background: #f0f0f0;
        border-radius: 99px;
        overflow: hidden;
    }

    .prog-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.7s cubic-bezier(.4,0,.2,1);
    }

    .fill-done   { background: linear-gradient(90deg, #16a34a, #86efac); }
    .fill-inprog { background: linear-gradient(90deg, #f59e0b, #fde68a); }
    .fill-empty  { width: 0; }

    .prog-label {
        font-size: 0.65rem;
        color: #bbb;
        font-weight: 700;
        text-align: right;
        margin-top: 0.3rem;
    }

    .tap-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 800;
        color: #ff8fab;
        margin-top: 0.8rem;
        opacity: 0;
        transform: translateY(5px);
        transition: opacity 0.2s, transform 0.2s;
    }

    .child-card:hover .tap-label {
        opacity: 1;
        transform: translateY(0);
    }

    .empty-wrap {
        text-align: center;
        padding: 3.5rem 1rem;
    }

    .empty-wrap .big-emoji { font-size: 4rem; }

    .empty-wrap p {
        font-size: 1rem;
        font-weight: 800;
        color: #ffb3c6;
        margin-top: 0.5rem;
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(0.88) translateY(12px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .child-card:nth-child(1) { animation-delay: 0.04s; }
    .child-card:nth-child(2) { animation-delay: 0.10s; }
    .child-card:nth-child(3) { animation-delay: 0.16s; }
    .child-card:nth-child(4) { animation-delay: 0.22s; }
    .child-card:nth-child(5) { animation-delay: 0.28s; }
    .child-card:nth-child(6) { animation-delay: 0.34s; }

    @media (max-width: 580px) {
        .fp-greeting { font-size: 1.5rem; }
        .children-grid { grid-template-columns: repeat(2, 1fr); gap: 0.9rem; }
        .fp-banner { padding: 1.6rem 1.4rem 2rem; }
    }
</style>

@php
    $user     = Auth::user();
    $family   = DB::table('families')->where('user_id', $user->user_id)->first();
    $students = DB::table('students')->where('family_id', $family->user_id)->orderBy('date_of_birth')->get();

    $scaleVersionId = DB::table('scale_versions')->where('name', 'ECCD 2004')->value('scale_version_id');
    $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
    $now            = now();
    $totalChildren  = $students->count();
@endphp

<div class="fp-page">

    {{-- Welcome Banner --}}
    <div class="fp-banner">
        <div class="fp-banner-inner">
            <div class="fp-greeting">{{ $family->family_name ?? 'Our Family' }}</div>
            <div class="fp-sub">Here's how your little ones are doing 🌈</div>
            <div class="fp-pills">
                <div class="fp-pill">
                    👶 {{ $totalChildren }} {{ $totalChildren == 1 ? 'Child' : 'Children' }}
                </div>
                <div class="fp-pill">
                    📅 {{ now()->format('F Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Section Header --}}
    <div class="fp-section-header">
        <div class="fp-section-icon">🌱</div>
        <div class="fp-section-title">Your Children</div>
    </div>

    {{-- Children Grid --}}
    @if($students->isEmpty())
        <div class="empty-wrap">
            <div class="big-emoji">🐣</div>
            <p>No children registered yet!</p>
        </div>
    @else
        <div class="children-grid">
            @foreach($students as $i => $s)
                @php
                    $ci     = $i % 6;
                    $age    = \Carbon\Carbon::parse($s->date_of_birth)->diff($now);
                    $ageStr = $age->y > 0
                        ? $age->y . ' yr' . ($age->y > 1 ? 's' : '') . ($age->m > 0 ? ' ' . $age->m . ' mo' : '')
                        : $age->m . ' mo';

                    $period = DB::table('assessment_periods')
                        ->where('student_id', $s->student_id)
                        ->where('status', 'scheduled')
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now)
                        ->first();

                    $periodDone = $period && DB::table('tests')
                        ->where('student_id', $s->student_id)
                        ->where('period_id', $period->period_id)
                        ->where('status', 'completed')
                        ->exists();

                    $inProgress = DB::table('tests')
                        ->where('student_id', $s->student_id)
                        ->where('examiner_id', $user->user_id)
                        ->where('status', 'in_progress')
                        ->orderByDesc('created_at')
                        ->first();

                    $answered = $inProgress
                        ? DB::table('test_responses')->where('test_id', $inProgress->test_id)->count()
                        : 0;

                    if ($periodDone) {
                        $ct = DB::table('tests')
                            ->where('student_id', $s->student_id)
                            ->where('period_id', $period->period_id)
                            ->where('status', 'completed')
                            ->orderByDesc('created_at')
                            ->first();
                        $answered = $ct
                            ? DB::table('test_responses')->where('test_id', $ct->test_id)->count()
                            : $totalQuestions;
                    }

                    $pct = $totalQuestions > 0 ? round(($answered / $totalQuestions) * 100) : 0;

                    if ($periodDone) {
                        $bc = 'badge-done';
                        $bt = '🌟 Completed';
                        $fc = 'fill-done';
                        $dp = 100;
                        $da = $totalQuestions;
                    } elseif ($inProgress && $answered > 0) {
                        $bc = 'badge-inprog';
                        $bt = '✏️ In Progress';
                        $fc = 'fill-inprog';
                        $dp = max($pct, 1);
                        $da = $answered;
                    } elseif ($period) {
                        $bc = 'badge-open';
                        $bt = '📋 Not Started';
                        $fc = 'fill-empty';
                        $dp = 0;
                        $da = 0;
                    } else {
                        $bc = 'badge-noperiod';
                        $bt = '⏳ No Period';
                        $fc = 'fill-empty';
                        $dp = 0;
                        $da = 0;
                    }
                @endphp

                <a href="{{ route('family.student.profile', $s->student_id) }}"
                   class="child-card c{{ $ci }}">

                    <div class="card-top"></div>

                    <div class="child-avatar">
                        @if($s->feature_path)
                            <img src="{{ asset('storage/' . $s->feature_path) }}" alt="{{ $s->first_name }}">
                        @else
                            🐣
                        @endif
                    </div>

                    <div class="child-name">{{ $s->first_name }} {{ $s->last_name }}</div>
                    <div class="child-age">🎂 {{ $ageStr }}</div>

                    <div class="child-badge {{ $bc }}">{{ $bt }}</div>

                    <div class="prog-wrap">
                        <div class="prog-track">
                            <div class="prog-fill {{ $fc }}" style="width: {{ $dp }}%"></div>
                        </div>
                        <div class="prog-label">{{ $da }} / {{ $totalQuestions }} questions</div>
                    </div>

                    <span class="tap-label">👆 Tap to view profile</span>
                </a>
            @endforeach
        </div>
    @endif

</div>
@endsection