<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ECCD Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Arial, sans-serif;
            background: #FFE66D;
            background-image: 
                repeating-linear-gradient(0deg, transparent, transparent 49px, #F4B740 49px, #F4B740 50px),
                repeating-linear-gradient(90deg, transparent, transparent 49px, #F4B740 49px, #F4B740 50px);
            background-size: 50px 50px;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 30px;
            padding: 3rem;
            border: 3px solid #2D3142;
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: #ddd;
            z-index: 1000;
        }

        .progress-fill {
            height: 100%;
            background: #A770EF;
            width: {{ $progressPercentage }}%;
            transition: width 0.3s ease;
        }

        .counter {
            text-align: center;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .answered-info {
            text-align: center;
            color: #A770EF;
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 0.95rem;
        }

        .domain-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .icon { font-size: 3rem; }
        .title { font-size: 2rem; font-weight: bold; color: #2D3142; }

        .question {
            font-size: 1.3rem;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 2rem;
            font-size: 2rem;
            font-weight: bold;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            color: white;
        }

        .btn-yes { background: #6BCF7F; }
        .btn-no { background: #FF6B6B; }
        .btn:hover { opacity: 0.9; transform: scale(1.02); }

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 15px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .nav a, .nav button {
            padding: 0.7rem 1.5rem;
            background: white;
            border: 2px solid #2D3142;
            border-radius: 10px;
            text-decoration: none;
            color: #2D3142;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
        }

        .nav a:hover, .nav button:hover { background: #2D3142; color: white; }
        .disabled { opacity: 0.3; pointer-events: none; }
        
        .btn-review {
            background: #FFA500 !important;
            color: white !important;
            border-color: #FFA500 !important;
        }
        
        .btn-review:hover {
            background: #FF8C00 !important;
        }

        /* Text-to-Speech Styles */
        .btn-speech {
            background: #6BCF7F;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(107, 207, 127, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
            vertical-align: middle;
        }

        .btn-speech:hover {
            background: #4CAF50;
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(107, 207, 127, 0.4);
        }

        .btn-speech.speaking {
            background: #FFA500;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .btn-speech:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            animation: none;
        }

        .speech-status {
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            display: inline-block;
            margin-left: 10px;
            vertical-align: middle;
        }

        .speech-status.visible {
            opacity: 1;
        }

        .question-with-tts {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="progress-bar"><div class="progress-fill"></div></div>

    <div class="container">
        <div class="counter">Question {{ $currentQuestionNumber }} of {{ $totalQuestions }}</div>
        <div class="answered-info">{{ $answeredCount }} of {{ $totalQuestions }} answered</div>
        
        <div class="domain-header">
            <div class="icon">{{ $domainIcon }}</div>
            <div class="title">{{ $currentDomain->name }}</div>
        </div>

        <div class="question-with-tts">
            <p class="question">{{ $currentQuestion->display_text ?? $currentQuestion->text }}</p>
            <button id="speechBtn" class="btn-speech" onclick="toggleSpeech()" title="Read question aloud">
                🔊
            </button>
            <div id="speechStatus" class="speech-status"></div>
        </div>

        <form action="{{ route('test.submit-answer', $test->test_id) }}" method="POST" id="form">
            @csrf
            <input type="hidden" name="question_id" value="{{ $currentQuestion->question_id }}">
            <input type="hidden" name="response" id="response">

            <div class="buttons">
                <button type="button" class="btn btn-yes" onclick="submitAnswer('yes')">YES</button>
                <button type="button" class="btn btn-no" onclick="submitAnswer('no')">NO</button>
            </div>

            <textarea name="notes" placeholder="Add comment (optional)">{{ $existingResponse->notes ?? '' }}</textarea>

            <div class="nav">
                @if($previousQuestionId)
                    <a href="{{ route('test.question', [$test->test_id, $previousQuestionId]) }}">← Previous</a>
                @else
                    <span class="nav a disabled">← Previous</span>
                @endif

                @if($isLastQuestion)
                    <a href="{{ route('test.review', $test->test_id) }}" class="btn-review">Review & Submit →</a>
                @elseif($nextQuestionId)
                    <a href="{{ route('test.question', [$test->test_id, $nextQuestionId]) }}">Skip / Next →</a>
                @endif
            </div>
        </form>
    </div>

    <script>
        // Text-to-Speech functionality
        let speechSynthesis = window.speechSynthesis;
        let currentUtterance = null;
        let isSpeaking = false;

        function toggleSpeech() {
            const speechBtn = document.getElementById('speechBtn');
            const speechStatus = document.getElementById('speechStatus');
            const questionText = document.querySelector('.question').innerText.trim();
            
            if (isSpeaking) {
                // Stop speaking
                if (currentUtterance) {
                    speechSynthesis.cancel();
                }
                stopSpeech();
            } else {
                // Start speaking
                speakQuestion(questionText);
            }
        }

        function speakQuestion(text) {
            const speechBtn = document.getElementById('speechBtn');
            const speechStatus = document.getElementById('speechStatus');
            
            // Check if speech synthesis is supported
            if (!('speechSynthesis' in window)) {
                showStatus('Speech not supported in this browser', 'error');
                return;
            }
            
            // Check if any voices are available
            if (speechSynthesis.getVoices().length === 0) {
                showStatus('No voices available', 'error');
                return;
            }
            
            // Cancel any ongoing speech
            speechSynthesis.cancel();
            
            // Create new utterance
            currentUtterance = new SpeechSynthesisUtterance(text);
            
            // Configure voice settings
            currentUtterance.rate = 0.9;      // Slightly slower for clarity
            currentUtterance.pitch = 1.0;     // Normal pitch
            currentUtterance.volume = 1.0;    // Full volume
            
            // Try to use a female voice (friendly for assessments)
            const voices = speechSynthesis.getVoices();
            const femaleVoice = voices.find(voice => 
                voice.name.includes('Female') || 
                voice.name.includes('female') ||
                voice.name.includes('Samantha') ||
                voice.name.includes('Karen') ||
                voice.name.includes('Moira') ||
                voice.lang.includes('en') && voice.name.includes('Google')
            );
            
            if (femaleVoice) {
                currentUtterance.voice = femaleVoice;
            } else {
                // Fallback to any English voice
                const englishVoice = voices.find(voice => voice.lang.includes('en'));
                if (englishVoice) {
                    currentUtterance.voice = englishVoice;
                }
            }
            
            // Event handlers
            currentUtterance.onstart = function() {
                isSpeaking = true;
                speechBtn.classList.add('speaking');
                speechBtn.innerHTML = '🔇';
                speechBtn.title = 'Stop speaking';
                showStatus('Speaking...', 'speaking');
            };
            
            currentUtterance.onend = function() {
                stopSpeech();
            };
            
            currentUtterance.onerror = function(event) {
                console.error('Speech error:', event);
                showStatus('Speech error occurred', 'error');
                stopSpeech();
            };
            
            // Start speaking
            speechSynthesis.speak(currentUtterance);
        }

        function stopSpeech() {
            const speechBtn = document.getElementById('speechBtn');
            const speechStatus = document.getElementById('speechStatus');
            
            isSpeaking = false;
            currentUtterance = null;
            
            speechBtn.classList.remove('speaking');
            speechBtn.innerHTML = '🔊';
            speechBtn.title = 'Read question aloud';
            
            // Hide status after a short delay
            setTimeout(() => {
                speechStatus.classList.remove('visible');
            }, 1000);
        }

        function showStatus(message, type = 'info') {
            const speechStatus = document.getElementById('speechStatus');
            
            speechStatus.textContent = message;
            speechStatus.className = 'speech-status visible';
            
            // Set color based on type
            if (type === 'error') {
                speechStatus.style.background = 'rgba(244, 67, 54, 0.9)';
            } else if (type === 'speaking') {
                speechStatus.style.background = 'rgba(255, 152, 0, 0.9)';
            } else {
                speechStatus.style.background = 'rgba(76, 175, 80, 0.9)';
            }
            
            // Auto-hide after 3 seconds for info messages
            if (type === 'info') {
                setTimeout(() => {
                    speechStatus.classList.remove('visible');
                }, 3000);
            }
        }

        // Initialize voices when page loads
        window.addEventListener('load', function() {
            // Some browsers need to load voices asynchronously
            if (speechSynthesis.getVoices().length === 0) {
                speechSynthesis.addEventListener('voiceschanged', function() {
                    console.log('Voices loaded:', speechSynthesis.getVoices().length);
                });
            } else {
                console.log('Voices available:', speechSynthesis.getVoices().length);
            }
        });

        // Stop speaking when answer is submitted
        function submitAnswer(answer) {
            if (isSpeaking) {
                toggleSpeech();
            }
            document.getElementById('response').value = answer;
            document.getElementById('form').submit();
        }
    </script>
</body>
</html>