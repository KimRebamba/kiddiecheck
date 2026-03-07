@extends('family.layout')

@section('title', 'Tests')

@section('content')
<style>
    body { background: #fdf4f8; }
    .tests-page { max-width: 1000px; margin: 0 auto; padding: 2rem 1rem 3rem; }

    .page-header { margin-bottom: 2rem; }
    .page-header h1 { font-size: 1.7rem; font-weight: 900; color: #c0185a; margin-bottom: 0.2rem; }
    .page-header p  { font-size: 0.88rem; color: #b07080; }

    /* Grid */
    .children-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.4rem;
        align-items: start;
    }

    /* Card */
    .child-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(200,60,100,0.10);
        border: 2px solid #fce8f0;
        transition: transform 0.22s, box-shadow 0.22s;
    }
    .child-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(200,60,100,0.18);
    }

    /* Banner — color by state */
    .card-banner {
        padding: 1.4rem 1.4rem 1rem;
        position: relative;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    /* 🟢 Green = Completed */
    .banner-done    { background: linear-gradient(135deg, #16a34a 0%, #4ade80 100%); }
    /* 🟡 Yellow = In Progress */
    .banner-inprog  { background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%); }
    /* 🔴 Red = Not Started / No Period */
    .banner-start   { background: linear-gradient(135deg, #dc2626 0%, #f87171 100%); }
    .banner-waiting { background: linear-gradient(135deg, #b91c1c 0%, #fca5a5 100%); }

    .avatar-wrap {
        width: 62px; height: 62px; flex-shrink: 0;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        border: 3px solid rgba(255,255,255,0.6);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .avatar-wrap img { width: 100%; height: 100%; object-fit: cover; }

    .banner-info { flex: 1; min-width: 0; }
    .child-name { font-size: 1.1rem; font-weight: 900; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .child-age  { font-size: 0.75rem; color: rgba(255,255,255,0.88); font-weight: 600; margin-top: 0.1rem; }

    /* Card body */
    .card-body { padding: 1.1rem 1.3rem 1.3rem; }

    /* Status pills */
    .status-row { margin-bottom: 0.65rem; }
    .status-pill {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.72rem; font-weight: 800;
        padding: 0.28rem 0.7rem; border-radius: 20px;
    }
    .status-pill.done    { background: #dcfce7; color: #15803d; }
    .status-pill.inprog  { background: #fef9c3; color: #92400e; }
    .status-pill.open    { background: #fee2e2; color: #991b1b; }
    .status-pill.waiting { background: #fef2f2; color: #b91c1c; }

    /* Period dates */
    .period-dates {
        font-size: 0.72rem; font-weight: 700;
        margin-bottom: 0.85rem;
        display: flex; align-items: center; gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
    }
    .period-dates.active { background: #fff0f6; color: #9b3a6a; border: 1px solid #fce0ec; }
    .period-dates.muted  { background: #f5f5f5; color: #aaa;    border: 1px solid #e5e5e5; }

    /* Progress — always shown */
    .prog-section { margin-bottom: 1rem; }
    .prog-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 0.45rem;
    }
    .prog-label { font-size: 0.72rem; font-weight: 800; color: #374151; }
    .prog-nums  { font-size: 0.7rem; font-weight: 700; color: #999; }

    .prog-track {
        height: 10px;
        background: #f3f4f6;
        border-radius: 99px;
        overflow: hidden;
    }
    .prog-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.7s cubic-bezier(.4,0,.2,1);
    }
    /* 🟢 green fill = done */
    .fill-done   { background: linear-gradient(90deg, #16a34a, #4ade80); }
    /* 🟡 yellow fill = in progress */
    .fill-inprog { background: linear-gradient(90deg, #d97706, #fbbf24); }
    /* empty track = not started */
    .fill-zero   { width: 0 !important; }

    /* CTA */
    .cta-btn {
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        padding: 0.65rem 1rem;
        border-radius: 12px;
        font-size: 0.85rem; font-weight: 800;
        text-decoration: none; width: 100%;
        border: none; cursor: pointer;
        transition: transform 0.18s, box-shadow 0.18s;
        letter-spacing: 0.01em;
    }
    .cta-btn:hover { transform: translateY(-2px); text-decoration: none; }

    /* 🔴 Red = Start */
    .cta-btn.start {
        background: linear-gradient(135deg, #dc2626, #f87171);
        color: white; box-shadow: 0 4px 14px rgba(220,38,38,0.35);
    }
    .cta-btn.start:hover { box-shadow: 0 8px 22px rgba(220,38,38,0.45); color: white; }

    /* 🟡 Yellow = Continue */
    .cta-btn.continue {
        background: linear-gradient(135deg, #d97706, #fbbf24);
        color: white; box-shadow: 0 4px 14px rgba(217,119,6,0.35);
    }
    .cta-btn.continue:hover { box-shadow: 0 8px 22px rgba(217,119,6,0.45); color: white; }

    /* 🟢 Green = Done */
    .cta-btn.done    { background: #dcfce7; color: #15803d; border: 2px solid #86efac; cursor: default; }
    .cta-btn.waiting { background: #f9fafb; color: #9ca3af; border: 2px solid #e5e7eb; cursor: default; font-size: 0.8rem; }
    .cta-btn.done:hover, .cta-btn.waiting:hover { transform: none; }

    /* Empty */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state .emoji { font-size: 4rem; margin-bottom: 1rem; }
    .empty-state p { font-size: 1rem; color: #ff6b9d; font-weight: 700; }

    @media (max-width: 600px) {
        .children-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="tests-page">

    <div class="page-header">
        <h1>⭐ Tests</h1>
        <p>Select a child to begin or continue their assessment.</p>
    </div>

    @php
        $user     = Auth::user();
        $family   = DB::table('families')->where('user_id', $user->user_id)->first();
        $students = DB::table('students')->where('family_id', $family->user_id)->orderBy('date_of_birth')->get();

        $scaleVersionId = DB::table('scale_versions')->where('name', 'ECCD 2004')->value('scale_version_id');
        $totalQuestions = DB::table('questions')->where('scale_version_id', $scaleVersionId)->count();
        $now = now();
    @endphp

    @if($students->isEmpty())
        <div class="empty-state">
            <div class="emoji">🐣</div>
            <p>No children registered yet.</p>
        </div>
    @else
        <div class="children-grid">
            @foreach($students as $i => $s)
                @php
                    // Age
                    $age = \Carbon\Carbon::parse($s->date_of_birth)->diff($now);
                    $ageStr = $age->y > 0
                        ? $age->y . ' yr' . ($age->y > 1 ? 's' : '') . ($age->m > 0 ? ' ' . $age->m . ' mo' : '')
                        : $age->m . ' month' . ($age->m != 1 ? 's' : '');

                    // Active period
                    $period = DB::table('assessment_periods')
                        ->where('student_id', $s->student_id)
                        ->where('status', 'scheduled')
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now)
                        ->first();

                    // Period completed?
                    $periodDone = $period && DB::table('tests')
                        ->where('student_id', $s->student_id)
                        ->where('period_id', $period->period_id)
                        ->where('status', 'completed')
                        ->exists();

                    // In-progress test
                    $inProgress = DB::table('tests')
                        ->where('student_id', $s->student_id)
                        ->where('examiner_id', $user->user_id)
                        ->where('status', 'in_progress')
                        ->orderByDesc('created_at')
                        ->first();

                    $answered = $inProgress
                        ? DB::table('test_responses')->where('test_id', $inProgress->test_id)->count()
                        : 0;

                    // Pull answered count for completed state
                    if ($periodDone) {
                        $completedTest = DB::table('tests')
                            ->where('student_id', $s->student_id)
                            ->where('period_id', $period->period_id)
                            ->where('status', 'completed')
                            ->orderByDesc('created_at')->first();
                        $answered = $completedTest
                            ? DB::table('test_responses')->where('test_id', $completedTest->test_id)->count()
                            : $totalQuestions;
                    }

                    $pct = $totalQuestions > 0 ? round(($answered / $totalQuestions) * 100) : 0;

                    // State → banner, pill, fill, display values
                    if ($periodDone) {
                        $state           = 'done';
                        $bannerClass     = 'banner-done';
                        $pillClass       = 'done';
                        $pillText        = '✅ Completed';
                        $fillClass       = 'fill-done';
                        $displayPct      = 100;
                        $displayAnswered = $totalQuestions;
                    } elseif ($inProgress && $answered > 0) {
                        $state           = 'continue';
                        $bannerClass     = 'banner-inprog';
                        $pillClass       = 'inprog';
                        $pillText        = '🟡 In Progress';
                        $fillClass       = 'fill-inprog';
                        $displayPct      = max($pct, 1);
                        $displayAnswered = $answered;
                    } elseif ($period) {
                        $state           = 'start';
                        $bannerClass     = 'banner-start';
                        $pillClass       = 'open';
                        $pillText        = '🔴 Not Started';
                        $fillClass       = 'fill-zero';
                        $displayPct      = 0;
                        $displayAnswered = 0;
                    } else {
                        $state           = 'waiting';
                        $bannerClass     = 'banner-waiting';
                        $pillClass       = 'waiting';
                        $pillText        = '⏳ No Active Period';
                        $fillClass       = 'fill-zero';
                        $displayPct      = 0;
                        $displayAnswered = 0;
                    }
                @endphp

                <div class="child-card">

                    <div class="card-banner {{ $bannerClass }}">
                        <div class="avatar-wrap">
                            @if($s->feature_path)
                                <img src="{{ asset('storage/' . $s->feature_path) }}" alt="{{ $s->first_name }}">
                            @else 🐣 @endif
                        </div>
                        <div class="banner-info">
                            <div class="child-name">{{ $s->first_name }} {{ $s->last_name }}</div>
                            <div class="child-age">🎂 Age: {{ $ageStr }}</div>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- Status pill --}}
                        <div class="status-row">
                            <span class="status-pill {{ $pillClass }}">{{ $pillText }}</span>
                        </div>

                        {{-- Period dates --}}
                        @if($period)
                            <div class="period-dates active">
                                📆 {{ \Carbon\Carbon::parse($period->start_date)->format('M d') }}
                                – {{ \Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
                            </div>
                        @else
                            <div class="period-dates muted">
                                📆 No active assessment period
                            </div>
                        @endif

                        {{-- Progress bar — always shown --}}
                        <div class="prog-section">
                            <div class="prog-header">
                                <span class="prog-label">Progress</span>
                                <span class="prog-nums">{{ $displayAnswered }} / {{ $totalQuestions }} &nbsp;·&nbsp; {{ $displayPct }}%</span>
                            </div>
                            <div class="prog-track">
                                <div class="prog-fill {{ $fillClass }}" style="width: {{ $displayPct }}%"></div>
                            </div>
                        </div>

                        {{-- CTA --}}
                        @if($state === 'start')
                            <a href="{{ route('family.tests.start.show', $s->student_id) }}" class="cta-btn start">
                                ▶&nbsp; Start Assessment
                            </a>
                        @elseif($state === 'continue')
                            <a href="{{ route('family.tests.question', ['test' => $inProgress->test_id, 'domain' => 1, 'index' => 1]) }}" class="cta-btn continue">
                                ↩&nbsp; Continue Test
                            </a>
                        @elseif($state === 'done')
                            <div class="cta-btn done">✅&nbsp; Assessment Submitted</div>
                        @else
                            <div class="cta-btn waiting">🕐&nbsp; No Assessment Open</div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection