{{-- Course Curriculum Viewer for Students/Users --}}
<div class="row gy-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="mb-0">Kurikulum Kursus</h4>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary">{{ $course->modules->count() }} Modul</span>
                        <span class="badge bg-success">{{ $course->modules->sum(function($module) { return $module->lessons->count(); }) }} Pelajaran</span>
                    </div>
                </div>

                {{-- Progress Info --}}
                @if(auth()->check() && auth()->user()->isEnrolledIn($course))
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>Progress Anda:</strong> {{ $course->getCompletionPercentage(auth()->user()) }}% selesai
                            </div>
                            <div class="progress" style="width: 200px; height: 8px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: {{ $course->getCompletionPercentage(auth()->user()) }}%"
                                     aria-valuenow="{{ $course->getCompletionPercentage(auth()->user()) }}"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Modules List --}}
                <div class="accordion" id="courseModulesAccordion">
                    @forelse($course->modules->sortBy('created_at') as $index => $module)
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="heading{{ $module->id }}">
                                <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $module->id }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="collapse{{ $module->id }}">
                                    <div class="w-100 d-flex align-items-center justify-content-between pe-3">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary me-3">Modul {{ $index + 1 }}</span>
                                            <strong>{{ $module->title }}</strong>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">{{ $module->lessons->count() }} pelajaran</small>
                                            @if($module->quiz)
                                                <i class="ph ph-question text-warning" title="Ada kuis di modul ini"></i>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse{{ $module->id }}"
                                 class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                 aria-labelledby="heading{{ $module->id }}"
                                 data-bs-parent="#courseModulesAccordion">
                                <div class="accordion-body">
                                    {{-- Lessons List --}}
                                    @if($module->lessons->count() > 0)
                                        <h6 class="fw-bold mb-3">Pelajaran:</h6>
                                        <div class="list-group mb-4">
                                            @foreach($module->lessons->sortBy('created_at') as $lessonIndex => $lesson)
                                                @php
                                                    $isCompleted = auth()->check() && auth()->user()->hasCompletedLesson($lesson->id);
                                                    $canAccess = !auth()->check() || auth()->user()->isEnrolledIn($course);
                                                @endphp
                                                <div class="list-group-item d-flex align-items-center justify-content-between {{ $isCompleted ? 'bg-light' : '' }}">
                                                    <div class="d-flex align-items-center flex-grow-1">
                                                        <div class="me-3">
                                                            @if($isCompleted)
                                                                <i class="ph ph-check-circle text-success fs-5"></i>
                                                            @else
                                                                <i class="ph ph-play-circle text-primary fs-5"></i>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <strong>{{ $lessonIndex + 1 }}. {{ $lesson->title }}</strong>
                                                                @if($lesson->content_type === 'video')
                                                                    <i class="ph ph-video text-info" title="Video"></i>
                                                                @elseif($lesson->content_type === 'file')
                                                                    <i class="ph ph-file text-warning" title="File"></i>
                                                                @else
                                                                    <i class="ph ph-article text-secondary" title="Artikel"></i>
                                                                @endif
                                                            </div>
                                                            @if($lesson->summary)
                                                                <small class="text-muted">{{ Str::limit($lesson->summary, 100) }}</small>
                                                            @endif
                                                            <div class="mt-1">
                                                                <span class="badge bg-light text-dark">{{ $lesson->duration_in_minutes }} menit</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        @if($canAccess)
                                                            @if($isCompleted)
                                                                <a href="{{ route('lesson.show', $lesson->id) }}"
                                                                   class="btn btn-sm btn-outline-success">
                                                                    Lihat Lagi
                                                                </a>
                                                            @else
                                                                <a href="{{ route('lesson.show', $lesson->id) }}"
                                                                   class="btn btn-sm btn-primary">
                                                                    {{ $lessonIndex === 0 ? 'Mulai' : 'Lanjutkan' }}
                                                                </a>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                                <i class="ph ph-lock me-1"></i>Terkunci
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light mb-4">
                                            <i class="ph ph-info me-2"></i>
                                            Belum ada pelajaran dalam modul ini.
                                        </div>
                                    @endif

                                    {{-- Quiz Section --}}
                                    @if($module->quiz)
                                        <h6 class="fw-bold mb-3">Kuis:</h6>
                                        @php
                                            $userAttempts = auth()->check() ? auth()->user()->quizAttempts()->where('quiz_id', $module->quiz->id)->get() : collect();
                                            $lastAttempt = $userAttempts->sortByDesc('created_at')->first();
                                            $hasPassed = $lastAttempt && $lastAttempt->score >= $module->quiz->passing_score;
                                            $canAttempt = auth()->check() && auth()->user()->isEnrolledIn($course) &&
                                                         ($module->quiz->max_attempts == 0 || $userAttempts->count() < $module->quiz->max_attempts);
                                        @endphp

                                        <div class="card border-warning">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <h6 class="mb-0 fw-bold">{{ $module->quiz->title }}</h6>
                                                            @if($hasPassed)
                                                                <i class="ph ph-check-circle text-success fs-5" title="Kuis telah diselesaikan"></i>
                                                            @endif
                                                        </div>
                                                        @if($module->quiz->description)
                                                            <p class="text-muted mb-2 small">{{ $module->quiz->description }}</p>
                                                        @endif
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="ph ph-clock me-1"></i>{{ $module->quiz->duration_in_minutes }} menit
                                                            </span>
                                                            <span class="badge bg-info">
                                                                <i class="ph ph-target me-1"></i>Nilai lulus: {{ $module->quiz->passing_score }}%
                                                            </span>
                                                            @if($module->quiz->max_attempts > 0)
                                                                <span class="badge bg-secondary">
                                                                    <i class="ph ph-repeat me-1"></i>Max {{ $module->quiz->max_attempts }} percobaan
                                                                </span>
                                                            @endif
                                                        </div>

                                                        {{-- Attempt History --}}
                                                        @if(auth()->check() && $userAttempts->count() > 0)
                                                            <div class="mt-3">
                                                                <small class="text-muted">
                                                                    Percobaan: {{ $userAttempts->count() }}
                                                                    @if($module->quiz->max_attempts > 0)
                                                                        / {{ $module->quiz->max_attempts }}
                                                                    @endif
                                                                    @if($lastAttempt)
                                                                        | Nilai terakhir:
                                                                        <span class="{{ $hasPassed ? 'text-success' : 'text-danger' }}">
                                                                            {{ $lastAttempt->score }}%
                                                                        </span>
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-3">
                                                        @if(auth()->check() && auth()->user()->isEnrolledIn($course))
                                                            @if($hasPassed)
                                                                <button class="btn btn-sm btn-success" disabled>
                                                                    <i class="ph ph-check me-1"></i>Selesai
                                                                </button>
                                                            @elseif($canAttempt)
                                                                <a href="{{ route('quiz.attempt', $module->quiz->id) }}"
                                                                   class="btn btn-sm btn-warning text-dark">
                                                                    <i class="ph ph-play me-1"></i>
                                                                    {{ $userAttempts->count() > 0 ? 'Coba Lagi' : 'Mulai Kuis' }}
                                                                </a>
                                                            @else
                                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                                    <i class="ph ph-x me-1"></i>Batas Tercapai
                                                                </button>
                                                            @endif
                                                        @else
                                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                                <i class="ph ph-lock me-1"></i>Terkunci
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ph ph-books text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">Belum Ada Kurikulum</h5>
                            <p class="text-muted">Kursus ini belum memiliki modul pembelajaran.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Completion Status --}}
                @if(auth()->check() && auth()->user()->isEnrolledIn($course))
                    @php
                        $completionPercentage = $course->getCompletionPercentage(auth()->user());
                        $isCompleted = $course->isCompletedByUser(auth()->user());
                    @endphp

                    @if($isCompleted)
                        <div class="alert alert-success mt-4">
                            <div class="d-flex align-items-center">
                                <i class="ph ph-trophy text-warning fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Selamat! Anda telah menyelesaikan kursus ini!</h6>
                                    <small>Anda telah menyelesaikan semua modul dan kuis dalam kursus ini.</small>
                                </div>
                            </div>
                        </div>
                    @elseif($completionPercentage > 0)
                        <div class="alert alert-primary mt-4">
                            <div class="d-flex align-items-center">
                                <i class="ph ph-clock text-primary fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Terus semangat belajar!</h6>
                                    <small>Anda sudah menyelesaikan {{ $completionPercentage }}% dari kursus ini.</small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #212529;
}

.list-group-item {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem !important;
    margin-bottom: 0.5rem;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.25rem;
}

.progress-bar {
    background-color: #0d6efd;
}

.badge {
    font-size: 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.accordion-item {
    border-radius: 0.375rem !important;
    overflow: hidden;
}
</style>
