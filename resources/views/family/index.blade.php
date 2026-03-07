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

    /* ── Bottom Row ── FIX 1: was missing grid definition */
    .bottom-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.2rem;
        margin-bottom: 1.2rem;
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
    /* FIX 2: Added missing .blue notification color (was .light which was undefined) */
    .notif-item.blue   { background: linear-gradient(135deg, #60a5fa, #93c5fd); }

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
        <div class="card" id="family-children">
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
                                <div class="prog-bar">
                                    <div class="prog-fill" style="width: 100%"></div>
                                </div>
                                <div class="prog-text">
                                    <span class="prog-pct" style="background:rgba(255,255,255,0.5);">✅ Test Completed</span>
                                </div>

                            @elseif($child['active_period'])
                                <div class="prog-bar">
                                    <div class="prog-fill" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="prog-text">
                                    <span class="prog-pct">{{ $pct }}% complete</span>
                                    <span style="opacity:0.8;">({{ $child['completed'] }}/{{ $child['total_tests'] }})</span>
                                </div>

                            @else
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

        {{-- Overall Progress (per-period summaries) --}}
        <div class="card">
            <div class="card-title">📈 Overall Progress</div>

            @php
                $progressItems = collect();
                foreach ($children as $child) {
                    $sid = $child['student_id'];
                    if (!empty($monitoring[$sid])) {
                        $childPeriods = collect($monitoring[$sid])->sortBy('start_date');
                        $latest       = $childPeriods->last();
                        if ($latest) {
                            $progressItems->push([
                                'name'           => $child['first_name'],
                                'label'          => $latest['label'],
                                'score'          => $latest['score'],
                                'interpretation' => $latest['interpretation'],
                                'start_date'     => $latest['start_date'],
                                'end_date'       => $latest['end_date'],
                            ]);
                        }
                    }
                }
            @endphp

            @if($progressItems->count() > 0)
                @foreach($progressItems as $item)
                    <div class="child-item" style="background:linear-gradient(135deg,#ff9f43,#ffdd57); margin-bottom:0.6rem;">
                        <div class="child-avatar">📈</div>
                        <div style="flex:1; min-width:0;">
                            <div class="child-name">{{ $item['name'] }}</div>
                            <div class="child-age">Period: {{ $item['label'] ?? 'Assessment' }}</div>
                            <div class="prog-text" style="margin-top:0.15rem;">
                                <span class="prog-pct">{{ $item['score'] ?? '—' }}</span>
                                <span style="opacity:0.9;">{{ $item['interpretation'] ?? 'No summary yet' }}</span>
                            </div>
                            <div class="prog-text" style="margin-top:0.1rem;">
                                <span style="opacity:0.8;">📅 {{ \Carbon\Carbon::parse($item['start_date'])->format('M d') }} – {{ \Carbon\Carbon::parse($item['end_date'])->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty">
                    <span>📈</span>
                    <p>No completed assessment periods yet.</p>
                </div>
            @endif
        </div>

        {{-- Upcoming Assessments --}}
        <div class="card">
            <div class="card-title">📅 Upcoming Assessments</div>

            {{-- FIX 3: Added missing opening div for card-scroll assess-scroll --}}
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
                        @if($daysLeft >= 0)
                            <div class="days-remaining">
                                ⏳ Starts in {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }}
                            </div>
                        @else
                            <div class="days-remaining" style="background:rgba(180,0,0,0.25);">
                                ⚠️ {{ abs($daysLeft) }} day{{ abs($daysLeft) !== 1 ? 's' : '' }} overdue
                            </div>
                        @endif

                        @php
                            $familyTest = $a->family_test ?? null;
                        @endphp

                        @if($now->between($start, $end))
                            @if($familyTest && in_array($familyTest->status, ['completed', 'finalized']))
                                <div class="start-btn" style="background:#e5e7eb;color:#16a34a;cursor:default;box-shadow:none;">
                                    ✔ Done for this period
                                </div>
                            @else
                                <a href="{{ route('family.tests.start.show', $a->student_id) }}" class="start-btn">
                                    {{ $familyTest && $familyTest->status === 'in_progress' ? '▶ Continue Test' : '▶ Start Now' }}
                                </a>
                            @endif
                        @endif
                    </div>
                @empty
                    <div class="empty">
                        <span>🗓️</span>
                        <p>No upcoming assessments.</p>
                    </div>
                @endforelse
            </div>{{-- FIX 3: closes card-scroll assess-scroll --}}
        </div>

        {{-- Results History --}}
        <div class="card" id="family-results">
            <div class="card-title">📊 Previous Results</div>

            @php
                $history = collect($latest_results);
            @endphp

            @if($history->count() > 0)
                @foreach($history as $item)
                    <div class="child-item" style="background:linear-gradient(135deg,#6366f1,#a855f7); margin-bottom:0.6rem;">
                        <div class="child-avatar">
                            @if($item['profile_image'])
                                <img src="{{ asset('storage/' . $item['profile_image']) }}" alt="{{ $item['child_name'] }}">
                            @else
                                🎯
                            @endif
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="child-name">{{ $item['child_name'] }}</div>
                            <div class="child-age">Score: {{ $item['score'] }} • {{ $item['interpretation'] }}</div>
                            <div class="prog-text" style="margin-top:0.15rem;">
                                <span style="opacity:0.9;">📅 {{ \Carbon\Carbon::parse($item['date'])->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty">
                    <span>📊</span>
                    <p>No previous results yet.</p>
                </div>
            @endif
        </div>

        {{-- Notifications --}}
        <div class="card">
            <div class="card-title">🔔 Notifications</div>

            @php
                use Carbon\Carbon;

                $incomplete = 0;
                foreach ($children as $c) {
                    if (!empty($c['needs_action'])) {
                        $incomplete++;
                    }
                }

                $pendingAssessments = 0;
                $now = Carbon::now();
                foreach ($assessments as $a) {
                    $start      = Carbon::parse($a->start_date);
                    $end        = Carbon::parse($a->end_date);
                    $familyTest = $a->family_test ?? null;
                    $done       = $familyTest && in_array($familyTest->status, ['completed', 'finalized']);

                    if ($end->gte($now) && !$done) {
                        $pendingAssessments++;
                    }
                }
            @endphp

            {{-- FIX 4: Added missing card-scroll notif-scroll wrapper --}}
            <div class="card-scroll notif-scroll">
                @if($incomplete > 0)
                    <div class="notif-item green">
                        <div class="notif-icon">📝</div>
                        <div class="notif-body">
                            <div class="notif-title">Unfinished Test</div>
                            <div class="notif-text">{{ $incomplete }} test(s) still need to be completed.</div>
                        </div>
                    </div>
                @endif

                @if(count($latest_results) > 0)
                    <div class="notif-item yellow">
                        <div class="notif-icon">🏆</div>
                        <div class="notif-body">
                            <div class="notif-title">Results Available</div>
                            <div class="notif-text">Check your child's latest score!</div>
                        </div>
                    </div>
                @endif

                @if($pendingAssessments > 0)
                    {{-- FIX 2: Changed undefined .light class to .blue --}}
                    <div class="notif-item blue">
                        <div class="notif-icon">📅</div>
                        <div class="notif-body">
                            <div class="notif-title">Assessment Scheduled</div>
                            <div class="notif-text">{{ $pendingAssessments }} assessment(s) coming up.</div>
                        </div>
                    </div>
                @endif

                @if($incomplete == 0 && count($latest_results) == 0 && $pendingAssessments == 0)
                    <div class="empty">
                        <span>🎉</span>
                        <p>All caught up!</p>
                    </div>
                @endif
            </div>{{-- closes card-scroll notif-scroll --}}
        </div>

    </div>

    {{-- Help & Tips --}}
    <div class="card" id="family-help" style="margin-top:1.5rem;">
        <div class="card-title">❓ Help & Tips</div>
        <p style="font-size:0.85rem; color:#374151; margin-bottom:0.4rem;">
            - Tap <strong>Current Test</strong> in the top menu to go straight to your child&apos;s active assessment.
        </p>
        <p style="font-size:0.85rem; color:#374151; margin-bottom:0.4rem;">
            - Use the <strong>Unfinished Test</strong> notification to quickly see if anything still needs your attention.
        </p>
        <p style="font-size:0.85rem; color:#374151; margin-bottom:0.4rem;">
            - In <strong>Previous Results</strong>, you can review older assessments and track progress over time.
        </p>
        <p style="font-size:0.85rem; color:#374151;">
            If something doesn&apos;t look right, you can return to the home screen anytime using the <strong>Home</strong> button in the header.
        </p>
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