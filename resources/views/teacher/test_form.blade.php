@extends('teacher.layout')

@section('content')
<!-- Text-to-Speech Styles -->
<style>
.btn-speech {
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 12px;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 8px;
    vertical-align: middle;
}

.btn-speech:hover {
    background: #45a049;
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
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
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    display: inline-block;
    margin-left: 5px;
    vertical-align: middle;
}

.speech-status.visible {
    opacity: 1;
}

.question-with-tts {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 8px;
}
</style>
<div class="d-flex align-items-center mb-3">
  <h1 class="h4 mb-0">Assessment - {{ $test->student->first_name }} {{ $test->student->last_name }}</h1>
  <div class="ms-auto">
    <a class="btn btn-sm btn-outline-secondary" href="{{ route('teacher.index') }}">Back</a>
  </div>
</div>

@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($progressPct !== null)
  <div class="progress mb-2" style="height: 6px;">
    <div class="progress-bar" role="progressbar" style="width: {{ $progressPct }}%" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
  <div class="text-muted mb-3">Progress: {{ $answeredCount }} / {{ $totalQuestions }} ({{ $progressPct }}%)</div>
@endif

<form method="post" action="{{ route('teacher.tests.form.submit', $test->test_id) }}">
  @csrf

  @foreach($domains as $domain)
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="mb-0">{{ $domain->name }}</h5>
      </div>
      <div class="card-body p-0">
        @if($domain->questions->isEmpty())
          <p class="p-3 text-muted">No questions in this domain.</p>
        @else
          <table class="table mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 60%;">Question</th>
                <th style="width: 40%;">Answer</th>
              </tr>
            </thead>
            <tbody>
              @foreach($domain->questions as $q)
                @php
                  $existingAnswer = $existing[$q->question_id] ?? null;
                @endphp
                <tr>
                  <td>
                    <div class="question-with-tts">
                      <div class="fw-semibold">{{ $q->text }}</div>
                      <button class="btn-speech" onclick="speakQuestion('{{ $q->text }}', this)" title="Read question aloud">
                        🔊
                      </button>
                      <div class="speech-status"></div>
                    </div>
                    @if($q->display_text)
                      <div class="text-muted small">{{ $q->display_text }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group" role="group" aria-label="Answer">
                      <input type="radio" class="btn-check" name="q_{{ $q->question_id }}" id="q{{ $q->question_id }}_yes" value="yes" {{ $existingAnswer === 'yes' ? 'checked' : '' }}>
                      <label class="btn btn-outline-success btn-sm" for="q{{ $q->question_id }}_yes">Yes</label>

                      <input type="radio" class="btn-check" name="q_{{ $q->question_id }}" id="q{{ $q->question_id }}_no" value="no" {{ $existingAnswer === 'no' ? 'checked' : '' }}>
                      <label class="btn btn-outline-danger btn-sm" for="q{{ $q->question_id }}_no">No</label>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  @endforeach

  <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Save &amp; View Result</button>
    <a href="{{ route('teacher.index') }}" class="btn btn-outline-secondary">Cancel</a>
  </div>
</form>

<style>
@import url('https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Nunito:wght@400;600;700;800;900&display=swap');

:root {
  --violet:      #845EC2;
  --violet-soft: #EDE4FF;
  --violet-bg:   #F8F4FF;
  --teal:        #2EC4B6;
  --teal-soft:   #C8F4F1;
  --coral:       #FF6B8A;
  --coral-soft:  #FFE0E8;
  --mint:        #52C27B;
  --mint-soft:   #D4F5E2;
  --lemon:       #F9C74F;
  --lemon-soft:  #FFF6CC;
  --peach:       #FF9A76;
  --text:        #2D2040;
  --text-muted:  #8A7A99;
  --radius:      14px;
  --shadow:      0 4px 20px rgba(100,60,160,0.09);
}

body { font-family: 'Nunito', sans-serif !important; background: var(--violet-bg); color: var(--text); }

/* ── PAGE HEADER ── */
.h4.fw-bold {
  font-family: 'Baloo 2', cursive !important;
  font-size: 1.45rem !important;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.student-subhead {
  font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-top: 1px;
}

/* ── BACK / CANCEL BUTTONS ── */
.btn-ghost-back {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.8rem;
  background: white; color: var(--text-muted);
  border: 1.5px solid #E8E0F0; border-radius: 10px;
  padding: 6px 14px; text-decoration: none; display: inline-flex; align-items: center;
  transition: all 0.18s;
}
.btn-ghost-back:hover { background: var(--violet-soft); color: var(--violet); border-color: var(--violet-soft); }
.btn-ghost-cancel {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.85rem;
  background: #F0E8FF; color: var(--text-muted);
  border: none; border-radius: 10px; padding: 8px 18px;
  text-decoration: none; display: inline-flex; align-items: center;
  transition: all 0.18s;
}
.btn-ghost-cancel:hover { background: var(--violet-soft); color: var(--violet); }

/* ── ALERTS ── */
.alert-custom {
  border-radius: 10px; padding: 11px 15px;
  font-size: 0.86rem; font-weight: 700; line-height: 1.5;
}
.alert-danger-custom  { background: var(--coral-soft);  border-left: 4px solid var(--coral);  color: #a0203a; }
.alert-success-custom { background: var(--mint-soft);   border-left: 4px solid var(--mint);   color: #1a6640; }

/* ── PROGRESS ── */
.progress-wrap { background: white; border-radius: var(--radius); padding: 14px 18px; box-shadow: var(--shadow); }
.progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.progress-label  { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-muted); }
.progress-count  { font-size: 0.78rem; font-weight: 700; color: var(--violet); }
.progress-track  { height: 8px; background: #F0E8FF; border-radius: 10px; overflow: hidden; }
.progress-fill   { height: 100%; border-radius: 10px; background: linear-gradient(90deg, var(--violet), var(--coral)); transition: width 0.6s ease; }

/* ── CARDS ── */
.card {
  border: none !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  overflow: hidden;
  animation: fadeUp 0.35s ease both;
  transition: box-shadow 0.2s;
}
@keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

/* ── CARD HEADERS ── */
.card-header {
  background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 13px 18px !important;
  display: flex; align-items: center; gap: 10px;
}
.card-header h5 {
  font-family: 'Baloo 2', cursive !important;
  font-size: 0.95rem !important; font-weight: 800 !important; color: var(--text) !important;
  margin: 0 !important;
}
/* cycle domain dot colors */
.card:nth-child(7n+1) .domain-dot { background: var(--violet); }
.card:nth-child(7n+2) .domain-dot { background: var(--teal); }
.card:nth-child(7n+3) .domain-dot { background: var(--coral); }
.card:nth-child(7n+4) .domain-dot { background: var(--mint); }
.card:nth-child(7n+5) .domain-dot { background: var(--lemon); }
.card:nth-child(7n+6) .domain-dot { background: var(--peach); }
.card:nth-child(7n+7) .domain-dot { background: var(--teal); }
.domain-dot {
  width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}

/* ── TABLE ── */
.table { margin-bottom: 0 !important; }
.table thead th {
  font-size: 0.7rem !important; font-weight: 800 !important;
  text-transform: uppercase; letter-spacing: 0.07em;
  color: var(--text-muted) !important; background: #FDFBFF !important;
  border-bottom: 2px solid #F0E8FF !important;
  padding: 10px 16px !important;
}
.table tbody tr { border-bottom: 1px solid #F9F5FF !important; transition: background 0.15s; }
.table tbody tr:last-child { border-bottom: none !important; }
.table tbody td { padding: 13px 16px !important; vertical-align: middle !important; border: none !important; }
.q-row:hover { background: #FDFBFF !important; }
.q-answered  { background: #FDFBFF; }

.q-text { font-weight: 800; font-size: 0.88rem; color: var(--text); line-height: 1.5; }
.q-sub  { font-size: 0.76rem; color: var(--text-muted); font-weight: 600; margin-top: 2px; }
.text-muted-italic { font-size: 0.85rem; color: var(--text-muted); font-style: italic; font-weight: 600; }

/* ── YES / NO BUTTONS ── */
.btn-answer {
  font-family: 'Nunito', sans-serif !important;
  font-weight: 800 !important; font-size: 0.78rem !important;
  padding: 5px 14px !important; border-radius: 0 !important;
  transition: all 0.15s !important;
}
.btn-group .btn-answer:first-of-type { border-radius: 9px 0 0 9px !important; }
.btn-group .btn-answer:last-of-type  { border-radius: 0 9px 9px 0 !important; }

/* Yes — mint */
.btn-yes {
  color: var(--mint) !important;
  border: 1.5px solid var(--mint-soft) !important;
  background: white !important;
}
.btn-yes:hover { background: var(--mint-soft) !important; }
.btn-check:checked + .btn-yes {
  background: var(--mint) !important;
  color: white !important;
  border-color: var(--mint) !important;
  box-shadow: 0 2px 8px rgba(82,194,123,0.3) !important;
}

/* No — coral */
.btn-no {
  color: var(--coral) !important;
  border: 1.5px solid var(--coral-soft) !important;
  background: white !important;
}
.btn-no:hover { background: var(--coral-soft) !important; }
.btn-check:checked + .btn-no {
  background: var(--coral) !important;
  color: white !important;
  border-color: var(--coral) !important;
  box-shadow: 0 2px 8px rgba(255,107,138,0.3) !important;
}

/* ── SAVE BUTTON ── */
.btn-primary-grad {
  font-family: 'Nunito', sans-serif; font-weight: 800; font-size: 0.88rem;
  background: linear-gradient(135deg, var(--violet), var(--coral));
  color: white; border: none; border-radius: 10px; padding: 9px 22px;
  box-shadow: 0 3px 12px rgba(132,94,194,0.28);
  display: inline-flex; align-items: center; transition: all 0.18s;
  cursor: pointer;
}
.btn-primary-grad:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(132,94,194,0.38); color: white; }
</style>

<!-- Text-to-Speech JavaScript -->
<script>
// Text-to-Speech functionality
let speechSynthesis = window.speechSynthesis;
let currentUtterance = null;
let isSpeaking = false;

function speakQuestion(text, buttonElement) {
    event.preventDefault(); // Prevent form submission
    event.stopPropagation(); // Stop event bubbling
    
    const speechBtn = buttonElement;
    const statusElement = buttonElement.nextElementSibling;
    
    // Check if speech synthesis is supported
    if (!('speechSynthesis' in window)) {
        showStatus(statusElement, 'Speech not supported in this browser', 'error');
        return;
    }
    
    // Check if any voices are available
    if (speechSynthesis.getVoices().length === 0) {
        showStatus(statusElement, 'No voices available', 'error');
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
        showStatus(statusElement, 'Speaking...', 'speaking');
    };
    
    currentUtterance.onend = function() {
        stopSpeech(statusElement, speechBtn);
    };
    
    currentUtterance.onerror = function(event) {
        console.error('Speech error:', event);
        showStatus(statusElement, 'Speech error occurred', 'error');
        stopSpeech(statusElement, speechBtn);
    };
    
    // Start speaking
    speechSynthesis.speak(currentUtterance);
}

function stopSpeech(statusElement, speechBtn) {
    isSpeaking = false;
    currentUtterance = null;
    
    speechBtn.classList.remove('speaking');
    speechBtn.innerHTML = '🔊';
    speechBtn.title = 'Read question aloud';
    
    // Hide status after a short delay
    setTimeout(() => {
        statusElement.classList.remove('visible');
    }, 1000);
}

function showStatus(statusElement, message, type = 'info') {
    statusElement.textContent = message;
    statusElement.className = 'speech-status visible';
    
    // Set color based on type
    if (type === 'error') {
        statusElement.style.background = 'rgba(244, 67, 54, 0.9)';
    } else if (type === 'speaking') {
        statusElement.style.background = 'rgba(255, 152, 0, 0.9)';
    } else {
        statusElement.style.background = 'rgba(76, 175, 80, 0.9)';
    }
    
    // Auto-hide after 3 seconds for info messages
    if (type === 'info') {
        setTimeout(() => {
            statusElement.classList.remove('visible');
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
</script>
@endsection
