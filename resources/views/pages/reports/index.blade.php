@extends('layouts.main')

@section('content')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header flex-between">
                    <h4 class="mb-0">Laporan Sistem</h4>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-16 rounded-12 bg-main-50">
                                <div class="text-gray-600">Total Kursus</div>
                                <h3 class="mb-0">{{ number_format($totalCourses ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-16 rounded-12 bg-main-two-50">
                                <div class="text-gray-600">Total Pengguna (non-admin)</div>
                                <h3 class="mb-0">{{ number_format($totalUsers ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-16 rounded-12 bg-purple-50">
                                <div class="text-gray-600">Total Penyelesaian</div>
                                <h3 class="mb-0">{{ number_format($totalCompletions ?? 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="p-16 rounded-12 bg-warning-50">
                                <div class="text-gray-600">Total Pengajar</div>
                                <h3 class="mb-0">{{ number_format($totalInstructors ?? 0) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="mt-24">
                        <h6 class="mb-12">Aktivitas Admin Terbaru</h6>
                        @php
                            $logs = $recentLogs ?? collect();
                            $resolveLogMeta = function($action) {
                                $map = [
                                    'user.created' => ['icon' => 'ph-user-plus', 'bg' => 'bg-main-50', 'fg' => 'text-main-600', 'badge' => 'User'],
                                    'user.updated' => ['icon' => 'ph-pencil', 'bg' => 'bg-warning-50', 'fg' => 'text-warning-600', 'badge' => 'User'],
                                    'user.deleted' => ['icon' => 'ph-trash', 'bg' => 'bg-danger-50', 'fg' => 'text-danger-600', 'badge' => 'User'],
                                    'course.created' => ['icon' => 'ph-book-open', 'bg' => 'bg-main-50', 'fg' => 'text-main-600', 'badge' => 'Course'],
                                    'course.updated' => ['icon' => 'ph-pencil-line', 'bg' => 'bg-warning-50', 'fg' => 'text-warning-600', 'badge' => 'Course'],
                                    'course.published' => ['icon' => 'ph-megaphone', 'bg' => 'bg-success-50', 'fg' => 'text-success-600', 'badge' => 'Course'],
                                    'course.unpublished' => ['icon' => 'ph-megaphone-slash', 'bg' => 'bg-gray-50', 'fg' => 'text-gray-600', 'badge' => 'Course'],
                                    'course.participants_updated' => ['icon' => 'ph-users-three', 'bg' => 'bg-purple-50', 'fg' => 'text-purple-600', 'badge' => 'Course'],
                                ];
                                return $map[$action] ?? ['icon' => 'ph-activity', 'bg' => 'bg-gray-50', 'fg' => 'text-gray-700', 'badge' => 'Activity'];
                            };
                        @endphp

                        @if($logs->count() > 0)
                            <div class="position-relative ps-24">
                                <span class="position-absolute start-8 top-0 bottom-0 w-2 bg-gray-100 rounded-pill"></span>
                                <ul class="list-unstyled mb-0">
                                    @foreach($logs as $log)
                                        @php $meta = $resolveLogMeta($log['action'] ?? ''); @endphp
                                        <li class="d-flex gap-12 align-items-start mb-16 position-relative">
                                            <span class="position-absolute start-0 translate-middle-x w-10 h-10 rounded-circle bg-white border border-gray-200"></span>
                                            <div class="flex-shrink-0 {{ $meta['bg'] }} rounded-12 w-36 h-36 d-flex align-items-center justify-content-center">
                                                <i class="ph {{ $meta['icon'] }} {{ $meta['fg'] }} text-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-8 flex-wrap">
                                                    @if(!empty($log['causer_name']))
                                                        <span class="fw-semibold text-gray-900 text-14">{{ $log['causer_name'] }}</span>
                                                    @endif
                                                    <span class="badge rounded-pill px-8 py-4 text-11 {{ $meta['bg'] }} {{ $meta['fg'] }}">{{ $meta['badge'] }}</span>
                                                    <span class="text-12 text-gray-500">{{ \Carbon\Carbon::parse($log['time'])->diffForHumans() }}</span>
                                                </div>
                                                <div class="text-14 text-gray-800 mt-4" style="word-break: break-word; overflow-wrap: anywhere;">
                                                    {{ $log['description'] ?? $log['text'] }}
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="w-56 h-56 bg-gray-50 rounded-circle mx-auto flex-center mb-10">
                                    <i class="ph ph-activity text-28 text-gray-400"></i>
                                </div>
                                <div class="text-gray-600">Belum ada aktivitas.</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
