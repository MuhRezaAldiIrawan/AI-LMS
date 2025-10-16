@extends('layouts.main')

@section('content')
    <div class="row gy-4">
        <!-- Header Card -->
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(90deg, rgba(247,247,250,1), rgba(255,255,255,1)); border-radius:12px;">
                <div class="card-body py-20 px-20 d-flex align-items-center justify-content-between flex-wrap">
                    <div class="me-3" style="min-width:260px;">
                        <h3 class="text-main-800 fw-bold mb-2" style="display:flex; align-items:center; gap:8px;">
                            <i class="ph-fill ph-trophy text-main-600" style="font-size:20px;"></i>
                            <span>My Learning Points</span>
                        </h3>
                        <p class="mb-0 text-main-600">Track your learning progress and achievements</p>
                    </div>

                    <div class="ms-auto d-flex align-items-center">
                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:96px;height:96px;">
                            <div class="text-center">
                                <div class="text-main-800 fw-bold" style="font-size:20px;">{{ number_format(Auth::user()->getTotalPoints()) }}</div>
                                <div class="text-main-600" style="font-size:12px;">Total Points</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point Breakdown Cards -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="flex-between mb-16">
                        <div class="w-48 h-48 bg-main-50 rounded-circle flex-center">
                            <i class="ph-fill ph-book-open text-main-600 text-2xl"></i>
                        </div>
                        <span class="badge bg-main-50 text-main-600 px-12 py-6 rounded-pill">
                            +5 pts each
                        </span>
                    </div>
                    <h4 class="text-main-600 mb-8">
                        {{ number_format(Auth::user()->pointLogs()->where('related_type', 'App\Models\Lesson')->sum('points_earned')) }}
                    </h4>
                    <p class="text-gray-600 mb-8">Points from Lessons</p>
                    <div class="flex-align gap-6">
                        <span class="text-sm text-gray-500">
                            {{ Auth::user()->pointLogs()->where('related_type', 'App\Models\Lesson')->count() }} lessons completed
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="flex-between mb-16">
                        <div class="w-48 h-48 bg-success-50 rounded-circle flex-center">
                            <i class="ph-fill ph-check-circle text-success-600 text-2xl"></i>
                        </div>
                        <span class="badge bg-success-50 text-success-600 px-12 py-6 rounded-pill">
                            +10 pts each
                        </span>
                    </div>
                    <h4 class="text-success-600 mb-8">
                        {{ number_format(Auth::user()->pointLogs()->where('related_type', 'App\Models\Quiz')->sum('points_earned')) }}
                    </h4>
                    <p class="text-gray-600 mb-8">Points from Quizzes</p>
                    <div class="flex-align gap-6">
                        <span class="text-sm text-gray-500">
                            {{ Auth::user()->pointLogs()->where('related_type', 'App\Models\Quiz')->count() }} quizzes passed
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="flex-between mb-16">
                        <div class="w-48 h-48 bg-warning-50 rounded-circle flex-center">
                            <i class="ph-fill ph-certificate text-warning-600 text-2xl"></i>
                        </div>
                        <span class="badge bg-warning-50 text-warning-600 px-12 py-6 rounded-pill">
                            +20 pts each
                        </span>
                    </div>
                    <h4 class="text-warning-600 mb-8">
                        {{ number_format(Auth::user()->pointLogs()->where('related_type', 'App\Models\Course')->sum('points_earned')) }}
                    </h4>
                    <p class="text-gray-600 mb-8">Points from Courses</p>
                    <div class="flex-align gap-6">
                        <span class="text-sm text-gray-500">
                            {{ Auth::user()->pointLogs()->where('related_type', 'App\Models\Course')->count() }} courses completed
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Point History -->
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-gray-100">
                    <div class="flex-between flex-wrap gap-16">
                        <h5 class="mb-0">Point History</h5>
                        <div class="flex-align gap-12">
                            <span class="text-sm text-gray-600">
                                Total Transactions: <strong>{{ Auth::user()->pointLogs()->count() }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        $pointLogs = Auth::user()->pointLogs()
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);
                    @endphp

                    @if($pointLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-24 py-16">Date & Time</th>
                                        <th class="px-24 py-16">Activity</th>
                                        <th class="px-24 py-16">Type</th>
                                        <th class="px-24 py-16 text-end">Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pointLogs as $log)
                                        <tr>
                                            <td class="px-24 py-16">
                                                <div class="text-sm text-gray-900">
                                                    {{ $log->created_at->format('d M Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $log->created_at->format('H:i') }}
                                                </div>
                                            </td>
                                            <td class="px-24 py-16">
                                                <div class="text-sm text-gray-900">
                                                    {{ $log->reason }}
                                                </div>
                                            </td>
                                            <td class="px-24 py-16">
                                                @if($log->related_type == 'App\Models\Lesson')
                                                    <span class="badge bg-main-50 text-main-600 px-12 py-4 rounded-pill">
                                                        <i class="ph-fill ph-book-open me-1"></i>
                                                        Lesson
                                                    </span>
                                                @elseif($log->related_type == 'App\Models\Quiz')
                                                    <span class="badge bg-success-50 text-success-600 px-12 py-4 rounded-pill">
                                                        <i class="ph-fill ph-check-circle me-1"></i>
                                                        Quiz
                                                    </span>
                                                @elseif($log->related_type == 'App\Models\Course')
                                                    <span class="badge bg-warning-50 text-warning-600 px-12 py-4 rounded-pill">
                                                        <i class="ph-fill ph-certificate me-1"></i>
                                                        Course
                                                    </span>
                                                @else
                                                    <span class="badge bg-gray-100 text-gray-600 px-12 py-4 rounded-pill">
                                                        <i class="ph-fill ph-star me-1"></i>
                                                        Other
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-24 py-16 text-end">
                                                <span class="fw-bold {{ $log->points_earned > 0 ? 'text-success-600' : 'text-danger-600' }}">
                                                    {{ $log->points_earned > 0 ? '+' : '' }}{{ number_format($log->points_earned) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($pointLogs->hasPages())
                            <div class="px-24 py-16 border-top border-gray-100">
                                {{ $pointLogs->links() }}
                            </div>
                        @endif
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-64">
                            <div class="w-80 h-80 bg-gray-50 rounded-circle mx-auto flex-center mb-16" style="width: 100px; height: 100px;">
                                <i class="ph ph-trophy text-64 text-gray-400"></i>
                            </div>
                            <h5 class="text-gray-600 mb-8">No Point History Yet</h5>
                            <p class="text-gray-500 mb-20">Start completing lessons and quizzes to earn points!</p>
                            <a href="{{ route('course') }}" class="btn btn-main px-24 py-12">
                                <i class="ph ph-book-open me-2"></i>
                                Browse Courses
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- How to Earn Points Info -->
        <div class="col-12">
            <div class="card border-warning-100 bg-warning-50">
                <div class="card-body">
                    <div class="flex-align gap-16 mb-16">
                        <div class="w-48 h-48 bg-warning-600 rounded-circle flex-center">
                            <i class="ph-fill ph-lightbulb text-white text-2xl"></i>
                        </div>
                        <h5 class="mb-0 text-warning-900">How to Earn More Points</h5>
                    </div>
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <div class="flex-align gap-8">
                                <i class="ph-fill ph-check-circle text-success-600 text-xl"></i>
                                <span class="text-sm text-gray-700">
                                    Complete lessons to earn <strong class="text-main-600">5 points</strong> each
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="flex-align gap-8">
                                <i class="ph-fill ph-check-circle text-success-600 text-xl"></i>
                                <span class="text-sm text-gray-700">
                                    Pass quizzes to earn <strong class="text-success-600">10 points</strong> each
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="flex-align gap-8">
                                <i class="ph-fill ph-check-circle text-success-600 text-xl"></i>
                                <span class="text-sm text-gray-700">
                                    Complete courses to earn <strong class="text-warning-600">20 points</strong> each
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
