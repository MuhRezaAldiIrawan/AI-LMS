@extends('layouts.app')

@section('content')

        <div class="dashboard-body">

            <div class="breadcrumb-with-buttons mb-24 flex-between flex-wrap gap-8">
                <!-- Breadcrumb Start -->
<div class="breadcrumb mb-24">
    <ul class="flex-align gap-4">
        <li><a href="index.html" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
        <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
        <li><span class="text-main-600 fw-normal text-15">Create Quiz</span></li>
    </ul>
</div>
<!-- Breadcrumb End -->

                <!-- Buttons Start -->
<div class="flex-align justify-content-end gap-8">
    <button type="button" class="btn btn-outline-main bg-main-100 border-main-100 text-main-600 rounded-pill py-9">Save as Draft</button>
    <button type="button" class="btn btn-main rounded-pill py-9" >Publish Course</button>
</div>
<!-- Buttons End -->

            </div>

                <!-- Create Course Step List Start -->
    <ul class="step-list mb-24">
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6 done">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span> 
            Course Details
            <span class="line position-relative"></span>
        </li>
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6 done">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span> 
            Create Module
            <span class="line position-relative"></span>
        </li>
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6 active ">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span> 
            Create Quiz
            <span class="line position-relative"></span>
        </li>
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6  ">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span> 
            Assign Participants
            <span class="line position-relative"></span>
        </li>
        <li class="step-list__item py-15 px-24 text-15 text-heading fw-medium flex-center gap-6  ">
            <span class="icon text-xl d-flex"><i class="ph ph-circle"></i></span> 
            Publish Course
            <span class="line position-relative"></span>
        </li>
    </ul>
    <!-- Create Course Step List End -->
            
            <!-- Course Tab Start -->
            <div class="card">
                <div class="card-header border-bottom border-gray-100 flex-between flex-wrap gap-8">
                    <div class="flex-align gap-8 flex-wrap">
                        <h5 class="mb-0">Quiz Questions</h5>        
                        <button type="button" class="text-main-600 text-md d-flex" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Quiz Questions">
                            <i class="ph-fill ph-question"></i>
                        </button>
                    </div>
                    <!-- Button trigger modal -->
                    <div class="flex-align gap-8 flex-wrap">
                        <button type="button" class="text-white border border-main-600 bg-main-600 rounded-pill py-8 px-20" data-bs-toggle="modal" data-bs-target="#exampleModalOne">
                        AI Generate Quiz
                    </button>
                    <button type="button" class=" border border-gray-100  rounded-pill py-8 px-20">
                        Simpan
                    </button>
                    </div>
                    
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModalOne" tabindex="-1" aria-labelledby="exampleModalLabelOne" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="exampleModalLabelOne">Add Question</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="question" class="h6 mb-8 fw-semibold">Jumlah Soal</label>
                                    <input type="text" class="form-control" placeholder="Jumlah Soal Quiz">
                                    <label for="question" class="mt-8 h6 mb-8 fw-semibold">Level Quiz</label>
                                    <div class="select-has-ico">
                                        <select class="form-control form-select rounded-8 border border-main-200 py-19">
                                            <option value="1" selected disabled>Select Level</option>
                                            <option value="2">Mudah</option>
                                            <option value="2">Sedang</option>
                                            <option value="2">Sulit</option>
                                        </select>
                            </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary py-9" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-main py-9">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card-body">
                    <form action="#">
                            <div class="row g-2 align-items-center mb-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="select-has-ico">
                                        <select class="form-control form-select rounded-8 border border-main-200 py-15">
                                            <option value="1" selected disabled>Select Module</option>
                                            <option value="2">Module - Title Module 1</option>
                                            <option value="3">Module - Title Module 2</option>
                                            <option value="4">Module - Title Module 3</option>
                                            <option value="5">Module - Title Module 4</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-2">
                                    <input type="number" class="form-control rounded-8 border border-main-200 py-15" 
                                        placeholder="Durasi (menit)" min="1">
                                </div>
                            </div>

                        <div class="mb-20 mt-8">
                            <label for="question" class="h6 mb-8 fw-semibold">Question</label>
                            <input type="text" class="form-control fw-medium text-15" id="question" value="1. Which discipline heavily influences the foundation of web design?" placeholder="Add question">
                        </div>
                        <div class="mb-20">
                            <label class="h6 mb-8 fw-semibold">Multiple Choices</label>
                            <div class="row g-20">
                                <div class="col-sm-6">
                                    <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                        <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">A</span>
                                        <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi A..." value="Web layout design">
                                        <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                        <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">B</span>
                                        <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi B..." value="Content Strategy">
                                        <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                        <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">c</span>
                                        <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi B..." value="Content Strategy">
                                        <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                        <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">d</span>
                                        <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi D..." value="Design Idea">
                                        <span class="text-gray-500"></span>
                                        <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-20">
                            <label for="question" class="h6 mb-8 fw-semibold">Answer</label>
                            <div class="select-has-ico">
                                <select class="form-control form-select rounded-8 bg-gray-50 border border-main-200 py-19">
                                    <option value="1" selected disabled>Select correct answer</option>
                                    <option value="2">Web laout idesign</option>
                                    <option value="2">Content Strategy</option>
                                    <option value="2">User Friendly</option>
                                    <option value="2">Design Idea</option>
                                </select>
                            </div>
                        </div>

                        <!-- Button trigger modal -->
                            <button type="button" class="text-main-600 mt-16 text-15 fw-medium" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Add Question
                            </button>
                            
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Add Questions</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="text" class="form-control" placeholder="Add Quiz Question">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary py-9" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-main py-9">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                   
                    <div class="flex-align justify-content-end gap-8">
                        <a href="{{ route('course.module') }}" class="btn btn-outline-main rounded-pill py-9">Back</a>
                        <a href="{{ route('course.student') }}" class="btn btn-main rounded-pill py-9">Continue</a>
                    </div>
                </div>
            </div>
            
            <div class="card  mt-24">
                <div class="card-body p-0">
                    <div class="card-header  ">
                        <div class="course-item">
                            <button type="button" class="course-item__button flex-align gap-4 w-100 p-16 text-start text-15 fw-medium">
                                <span class="d-block text-start">
                                    <span class="d-block h5 mb-0 text-line-1">
                                        Modul 1 : Modul Title Test
                                    </span>
                                    <span class="d-block text-15 text-gray-300">Quiz | 10 Menit</span>
                                </span>
                                <span class="course-item__arrow ms-auto text-20 text-gray-500"><i class="ph ph-arrow-right"></i></span>
                            </button>
                            <div class="course-item-dropdown border-top border-gray-100">
                                <div class="mb-20 mt-8">
                                <label for="question" class="h6 mb-8 fw-semibold">Question</label>
                                <input type="text" class="form-control fw-medium text-15" id="question" value="1. Which discipline heavily influences the foundation of web design?" placeholder="Add question">
                            </div>
                            <div class="mb-20">
                                <label class="h6 mb-8 fw-semibold">Multiple Choices</label>
                                <div class="row g-20">
                                    <div class="col-sm-6">
                                        <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                            <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">A</span>
                                            <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi A..." value="Web layout design">
                                            <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                            <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">B</span>
                                            <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi B..." value="Content Strategy">
                                            <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                            <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">c</span>
                                            <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi B..." value="Content Strategy">
                                            <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="delete-item py-15 px-16 rounded-8 bg-gray-50 border border-main-200 flex-align gap-8">
                                            <span class="w-24 h-24 bg-white rounded-circle flex-center text-capitalize text-14">d</span>
                                            <input type="text" class="form-control border-0 bg-transparent text-gray-700" placeholder="Tulis opsi D..." value="Design Idea">
                                            <span class="text-gray-500"></span>
                                            <button type="button" class="delete-btn text-danger-600 text-xl ms-auto d-flex"><i class="ph-fill ph-x-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-20">
                                <label for="question" class="h6 mb-8 fw-semibold">Answer</label>
                                <div class="select-has-ico">
                                    <select class="form-control form-select rounded-8 bg-gray-50 border border-main-200 py-19">
                                        <option value="1" selected disabled>Select correct answer</option>
                                        <option value="2">Web laout idesign</option>
                                        <option value="2">Content Strategy</option>
                                        <option value="2">User Friendly</option>
                                        <option value="2">Design Idea</option>
                                    </select>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
@endsection