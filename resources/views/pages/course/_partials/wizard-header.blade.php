@php
    // Props: $activeStep (details|module|quiz|participants|publish), $title (string), optional $course
    $title = $title ?? 'Create Course';
    $activeStep = $activeStep ?? 'details';

    $steps = [
        'details' => 'Course Details',
        'module' => 'Create Module & Quiz', // Curriculum step (module + quiz)
        'participants' => 'Assign Participants',
        'publish' => 'Publish Course',
    ];

    $order = array_keys($steps);
    $activeIndex = array_search($activeStep, $order, true);
@endphp

<div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
    <!-- Breadcrumb Start -->
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li>
                <a href="{{ url('/') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a>
            </li>
            <li>
                <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span>
            </li>
            <li><span class="text-main-600 fw-normal text-15">{{ $title }}</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <!-- Buttons Start -->
    <div class="flex-align justify-content-end gap-8">
        <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9" id="btnSaveDraft" {{ empty($course?->id) ? 'disabled' : '' }}>Save as Draft</button>
        <button type="button" class="btn btn-main rounded-pill py-9" id="btnPublishCourse" {{ empty($course?->id) ? 'disabled' : '' }}>Publish Course</button>
    </div>
    <!-- Buttons End -->
</div>

<!-- Create Course Step List Start -->
<ul class="step-list mb-24" id="courseWizardSteps" data-active-step="{{ $activeStep }}">
    @foreach($steps as $key => $label)
        @php
            $index = array_search($key, $order, true);
            $classes = '';
            if ($index < $activeIndex) $classes = 'done';
            elseif ($index === $activeIndex) $classes = 'active';
        @endphp
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6 {{ $classes }}">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span>
            {{ $label }}
            <span class="line position-relative"></span>
        </li>
    @endforeach
    </ul>
<!-- Create Course Step List End -->

@push('js')
<script>
    // Optional client-side helpers for Draft/Publish actions (no-ops if routes not wired yet)
    (function(){
        const btnDraft = document.getElementById('btnSaveDraft');
        const btnPublish = document.getElementById('btnPublishCourse');
        if(btnDraft){
            btnDraft.addEventListener('click', function(){
                // Placeholder: hook your save-draft route here
                window.dispatchEvent(new CustomEvent('course:saveDraft'));
            });
        }
        if(btnPublish){
            btnPublish.addEventListener('click', function(){
                // Placeholder: hook your publish route here
                window.dispatchEvent(new CustomEvent('course:publish'));
            });
        }
    })();

    // Helper to set active step dynamically (e.g., when switching tabs)
    window.setCourseWizardStep = function(stepKey){
        const el = document.getElementById('courseWizardSteps');
        if(!el) return;
        const order = ['details','module','participants','publish'];
        const activeIdx = order.indexOf(stepKey);
        if(activeIdx === -1) return;
        // update classes
        [...el.children].forEach((li, idx) => {
            li.classList.remove('active','done');
            if(idx < activeIdx) li.classList.add('done');
            else if(idx === activeIdx) li.classList.add('active');
        });
        el.dataset.activeStep = stepKey;
    }
</script>
@endpush
