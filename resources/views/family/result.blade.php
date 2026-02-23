@extends('family.layout')

@section('content')
<style>
.result-card {
    background: #fff;
    border-radius: 20px;
    padding: 40px 48px;
    max-width: 820px;
    margin: 0 auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.page-title {
    text-align: center;
    font-size: 22px;
    font-weight: 800;
    color: #2f5130;
    margin-bottom: 4px;
}

.student-name {
    text-align: center;
    font-size: 14px;
    color: #888;
    margin-bottom: 20px;
}

.progress-bar {
    background: #7eaf64;
    border-radius: 10px;
    padding: 14px 20px;
    text-align: center;
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
}

.alert {
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 14px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 12px;
}

.alert-danger {
    background: #FDECEA;
    color: #C62828;
    border: 1px solid #F5C6C6;
}

.alert-success {
    background: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #C8E6C9;
}

.legend {
    display: flex;
    gap: 20px;
    justify-content: center;
    font-size: 13px;
    color: #555;
    margin-bottom: 20px;
}

.legend span {
    display: flex;
    align-items: center;
    gap: 6px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.dot-complete {
    background: #4CAF50;
}

.dot-incomplete {
    background: #FF9800;
}

.domains-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 28px;
}

@media (max-width: 640px) {
    .domains-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

.domain-card {
    border-radius: 14px;
    padding: 16px 14px;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    display: block;
    transition: transform 0.15s, opacity 0.15s;
}

.domain-card:hover {
    transform: translateY(-2px);
    opacity: 0.92;
}

.domain-card.complete {
    background: #4CAF50;
    color: #fff;
}

.domain-card.incomplete {
    background: #FF9800;
    color: #fff;
}

.d-name {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 6px;
    opacity: 0.9;
}

.d-score {
    font-size: 22px;
    font-weight: 900;
    margin-bottom: 6px;
}

.d-badge {
    display: inline-block;
    background: rgba(0,0,0,0.18);
    border-radius: 20px;
    padding: 3px 10px;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 8px;
}

.d-badge.ok {
    background: rgba(255,255,255,0.28);
}

.d-yes {
    font-size: 11px;
    opacity: 0.85;
}

.btn-answer {
    display: block;
    margin-top: 10px;
    background: rgba(0,0,0,0.18);
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    text-decoration: none;
}

.btn-answer:hover {
    background: rgba(0,0,0,0.30);
}

.bottom-row {
    display: flex;
    justify-content: center;
    gap: 14px;
}

.btn-continue, .btn-submit {
    border: none;
    border-radius: 10px;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}

.btn-continue {
    background: #2f5130;
    color: #fff;
}

.btn-continue:hover {
    background: #3a6340;
}

.btn-submit {
    background: #4CAF50;
    color: #fff;
}

.btn-submit:disabled {
    background: #aaa;
    cursor: not-allowed;
    opacity: 0.7;
}

.btn-submit:not(:disabled):hover {
    background: #388E3C;
}
</style>

<div class="result-card">
    <div class="page-title">📋 Test Review Dashboard</div>
    <div class="student-name">Student: {{ $test->first_name }} {{ $test->last_name }}</div>

    <div class="progress-bar">
        {{ $totalAnswered }} / {{ $totalQuestions }} Questions Answered
    </div>

    @if($allAnswered)
        <div class="alert alert-success">
            ✓ All questions answered! You can now submit the test.
        </div>
    @else
        <div class="alert alert-danger">
            ⚠ You must answer ALL questions before submitting the test!
            {{ $totalQuestions - $totalAnswered }} questions remaining
        </div>
    @endif

    <div class="legend">
        <span><span class="dot dot-complete"></span> Complete</span>
        <span><span class="dot dot-incomplete"></span> Incomplete</span>
    </div>

    @php
        $domainIcons = [
            'Gross Motor'         => '⚡',
            'Fine Motor'          => '✏️',
            'Self-Help'           => '🛠️',
            'Receptive Language'  => '👂',
            'Expressive Language' => '💬',
            'Cognitive'           => '🧠',
            'Social-Emotional'    => '❤️',
        ];
    @endphp

    <div class="domains-grid">
        @foreach($domainStats as $d)
            @php
                $icon = $domainIcons[$d['domain_name']] ?? '📋';
                $cssClass = $d['is_complete'] ? 'complete' : 'incomplete';
                $badgeText = $d['is_complete'] ? '✓ Complete' : $d['answered'] . ' answered';
            @endphp

            <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $d['domain_number'], 'index' => $d['first_unanswered_index']]) }}"
               class="domain-card {{ $cssClass }}">
                <div class="d-name">{{ $icon }} {{ $d['domain_name'] }}</div>
                <div class="d-score">{{ $d['answered'] }}/{{ $d['total'] }}</div>
                <div class="d-badge {{ $d['is_complete'] ? 'ok' : '' }}">{{ $badgeText }}</div>

                @if($d['is_complete'])
                    <div class="d-yes">{{ $d['yes_count'] }} YES</div>
                @else
                    <span class="btn-answer">Answer Questions →</span>
                @endif
            </a>
        @endforeach
    </div>

    <div class="bottom-row">
        @php
            $firstIncomplete = collect($domainStats)->first(fn($d) => !$d['is_complete']);
            $continueDomain = $firstIncomplete ? $firstIncomplete['domain_number'] : 1;
            $continueIndex = $firstIncomplete ? $firstIncomplete['first_unanswered_index'] : 1;
        @endphp

        <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $continueDomain, 'index' => $continueIndex]) }}"
           class="btn-continue">
            Continue Answering
        </a>

        <form method="POST" action="{{ route('family.tests.finalize', $testId) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-submit" {{ $allAnswered ? '' : 'disabled' }}>
                @if($allAnswered)
                    ✓ Submit Test
                @else
                    ✓ Submit Test ({{ $totalQuestions - $totalAnswered }} remaining)
                @endif
            </button>
        </form>
    </div>
</div>
@endsection