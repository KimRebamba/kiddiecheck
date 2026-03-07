@extends('family.layout')

@section('title', 'Results History')

@section('content')
<style>
body { background: #fffbf0; font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; }

.rh-page { max-width: 860px; margin: 0 auto; padding: 2rem 1.4rem 5rem; }

/* ── Banner ── */
.rh-banner {
    background: linear-gradient(135deg, #ff8fab 0%, #ffca3a 58%, #8ac926 100%);
    border-radius: 26px;
    padding: 2rem 2rem 2.2rem;
    margin-bottom: 2rem;
    position: relative; overflow: hidden;
    box-shadow: 0 6px 24px rgba(255,143,171,0.28);
}
.rh-banner-bg1 {
    position: absolute; top: -30px; right: -30px;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,0.13); pointer-events: none;
}
.rh-banner-bg2 {
    position: absolute; bottom: -40px; left: 20%;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,0.09); pointer-events: none;
}
.rh-banner-inner { position: relative; z-index: 1; }
.rh-banner-dec  { position: absolute; top: 1rem; right: 1.3rem; font-size: 1.6rem; z-index: 1; }

.rh-title { font-size: 1.75rem; font-weight: 900; color: white; letter-spacing: -0.02em; margin-bottom: 0.25rem; text-shadow: 0 2px 8px rgba(0,0,0,0.1); }
.rh-sub   { font-size: 0.84rem; font-weight: 700; color: rgba(255,255,255,0.88); margin-bottom: 1rem; }

.rh-pills { display: flex; gap: 0.65rem; flex-wrap: wrap; }
.rh-pill  {
    background: rgba(255,255,255,0.24); border: 2px solid rgba(255,255,255,0.42);
    border-radius: 50px; padding: 0.32rem 0.9rem;
    font-size: 0.75rem; font-weight: 800; color: white;
    display: inline-flex; align-items: center; gap: 0.3rem;
}

/* ── Filter tabs ── */
.filter-row { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1.8rem; }
.f-btn {
    background: white; border: 2px solid #ffe0ec; border-radius: 50px;
    padding: 0.4rem 1.1rem; font-size: 0.78rem; font-weight: 800;
    color: #ccc; cursor: pointer; transition: all 0.16s;
    display: inline-flex; align-items: center; gap: 0.4rem;
    font-family: inherit;
}
.f-btn:hover  { border-color: #ff8fab; color: #ff8fab; }
.f-btn.active { background: #ff8fab; border-color: #ff8fab; color: white; box-shadow: 0 3px 12px rgba(255,143,171,0.38); }

/* ── Child section ── */
.child-block { margin-bottom: 2.5rem; }

.child-header {
    display: flex; align-items: center; gap: 0.75rem;
    margin-bottom: 1rem; padding-bottom: 0.7rem;
    border-bottom: 2.5px dashed #fde8f3;
}
.ch-avatar {
    width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; border: 3px solid white;
    box-shadow: 0 3px 10px rgba(0,0,0,0.10); overflow: hidden;
}
.ch-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ch-name  { font-size: 0.97rem; font-weight: 900; color: #2d2d2d; }
.ch-meta  { font-size: 0.7rem; font-weight: 700; color: #ccc; margin-top: 0.05rem; }

.av0 { background: linear-gradient(135deg,#ff8fab,#ffb3c6); }
.av1 { background: linear-gradient(135deg,#ffca3a,#ffe08a); }
.av2 { background: linear-gradient(135deg,#8ac926,#c5f13e); }
.av3 { background: linear-gradient(135deg,#ff8fab,#ffca3a); }
.av4 { background: linear-gradient(135deg,#ffca3a,#8ac926); }
.av5 { background: linear-gradient(135deg,#8ac926,#ff8fab); }

/* ── Result cards ── */
.results-list { display: flex; flex-direction: column; gap: 0.9rem; }

.rc {
    background: white; border-radius: 20px;
    padding: 1.1rem 1.25rem;
    border: 2px solid #fdf0f6;
    box-shadow: 0 3px 12px rgba(0,0,0,0.055);
    display: flex; align-items: center; gap: 1rem;
    transition: transform 0.18s, box-shadow 0.18s;
    opacity: 0; transform: translateY(8px);
}
.rc.visible { opacity: 1; transform: translateY(0); transition: opacity 0.36s ease, transform 0.36s ease, box-shadow 0.18s; }
.rc:hover   { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.09); }

/* score bubble */
.rc-score {
    flex-shrink: 0; width: 64px; height: 64px;
    border-radius: 18px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}
.rc-score-num { font-size: 1.5rem; font-weight: 900; color: white; line-height: 1; }
.rc-score-lbl { font-size: 0.52rem; font-weight: 800; color: rgba(255,255,255,0.82); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px; }

.s-purple { background: linear-gradient(135deg,#8b5cf6,#a78bfa); }
.s-blue   { background: linear-gradient(135deg,#3b82f6,#60a5fa); }
.s-green  { background: linear-gradient(135deg,#8ac926,#c5f13e); }
.s-yellow { background: linear-gradient(135deg,#f59e0b,#fde68a); }
.s-orange { background: linear-gradient(135deg,#f97316,#fdba74); }
.s-pink   { background: linear-gradient(135deg,#ff8fab,#ffb3c6); }

/* card body */
.rc-body { flex: 1; min-width: 0; }
.rc-interp { font-size: 0.9rem; font-weight: 900; color: #222; margin-bottom: 0.18rem; }
.rc-date   { font-size: 0.7rem; font-weight: 700; color: #ccc; margin-bottom: 0.55rem; }

.rc-domains { display: flex; gap: 0.35rem; flex-wrap: wrap; }
.rc-domain  {
    font-size: 0.6rem; font-weight: 800;
    padding: 0.18rem 0.55rem; border-radius: 50px;
    background: #f6f6f6; color: #999;
    white-space: nowrap;
}

/* period tag */
.rc-period {
    flex-shrink: 0; align-self: flex-start;
    font-size: 0.64rem; font-weight: 800;
    padding: 0.28rem 0.7rem; border-radius: 50px;
    background: #fff5fb; color: #ff8fab;
    border: 1.5px solid #ffd6e7;
    white-space: nowrap;
}

/* ── Empty states ── */
.no-tests {
    text-align: center; padding: 1.5rem;
    font-size: 0.82rem; font-weight: 700; color: #e0e0e0;
}
.no-tests-icon { font-size: 2.2rem; margin-bottom: 0.4rem; }

.empty-all {
    text-align: center; padding: 5rem 1rem;
}
.empty-all .ea-icon { font-size: 4.5rem; margin-bottom: 0.8rem; }
.empty-all .ea-title { font-size: 1.05rem; font-weight: 900; color: #ffb3c6; }
.empty-all .ea-sub   { font-size: 0.8rem; font-weight: 700; color: #ddd; margin-top: 0.3rem; }

@media (max-width: 560px) {
    .rh-title  { font-size: 1.45rem; }
    .rc-score  { width: 54px; height: 54px; border-radius: 14px; }
    .rc-score-num { font-size: 1.25rem; }
    .rc-period { display: none; }
}
</style>

@php
    use Carbon\Carbon;
    $user   = Auth::user();
    $family = DB::table('families')->where('user_id', $user->user_id)->first();

    $students = DB::table('students')
        ->where('family_id', $family->user_id)
        ->orderBy('date_of_birth')->get();

    $scaleVersionId    = DB::table('scale_versions')->where('name', 'ECCD 2004')->value('scale_version_id');
    $allChildData      = [];
    $totalResultsCount = 0;

    foreach ($students as $idx => $s) {
        $age    = Carbon::parse($s->date_of_birth)->diff(now());
        $ageStr = $age->y > 0
            ? $age->y . ' yr' . ($age->y > 1 ? 's' : '') . ($age->m > 0 ? ' ' . $age->m . ' mo' : '')
            : $age->m . ' mo';

        $tests = DB::table('tests as t')
            ->join('test_standard_scores as ss', 'ss.test_id', '=', 't.test_id')
            ->leftJoin('assessment_periods as ap', 'ap.period_id', '=', 't.period_id')
            ->where('t.student_id', $s->student_id)
            ->whereIn('t.status', ['completed', 'finalized'])
            ->orderBy('t.test_date', 'asc')
            ->select('t.test_id', 't.test_date', 'ss.standard_score', 'ss.interpretation', 'ap.start_date as period_start')
            ->get();

        $results = [];
        foreach ($tests as $t) {
            $domainScores = DB::table('test_domain_scaled_scores as ds')
                ->join('domains as d', 'd.domain_id', '=', 'ds.domain_id')
                ->where('ds.test_id', $t->test_id)
                ->select('d.name', 'ds.scaled_score')
                ->orderBy('ds.domain_id')->get();

            $sc  = $t->standard_score;
            $cls = match(true) {
                $sc >= 120 => 's-purple',
                $sc >= 110 => 's-blue',
                $sc >= 90  => 's-green',
                $sc >= 80  => 's-yellow',
                $sc >= 70  => 's-orange',
                default    => 's-pink',
            };

            $results[] = [
                'date'    => Carbon::parse($t->test_date)->format('M d, Y'),
                'score'   => $sc,
                'interp'  => $t->interpretation ?? '—',
                'cls'     => $cls,
                'domains' => $domainScores,
                'period'  => $t->period_start ? Carbon::parse($t->period_start)->format('M Y') : null,
            ];
        }

        $totalResultsCount += count($results);
        $allChildData[] = ['s' => $s, 'idx' => $idx, 'age' => $ageStr, 'results' => $results];
    }
@endphp

<div class="rh-page">

    {{-- Banner --}}
    <div class="rh-banner">
        <div class="rh-banner-bg1"></div>
        <div class="rh-banner-bg2"></div>
        <div class="rh-banner-dec">🏆✨</div>
        <div class="rh-banner-inner">
            <div class="rh-title">📊 Results History</div>
            <div class="rh-sub">All your children's assessment results, oldest to latest 🌈</div>
            <div class="rh-pills">
                <span class="rh-pill">👶 {{ $students->count() }} {{ $students->count() == 1 ? 'Child' : 'Children' }}</span>
                <span class="rh-pill">🏅 {{ $totalResultsCount }} {{ $totalResultsCount == 1 ? 'Result' : 'Results' }}</span>
            </div>
        </div>
    </div>

    @if($totalResultsCount === 0)
        {{-- Empty --}}
        <div class="empty-all">
            <div class="ea-icon">📭</div>
            <div class="ea-title">No results yet!</div>
            <div class="ea-sub">Results will appear here once a test is completed.</div>
        </div>
    @else
        {{-- Filter row --}}
        <div class="filter-row">
            <button class="f-btn active" data-target="all">🌈 All</button>
            @foreach($allChildData as $cd)
                @if(count($cd['results']) > 0)
                    <button class="f-btn" data-target="cb-{{ $cd['s']->student_id }}">
                        {{ $cd['s']->first_name }}
                        <span style="opacity:.55;">({{ count($cd['results']) }})</span>
                    </button>
                @endif
            @endforeach
        </div>

        {{-- Child blocks --}}
        @foreach($allChildData as $cd)
            <div class="child-block" id="cb-{{ $cd['s']->student_id }}">

                <div class="child-header">
                    <div class="ch-avatar av{{ $cd['idx'] % 6 }}">
                        @if($cd['s']->feature_path)
                            <img src="{{ asset('storage/' . $cd['s']->feature_path) }}" alt="">
                        @else 🐣 @endif
                    </div>
                    <div>
                        <div class="ch-name">{{ $cd['s']->first_name }} {{ $cd['s']->last_name }}</div>
                        <div class="ch-meta">
                            🎂 {{ $cd['age'] }} &nbsp;·&nbsp;
                            {{ count($cd['results']) }} {{ count($cd['results']) == 1 ? 'result' : 'results' }}
                        </div>
                    </div>
                </div>

                @if(count($cd['results']) === 0)
                    <div class="no-tests">
                        <div class="no-tests-icon">📋</div>
                        No completed tests yet.
                    </div>
                @else
                    <div class="results-list">
                        @foreach($cd['results'] as $r)
                            <div class="rc">
                                <div class="rc-score {{ $r['cls'] }}">
                                    <div class="rc-score-num">{{ $r['score'] }}</div>
                                    <div class="rc-score-lbl">Score</div>
                                </div>
                                <div class="rc-body">
                                    <div class="rc-interp">{{ $r['interp'] }}</div>
                                    <div class="rc-date">📅 {{ $r['date'] }}</div>
                                    @if($r['domains']->isNotEmpty())
                                        <div class="rc-domains">
                                            @foreach($r['domains'] as $d)
                                                <span class="rc-domain">{{ \Str::limit($d->name, 13) }}: {{ $d->scaled_score }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                @if($r['period'])
                                    <div class="rc-period">📆 {{ $r['period'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endforeach
    @endif

</div>

<script>
// Stagger-animate cards on load
document.querySelectorAll('.rc').forEach(function(card, i) {
    setTimeout(function() { card.classList.add('visible'); }, 60 + i * 55);
});

// Filter buttons
document.querySelectorAll('.f-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var target = this.dataset.target;
        document.querySelectorAll('.f-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.child-block').forEach(function(block) {
            block.style.display = (target === 'all' || block.id === target) ? '' : 'none';
        });
    });
});
</script>
@endsection