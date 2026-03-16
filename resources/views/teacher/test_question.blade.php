@extends('teacher.layout')

@section('content')
<!-- Text-to-Speech Styles -->
<style>
.btn-speech {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    transition: all 0.3s ease;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-speech:hover {
    background: #45a049;
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
}

.btn-speech.speaking {
    background: #FF9800;
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
    position: fixed;
    top: 80px;
    right: 20px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 10;
    pointer-events: none;
}

.speech-status.visible {
    opacity: 1;
}
</style>

<!-- Text-to-Speech Button -->
<button id="speechBtn" class="btn-speech" onclick="toggleSpeech(event)" title="Read question aloud">
    🔊
</button>
<div id="speechStatus" class="speech-status"></div>
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back</a>
  </div>
</div>

@php
  $totalQuestions = \App\Models\Domain::with('questions')->get()->sum(fn($d)=>$d->questions->count());
  $answeredCount = $test->responses->count();
  $progressPct = $totalQuestions ? round(($answeredCount / max(1,$totalQuestions)) * 100) : null;
@endphp

@if($progressPct !== null)
  <div class="progress mb-3" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: {{ $progressPct }}%" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: {{ $answeredCount }} / {{ $totalQuestions }} ({{ $progressPct }}%)</div>
@endif

<div class="card mb-3">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <div class="text-muted">Domain</div>
        <h2 class="h5 mb-0">{{ $domain->name }}</h2>
      </div>
      <span class="badge bg-primary">Question {{ $index + 1 }}</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <p class="fs-5">{{ $question->text }}</p>
    @if($question->display_text)
      <p><strong>Display Text:</strong> {{ $question->display_text }}</p>
    @endif

    <form method="post" action="{{ route('teacher.tests.question.submit', [$test->test_id, $domain->domain_id, $index]) }}" class="mt-3">
      @csrf
      <div class="btn-group" role="group" aria-label="Answer">
        <input type="radio" class="btn-check" name="answer" id="answerYes" value="yes" required>
        <label class="btn btn-outline-success" for="answerYes">Yes</label>

        <input type="radio" class="btn-check" name="answer" id="answerNo" value="no">
        <label class="btn btn-outline-danger" for="answerNo">No</label>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Next</button>
      </div>
    </form>
    <div class="mt-3 d-flex gap-2">
      <form method="post" action="{{ route('teacher.tests.pause', $test->test_id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Pause</button>
      </form>
      <form method="post" action="{{ route('teacher.tests.cancel', $test->test_id) }}">
        @csrf
        <button type="submit" class="btn btn-outline-danger">Cancel</button>
      </form>
    </div>
  </div>
</div>

<!-- Text-to-Speech JavaScript -->
<script>
// Text-to-Speech functionality
let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;
let isSpeaking = false;

function toggleSpeech(event) {
    if (event) {
        event.preventDefault(); // Prevent form submission
        event.stopPropagation(); // Stop event bubbling
    }
    
    const speechBtn = document.getElementById('speechBtn');
    const speechStatus = document.getElementById('speechStatus');
    const questionText = document.querySelector('.fs-5').innerText.trim();
    
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
    
    // Try to use a male voice (more appropriate for teacher interface)
    const voices = speechSynthesis.getVoices();
    const maleVoice = voices.find(voice => 
        voice.name.includes('Male') || 
        voice.name.includes('male') ||
        voice.name.includes('Alex') ||
        voice.name.includes('Daniel') ||
        voice.name.includes('Google US English Male')
    );
    
    if (maleVoice) {
        currentUtterance.voice = maleVoice;
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

// Stop speaking when any button is clicked
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'BUTTON' && isSpeaking) {
        toggleSpeech();
    }
});
</script>
@endsection
