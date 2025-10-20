<div class="col-xxl-3 col-lg-4 col-sm-6">
    <div class="mentor-card rounded-8 card border border-gray-100">
        <div class="card-body p-8">
            @php
                // Mode belajar pakai flag global dari view parent (karyawan atau pengajar-learnerMode)
                $learnerMode = isset($learnerMode) && $learnerMode;
                // Hanya pemilik (owner) yang boleh mengelola; admin non-owner hanya bisa lihat overview
                $canManage = !$learnerMode && auth()->check() && auth()->id() === $course->user_id;
                // Direct owner to Course Details tab explicitly
                $manageUrl = route('course.show', $course->id) . '#informasi-umum';
                // In learner mode, force view as learner with query param (no #overview)
                $overviewUrl = $learnerMode
                    ? route('course.show', $course->id) . '?mode=learn'
                    : route('course.show', $course->id) . '#overview';
            @endphp
            <a href="{{ $canManage ? $manageUrl : $overviewUrl }}"
                class="bg-main-100 rounded-8 overflow-hidden text-center mb-8 h-164 flex-center p-8">
                <img src="{{ asset('storage' . '/' . $course->thumbnail) }}" alt="Course Image"
                    class="w-100 h-100 object-fit-cover" style="border-radius: 8px">
            </a>
            <div class="p-8">
                <span
                    class="text-13 py-2 px-10 rounded-pill bg-success-50 text-success-600 mb-16">{{ $course->category->name }}</span>
                <h5 class="mb-0">
                    <a href="{{ $canManage ? $manageUrl : $overviewUrl }}" class="hover-text-main-600 text-truncate d-inline-block"
                        style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $course->title }}
                    </a>
                </h5>


                <div class="flex-align gap-8 flex-wrap mt-16">
                    <img src="{{ asset('storage' . '/' . $course->author->profile_photo_path) }}"
                        class="w-32 h-32 rounded-circle object-fit-cover" alt="User Image">
                    <div>
                        <span class="text-gray-600 text-13">{{ $course->author->name }}</span>
                        <div class="flex-align gap-4">
                            <small class="text-gray-500 text-10">Instructor</small>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400  flex flex-wrap items-center gap-x-6 gap-y-3"
                    style="display: flex; justify-content: space-between; margin-top: 15px">
                    <!-- Lesson -->
                    <span class="flex items-center gap-2" title="Pelajaran">
                        <svg class="w-15 h-15 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span style="font-size: 13px;">{{ $course->modules->flatMap->lessons->count() }} Lesson</span>
                    </span>

                    <!-- Quiz -->
                    <span class="flex items-center gap-2" title="Kuis">
                        <svg class="w-15 h-15 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span style="font-size: 13px">{{ $course->modules->whereNotNull('quiz')->count() }} Quiz</span>
                    </span>

                    <!-- Participant -->
                    <span class="flex items-center gap-2" title="Peserta">
                        <svg class="w-15 h-15 text-emerald-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2" />
                        </svg>
                        <span style="font-size: 13px">{{ $course->enrolledUsers->count() }} Participant</span>
                    </span>
                </div>

                <a href="{{ $canManage ? $manageUrl : $overviewUrl }}"
                    class="btn btn-outline-main rounded-pill py-9 w-100 mt-24">
                    {{ $canManage ? 'Kelola Kursus' : 'Lihat Kursus' }}
                </a>
            </div>
        </div>
    </div>
</div>
