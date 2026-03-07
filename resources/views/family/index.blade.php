@extends('family.layout')

@section('title', 'Family Dashboard')

@section('content')
<style>
    body { background: #FFF8FF; }

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
        border: 3px solid #ffe0ec;
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

    .fun-dots { display: flex; gap: 0.4rem; }
    .fun-dot {
        width: 9px; height: 9px; border-radius: 50%;
        animation: bdot 1.2s infinite alternate;
    }
    .fun-dot:nth-child(1) { background: #ff6b9d; animation-delay: 0s; }
    .fun-dot:nth-child(2) { background: #ff9f43; animation-delay: 0.2s; }
    .fun-dot:nth-child(3) { background: #6bcf7f; animation-delay: 0.4s; }
    .fun-dot:nth-child(4) { background: #ff6b9d; animation-delay: 0.6s; }
    @keyframes bdot { from { transform: translateY(0); } to { transform: translateY(-5px); } }

    /* ── Card Base ── */
    .card {
        background: white;
        border-radius: 24px;
        padding: 1.2rem 1.5rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.07);
        border: 3px solid #ffe0ec;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 900;
        color: #c0392b;
        margin-bottom: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 2px dashed #ffe0ec;
    }

    /* ── Scrollable card body ── */
    .card-scroll {
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 4px;
        scroll-behavior: smooth;
    }

    .card-scroll.children-scroll  { max-height: 112px; }
    .card-scroll.assess-scroll    { max-height: 210px; }
    .card-scroll.notif-scroll     { max-height: 265px; }

    /* Custom scrollbar */
    .card-scroll::-webkit-scrollbar { width: 6px; }
    .card-scroll::-webkit-scrollbar-track { background: #ffe0ec; border-radius: 10px; }
    .card-scroll::-webkit-scrollbar-thumb { background: #ff6b9d; border-radius: 10px; }
    .card-scroll::-webkit-scrollbar-thumb:hover { background: #e0456a; }

    /* ── Child Items ── */
    .child-item {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        background: linear-gradient(135deg, #ff6b9d, #ffb3d1);
        border-radius: 16px;
        padding: 0.8rem 1rem;
        margin-bottom: 0.7rem;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255,255,255,0.3);
        position: relative;
        overflow: hidden;
    }

    .child-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(255,107,157,0.4);
    }
    .child-item:last-child { margin-bottom: 0; }

    /* ── Completed Child Item (green) ── */
    .child-item.completed {
        background: linear-gradient(135deg, #6bcf7f, #a3e4a1);
    }

    .child-item.completed:hover {
        box-shadow: 0 8px 18px rgba(107, 207, 127, 0.4);
    }

    .child-avatar {
        width: 46px; height: 46px;
        background: white;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
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
        height: 10px;
        background: rgba(255,255,255,0.3);
        border-radius: 10px;
        overflow: hidden;
        margin-top: 0.5rem;
        border: 1.5px solid rgba(255,255,255,0.4);
    }

    .prog-fill {
        height: 100%;
        background: white;
        border-radius: 10px;
        transition: width 0.5s ease;
        box-shadow: 0 0 6px rgba(255,255,255,0.7);
        min-width: 4px;
    }

    .prog-text {
        font-size: 0.72rem;
        font-weight: 800;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .prog-pct {
        background: rgba(255,255,255,0.3);
        border-radius: 20px;
        padding: 0.1rem 0.5rem;
        font-weight: 900;
        font-size: 0.75rem;
    }

    /* ── Score Card ── */
    .score-card {
        background: linear-gradient(135deg, #ff9f43, #ffdd57);
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
        width: 46px; height: 46px;
        background: white; border-radius: 50%;
        overflow: hidden; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
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
        font-weight: 900;
        background: rgba(0,0,0,0.25);
        color: #fff;
        display: inline-block;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        text-align: center;
        width: 100%;
        margin-bottom: 0.3rem;
        letter-spacing: 0.02em;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        border: 1.5px solid rgba(255,255,255,0.35);
        box-sizing: border-box;
    }

    .score-date { font-size: 0.73rem; opacity: 0.85; text-align: center; margin-top: 0.2rem; }

    /* ── Assessment Items ── */
    .assess-item {
        background: linear-gradient(135deg, #ff9f43, #ffdd57);
        border-radius: 16px;
        padding: 0.9rem 1rem;
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

    .assess-name  { font-size: 0.9rem; font-weight: 900; margin-bottom: 0.25rem; }
    .assess-dates { font-size: 0.75rem; opacity: 0.95; margin-bottom: 0.2rem; font-weight: 700; }

    .days-remaining {
        display: inline-block;
        background: rgba(0,0,0,0.18);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 900;
        padding: 0.15rem 0.6rem;
        border-radius: 20px;
        margin-bottom: 0.4rem;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .badge-status {
        display: inline-block;
        padding: 0.15rem 0.55rem;
        border-radius: 8px;
        font-size: 0.67rem;
        font-weight: 800;
        background: rgba(0,0,0,0.2);
        color: white;
        margin-left: 0.3rem;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .start-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 0.6rem;
        background: white;
        color: #e07b00;
        padding: 0.55rem 1rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 900;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        width: 100%;
        letter-spacing: 0.02em;
        box-sizing: border-box;
    }

    .start-btn:hover {
        transform: translateY(-2px);
        color: #c06000;
        box-shadow: 0 6px 14px rgba(0,0,0,0.18);
    }

    /* ── Notification Items ── */
    .notif-item {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        border-radius: 16px;
        padding: 0.9rem 1rem;
        margin-bottom: 0.7rem;
        color: white;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 3px solid rgba(255,255,255,0.3);
        cursor: pointer;
        text-decoration: none;
    }

    .notif-item:hover {
        transform: translateX(4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.12);
    }
    .notif-item:last-child { margin-bottom: 0; }
    .notif-item.pink   { background: linear-gradient(135deg, #ff6b9d, #ffb3d1); }
    .notif-item.green  { background: linear-gradient(135deg, #6bcf7f, #a3e4a1); }
    .notif-item.yellow { background: linear-gradient(135deg, #ff9f43, #ffdd57); }

    .notif-icon {
        font-size: 1.5rem;
        width: 50px;
        height: 50px;
        min-width: 50px;
        background: rgba(255,255,255,0.28);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.4);
    }

    .notif-body { flex: 1; min-width: 0; }

    .notif-title {
        font-size: 0.92rem;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 0.15rem;
    }

    .notif-text {
        font-size: 0.73rem;
        opacity: 0.92;
        font-weight: 600;
        margin-bottom: 0.2rem;
    }

    .notif-cta {
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        opacity: 0.75;
        margin-top: 0.15rem;
    }

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
                <div class="fun-dots">
                    <div class="fun-dot"></div>
                    <div class="fun-dot"></div>
                    <div class="fun-dot"></div>
                    <div class="fun-dot"></div>
                </div>
            </div>
            <img src="{{ asset('images/kids.png') }}" alt="Kids">
        </div>

        {{-- Your Children --}}
        <div class="card">
            <div class="card-title">🧒 Your Children</div>
            <div class="card-scroll children-scroll">
                @forelse($children as $child)
    @php
        $pct = $child['total_tests'] > 0
            ? round(($child['completed'] / $child['total_tests']) * 100)
            : 0;
    @endphp
    <div class="child-item {{ $child['period_completed'] ? 'completed' : '' }}">
        <div class="child-avatar">
            @if($child['profile_image'])
                <img src="{{ asset('storage/' . $child['profile_image']) }}" alt="{{ $child['name'] }}">
            @else
                🐣
            @endif
        </div>
        <div style="flex:1; min-width:0;">
            <div class="child-name">{{ $child['first_name'] }}</div>
            <div class="child-age">🎂 Age: {{ $child['age'] }}</div>

            @if($child['period_completed'])
                {{-- Test is done — green card, 100% bar, completed badge --}}
                <div class="prog-bar">
                    <div class="prog-fill" style="width: 100%"></div>
                </div>
                <div class="prog-text">
                    <span class="prog-pct" style="background:rgba(255,255,255,0.5);">✅ Test Completed</span>
                </div>

            @elseif($child['active_period'])
                {{-- Active period exists — show real progress bar --}}
                <div class="prog-bar">
                    <div class="prog-fill" style="width: {{ $pct }}%"></div>
                </div>
                <div class="prog-text">
                    <span class="prog-pct">{{ $pct }}% complete</span>
                    <span style="opacity:0.8;">({{ $child['completed'] }}/{{ $child['total_tests'] }})</span>
                </div>

            @else
                {{-- No active period, not completed — empty bar as placeholder --}}
                <div class="prog-bar">
                    <div class="prog-fill" style="width: 0%"></div>
                </div>
                <div class="prog-text">
                    <span class="prog-pct" style="opacity:0.6;">No active assessment</span>
                </div>
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

    </div>

    {{-- ── Bottom Row ── --}}
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

            <div class="card-scroll assess-scroll">
                @forelse($assessments as $a)
                    @php
                        $now      = now();
                        $start    = \Carbon\Carbon::parse($a->start_date);
                        $end      = \Carbon\Carbon::parse($a->end_date);
                        $daysLeft = (int) $now->diffInDays($start, false);
                    @endphp
                    <div class="assess-item">
                        <div class="assess-name">
                            {{ $a->first_name }} {{ $a->last_name }}
                            <span class="badge-status">🟢 Upcoming</span>
                        </div>
                        <div class="assess-dates">
                            📆 {{ $start->format('M d') }} – {{ $end->format('M d, Y') }}
                        </div>
                        <div class="days-remaining">
                            ⏳ Starts in {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }}
                        </div>
                    </div>
                @empty
                    <div class="empty">
                        <span>🗓️</span>
                        <p>No upcoming assessments.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Notifications --}}
        <div class="card">
            <div class="card-title">🔔 Notifications</div>

            <div class="card-scroll notif-scroll">
                @forelse($notifications as $n)
                    @if($n['link'])
                        <a href="{{ $n['link'] }}" class="notif-item {{ $n['color'] }}">
                            <div class="notif-icon">{{ $n['icon'] }}</div>
                            <div class="notif-body">
                                <div class="notif-title">{{ $n['title'] }}</div>
                                <div class="notif-text">{{ $n['text'] }}</div>
                                @if($n['cta'])
                                    <div class="notif-cta">{{ $n['cta'] }}</div>
                                @endif
                            </div>
                        </a>
                    @else
                        <div class="notif-item {{ $n['color'] }}">
                            <div class="notif-icon">{{ $n['icon'] }}</div>
                            <div class="notif-body">
                                <div class="notif-title">{{ $n['title'] }}</div>
                                <div class="notif-text">{{ $n['text'] }}</div>
                                @if($n['cta'])
                                    <div class="notif-cta">{{ $n['cta'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="empty">
                        <span>🎉</span>
                        <p>All caught up!</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Children: use tallest item so completed badge is never clipped
    var childContainer = document.querySelector('.children-scroll');
    if (childContainer) {
        var childItems = childContainer.children;
        if (childItems.length > 0) {
            var maxH = 0;
            for (var i = 0; i < childItems.length; i++) {
                var h = childItems[i].getBoundingClientRect().height;
                if (h > maxH) maxH = h;
            }
            childContainer.style.maxHeight = maxH + 'px';
        }
    }

    // Assessments: show exactly 1 item height
    var assessContainer = document.querySelector('.assess-scroll');
    if (assessContainer) {
        var firstAssess = assessContainer.firstElementChild;
        if (firstAssess) {
            assessContainer.style.maxHeight = firstAssess.getBoundingClientRect().height + 'px';
        }
    }

    // Notifications: show exactly 2 items
    var notifContainer = document.querySelector('.notif-scroll');
    if (notifContainer) {
        var items = notifContainer.children;
        if (items.length >= 2) {
            var total = 0;
            for (var i = 0; i < 2; i++) {
                total += items[i].getBoundingClientRect().height;
                if (i < 1) {
                    total += parseFloat(window.getComputedStyle(items[i]).marginBottom) || 0;
                }
            }
            notifContainer.style.maxHeight = total + 'px';
        } else if (items.length === 1) {
            notifContainer.style.maxHeight = items[0].getBoundingClientRect().height + 'px';
        }
    }
});
</script>
@endsection