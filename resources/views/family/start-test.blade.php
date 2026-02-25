<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #F5C518;
            background-image:
                linear-gradient(rgba(255,140,0,0.3) 2px, transparent 2px),
                linear-gradient(90deg, rgba(255,140,0,0.3) 2px, transparent 2px);
            background-size: 50px 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 20px;
            min-height: 100vh;
        }

        .card {
            background: #fff;
            border-radius: 30px;
            padding: 50px 60px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
            text-align: center;
        }

        .icon { font-size: 4rem; margin-bottom: 1rem; }

        h1 {
            font-size: 1.8rem;
            font-weight: 900;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }

        .sub {
            font-size: 0.95rem;
            color: #888;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .info-box {
            background: #fffbea;
            border: 2px dashed #f5a623;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            margin-bottom: 0.4rem;
            color: #555;
        }

        .info-row:last-child { margin-bottom: 0; }

        .info-label { font-weight: 700; color: #999; }
        .info-value { font-weight: 800; color: #333; }

        .btn-start {
            display: block;
            width: 100%;
            padding: 18px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 0 #2e7d32;
            transition: transform 0.15s;
            margin-bottom: 1rem;
        }

        .btn-start:hover { transform: translateY(-2px); color: white; }

        .btn-continue {
            display: block;
            width: 100%;
            padding: 18px;
            background: #ff9f43;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1rem;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 0 #e07b00;
            transition: transform 0.15s;
            margin-bottom: 1rem;
        }

        .btn-continue:hover { transform: translateY(-2px); color: white; }

        .btn-back {
            display: inline-block;
            color: #aaa;
            font-size: 0.88rem;
            font-weight: 700;
            text-decoration: none;
            margin-top: 0.5rem;
        }

        .btn-back:hover { color: #888; }

        @media (max-width: 600px) {
            .card { padding: 30px 20px; }
            h1 { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
<div class="card">

    <div class="icon">📋</div>
    <h1>{{ $student->first_name }}'s Assessment</h1>
    <p class="sub">Ready to begin the ECCD checklist for {{ $student->first_name }} {{ $student->last_name }}?</p>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Assessment Period</span>
            <span class="info-value">
                {{ \Carbon\Carbon::parse($period->start_date)->format('M d') }} –
                {{ \Carbon\Carbon::parse($period->end_date)->format('M d, Y') }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Student</span>
            <span class="info-value">{{ $student->first_name }} {{ $student->last_name }}</span>
        </div>
        @if($existingTest)
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value" style="color:#ff9f43;">⚠️ Test in progress</span>
            </div>
        @endif
    </div>

    @if($existingTest && $answeredCount > 0)
    {{-- Has existing test with some answers — show continue --}}
    <div class="info-row" style="margin-bottom:1.5rem;">
        <span class="info-label">Progress</span>
        <span class="info-value" style="color:#ff9f43;">{{ $answeredCount }}/{{ $totalQuestions }} questions answered</span>
    </div>

    <a href="{{ route('family.tests.question', ['test' => $existingTest->test_id, 'domain' => 1, 'index' => 1]) }}"
       class="btn-continue">
        ▶ Continue Test
    </a>
    @else
    {{-- No test started yet — show Start Test only --}}
    <form method="POST" action="{{ route('family.tests.start', $student->student_id) }}">
        @csrf
        <button type="submit" class="btn-start">
            ▶ Start Test
        </button>
    </form>
    @endif

    <a href="{{ route('family.index') }}" class="btn-back">← Back to Dashboard</a>

</div>
</body>
</html>