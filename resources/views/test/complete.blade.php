<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Complete</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #FFE66D;
            padding: 2rem;
        }

        .complete-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 3rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        h1 {
            color: #2D3142;
            text-align: center;
            margin-bottom: 2rem;
        }

        .score-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .score-card {
            background: linear-gradient(135deg, #A770EF 0%, #CF8BF3 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 20px;
            text-align: center;
        }

        .score-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .score-number {
            font-size: 2.5rem;
            font-weight: 900;
        }

        .btn-home {
            display: block;
            margin: 2rem auto 0;
            padding: 1rem 3rem;
            background: #2D3142;
            color: white;
            text-decoration: none;
            border-radius: 15px;
            font-weight: 700;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="complete-container">
        <h1>✅ Test Completed!</h1>
        <p style="text-align: center; font-size: 1.2rem; color: #666;">
            Student: <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
        </p>

        <div class="score-grid">
            @foreach($domainScores as $score)
                <div class="score-card">
                    <h3>{{ $score['icon'] }} {{ $score['domain'] }}</h3>
                    <div class="score-number">{{ $score['answered'] }}/{{ $score['total'] }}</div>
                    <small style="font-size: 0.9rem; opacity: 0.9;">{{ $score['score'] }} correct (yes)</small>
                    <p>{{ $score['percentage'] }}%</p>
                </div>
            @endforeach
        </div>

        <a href="/family" class="btn-home">Back to Dashboard</a>
    </div>
</body>
</html>