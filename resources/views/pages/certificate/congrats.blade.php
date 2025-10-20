@extends('layouts.main')

@section('css')
<style>
    .cert-banner{
        background: #d1fae5;
        border: 1px solid #86efac;
        color: #166534;
        border-radius: 12px;
        padding: 14px 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .cert-hero{
        background: #ffffff;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    }
    .cert-cat{
        background: #ecfdf5;
        color: #065f46;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }
    .cert-badge{ background: #d1fae5; color:#065f46; }
    .cert-actions .btn{ min-width: 180px; }
    .cert-thumb{ max-width: 560px; border-radius: 12px; box-shadow: 0 10px 24px rgba(16,185,129,0.15); }
    @media (max-width: 992px){ .cert-thumb{ max-width: 100%; } }
    .back-circle{
        width: 38px; height: 38px; border-radius: 999px; border: none;
        background: #e5edff; color: #2952ff; display: inline-flex; align-items:center; justify-content:center;
    }
    .preview-link{ color:#94a3b8; text-decoration:none; display: inline-flex; align-items:center; gap:6px; }
    .preview-link:hover{ color:#64748b; }
</style>
@endsection

@section('content')
<div class="breadcrumb mb-24">
    <ul class="flex-align gap-4">
        <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Dashboard</a></li>
        <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
        <li><span class="text-main-600 fw-normal text-15">Get Certificate</span></li>
    </ul>
</div>

<div class="card">
    <div class="card-body">
        <!-- Back circle button -->
        <div class="mb-12">
            <a href="{{ route('course.show', $course->id) . '?mode=learn' }}" class="back-circle" title="Kembali ke Kursus"><i class="ph ph-arrow-left"></i></a>
        </div>
        <!-- Completed Banner -->
        <div class="cert-banner mb-16">
            <i class="ph ph-seal-check"></i>
            <span>Course Completed</span>
        </div>

        <!-- Hero Section -->
        <div class="cert-hero">
            <div class="row g-24 align-items-center">
                <div class="col-lg-5">
                    <img src="{{ $course->thumbnail ? asset('storage/'.$course->thumbnail) : asset('assets/images/thumbs/course-02.png') }}" class="img-fluid cert-thumb w-100" alt="Thumbnail">
                </div>
                <div class="col-lg-7">
                    @if($course->category)
                        <div class="mb-10">
                            <span class="cert-cat">{{ $course->category->name }}</span>
                        </div>
                    @endif
                    <h2 class="fw-bold mb-12" style="letter-spacing:-0.3px;">{{ $course->title }}</h2>
                    <p class="text-gray-700 mb-16" style="font-size:16px; line-height:1.7;">{{ $course->summary ?? Str::limit(strip_tags($course->description), 240) }}</p>

                    @if($course->author)
                        <div class="d-flex align-items-center gap-12 mb-20">
                            <img src="{{ $course->author->profile_photo_url ?? asset('assets/images/thumbs/user.png') }}" class="w-48 h-48 rounded-circle object-fit-cover" alt="author"/>
                            <div>
                                <div class="text-13 text-gray-500">Created by</div>
                                <div class="fw-bold">{{ $course->author->name }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="cert-actions d-flex align-items-center gap-16 flex-wrap">
                        @if($certificate)
                            <a href="{{ route('certificate.download', $certificate->id) }}" class="btn btn-primary rounded-pill py-10 px-20"><i class="ph ph-download me-1"></i> Download Certificate</a>
                            <a href="{{ route('certificate.preview', $certificate->id) }}" target="_blank" class="preview-link"><i class="ph ph-eye"></i> Preview</a>
                        @else
                            <button class="btn btn-secondary rounded-pill py-10 px-20" disabled><i class="ph ph-hourglass me-1"></i> Generating Certificate...</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
