<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECCD Test</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

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
        }

        .question-card {
            background: #fff;
            border-radius: 30px;
            padding: 50px 60px;
            max-width: 820px;
            width: 100%;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            border: 3px solid #000;
        }

        .progress-info {
            text-align: center;
            margin-bottom: 32px;
        }

        .progress-answered {
            font-size: 14px;
            color: #7C3AED;
            font-weight: 700;
        }

        .domain-icon {
            text-align: center;
            font-size: 50px;
            margin-bottom: 12px;
        }

        .domain-title {
            text-align: center;
            font-size: 32px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 32px;
        }

        .question-text {
            text-align: center;
            font-size: 20px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 40px;
            font-weight: 500;
        }

        .question-answered {
            color: #999;
        }

        .btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 24px;
        }

        .btn-answer {
            padding: 28px;
            border: none;
            border-radius: 16px;
            font-size: 28px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: inherit;
            transform: scale(1);
        }

        .btn-yes {
            background: #4CAF50;
            color: #fff;
            box-shadow: 0 4px 0 #2e7d32;
        }

        .btn-no {
            background: #F06060;
            color: #fff;
            box-shadow: 0 4px 0 #c62828;
        }

        .btn-answer:hover {
            transform: translateY(-2px);
        }

        .btn-answer:active {
            transform: translateY(2px) scale(1);
        }

        /* Selected state - bigger with lighter color */
        .btn-yes.selected {
            background: #A5D6A7;
            color: #2E7D32;
            box-shadow: 0 6px 0 #66BB6A, 0 0 0 4px #C8E6C9;
            transform: scale(1.05);
            border: 3px solid #4CAF50;
        }

        .btn-no.selected {
            background: #FFCDD2;
            color: #C62828;
            box-shadow: 0 6px 0 #EF5350, 0 0 0 4px #FFEBEE;
            transform: scale(1.05);
            border: 3px solid #F06060;
        }

        .btn-yes.selected:hover {
            transform: scale(1.05);
        }

        .btn-no.selected:hover {
            transform: scale(1.05);
        }

        .comment-section {
            display: none;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease;
        }

        .comment-section.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-box {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 15px;
            color: #666;
            resize: vertical;
            min-height: 70px;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .comment-box::placeholder {
            color: #aaa;
        }

        .comment-box:focus {
            border-color: #7C3AED;
        }

        .nav-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
            gap: 12px;
        }

        .nav-center {
            display: flex;
            gap: 12px;
        }

        .btn-nav {
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid #ccc;
            cursor: pointer;
            background: #fff;
            color: #333;
        }

        .btn-nav:hover {
            background: #f5f5f5;
            transform: translateY(-1px);
        }

        .btn-nav.hidden {
            visibility: hidden;
        }

        .btn-prev {
            background: #f5f5f5;
            border-color: #999;
            color: #666;
        }

        .btn-prev:hover {
            background: #e0e0e0;
            color: #333;
        }

        @media (max-width: 768px) {
            .question-card {
                padding: 40px 30px;
            }
            
            .domain-title {
                font-size: 26px;
            }
            
            .question-text {
                font-size: 18px;
            }
            
            .btn-answer {
                font-size: 24px;
                padding: 24px;
            }

            .btn-yes.selected,
            .btn-no.selected {
                transform: scale(1.03);
            }

            .nav-footer {
                flex-wrap: wrap;
            }

            .btn-prev {
                width: 100%;
                order: -1;
            }

            .nav-center {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="question-card">
    <div class="progress-info">
        <div class="progress-answered">{{ $totalAnswered }} of {{ $totalQuestions }} answered</div>
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
        $icon = $domainIcons[$currentDomain->domain_name] ?? '📋';
    @endphp

    <div class="domain-icon">{{ $icon }}</div>
    <div class="domain-title">{{ $currentDomain->domain_name }}</div>
    <div class="question-text {{ $existingResponse ? 'question-answered' : '' }}">{{ $questionText }}</div>

    <form method="POST" action="{{ route('family.tests.question.submit', ['test' => $testId, 'domain' => $domainNumber, 'index' => $questionIndex]) }}" id="answerForm">
        @csrf
        <input type="hidden" name="response" id="responseInput" value="{{ $existingResponse ?? '' }}">

        <div class="btn-group">
            <button type="button" onclick="selectAnswer('yes')" class="btn-answer btn-yes {{ $existingResponse === 'yes' ? 'selected' : '' }}" id="btnYes">
                YES
            </button>

            <button type="button" onclick="selectAnswer('no')" class="btn-answer btn-no {{ $existingResponse === 'no' ? 'selected' : '' }}" id="btnNo">
                NO
            </button>
        </div>

        <div class="comment-section {{ $existingResponse === 'no' ? 'show' : '' }}" id="commentSection">
            <textarea
                class="comment-box"
                name="comment"
                placeholder="Add comment (optional)"
                id="commentBox"
            ></textarea>
        </div>

        <div class="nav-footer">
            @if($prevDomain && $prevIndex)
                <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $prevDomain, 'index' => $prevIndex]) }}" class="btn-nav btn-prev">
                    ← Previous
                </a>
            @else
                <span class="btn-nav btn-prev" style="visibility: hidden;">← Previous</span>
            @endif

            <div class="nav-center">
                <button type="submit" class="btn-nav" id="btnNext">
                    Next →
                </button>

                @if($nextDomain && $nextIndex)
                    <a href="{{ route('family.tests.question', ['test' => $testId, 'domain' => $nextDomain, 'index' => $nextIndex]) }}" class="btn-nav">
                        Skip →
                    </a>
                @else
                    <a href="{{ route('family.tests.result', $testId) }}" class="btn-nav">
                        Review →
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
function selectAnswer(value) {
    const btnYes = document.getElementById('btnYes');
    const btnNo = document.getElementById('btnNo');
    const commentSection = document.getElementById('commentSection');
    const responseInput = document.getElementById('responseInput');

    // Remove selected class from both buttons
    btnYes.classList.remove('selected');
    btnNo.classList.remove('selected');

    // Add selected class to clicked button
    if (value === 'yes') {
        btnYes.classList.add('selected');
        commentSection.classList.remove('show');
    } else {
        btnNo.classList.add('selected');
        commentSection.classList.add('show');
    }

    // Update hidden input value
    responseInput.value = value;
}

// Form validation - ensure answer is selected before submitting
document.getElementById('answerForm').addEventListener('submit', function(e) {
    const responseValue = document.getElementById('responseInput').value;
    
    if (!responseValue) {
        e.preventDefault();
        alert('Please select YES or NO before continuing.');
    }
});
</script>

</body>
</html>