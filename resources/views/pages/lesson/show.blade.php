@extends('layouts.main')

@section('css')
<style>
    .lesson-video-container {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        background: #000;
        border-radius: 12px;
        overflow: hidden;
    }

    .lesson-video-container iframe,
    .lesson-video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .lesson-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .lesson-navigation {
        position: sticky;
        top: 20px;
    }

    .lesson-progress {
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
        overflow: hidden;
    }

    .lesson-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    .lesson-sidebar {
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }

    .lesson-item {
        transition: all 0.2s ease;
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .lesson-item:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }

    .lesson-item.active {
        background: #dbeafe;
        border-left: 4px solid #3b82f6;
    }

    .completion-button {
        transition: all 0.3s ease;
    }

    .completion-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }

    .lesson-text-content {
        line-height: 1.8;
    }

    .lesson-file-content {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .pdf-viewer iframe {
        border: none;
    }

    .image-viewer img {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    /* AI Chat Modal Styles */
    .ai-chat-widget {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1000;
    }

    .ai-chat-toggle {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        position: relative;
    }

    .ai-chat-toggle:hover {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.7);
    }

    .ai-chat-toggle::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 0;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.3;
        }
    }

    /* Modal Overlay */
    .ai-chat-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    .ai-chat-modal-overlay.active {
        display: flex;
    }

    /* Modal Container */
    .ai-chat-modal {
        width: 90%;
        max-width: 900px;
        height: 85vh;
        max-height: 700px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalSlideIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.8) translateY(50px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Modal Header */
    .ai-chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 24px 32px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .ai-chat-header-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .ai-chat-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        backdrop-filter: blur(10px);
    }

    .ai-chat-header-text h5 {
        margin: 0;
        font-weight: 700;
        font-size: 20px;
        letter-spacing: -0.5px;
    }

    .ai-chat-header-text small {
        font-size: 13px;
        opacity: 0.9;
        font-weight: 400;
    }

    .ai-chat-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        font-size: 28px;
        cursor: pointer;
        padding: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .ai-chat-close:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: rotate(90deg);
    }

    /* Messages Area */
    .ai-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 32px;
        background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .ai-chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    .ai-chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .ai-chat-messages::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .ai-message {
        display: flex;
        gap: 12px;
        animation: messageSlideIn 0.4s ease;
        max-width: 85%;
    }

    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .ai-message.user {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .ai-message-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .ai-message.ai .ai-message-avatar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .ai-message.user .ai-message-avatar {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        color: white;
    }

    .ai-message-content {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .ai-message-bubble {
        padding: 16px 20px;
        border-radius: 16px;
        font-size: 15px;
        line-height: 1.6;
        word-wrap: break-word;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .ai-message.user .ai-message-bubble {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .ai-message.ai .ai-message-bubble {
        background: white;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        border: 1px solid #e5e7eb;
    }

    .ai-message-time {
        font-size: 12px;
        color: #9ca3af;
        margin-left: 4px;
    }

    .ai-message.user .ai-message-time {
        text-align: right;
        margin-right: 4px;
    }

    /* Input Area */
    .ai-chat-input-area {
        padding: 24px 32px;
        background: white;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
    }

    .ai-chat-input-wrapper {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }

    .ai-chat-input-container {
        flex: 1;
        position: relative;
    }

    .ai-chat-input {
        width: 100%;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px 20px;
        font-size: 15px;
        outline: none;
        transition: all 0.3s;
        resize: none;
        min-height: 56px;
        max-height: 120px;
        font-family: inherit;
    }

    .ai-chat-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .ai-chat-input::placeholder {
        color: #9ca3af;
    }

    .ai-chat-send {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        flex-shrink: 0;
    }

    .ai-chat-send:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .ai-chat-send:active:not(:disabled) {
        transform: translateY(0);
    }

    .ai-chat-send:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .ai-typing-indicator {
        display: flex;
        gap: 6px;
        padding: 16px 20px;
        background: white;
        border-radius: 16px;
        width: fit-content;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .ai-typing-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        animation: typing 1.4s infinite;
    }

    .ai-typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .ai-typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.4;
        }
        30% {
            transform: translateY(-12px);
            opacity: 1;
        }
    }

    /* Welcome Message */
    .ai-welcome-message {
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin: 20px 0;
        border: 2px dashed #667eea50;
    }

    .ai-welcome-message i {
        font-size: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .ai-welcome-message h6 {
        color: #1f2937;
        font-weight: 600;
        margin: 12px 0 8px;
    }

    .ai-welcome-message p {
        color: #6b7280;
        font-size: 14px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .ai-chat-modal {
            width: 95%;
            height: 90vh;
            border-radius: 16px;
        }

        .ai-chat-header {
            padding: 20px 24px;
        }

        .ai-chat-messages {
            padding: 24px 20px;
        }

        .ai-chat-input-area {
            padding: 20px;
        }

        .ai-message {
            max-width: 90%;
        }
    }
</style>
@endSection

@section('content')
<div class="container-fluid px-0">
    <div class="row g-0">
        <!-- Main Content Area -->
        <div class="col-lg-9 col-md-8">
            <div class="lesson-content h-100">
                <!-- Lesson Header -->
                <div class="p-24 border-bottom border-gray-100">
                    <div class="d-flex align-items-center justify-content-between mb-16">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('course.show', $lesson->module->course->id) }}" class="text-decoration-none">
                                        <i class="ph ph-arrow-left me-1"></i> {{ $lesson->module->course->title }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item text-muted">{{ $lesson->module->title }}</li>
                                <li class="breadcrumb-item active">{{ $lesson->title }}</li>
                            </ol>
                        </nav>

                        <div class="d-flex align-items-center gap-12">
                            <span class="badge bg-success-50 text-success-600 py-6 px-12 rounded-pill">
                                <i class="ph ph-video me-1"></i>Pelajaran
                            </span>
                            @if($lesson->duration_in_minutes)
                                <span class="text-gray-500 text-14">
                                    <i class="ph ph-clock me-1"></i>{{ $lesson->duration_in_minutes }} menit
                                </span>
                            @endif
                        </div>
                    </div>

                    <h1 class="h3 fw-bold text-gray-900 mb-8">{{ $lesson->title }}</h1>
                    @if($lesson->description)
                        <p class="text-gray-600 mb-0">{{ $lesson->description }}</p>
                    @endif
                </div>

                <!-- Content Area Based on Type -->
                <div class="p-24">
                    @if($lesson->content_type === 'video' && $lesson->video_url)
                        <!-- Video Content -->
                        <div class="lesson-video-container mb-24">
                            @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                @php
                                    $video_id = '';
                                    if(str_contains($lesson->video_url, 'youtu.be')) {
                                        $video_id = substr(parse_url($lesson->video_url, PHP_URL_PATH), 1);
                                    } elseif(str_contains($lesson->video_url, 'youtube.com')) {
                                        parse_str(parse_url($lesson->video_url, PHP_URL_QUERY), $query);
                                        $video_id = $query['v'] ?? '';
                                    }
                                @endphp
                                @if($video_id)
                                    <iframe src="https://www.youtube.com/embed/{{ $video_id }}?rel=0&modestbranding=1"
                                            frameborder="0" allowfullscreen></iframe>
                                @else
                                    <div class="text-center py-64">
                                        <i class="ph ph-video-camera text-danger-500" style="font-size: 64px;"></i>
                                        <h5 class="text-gray-500 mt-16 mb-8">Video URL Tidak Valid</h5>
                                        <p class="text-gray-400">Pastikan URL YouTube sudah benar</p>
                                    </div>
                                @endif
                            @elseif(str_contains($lesson->video_url, 'vimeo.com'))
                                @php
                                    preg_match('/vimeo\.com\/(\d+)/', $lesson->video_url, $matches);
                                    $vimeo_id = $matches[1] ?? '';
                                @endphp
                                @if($vimeo_id)
                                    <iframe src="https://player.vimeo.com/video/{{ $vimeo_id }}"
                                            frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
                                @endif
                            @else
                                <video controls class="w-100">
                                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                                    Browser Anda tidak mendukung video HTML5.
                                </video>
                            @endif
                        </div>
                    @elseif($lesson->content_type === 'text' && $lesson->content_text)
                        <!-- Text Content -->
                        <div class="lesson-text-content">
                            <div class="bg-gray-50 rounded-12 p-24">
                                <h4 class="fw-bold mb-20 text-gray-900">
                                    <i class="ph ph-article me-2 text-primary-600"></i>Materi Pembelajaran
                                </h4>
                                <div class="text-gray-700 line-height-lg" style="font-size: 15px;">
                                    {!! nl2br(e($lesson->content_text)) !!}
                                </div>
                            </div>
                        </div>
                    @elseif($lesson->content_type === 'file' && $lesson->attachment_path)
                        <!-- File Content -->
                        <div class="lesson-file-content">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-12 p-24">
                                <h4 class="fw-bold mb-20 text-gray-900">
                                    <i class="ph ph-file-text me-2 text-primary-600"></i>File Materi
                                </h4>

                                @php
                                    $file_extension = pathinfo($lesson->attachment_path, PATHINFO_EXTENSION);
                                    $file_name = basename($lesson->attachment_path);
                                    $file_url = asset('storage/' . $lesson->attachment_path);

                                    // Determine file type
                                    $is_pdf = in_array(strtolower($file_extension), ['pdf']);
                                    $is_image = in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $is_document = in_array(strtolower($file_extension), ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
                                @endphp

                                <div class="d-flex align-items-center justify-content-between bg-white rounded-8 p-20 mb-20">
                                    <div class="d-flex align-items-center">
                                        @if($is_pdf)
                                            <div class="w-48 h-48 d-flex align-items-center justify-content-center bg-danger-50 rounded-8 me-16">
                                                <i class="ph ph-file-pdf text-danger-600" style="font-size: 24px;"></i>
                                            </div>
                                        @elseif($is_image)
                                            <div class="w-48 h-48 d-flex align-items-center justify-content-center bg-success-50 rounded-8 me-16">
                                                <i class="ph ph-image text-success-600" style="font-size: 24px;"></i>
                                            </div>
                                        @elseif($is_document)
                                            <div class="w-48 h-48 d-flex align-items-center justify-content-center bg-primary-50 rounded-8 me-16">
                                                <i class="ph ph-file-doc text-primary-600" style="font-size: 24px;"></i>
                                            </div>
                                        @else
                                            <div class="w-48 h-48 d-flex align-items-center justify-content-center bg-gray-100 rounded-8 me-16">
                                                <i class="ph ph-file text-gray-600" style="font-size: 24px;"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <h6 class="fw-semibold text-gray-900 mb-4">{{ $lesson->title }}</h6>
                                            <p class="text-14 text-gray-500 mb-0">{{ strtoupper($file_extension) }} File</p>
                                        </div>
                                    </div>

                                    <a href="{{ $file_url }}" download class="btn btn-primary rounded-pill py-8 px-20">
                                        <i class="ph ph-download me-2"></i>Download
                                    </a>
                                </div>

                                @if($is_pdf)
                                    <!-- PDF Viewer -->
                                    <div class="pdf-viewer bg-white rounded-8 overflow-hidden" style="height: 600px;">
                                        <iframe src="{{ $file_url }}" width="100%" height="100%" frameborder="0"></iframe>
                                    </div>
                                @elseif($is_image)
                                    <!-- Image Viewer -->
                                    <div class="image-viewer text-center bg-white rounded-8 p-20">
                                        <img src="{{ $file_url }}" alt="{{ $lesson->title }}" class="img-fluid rounded-8" style="max-height: 600px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- No Content -->
                        <div class="text-center py-64">
                            <i class="ph ph-file-x text-gray-300" style="font-size: 64px;"></i>
                            <h5 class="text-gray-500 mt-16 mb-8">Konten Belum Tersedia</h5>
                            <p class="text-gray-400">Materi pembelajaran akan segera ditambahkan</p>
                        </div>
                    @endif

                    <!-- Completion Section -->
                    <div class="mt-40 pt-24 border-top border-gray-100">
                        <div class="text-center">
                            @if($isCompleted)
                                <div class="d-inline-flex align-items-center bg-success-50 text-success-600 py-12 px-24 rounded-pill mb-16">
                                    <i class="ph ph-check-circle me-2" style="font-size: 20px;"></i>
                                    <span class="fw-medium">Pelajaran Selesai</span>
                                </div>
                                <p class="text-gray-600 mb-24">Selamat! Anda telah menyelesaikan pelajaran ini.</p>
                            @else
                                <h5 class="fw-bold text-gray-900 mb-16">Selesaikan Pelajaran</h5>
                                <p class="text-gray-600 mb-24">Tandai sebagai selesai untuk melanjutkan ke materi berikutnya</p>
                                <button type="button" id="completeBtn" class="btn btn-success btn-lg rounded-pill py-12 px-32 completion-button">
                                    <i class="ph ph-check-circle me-2"></i>
                                    Tandai Selesai
                                </button>
                            @endif

                            <!-- Navigation Buttons -->
                            <div class="d-flex justify-content-between mt-32">
                                @if($previousLesson)
                                    <a href="{{ route('lesson.show', $previousLesson->id) }}" class="btn btn-outline-primary rounded-pill py-10 px-20">
                                        <i class="ph ph-arrow-left me-1"></i> Sebelumnya
                                    </a>
                                @else
                                    <div></div>
                                @endif

                                @if($nextLesson)
                                    <a href="{{ route('lesson.show', $nextLesson->id) }}" class="btn btn-primary rounded-pill py-10 px-20" id="nextLessonBtn">
                                        Selanjutnya <i class="ph ph-arrow-right ms-1"></i>
                                    </a>
                                @elseif($moduleQuiz)
                                    <a href="{{ route('quiz.show', $moduleQuiz->id) }}" class="btn btn-warning rounded-pill py-10 px-20">
                                        <i class="ph ph-exam me-1"></i> Kerjakan Quiz
                                    </a>
                                @else
                                    <div class="text-muted small">Tidak ada materi selanjutnya</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3 col-md-4 bg-gray-50 border-start lesson-sidebar">
            <div class="p-24">
                <div class="lesson-navigation">
                    <h6 class="fw-bold text-gray-900 mb-16">
                        <i class="ph ph-list-bullets me-2"></i>Daftar Materi
                    </h6>

                    <!-- Progress Bar -->
                    <div class="mb-20">
                        <div class="d-flex justify-content-between mb-8">
                            <span class="text-14 text-gray-600">Progress</span>
                            <span class="text-14 fw-medium text-success-600">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="lesson-progress">
                            <div class="lesson-progress-bar" style="width: {{ $completionPercentage }}%"></div>
                        </div>
                    </div>

                    <!-- Lessons List -->
                    @foreach($courseModules as $module)
                        <div class="mb-20">
                            <h6 class="fw-medium text-gray-800 mb-12 text-14">{{ $module->title }}</h6>

                            @foreach($module->lessons as $moduleLesson)
                                <div class="lesson-item {{ $moduleLesson->id == $lesson->id ? 'active' : '' }} p-12">
                                    <a href="{{ route('lesson.show', $moduleLesson->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            @if($moduleLesson->isCompletedByUser(Auth::user()))
                                                <i class="ph ph-check-circle text-success-600" style="font-size: 16px;"></i>
                                            @else
                                                <i class="ph ph-circle text-gray-400" style="font-size: 16px;"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-14 fw-medium text-gray-800 mb-2">{{ $moduleLesson->title }}</div>
                                            <div class="text-12 text-gray-500">
                                                <i class="ph ph-video me-1"></i>
                                                {{ $moduleLesson->duration_in_minutes ?? 5 }} menit
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach

                            @if($module->quiz)
                                <div class="lesson-item p-12">
                                    <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-decoration-none d-flex align-items-start">
                                        <div class="me-8 mt-2">
                                            <i class="ph ph-exam text-warning-600" style="font-size: 16px;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-14 fw-medium text-gray-800 mb-2">Quiz: {{ $module->quiz->title }}</div>
                                            <div class="text-12 text-warning-600">
                                                <i class="ph ph-question me-1"></i>
                                                {{ $module->quiz->questions->count() }} pertanyaan
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for completion -->
<form id="completeForm" action="{{ route('lesson.complete', $lesson->id) }}" method="POST" class="d-none">
    @csrf
</form>

<!-- AI Chat Widget -->
<div class="ai-chat-widget">
    <button class="ai-chat-toggle" id="aiChatToggle" title="Tanya AI Assistant">
        <i class="ph ph-chats-circle"></i>
    </button>
</div>

<!-- AI Chat Modal -->
<div class="ai-chat-modal-overlay" id="aiChatModalOverlay">
    <div class="ai-chat-modal" id="aiChatModal">
        <!-- Header -->
        <div class="ai-chat-header">
            <div class="ai-chat-header-content">
                <div class="ai-chat-avatar">
                    <i class="ph ph-robot"></i>
                </div>
                <div class="ai-chat-header-text">
                    <h5>AI Assistant</h5>
                    <small>{{ Str::limit($lesson->title, 50) }}</small>
                </div>
            </div>
            <button class="ai-chat-close" id="aiChatClose" title="Tutup">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="ai-chat-messages" id="aiChatMessages">
            <!-- Welcome Message -->
            <div class="ai-welcome-message">
                <i class="ph ph-chats-teardrop"></i>
                <h6>Selamat Datang di AI Assistant!</h6>
                <p>Tanyakan apapun tentang materi pelajaran ini. Saya siap membantu Anda! 🚀</p>
            </div>
        </div>

        <!-- Input Area -->
        <div class="ai-chat-input-area">
            <div class="ai-chat-input-wrapper">
                <div class="ai-chat-input-container">
                    <textarea
                        class="ai-chat-input"
                        id="aiChatInput"
                        placeholder="Tanyakan sesuatu tentang materi ini..."
                        rows="1"
                        maxlength="1000"
                    ></textarea>
                </div>
                <button class="ai-chat-send" id="aiChatSend" title="Kirim pesan">
                    <i class="ph ph-paper-plane-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AI Chat Modal JavaScript
    const chatToggle = document.getElementById('aiChatToggle');
    const chatModalOverlay = document.getElementById('aiChatModalOverlay');
    const chatModal = document.getElementById('aiChatModal');
    const chatClose = document.getElementById('aiChatClose');
    const chatInput = document.getElementById('aiChatInput');
    const chatSend = document.getElementById('aiChatSend');
    const chatMessages = document.getElementById('aiChatMessages');
    const lessonId = {{ $lesson->id }};

    // Open modal
    chatToggle.addEventListener('click', () => {
        chatModalOverlay.classList.add('active');
        setTimeout(() => chatInput.focus(), 400);
    });

    // Close modal
    chatClose.addEventListener('click', closeModal);

    // Close on overlay click
    chatModalOverlay.addEventListener('click', (e) => {
        if (e.target === chatModalOverlay) {
            closeModal();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && chatModalOverlay.classList.contains('active')) {
            closeModal();
        }
    });

    function closeModal() {
        chatModalOverlay.classList.remove('active');
    }

    // Auto-resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });

    // Send message on Enter key
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Send message on button click
    chatSend.addEventListener('click', sendMessage);

    function sendMessage() {
        const message = chatInput.value.trim();

        if (!message) {
            return;
        }

        // Disable input while sending
        chatInput.disabled = true;
        chatSend.disabled = true;

        // Add user message to chat
        appendMessage('user', message);
        chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        // Send to backend
        $.ajax({
            url: '{{ route("aiassistant.lesson-chat") }}',
            method: 'POST',
            data: {
                message: message,
                lesson_id: lessonId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Response:', response);
                removeTypingIndicator();

                // Display AI answer
                if (response.answer) {
                    appendMessage('ai', response.answer);
                } else {
                    appendMessage('ai', 'Maaf, tidak ada respons dari AI.');
                }

                // Re-enable input
                chatInput.disabled = false;
                chatSend.disabled = false;
                chatInput.focus();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                removeTypingIndicator();

                // Try to get error message from response
                let errorMessage = 'Maaf, terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.answer) {
                    errorMessage = xhr.responseJSON.answer;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                appendMessage('ai', errorMessage);

                // Re-enable input
                chatInput.disabled = false;
                chatSend.disabled = false;
            }
        });
    }

    function appendMessage(type, text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `ai-message ${type}`;

        const time = new Date().toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });

        const avatarIcon = type === 'user' ? 'ph-user' : 'ph-robot';

        messageDiv.innerHTML = `
            <div class="ai-message-avatar">
                <i class="ph ${avatarIcon}"></i>
            </div>
            <div class="ai-message-content">
                <div class="ai-message-bubble">${escapeHtml(text)}</div>
                <div class="ai-message-time">${time}</div>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'ai-message ai';
        indicator.id = 'typingIndicator';
        indicator.innerHTML = `
            <div class="ai-message-avatar">
                <i class="ph ph-robot"></i>
            </div>
            <div class="ai-message-content">
                <div class="ai-typing-indicator">
                    <div class="ai-typing-dot"></div>
                    <div class="ai-typing-dot"></div>
                    <div class="ai-typing-dot"></div>
                </div>
            </div>
        `;
        chatMessages.appendChild(indicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        if (indicator) {
            indicator.remove();
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Lesson completion logic
    const completeBtn = document.getElementById('completeBtn');
    const completeForm = document.getElementById('completeForm');

    if (completeBtn && completeForm) {
        completeBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Selesaikan Pelajaran?',
                text: 'Tandai pelajaran ini sebagai selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    completeBtn.disabled = true;
                    completeBtn.innerHTML = '<i class="ph ph-spinner me-2" style="animation: spin 1s linear infinite;"></i>Memproses...';

                    // Submit form
                    fetch(completeForm.action, {
                        method: 'POST',
                        body: new FormData(completeForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            @if($nextLesson)
                                // Redirect to next lesson
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Lanjut ke materi berikutnya...',
                                    icon: 'success',
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("lesson.show", $nextLesson->id) }}';
                                });
                            @elseif($moduleQuiz)
                                // Redirect to quiz
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Saatnya mengerjakan quiz...',
                                    icon: 'success',
                                    timer: 1000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = '{{ route("quiz.show", $moduleQuiz->id) }}';
                                });
                            @else
                                // No next lesson or quiz, just reload
                                Swal.fire({
                                    title: '✅ Pelajaran Selesai!',
                                    text: 'Selamat! Anda telah menyelesaikan materi ini.',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            @endif
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    })
                    .catch(error => {
                        completeBtn.disabled = false;
                        completeBtn.innerHTML = '<i class="ph ph-check-circle me-2"></i>Tandai Selesai';

                        Swal.fire({
                            title: '❌ Gagal!',
                            text: 'Terjadi kesalahan. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'Oke'
                        });
                    });
                }
            });
        });
    }
});
</script>
@endsection
