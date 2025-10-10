@extends('layouts.main')

@section('title', 'Kelola Kuis - ' . $quiz->title)

@section('css')
    <style>
        .question-card {
            border: 1px solid #e3e6f0;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .question-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .question-header {
            padding: 20px 20px 15px 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .question-content {
            padding: 20px;
        }

        .question-text {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            line-height: 1.5;
            margin-bottom: 0;
            margin-left: 10px;
            padding-right: 15px;
            flex: 1;
            display: flex;
            align-items: center;
        }

        .question-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .question-actions a {
            font-size: 14px;
            padding: 4px 8px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .question-actions a:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        .option-item {
            padding: 12px 16px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .option-item:hover {
            border-color: #cbd5e0;
        }

        .option-item.correct {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            font-weight: 500;
        }

        .option-item.correct:hover {
            background-color: #c3e6cb;
        }

        .option-label {
            font-weight: 700;
            color: #4a5568;
        }

        .option-item.correct .option-label {
            color: #155724;
        }

        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
            align-self: center;
        }

        .options-grid {
            margin-top: 15px;
        }

        .separator {
            color: #a0aec0;
            font-weight: 400;
        }
    </style>
@endsection

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Kelola Kuis</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="{{ route('course.show', $quiz->module->course->id) }}"
                    class="d-flex align-items-center gap-1 hover-text-primary">
                    <i class="ph ph-arrow-left"></i>
                    Kembali ke Kursus
                </a>
            </li>
            {{-- <li class="fw-medium">-</li> --}}
            {{-- <li class="fw-medium"> --}}
            {{-- <a href="{{ route('course.show', $quiz->module->course->id) }}" class="d-flex align-items-center gap-1 hover-text-primary">
                        {{ $quiz->module->course->title }}
                    </a> --}}
            {{-- </li> --}}
            {{-- <li class="fw-medium">-</li> --}}
            {{-- <li class="fw-medium text-primary-600">{{ $quiz->title }}</li> --}}
        </ul>
    </div>

    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0">{{ $quiz->title }}</h5>
                            <small class="text-muted">{{ $quiz->module->title }} -
                                {{ $quiz->module->course->title }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-info">{{ $quiz->questions->count() }} Pertanyaan</span>
                            <span class="badge bg-warning">{{ $quiz->duration_in_minutes }} menit</span>
                            <span class="badge bg-success">Lulus: {{ $quiz->passing_score }}%</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Add Question Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Tambah Pertanyaan Baru</h6>
                        </div>
                        <div class="card-body">
                            <form id="questionForm">
                                @csrf
                                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                                <input type="hidden" id="questionMethod" name="_method" value="POST">
                                <input type="hidden" id="questionId" name="question_id" value="">

                                <div class="mb-20">
                                    <label for="questionText" class="form-label">Pertanyaan</label>
                                    <textarea class="form-control" id="questionText" name="question_text" rows="3"
                                        placeholder="Masukkan pertanyaan kuis..." required></textarea>
                                </div>

                                <div class="row mb-20">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="optionA" class="form-label">Pilihan A</label>
                                            <input type="text" class="form-control" id="optionA" name="option_a"
                                                placeholder="Pilihan A" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="optionB" class="form-label">Pilihan B</label>
                                            <input type="text" class="form-control" id="optionB" name="option_b"
                                                placeholder="Pilihan B" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-20">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="optionC" class="form-label">Pilihan C</label>
                                            <input type="text" class="form-control" id="optionC" name="option_c"
                                                placeholder="Pilihan C" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="optionD" class="form-label">Pilihan D</label>
                                            <input type="text" class="form-control" id="optionD" name="option_d"
                                                placeholder="Pilihan D" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-20">
                                    <label for="correctAnswer" class="form-label">Jawaban Benar</label>
                                    <select class="form-select" id="correctAnswer" name="correct_answer" required>
                                        <option value="">Pilih jawaban yang benar</option>
                                        <option value="a">A</option>
                                        <option value="b">B</option>
                                        <option value="c">C</option>
                                        <option value="d">D</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="questionSubmitBtn">
                                        Tambah Pertanyaan
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="cancelEditBtn"
                                        style="display: none;" onclick="cancelEditQuestion()">
                                        Batal Edit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Daftar Pertanyaan</h6>
                        </div>
                        <div class="card-body">
                            @if ($quiz->questions->count() > 0)
                                <div id="questionsList">
                                    @foreach ($quiz->questions as $index => $question)
                                        <div class="question-card" data-question-id="{{ $question->id }}">
                                            <!-- Question Header -->
                                            <div class="question-header">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="question-number">{{ $index + 1 }}</div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="question-text">{{ $question->question_text }}
                                                            </div>
                                                            <div class="question-actions">
                                                                <a href="#"
                                                                    class="text-primary text-decoration-none fw-bold"
                                                                    onclick="editQuestion({{ $question->id }})">Edit</a>
                                                                <span class="separator">|</span>
                                                                <a href="#"
                                                                    class="text-danger text-decoration-none fw-bold"
                                                                    onclick="deleteQuestion({{ $question->id }})">Hapus</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question Options -->
                                            <div class="question-content">
                                                <div class="row options-grid">
                                                    @foreach ($question->options as $option)
                                                        <div class="col-md-6">
                                                            <div
                                                                class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                                                <span
                                                                    class="option-label">{{ strtoupper($option->option_key) }}.</span>
                                                                {{ $option->option_text }}
                                                                @if ($option->is_correct)
                                                                    <i class="fas fa-check float-end text-success"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada pertanyaan</h5>
                                    <p class="text-muted">Mulai tambahkan pertanyaan untuk kuis ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let isEditMode = false;
        let currentQuestionId = null;

        // Question form submit
        $('#questionForm').on('submit', function(e) {
            e.preventDefault();

            const submitButton = $('#questionSubmitBtn');
            const originalText = submitButton.text();
            const loadingText = isEditMode ? 'Mengupdate...' : 'Menyimpan...';
            submitButton.prop('disabled', true).text(loadingText);

            let formData = new FormData(this);
            let ajaxConfig = {
                processData: false,
                contentType: false,
                data: formData
            };

            if (isEditMode) {
                ajaxConfig.url = `/question/${currentQuestionId}`;
                ajaxConfig.method = "POST"; // Laravel uses POST with _method=PUT
            } else {
                ajaxConfig.url = "{{ route('question.store') }}";
                ajaxConfig.method = "POST";
            }

            $.ajax(ajaxConfig).done(function(response) {
                submitButton.prop('disabled', false).text(originalText);

                const successText = isEditMode ? 'Pertanyaan berhasil diperbarui.' :
                    'Pertanyaan berhasil ditambahkan.';
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: successText,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }).fail(function(xhr) {
                submitButton.prop('disabled', false).text(originalText);

                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = '';

                    $.each(errors, function(key, value) {
                        errorMessages += `• ${value[0]}<br>`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMessages,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Silakan coba lagi nanti.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                }
            });
        });

        // Edit question function
        function editQuestion(questionId) {
            // Find question data from DOM
            const questionCard = $(`[data-question-id="${questionId}"]`);
            const questionText = questionCard.find('.question-text').text().trim();
            const options = questionCard.find('.option-item');

            let optionA = '',
                optionB = '',
                optionC = '',
                optionD = '',
                correctAnswer = '';

            options.each(function(index) {
                const optionElement = $(this);
                const optionLabel = optionElement.find('.option-label').text().replace('.', '').toLowerCase().trim();
                
                // Get option text by removing label and icon
                let optionText = optionElement.clone();
                optionText.find('.option-label').remove(); // Remove label
                optionText.find('i').remove(); // Remove check icon
                optionText = optionText.text().trim();

                console.log('Option Label:', optionLabel, 'Option Text:', optionText);

                if (optionLabel === 'a') {
                    optionA = optionText;
                    if (optionElement.hasClass('correct')) correctAnswer = 'a';
                } else if (optionLabel === 'b') {
                    optionB = optionText;
                    if (optionElement.hasClass('correct')) correctAnswer = 'b';
                } else if (optionLabel === 'c') {
                    optionC = optionText;
                    if (optionElement.hasClass('correct')) correctAnswer = 'c';
                } else if (optionLabel === 'd') {
                    optionD = optionText;
                    if (optionElement.hasClass('correct')) correctAnswer = 'd';
                }
            });

            // Fill form
            $('#questionText').val(questionText);
            $('#optionA').val(optionA);
            $('#optionB').val(optionB);
            $('#optionC').val(optionC);
            $('#optionD').val(optionD);
            $('#correctAnswer').val(correctAnswer);

            // Set edit mode
            isEditMode = true;
            currentQuestionId = questionId;
            $('#questionMethod').val('PUT');
            $('#questionId').val(questionId);
            $('#questionSubmitBtn').text('Update Pertanyaan').removeClass('btn-primary').addClass('btn-warning');
            $('#cancelEditBtn').show();

            // Scroll to form
            $('html, body').animate({
                scrollTop: $('#questionForm').offset().top - 100
            }, 500);
        }

        // Cancel edit function
        function cancelEditQuestion() {
            $('#questionForm')[0].reset();
            isEditMode = false;
            currentQuestionId = null;
            $('#questionMethod').val('POST');
            $('#questionId').val('');
            $('#questionSubmitBtn').text('Tambah Pertanyaan').removeClass('btn-warning').addClass('btn-primary');
            $('#cancelEditBtn').hide();
        }

        // Delete question function
        function deleteQuestion(questionId) {
            Swal.fire({
                title: 'Hapus Pertanyaan?',
                text: 'Apakah Anda yakin ingin menghapus pertanyaan ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/question/${questionId}`,
                        method: 'DELETE',
                        data: {
                            _token: $('input[name="_token"]').first().val()
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pertanyaan berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Gagal menghapus pertanyaan.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endsection
