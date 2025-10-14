@extends('layouts.main')

@section('css')
<style>
    .review-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .question-review {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 24px;
        overflow: hidden;
    }

    .question-review.correct {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .question-review.incorrect {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .option-review {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        background: white;
    }

    .option-review.correct-answer {
        border-color: #10b981;
        background: #dcfce7;
    }

    .option-review.user-wrong {
        border-color: #ef4444;
        background: #fee2e2;
    }

    .result-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .result-badge.correct {
        background: #dcfce7;
        color: #166534;
    }

    .result-badge.incorrect {
        background: #fee2e2;
        color: #991b1b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid p-20">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="review-container">
                <!-- Header -->
                <div class="p-24 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-20">
                        <div>
                            <a href="{{ route('quiz.show', $quiz->id) }}" class="text-decoration-none text-gray-600 mb-8 d-inline-block">
                                <i class="ph ph-arrow-left me-1"></i> Kembali ke Quiz
                            </a>
                            <h4 class="fw-bold text-gray-900 mb-8">Review Jawaban Quiz</h4>
                            <p class="text-gray-600 mb-0">{{ $quiz->title }}</p>
                        </div>
                        <div class="text-end">
                            <div class="text-14 text-gray-500 mb-4">Skor Anda</div>
                            <div class="display-6 fw-bold {{ $attempt->passed ? 'text-success-600' : 'text-danger-600' }}">
                                {{ $attempt->score }}%
                            </div>
                            @if($attempt->passed)
                                <span class="badge bg-success-50 text-success-600 py-4 px-12 rounded-pill mt-8">
                                    <i class="ph ph-check me-1"></i>LULUS
                                </span>
                            @else
                                <span class="badge bg-danger-50 text-danger-600 py-4 px-12 rounded-pill mt-8">
                                    <i class="ph ph-x me-1"></i>TIDAK LULUS
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row g-12">
                        <div class="col-md-3">
                            <div class="bg-gray-50 rounded-8 p-16 text-center">
                                <div class="text-14 text-gray-600 mb-4">Total Soal</div>
                                <div class="fw-bold text-20 text-gray-900">{{ count($reviewData) }}</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-success-50 rounded-8 p-16 text-center">
                                <div class="text-14 text-success-600 mb-4">Jawaban Benar</div>
                                <div class="fw-bold text-20 text-success-600">
                                    {{ collect($reviewData)->where('is_correct', true)->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-danger-50 rounded-8 p-16 text-center">
                                <div class="text-14 text-danger-600 mb-4">Jawaban Salah</div>
                                <div class="fw-bold text-20 text-danger-600">
                                    {{ collect($reviewData)->where('is_correct', false)->count() }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-warning-50 rounded-8 p-16 text-center">
                                <div class="text-14 text-warning-600 mb-4">Nilai Lulus</div>
                                <div class="fw-bold text-20 text-warning-600">{{ $quiz->passing_score }}%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Review -->
                <div class="p-24">
                    @foreach($reviewData as $index => $data)
                        <div class="question-review {{ $data['is_correct'] ? 'correct' : 'incorrect' }}">
                            <div class="p-20 border-bottom bg-white">
                                <div class="d-flex justify-content-between align-items-start mb-12">
                                    <h6 class="fw-medium text-gray-900 mb-0">
                                        Soal {{ $index + 1 }}
                                    </h6>
                                    <span class="result-badge {{ $data['is_correct'] ? 'correct' : 'incorrect' }}">
                                        @if($data['is_correct'])
                                            <i class="ph ph-check-circle me-1"></i> Benar
                                        @else
                                            <i class="ph ph-x-circle me-1"></i> Salah
                                        @endif
                                    </span>
                                </div>
                                <h5 class="fw-bold text-gray-900 mb-0">{{ $data['question']->question_text }}</h5>
                            </div>

                            <div class="p-20">
                                <div class="row g-12">
                                    @foreach($data['question']->options as $option)
                                        @php
                                            $isCorrect = $data['correct_answer'] && $option->id === $data['correct_answer']->id;
                                            $isUserAnswer = $data['user_answer'] && $option->id === $data['user_answer']->id;
                                            $optionClass = '';
                                            if ($isCorrect) {
                                                $optionClass = 'correct-answer';
                                            } elseif ($isUserAnswer && !$data['is_correct']) {
                                                $optionClass = 'user-wrong';
                                            }
                                        @endphp

                                        <div class="col-md-6">
                                            <div class="option-review {{ $optionClass }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-12">
                                                        @if($isCorrect)
                                                            <i class="ph ph-check-circle text-success-600" style="font-size: 24px;"></i>
                                                        @elseif($isUserAnswer && !$data['is_correct'])
                                                            <i class="ph ph-x-circle text-danger-600" style="font-size: 24px;"></i>
                                                        @else
                                                            <div style="width: 24px; height: 24px; border: 2px solid #d1d5db; border-radius: 50%;"></div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-medium text-gray-900">{{ $option->option_text }}</div>
                                                        @if($isCorrect)
                                                            <div class="text-12 text-success-600 mt-4">
                                                                <i class="ph ph-check me-1"></i>Jawaban Benar
                                                            </div>
                                                        @elseif($isUserAnswer && !$data['is_correct'])
                                                            <div class="text-12 text-danger-600 mt-4">
                                                                <i class="ph ph-arrow-left me-1"></i>Jawaban Anda
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if(!$data['user_answer'])
                                    <div class="alert alert-warning mt-12 mb-0">
                                        <i class="ph ph-warning me-2"></i>
                                        Anda tidak menjawab pertanyaan ini
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer Actions -->
                <div class="p-24 border-top bg-gray-50 text-center">
                    <a href="{{ route('quiz.show', $quiz->id) }}" class="btn btn-primary rounded-pill py-12 px-32">
                        <i class="ph ph-arrow-left me-2"></i>Kembali ke Quiz
                    </a>
                    @if($attempt->quiz->attempts()->where('user_id', auth()->id())->count() < $attempt->quiz->max_attempts)
                        <a href="{{ route('quiz.attempt', $quiz->id) }}" class="btn btn-warning rounded-pill py-12 px-32">
                            <i class="ph ph-repeat me-2"></i>Coba Lagi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
