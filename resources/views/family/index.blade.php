@extends('family.layout')

@section('title', 'Family Dashboard')

@section('content')
<style>
    body { background: #ffe8f0; }

    .dashboard {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem;
    }

    /* ── Top Row ── */
    .top-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
        margin-bottom: 1.2rem;
    }

    /* ── Bottom Row ── */
    .bottom-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.2rem;
    }

    /* ── Welcome Banner ── */
    .welcome-banner {
        background: white;
        border-radius: 24px;
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        border: 3px solid rgba(255,255,255,0.3);
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::before {
        content: '🌟';
        position: absolute;
        font-size: 6rem;
        opacity: 0.08;
        right: 120px;
        top: -5px;
    }

    .welcome-banner h1 {
        font-size: 1.8rem;
        font-weight: 900;
        color: #7a4f00;
        margin-bottom: 0.3rem;
    }

    .welcome-banner p {
        font-size: 0.9rem;
        color: #9a6a00;
        margin: 0 0 0.8rem 0;
    }

    .welcome-banner img {
        width: 170px;
        height: 170px;
        object-fit: contain;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.12));
        flex-shrink: 0;
    }

    .fun-dots {
        display: flex;
        gap: 0.4rem;
    }

    .fun-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        animation: bdot 1.2s infinite alternate;
    }

    .fun-dot:nth-child(1) { background: #ff6b9d; animation-delay: 0s; }
    .fun-dot:nth-child(2) { background: #ff9f43; animation-delay: 0.2s; }
    .fun-dot:nth-child(3) { background: #6bcf7f; animation-delay: 0.4s; }
    .fun-dot:nth-child(4) { background: #ff6b9d; animation-delay: 0.6s; }

    @keyframes bdot {
        from { transform: translateY(0); }
        to   { transform: translateY(-5px); }
    }

    /* ── Card Base ── */
    .card {
        background: white;
        border-radius: 24px;
        padding: 1.2rem 1.5rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
        border: 3px solid #ffe0ec;
        height: 100%;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 900;
        color: #c0392b;
        margin-bottom: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 2px dashed #ffe0ec;
    }

    /* ── Child Items ── */
    .child-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: linear-gradient(135deg, #ff6b9d, #ff9f43);
        border-radius: 16px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.7rem;
        color: white;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255,255,255,0.3);
        position: relative;
        overflow: hidden;
    }

    .child-item::after {
        content: '✨';
        position: absolute;
        right: 10px;
        font-size: 1rem;
        opacity: 0.5;
    }

    .child-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(255,107,157,0.4);
    }

    .child-item:last-child { margin-bottom: 0; }

    .child-avatar {
        width: 46px;
        height: 46px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid rgba(255,255,255,0.6);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }

    .child-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .child-name { font-size: 0.95rem; font-weight: 900; }
    .child-age  { font-size: 0.73rem; opacity: 0.9; margin-top: 0.1rem; }

    .prog-bar {
        height: 5px;
        background: rgba(255,255,255,0.3);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 0.4rem;
    }

    .prog-fill {
        height: 100%;
        background: white;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .prog-text { font-size: 0.68rem; opacity: 0.9; margin-top: 0.2rem; }

    /* ── Score Card ── */
    .score-card {
        background: linear-gradient(135deg, #ff6b9d, #ffb347);
        border-radius: 18px;
        padding: 1.2rem;
        color: white;
        position: relative;
        overflow: hidden;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .score-card::before {
        content: '🏆';
        position: absolute;
        font-size: 5rem;
        opacity: 0.07;
        right: -5px;
        bottom: -10px;
    }

    .score-header {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        margin-bottom: 1rem;
    }

    .score-avatar {
        width: 46px;
        height: 46px;
        background: white;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        border: 3px solid rgba(255,255,255,0.6);
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }

    .score-avatar img { width: 100%; height: 100%; object-fit: cover; }

    .score-child-name { font-size: 0.95rem; font-weight: 900; }
    .score-label      { font-size: 0.73rem; opacity: 0.85; }

    .score-number {
        font-size: 3.2rem;
        font-weight: 900;
        line-height: 1;
        text-align: center;
        margin-bottom: 0.3rem;
        text-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .score-interp {
        font-size: 0.9rem;
        font-weight: 800;
        background: rgba(255,255,255,0.25);
        display: inline-block;
        padding: 0.2rem 1rem;
        border-radius: 20px;
        text-align: center;
        width: 100%;
        margin-bottom: 0.3rem;
    }

    .score-date { font-size: 0.73rem; opacity: 0.85; text-align: center; margin-top: 0.2rem; }

    /* ── Assessment Items ── */
    .assess-item {
        background: linear-gradient(135deg, #ff9f43, #ffdd57);
        border-radius: 16px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.7rem;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255,255,255,0.3);
    }

    .assess-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(255,159,67,0.4);
    }

    .assess-item:last-child { margin-bottom: 0; }

    .assess-name  { font-size: 0.9rem; font-weight: 900; margin-bottom: 0.2rem; }
    .assess-dates { font-size: 0.75rem; opacity: 0.9; margin-bottom: 0.3rem; }

    .badge-status {
        display: inline-block;
        padding: 0.15rem 0.55rem;
        border-radius: 8px;
        font-size: 0.67rem;
        font-weight: 800;
        background: rgba(255,255,255,0.3);
        color: white;
        margin-left: 0.3rem;
    }

    .start-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        margin-top: 0.4rem;
        background: white;
        color: #ff9f43;
        padding: 0.28rem 0.85rem;
        border-radius: 20px;
        font-size: 0.76rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .start-btn:hover {
        transform: scale(1.06);
        color: #ff9f43;
        box-shadow: 0 4px 10px rgba(0,0,0,0.13);
    }

    /* ── Notification Items ── */
    .notif-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        border-radius: 16px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.7rem;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255,255,255,0.3);
        cursor: pointer;
    }

    .notif-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 14px rgba(0,0,0,0.1);
    }

    .notif-item:last-child { margin-bottom: 0; }
    .notif-item.green  { background: linear-gradient(135deg, #6bcf7f, #a3e4a1); }
    .notif-item.yellow { background: linear-gradient(135deg, #ff9f43, #ffdd57); }
    .notif-item.light  { background: linear-gradient(135deg, #ff6b9d, #ffb3d1); }

    .notif-icon {
        font-size: 1.6rem;
        width: 42px;
        height: 42px;
        background: rgba(255,255,255,0.25);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notif-title { font-size: 0.88rem; font-weight: 900; }
    .notif-text  { font-size: 0.73rem; opacity: 0.9; margin-top: 0.1rem; }

    /* ── Empty State ── */
    .empty {
        text-align: center;
        padding: 1.2rem;
        color: #ffb3d1;
        font-size: 0.83rem;
    }

    .empty span { font-size: 2rem; display: block; margin-bottom: 0.4rem; }
    .empty p    { color: #ff6b9d; font-weight: 700; margin: 0; }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .top-row    { grid-template-columns: 1fr; }
        .bottom-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 600px) {
        .welcome-banner { flex-direction: column; text-align: center; gap: 1rem; }
        .welcome-banner h1 { font-size: 1.4rem; }
    }
</style>

<div class="dashboard">

    {{-- ── Top Row: Welcome (left) + Children (right) ── --}}
    <div class="top-row">

        {{-- Welcome Banner --}}
        <div class="welcome-banner">
            <div>
                <h1>Hello, {{ $family_name }}!</h1>
                <p>Let's check on your little ones today! 🌱</p>
            </div>
            <img src="{{ asset('images/kids.png') }}" alt="Kids">
        </div>

        {{-- Your Children --}}
        <div class="card">
            <div class="card-title">🧒 Your Children</div>

            @forelse($children as $child)
                <div class="child-item">
                    <div class="child-avatar">
                        @if($child['profile_image'])
                            <img src="{{ asset('storage/' . $child['profile_image']) }}" alt="{{ $child['name'] }}">
                        @else
                            🐣
                        @endif
                    </div>
                    <div style="flex:1">
                        <div class="child-name">{{ $child['first_name'] }}</div>
                        <div class="child-age">🎂 Age: {{ $child['age'] }}</div>
                        @if($child['total_tests'] > 0)
                            <div class="prog-bar">
                                <div class="prog-fill" style="width: {{ ($child['completed'] / $child['total_tests']) * 100 }}%"></div>
                            </div>
                            <div class="prog-text">✅ {{ $child['completed'] }}/{{ $child['total_tests'] }} questions answered</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty">
                    <span>🐣</span>
                    <p>No children registered yet.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- ── Bottom Row: Latest Result (left) + Assessments (middle) + Notifications (right) ── --}}
    <div class="bottom-row">

        {{-- Latest Result --}}
        <div class="card">
            <div class="card-title">⭐ Latest Test Result</div>

            @if(count($latest_results) > 0)
                @php $r = $latest_results[0]; @endphp
                <div class="score-card">
                    <div class="score-header">
                        <div class="score-avatar">
                            @if($r['profile_image'])
                                <img src="{{ asset('storage/' . $r['profile_image']) }}" alt="">
                            @else
                                🌟
                            @endif
                        </div>
                        <div>
                            <div class="score-child-name">{{ $r['child_name'] }}</div>
                            <div class="score-label">Most Recent Assessment</div>
                        </div>
                    </div>
                    <div class="score-number">{{ $r['score'] }}</div>
                    <div class="score-interp">{{ $r['interpretation'] }}</div>
                    <div class="score-date">📅 {{ \Carbon\Carbon::parse($r['date'])->format('M d, Y') }}</div>
                </div>
            @else
                <div class="empty">
                    <span>📋</span>
                    <p>No results yet.</p>
                </div>
            @endif
        </div>

        {{-- Upcoming Assessments --}}
        <div class="card">
            <div class="card-title">📅 Upcoming Assessments</div>

            @forelse($assessments as $a)
                @php
                    $now   = now();
                    $start = \Carbon\Carbon::parse($a->start_date);
                    $end   = \Carbon\Carbon::parse($a->end_date);

                    if ($now->between($start, $end))  { $badgeText = '🟡 In Progress'; }
                    elseif ($now->gt($end))           { $badgeText = '🔴 Overdue'; }
                    else                              { $badgeText = '🟢 Upcoming'; }
                @endphp
                <div class="assess-item">
                    <div class="assess-name">
                        {{ $a->first_name }} {{ $a->last_name }}
                        <span class="badge-status">{{ $badgeText }}</span>
                    </div>
                    <div class="assess-dates">
                        📆 {{ $start->format('M d') }} – {{ $end->format('M d, Y') }}
                    </div>
                    @if($now->between($start, $end))
                    <a href="{{ route('family.tests.start.show', $a->student_id) }}" class="start-btn">
                    ▶ Start Now
                </a>
                @endif
                </div>
            @empty
                <div class="empty">
                    <span>🗓️</span>
                    <p>No upcoming assessments.</p>
                </div>
            @endforelse
        </div>

        {{-- Notifications --}}
        <div class="card">
            <div class="card-title">🔔 Notifications</div>

            @php
                $incomplete = 0;
                foreach ($children as $c) {
                    if ($c['completed'] < $c['total_tests']) {
                        $incomplete++;
                    }
                }
            @endphp

            @if($incomplete > 0)
                <div class="notif-item green">
                    <div class="notif-icon">📝</div>
                    <div>
                        <div class="notif-title">Unfinished Test</div>
                        <div class="notif-text">{{ $incomplete }} test(s) still need to be completed.</div>
                    </div>
                </div>
            @endif

            @if(count($latest_results) > 0)
                <div class="notif-item yellow">
                    <div class="notif-icon">🏆</div>
                    <div>
                        <div class="notif-title">Results Available</div>
                        <div class="notif-text">Check your child's latest score!</div>
                    </div>
                </div>
            @endif

            @if(count($assessments) > 0)
                <div class="notif-item light">
                    <div class="notif-icon">📅</div>
                    <div>
                        <div class="notif-title">Assessment Scheduled</div>
                        <div class="notif-text">{{ count($assessments) }} assessment(s) coming up.</div>
                    </div>
                </div>
            @endif

            @if($incomplete == 0 && count($latest_results) == 0 && count($assessments) == 0)
                <div class="empty">
                    <span>🎉</span>
                    <p>All caught up!</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection