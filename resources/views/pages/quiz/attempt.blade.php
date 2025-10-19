@extends('layouts.main')

@section('css')
<style>
    .quiz-attempt-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        min-height: calc(100vh - 40px);
    }

    /* Ensure consistent gutters for custom g-16/g-20 classes */
    .row.g-16 { --bs-gutter-x: 16px; --bs-gutter-y: 16px; }
    .row.g-20 { --bs-gutter-x: 20px; --bs-gutter-y: 20px; }

    .quiz-timer {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        background: #ef4444;
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }

    .quiz-timer.warning {
        background: #f59e0b;
        animation: pulse 1s infinite;
    }

    .quiz-timer.critical {
        background: #dc2626;
        animation: pulse 0.5s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .question-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .question-card.current {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .option-item {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
        /* extra spacing inside each option */
        margin-bottom: 0; /* spacing handled by row gutters */
    }

    .option-item:hover {
        border-color: #9ca3af;
        background: #f9fafb;
    }

    .option-item.selected {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .option-circle {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        transition: all 0.2s ease;
    }

    .option-item.selected .option-circle {
        border-color: #3b82f6;
        background: #3b82f6;
        position: relative;
    }

    .option-item.selected .option-circle::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .question-nav {
        position: sticky;
        top: 20px;
        max-height: calc(100vh - 40px);
        overflow-y: auto;
    }

    .question-nav-item {
        width: 40px;
        height: 40px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 4px;
    }

    .question-nav-item:hover {
        border-color: #9ca3af;
    }

    .question-nav-item.answered {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }

    .question-nav-item.current {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    .submit-section {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 12px;
        border: 2px dashed #9ca3af;
    }
</style>
@endsection

@section('content')
<!-- Timer -->
<div class="quiz-timer" id="quizTimer">
    <i class="ph ph-clock me-2"></i>
    <span id="timeDisplay">{{ $timeLimit }}:00</span>
</div>

<div class="container-fluid p-20">
    <div class="row g-20">
        <!-- Main Quiz Area -->
        <div class="col-lg-9">
            <div class="quiz-attempt-container p-24">
                <!-- Quiz Header -->
                <div class="border-bottom border-gray-100 pb-20 mb-24">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold text-gray-900 mb-8">{{ $quiz->title }}</h4>
                            <p class="text-gray-600 mb-0">
                                <span class="me-16">
                                    <i class="ph ph-question me-1"></i>
                                    {{ $questions->count() }} Pertanyaan
                                </span>
                                <span>
                                    <i class="ph ph-target me-1"></i>
                                    Nilai Lulus: {{ $quiz->passing_score ?? 70 }}%
                                </span>
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="text-14 text-gray-500">Progress</div>
                            <div class="fw-bold text-20 text-primary-600" id="progressText">1 / {{ $questions->count() }}</div>
                        </div>
                    </div>
                </div>

                <form id="quizForm" action="{{ route('quiz.submit', ['quizId' => $quiz->id, 'attemptId' => $attempt->id]) }}" method="POST">
                    @csrf
                    <!-- Questions -->
                    @foreach($questions as $index => $question)
                        <div class="question-card {{ $index === 0 ? 'current' : 'd-none' }} p-24 mb-24" id="question-{{ $question->id }}">
                            <div class="mb-20">
                                <div class="d-flex justify-content-between align-items-start mb-16">
                                    <h6 class="fw-medium text-gray-500 mb-0">Pertanyaan {{ $index + 1 }}</h6>
                                    <span class="badge bg-primary-50 text-primary-600 py-4 px-12 rounded-pill">
                                        Pilihan Ganda
                                    </span>
                                </div>
                                <h5 class="fw-bold text-gray-900 mb-16">{{ $question->question_text }}</h5>
                            </div>

                            <!-- Options -->
                            <div class="row g-16">
                                @foreach($question->options as $option)
                                    <div class="col-md-6">
                                        <div class="option-item p-16" onclick="selectOption({{ $question->id }}, {{ $option->id }})">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                                   class="d-none option-input" id="option-{{ $option->id }}">
                                            <div class="d-flex align-items-start">
                                                <div class="me-12 mt-2">
                                                    <div class="option-circle"></div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium text-gray-900">{{ $option->option_text }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Navigation -->
                            <div class="d-flex justify-content-between align-items-center mt-24">
                                <button type="button" class="btn btn-outline-gray-600 rounded-pill py-8 px-20 {{ $index === 0 ? 'invisible' : '' }}"
                                        onclick="previousQuestion()">
                                    <i class="ph ph-arrow-left me-1"></i> Sebelumnya
                                </button>

                                <div class="d-flex gap-8">
                                    <button type="button" class="btn btn-primary rounded-pill py-8 px-20 {{ $index === count($questions) - 1 ? 'd-none' : '' }}"
                                            onclick="nextQuestion()">
                                        Selanjutnya <i class="ph ph-arrow-right ms-1"></i>
                                    </button>

                                    <button type="button" class="btn btn-success rounded-pill py-8 px-20 {{ $index === count($questions) - 1 ? '' : 'd-none' }}"
                                            onclick="submitQuiz()">
                                        <i class="ph ph-paper-plane-tilt me-1"></i> Kirim Jawaban
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>

        <!-- Question Navigation -->
        <div class="col-lg-3">
            <div class="question-nav">
                <div class="bg-white rounded-12 p-20 mb-20">
                    <h6 class="fw-bold text-gray-900 mb-16">Navigasi Soal</h6>
                    <div class="d-flex flex-wrap" id="questionNav">
                        @foreach($questions as $index => $question)
                            <div class="question-nav-item {{ $index === 0 ? 'current' : '' }}"
                                 onclick="goToQuestion({{ $index }})" id="nav-{{ $index }}">
                                {{ $index + 1 }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-12 p-20 mb-20">
                    <h6 class="fw-bold text-gray-900 mb-16">Keterangan</h6>
                    <div class="d-flex align-items-center mb-12">
                        <div class="question-nav-item me-12" style="width: 24px; height: 24px; margin: 0;">1</div>
                        <span class="text-14 text-gray-600">Belum dijawab</span>
                    </div>
                    <div class="d-flex align-items-center mb-12">
                        <div class="question-nav-item answered me-12" style="width: 24px; height: 24px; margin: 0;">1</div>
                        <span class="text-14 text-gray-600">Sudah dijawab</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="question-nav-item current me-12" style="width: 24px; height: 24px; margin: 0;">1</div>
                        <span class="text-14 text-gray-600">Sedang dikerjakan</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white rounded-12 p-20 border-2 border-success">
                    <h6 class="fw-bold text-gray-900 mb-12">
                        <i class="ph ph-check-circle me-2 text-success-600"></i>Selesai?
                    </h6>
                    <p class="text-14 text-gray-600 mb-8" id="answeredCount">
                        Dijawab: <span class="fw-bold text-success-600">0</span> / {{ $questions->count() }}
                    </p>

                    <!-- Progress Bar -->
                    <div class="quiz-progress mb-16">
                        <div class="quiz-progress-bar" id="answeredProgressBar" style="width: 0%"></div>
                    </div>

                    <button type="button" onclick="submitQuiz()" class="btn btn-success w-100 rounded-pill py-12 fw-bold">
                        <i class="ph ph-paper-plane-tilt me-2"></i>Kirim Jawaban
                    </button>
                    <button type="button" onclick="reviewQuiz()" class="btn btn-outline-gray-600 w-100 rounded-pill py-8 mt-8">
                        <i class="ph ph-eye me-2"></i>Review
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let currentQuestion = 0;
let totalQuestions = {{ $questions->count() }};
let timeLimit = {{ $timeLimit * 60 }}; // Convert to seconds
let timerInterval;
let isSubmitting = false; // Flag to prevent beforeunload alert on submit

// Initialize quiz
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    updateProgress();
    updateAnsweredCount();
});

// Timer functionality
function startTimer() {
    timerInterval = setInterval(function() {
        timeLimit--;
        updateTimerDisplay();

        if (timeLimit <= 300) { // 5 minutes warning
            document.getElementById('quizTimer').classList.add('warning');
        }

        if (timeLimit <= 60) { // 1 minute critical
            document.getElementById('quizTimer').classList.add('critical');
        }

        if (timeLimit <= 0) {
            clearInterval(timerInterval);
            autoSubmitQuiz();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLimit / 60);
    const seconds = timeLimit % 60;
    document.getElementById('timeDisplay').textContent =
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Question navigation
function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        goToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 0) {
        goToQuestion(currentQuestion - 1);
    }
}

function goToQuestion(index) {
    // Hide current question
    document.getElementById(`question-${getQuestionId(currentQuestion)}`).classList.add('d-none');
    document.getElementById(`question-${getQuestionId(currentQuestion)}`).classList.remove('current');

    // Update navigation
    document.getElementById(`nav-${currentQuestion}`).classList.remove('current');

    // Show new question
    currentQuestion = index;
    document.getElementById(`question-${getQuestionId(currentQuestion)}`).classList.remove('d-none');
    document.getElementById(`question-${getQuestionId(currentQuestion)}`).classList.add('current');

    // Update navigation
    document.getElementById(`nav-${currentQuestion}`).classList.add('current');

    updateProgress();

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function getQuestionId(index) {
    const questions = @json($questions->pluck('id'));
    return questions[index];
}

// Option selection
function selectOption(questionId, optionId) {
    // Remove previous selections
    document.querySelectorAll(`input[name="answers[${questionId}]"]`).forEach(input => {
        input.closest('.option-item').classList.remove('selected');
    });

    // Select new option
    const selectedInput = document.getElementById(`option-${optionId}`);
    selectedInput.checked = true;
    selectedInput.closest('.option-item').classList.add('selected');

    // Mark question as answered
    markQuestionAsAnswered(currentQuestion);
}

function markQuestionAsAnswered(index) {
    document.getElementById(`nav-${index}`).classList.add('answered');
    updateAnsweredCount();
}

// Progress update
function updateProgress() {
    document.getElementById('progressText').textContent = `${currentQuestion + 1} / ${totalQuestions}`;
}

// Update answered count
function updateAnsweredCount() {
    const answeredCount = document.querySelectorAll('#questionNav .question-nav-item.answered').length;
    const percentage = Math.round((answeredCount / totalQuestions) * 100);

    document.getElementById('answeredCount').innerHTML =
        `Dijawab: <span class="fw-bold text-success-600">${answeredCount}</span> / ${totalQuestions}`;

    document.getElementById('answeredProgressBar').style.width = `${percentage}%`;
}

// Review and submit
function reviewQuiz() {
    // Find first unanswered question
    let firstUnanswered = -1;
    for (let i = 0; i < totalQuestions; i++) {
        const navItem = document.getElementById(`nav-${i}`);
        if (!navItem.classList.contains('answered')) {
            firstUnanswered = i;
            break;
        }
    }

    // Go to first unanswered question or first question
    if (firstUnanswered >= 0) {
        goToQuestion(firstUnanswered);
        Swal.fire({
            title: '⚠️ Perhatian!',
            text: `Anda berada di pertanyaan ${firstUnanswered + 1} yang belum dijawab.`,
            icon: 'info',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        goToQuestion(0);
        Swal.fire({
            title: '✅ Semua Sudah Dijawab!',
            text: 'Anda sudah menjawab semua pertanyaan. Silakan kirim jawaban.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

function submitQuiz() {
    const answeredCount = document.querySelectorAll('.question-nav-item.answered').length;
    const unansweredCount = totalQuestions - answeredCount;

    let warningText = 'Pastikan semua jawaban sudah benar. Anda tidak dapat mengubah setelah mengirim.';
    let icon = 'question';

    if (unansweredCount > 0) {
        warningText = `⚠️ Anda masih memiliki ${unansweredCount} pertanyaan yang belum dijawab!\n\n` +
                     'Pertanyaan yang tidak dijawab akan dianggap salah.\n' +
                     'Yakin ingin mengirim sekarang?';
        icon = 'warning';
    }

    Swal.fire({
        title: '📝 Kirim Jawaban?',
        html: warningText.replace(/\n/g, '<br>'),
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: unansweredCount > 0 ? '#f59e0b' : '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kirim!',
        cancelButtonText: unansweredCount > 0 ? 'Kembali Mengerjakan' : 'Review Lagi'
    }).then((result) => {
        if (result.isConfirmed) {
            isSubmitting = true; // Set flag to prevent beforeunload alert
            clearInterval(timerInterval);
            document.getElementById('quizForm').submit();
        }
    });
}

function autoSubmitQuiz() {
    isSubmitting = true; // Set flag to prevent beforeunload alert
    Swal.fire({
        title: '⏰ Waktu Habis!',
        text: 'Quiz akan otomatis dikirim karena waktu telah habis.',
        icon: 'warning',
        timer: 3000,
        showConfirmButton: false
    }).then(() => {
        document.getElementById('quizForm').submit();
    });
}

// Prevent page refresh/close
window.addEventListener('beforeunload', function(e) {
    if (timeLimit > 0 && !isSubmitting) {
        e.preventDefault();
        e.returnValue = 'Quiz sedang berlangsung. Yakin ingin keluar?';
        return 'Quiz sedang berlangsung. Yakin ingin keluar?';
    }
});
</script>
@endsection
