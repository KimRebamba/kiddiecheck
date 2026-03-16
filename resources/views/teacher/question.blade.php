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
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">
            Question {{ $questionIndex + 1 }}
        </h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 max-w-2xl">
            <div class="mb-4">
                <p class="text-lg font-medium text-gray-700 mb-2">
                    {{ $question->text }}
                </p>
            </div>
            
            <div class="space-y-4">
                <form action="{{ route('teacher.tests.question.submit', [$test->test_id, $domainNumber, $questionIndex]) }}" method="POST">
                    @csrf
                    
                    <div class="flex flex-col space-y-4">
                        <div class="flex space-x-4">
                            <button type="submit" name="response" value="yes" 
                                class="px-6 py-3 bg-green-500 text-white rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                Yes
                            </button>
                            <button type="submit" name="response" value="no" 
                                class="px-6 py-3 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                No
                            </button>
                        </div>
                        
                        @if ($existingResponse)
                            <div class="mt-4 p-4 bg-blue-50 rounded-md">
                                <p class="text-sm text-blue-700">
                                    Current response: <strong>{{ $existingResponse }}</strong>
                                </p>
                            </div>
                        @endif
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes (optional):
                            </label>
                            <textarea name="notes" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Add any notes about this response...">{{ $existingResponse->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="flex justify-between mt-6">
                <a href="{{ route('teacher.tests.question', [$test->test_id, $domainNumber, $maxIndex]) }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Skip to Last Question
                </a>
                
                <a href="{{ route('teacher.tests.form', $test->test_id) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Back to Test Form
                </a>
            </div>
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
    const questionText = document.querySelector('.text-2xl').innerText.trim();
    
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
