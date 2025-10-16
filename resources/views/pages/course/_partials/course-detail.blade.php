<div class="card mt-24">
<div class="card-header border-bottom">
    <h4 class="mb-4">Detail Kursus</h4>
</div>
<div class="card-body">
    <form id="editCourseForm" enctype="multipart/form-data">
        @csrf
        <div class="row g-20">
            <input type="hidden" name="courseid" id="courseid" value="{{ $course->id ?? '' }}">

            <div class="col-sm-12">
                <label for="title" class="h7 mb-8 fw-semibold font-heading">Judul Kursus</label>
                <div class="position-relative">
                    <input type="text" name="title" id="title" class="form-control py-9"
                        value="{{ old('title', $course->title ?? '') }}">
                </div>
            </div>

            <div class="col-sm-12">
                <label for="category_id" class="h7 mb-8 fw-semibold font-heading">Kategori</label>
                <div class="position-relative">
                    <select name="category_id" id="category_id" class="form-select py-9">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ $category->id == $course->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="course_type_id" class="h7 mb-8 fw-semibold font-heading">Tipe Kursus</label>
                <div class="position-relative">
                    <select name="course_type_id" id="course_type_id" class="form-select py-9">
                        <option value="">Pilih Tipe Kursus</option>
                        @foreach ($courseType as $type)
                            <option value="{{ $type->id }}"
                                {{ $type->id == $course->course_type_id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="summary" class="h7 mb-8 fw-semibold font-heading">About Course (Ringkasan)</label>
                <div class="position-relative">
                    <textarea name="summary" id="summary" cols="30" rows="4" class="form-control py-9" placeholder="Tuliskan ringkasan singkat tentang kursus ini...">{{ old('summary', $course->summary ?? '') }}</textarea>

                </div>
            </div>

            <div class="col-sm-12">
                <label for="description" class="h7 mb-8 fw-semibold font-heading">Deskripsi</label>
                <div class="position-relative">
                    <textarea name="description" id="description" cols="30" rows="4" class="form-control py-9" placeholder="Tuliskan Deskripsi tentang kursus ini...">{{ old('description', $course->description ?? '') }}</textarea>
                </div>
            </div>

            <div class="col-sm-12">
                <label for="thumbnail" class="h7 mb-8 fw-semibold font-heading">Thumbnail</label>
                <div id="fileUpload" class="fileUpload image-upload" name="thumbnail"
                    data-preview="{{ !empty($course->thumbnail) ? asset('storage/' . $course->thumbnail) : '' }}">
                </div>
            </div>

        </div>

        <div class="flex-align justify-content-end gap-8 mt-16">
            <a href="{{ route('course') }}" class="btn btn-outline-main rounded-pill py-9">Batal</a>
            <button class="btn btn-main rounded-pill py-9" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</div>
</div>
