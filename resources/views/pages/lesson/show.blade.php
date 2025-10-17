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

    /* Sidebar/course item styles to match course page */
    .course-item__arrow i { transition: transform 0.3s ease; }
    .course-item-dropdown { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; will-change: max-height; }
    .course-item.active .course-item__arrow i { transform: rotate(90deg); }
    .course-list__item:hover { background-color: #f8f9fa; border-radius: 8px; transition: background-color 0.2s ease; }
    .progress { border-radius: 10px; background-color: #e9ecef; }
    .progress-bar { border-radius: 10px; transition: width 0.6s ease; }

    /* Simple spinner for current lesson in-progress */
    @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .ph-spinner { animation: spin 1s linear infinite; display: inline-block; }

    /* Explicit hollow dot for current lesson (no check icon at all) */
    .status-dot {
        width: 20px;
        height: 20px;
        border: 2px solid currentColor;
        border-radius: 50%;
        display: inline-block;
    }

    /* Static progress icon only: spinner animation removed */

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
@php
    $course = $lesson->module->course;
    $user = Auth::user();
    $rightProgress = method_exists($course, 'getCompletionPercentage') && $user ? $course->getCompletionPercentage($user) : ($completionPercentage ?? 0);
    $courseModules = isset($courseModules) ? $courseModules : $course->modules;
@endphp
<div class="row gy-4">
    <!-- Main Content Area -->
    <div class="col-lg-8 col-md-8">
        <div class="card">
            <div class="card-body p-lg-20 p-sm-3">
                <div class="flex-between flex-wrap gap-12 mb-20">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('course.show', $course->id) }}" class="text-gray-200 fw-normal text-15 hover-text-main-600 text-decoration-none">
                                    <i class="ph ph-arrow-left me-1"></i> {{ $course->title }}
                                </a>
                            </li>
                            <li class="breadcrumb-item text-muted">{{ $lesson->module->title }}</li>
                            <li class="breadcrumb-item active">{{ $lesson->title }}</li>
                        </ol>
                    </nav>

                    <div class="d-flex align-items-center gap-12">
                        <span class="py-6 px-12 bg-success-50 text-success-600 rounded-pill text-15">
                            <i class="ph ph-video me-1"></i>Pelajaran
                        </span>
                        @if($lesson->duration_in_minutes)
                            <span class="text-gray-500 text-14">
                                <i class="ph ph-clock me-1"></i>{{ $lesson->duration_in_minutes }} menit
                            </span>
                        @endif
                    </div>
                </div>

                <h3 class="mb-8">{{ $lesson->title }}</h3>
                @if($lesson->description)
                    <p class="text-gray-300 text-15">{{ $lesson->description }}</p>
                @endif

                <!-- Content Area Based on Type -->
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

                <!-- Navigasi Bawah (langsung di bawah materi) -->
                <div class="d-flex justify-content-between mt-32">
                    @if($previousLesson)
                        <a href="{{ route('lesson.show', $previousLesson->id) }}" class="btn btn-outline-primary rounded-pill py-10 px-20">
                            <i class="ph ph-arrow-left me-1"></i> Sebelumnya
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextLesson)
                        <a href="{{ route('lesson.show', $nextLesson->id) }}" class="btn btn-primary rounded-pill py-10 px-20" id="nextLessonBtn" data-completed="{{ $isCompleted ? '1' : '0' }}">
                            Selanjutnya <i class="ph ph-arrow-right ms-1"></i>
                        </a>
                    @elseif($moduleQuiz)
                        <a href="{{ route('quiz.show', $moduleQuiz->id) }}" class="btn btn-warning rounded-pill py-10 px-20" id="nextLessonBtn" data-completed="{{ $isCompleted ? '1' : '0' }}">
                            <i class="ph ph-exam me-1"></i> Kerjakan Quiz
                        </a>
                    @else
                        <button type="button" class="btn btn-success rounded-pill py-10 px-20" id="nextLessonBtn" data-completed="{{ $isCompleted ? '1' : '0' }}">
                            Tandai Selesai
                        </button>
                    @endif
                </div>

                <!-- Ringkasan Pelajaran -->
                <div class="mt-32">
                    <h5 class="fw-bold text-gray-900 mb-12">Ringkasan Pelajaran</h5>
                    @php $summary = $lesson->summary ?? $lesson->description; @endphp
                    <div class="text-gray-700" style="font-size: 15px;">{!! $summary ? nl2br(e($summary)) : 'Ringkasan belum tersedia.' !!}</div>
                </div>

                <!-- Instruktur -->
                <div class="mt-24 pt-24 border-top border-gray-100">
                    <h5 class="fw-bold text-gray-900 mb-12">Instruktur</h5>
                    <div class="d-flex align-items-center gap-12">
                        @php $author = $lesson->module->course->author; @endphp
                        @if($author && ($author->avatar ?? null))
                            <img src="{{ Storage::url($author->avatar) }}" alt="{{ $author->name }}" class="w-56 h-56 rounded-circle object-fit-cover">
                        @else
                            <div class="w-56 h-56 rounded-circle bg-main-50 d-flex align-items-center justify-content-center">
                                <i class="ph ph-user text-main-600" style="font-size: 24px;"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold text-gray-900">{{ $author->name ?? 'Administrator' }}</div>
                            <div class="text-13 text-gray-500">{{ $author->position ?? 'Instruktur' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4 col-md-4">
        @php
            $totalLessons = $course->modules->sum(fn($m) => $m->lessons->count());
            $totalQuizzes = $course->modules->whereNotNull('quiz')->count();
            $allQuizzes = $course->modules->map->quiz->filter();
            $passedQuizzes = 0;
            if($user){
                foreach ($allQuizzes as $quiz) {
                    if ($user->quizAttempts()->where('quiz_id', $quiz->id)->where('passed', true)->exists()) {
                        $passedQuizzes++;
                    }
                }
                $completedLessons = $user->completedLessons()->whereIn('lesson_id', $course->modules->flatMap->lessons->pluck('id'))->count();
            } else {
                $completedLessons = 0;
            }
            $totalItems = $totalLessons + $totalQuizzes;
            $completedItems = $completedLessons + $passedQuizzes;
        @endphp

        <!-- Progress card -->
        <div class="card mt-0">
            <div class="card-body">
                <h6 class="mb-12">Progress Pembelajaran</h6>
                <div class="d-flex justify-content-between mb-8 text-14">
                    <span class="text-gray-600">{{ $completedItems }} dari {{ $totalItems }} materi selesai</span>
                    <span class="text-main-600 fw-medium">{{ $rightProgress }}%</span>
                </div>
                <div class="progress" style="height:8px;">
                    <div class="progress-bar bg-main-600" role="progressbar" style="width: {{ $rightProgress }}%"></div>
                </div>
            </div>
        </div>

        <!-- Daftar Materi card using course accordion style -->
        <div class="card mt-24">
            <div class="card-body p-0">
                @php
                    $firstLessonCandidate = $course->modules->sortBy('order')->flatMap->lessons->sortBy('order')->first();
                    $firstLessonUrl = $firstLessonCandidate ? route('lesson.show', $firstLessonCandidate->id) : null;
                @endphp

                <div class="course-item">
                    <button type="button" class="course-item__button flex-align gap-4 w-100 p-16 ">
                        <span class="d-block text-start">
                            <span class="d-block h5 mb-0 text-line-1">Intro Kursus</span>
                            <span class="d-block text-15 text-gray-300">Ringkasan & mulai</span>
                        </span>
                        <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-caret-down"></i></span>
                    </button>
                    <div class="course-item-dropdown border-top border-gray-100">
                        <ul class="course-list p-16 pb-0">
                            <li class="course-list__item flex-align gap-8 mb-16 active">
                                <span class="circle flex-shrink-0 text-32 d-flex text-main-600"><i class="ph-fill ph-check-circle"></i></span>
                                <div class="w-100">
                                    <a href="{{ route('course.show', $course->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                        Buka Ringkasan Kursus
                                        <span class="text-gray-300 fw-normal d-block">Lihat deskripsi, pengajar, dan daftar modul</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                @forelse($courseModules as $index => $module)
                    <div class="course-item border-top border-gray-100">
                        <button type="button" class="course-item__button flex-align gap-4 w-100 p-16 ">
                            <span class="d-block text-start">
                                <span class="d-block h5 mb-0 text-line-1">{{ $module->title }}</span>
                                <span class="d-block text-15 text-gray-300">{{ $module->lessons->count() }} pelajaran</span>
                            </span>
                            <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-arrow-right"></i></span>
                        </button>
                        <div class="course-item-dropdown border-top border-gray-100 {{ $module->lessons->contains('id', $lesson->id) ? 'active' : '' }}">
                            <ul class="course-list p-16 pb-0">
                                @foreach($module->lessons as $lessonIndex => $moduleLesson)
                                    @php
                                        $isCurrent = $moduleLesson->id == $lesson->id;
                                        $isDone = $moduleLesson->isCompletedByUser($user);
                                        // Jika ini adalah pelajaran yang sedang dibuka dan BELUM selesai,
                                        // paksa ikon menjadi bulat kosong meskipun ada data lama
                                        $forceEmpty = $isCurrent && (isset($isCompleted) && !$isCompleted);
                                        // Tampilkan ceklist hanya jika SELESAI dan BUKAN pelajaran yang sedang dibuka
                                        $displayCheck = !$isCurrent && $isDone;
                                    @endphp
                                    <li class="course-list__item flex-align gap-8 mb-16 {{ $isCurrent ? 'is-current' : '' }}">
                                        <span class="circle flex-shrink-0 d-flex {{ $isCurrent ? 'text-main-600' : ($isDone ? 'text-main-600' : 'text-gray-100') }}">
                                            @if($isCurrent)
                                                <i class="ph ph-circle text-32"></i>
                                            @elseif($isDone)
                                                <i class="ph-fill ph-check-circle text-32"></i>
                                            @else
                                                <i class="ph ph-circle text-32"></i>
                                            @endif
                                        </span>
                                        <div class="w-100 d-flex align-items-start justify-content-between gap-8">
                                            <a href="{{ route('lesson.show', $moduleLesson->id) }}" class="fw-medium d-block hover-text-main-600 d-lg-block flex-grow-1 {{ $isCurrent ? 'text-main-600' : 'text-gray-300' }}">
                                                {{ $lessonIndex + 1 }}. {{ $moduleLesson->title }}
                                                <span class="text-gray-300 fw-normal d-block">{{ $moduleLesson->duration_in_minutes ?? 5 }} menit</span>
                                            </a>
                                            @if($isCurrent && ($forceEmpty || !$isDone))
                                                <span title="Sedang dipelajari" class="text-main-600 mt-1"><i class="ph ph-spinner ph-spinner"></i></span>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                                @if($module->quiz)
                                    <li class="course-list__item flex-align gap-8 mb-16">
                                        <span class="circle flex-shrink-0 text-32 d-flex text-warning-600"><i class="ph ph-question"></i></span>
                                        <div class="w-100">
                                            <a href="{{ route('quiz.show', $module->quiz->id) }}" class="text-gray-300 fw-medium d-block hover-text-main-600 d-lg-block">
                                                Quiz: {{ $module->quiz->title }}
                                                <span class="text-gray-300 fw-normal d-block">{{ $module->quiz->questions->count() }} pertanyaan • {{ $module->quiz->duration_in_minutes }} min</span>
                                            </a>
                                        </div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="p-20 text-center">
                        <i class="ph ph-book text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada modul</h5>
                        <p class="text-muted">Modul akan ditampilkan di sini setelah dibuat.</p>
                    </div>
                @endforelse
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

    // Next button marks lesson complete before navigation
    const nextBtn = document.getElementById('nextLessonBtn');
    const completeForm = document.getElementById('completeForm');

    async function markCompleteThenNavigate(navigateTo) {
        try {
            // Update sidebar icon to a static progress indicator while processing
            const currentItem = document.querySelector('.course-list__item.active .circle i');
            if (currentItem) {
                currentItem.className = 'ph ph-circle';
                currentItem.style.animation = '';
            }

            const resp = await fetch(completeForm.action, {
                method: 'POST',
                body: new FormData(completeForm),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await resp.json();
            if (!data.success) throw new Error(data.message || 'Terjadi kesalahan');

            // Keep current icon as progress circle; the page change will render it as check on previous item

            if (navigateTo) {
                window.location.href = navigateTo;
            } else {
                window.location.reload();
            }
        } catch (e) {
            Swal.fire({
                title: '❌ Gagal!',
                text: 'Terjadi kesalahan saat menandai selesai. Coba lagi.',
                icon: 'error'
            });
        }
    }

    if (nextBtn && completeForm) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const already = this.getAttribute('data-completed') === '1';
            const href = this.getAttribute('href');
            if (already) {
                if (href) window.location.href = href; else window.location.reload();
                return;
            }
            // mark complete then go
            markCompleteThenNavigate(href);
        });
    }
});

// Sidebar accordion logic to mirror course page behavior
document.addEventListener('DOMContentLoaded', function () {
    const courseItems = document.querySelectorAll('.course-item');
    courseItems.forEach(item => {
        const button = item.querySelector('.course-item__button');
        const dropdown = item.querySelector('.course-item-dropdown');
        const arrowIcon = item.querySelector('.course-item__arrow i');
        if (button && dropdown) {
            button.addEventListener('click', () => {
                const isActive = item.classList.contains('active');
                courseItems.forEach(other => {
                    other.classList.remove('active');
                    const dd = other.querySelector('.course-item-dropdown');
                    if (dd) dd.style.maxHeight = null;
                    const arr = other.querySelector('.course-item__arrow i');
                    if (arr) arr.style.transform = 'rotate(0deg)';
                });
                if (!isActive) {
                    item.classList.add('active');
                    dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
                    if (arrowIcon) arrowIcon.style.transform = 'rotate(90deg)';
                }
            });
        }
    });

    // Open the dropdown that contains the current lesson
    const activeDropdown = document.querySelector('.course-item-dropdown.active');
    if (activeDropdown) {
        const parent = activeDropdown.closest('.course-item');
        if (parent) {
            parent.classList.add('active');
            activeDropdown.style.maxHeight = activeDropdown.scrollHeight + 'px';
            const arr = parent.querySelector('.course-item__arrow i');
            if (arr) arr.style.transform = 'rotate(90deg)';
        }
    }
});
</script>
@endsection
