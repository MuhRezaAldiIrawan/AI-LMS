<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card mt-24">
            <div class="card-body">
                <h4 class="mb-12">Susun Kurikulum</h4>
                {{-- add Modules --}}
                <div class="card-section mt-24 p-3">
                    <div class="card-body">
                        <form id="createModuleForm">
                            @csrf
                            <small> Judul Modul Baru</small>
                            <div class="input-group mt-2">
                                <input type="text" name="title" class="form-control" placeholder="Judul Modul Baru">
                                <button class="btn btn-primary" type="submit">Tambah Modul</button>
                            </div>
                        </form>
                    </div>
                </div>


                {{-- Modules List --}}
                <div class=" mt-24 p-3">
                    <div class="">
                        <h5 class="mb-20">Modul List</h5>

                        <div class="row g-3" id="moduleContainer">
                            <!-- Card utama -->
                            <div class="col-12">

                                <div class="card card-section mt-24">
                                    <div class="card-body">
                                        <div class="pb-24 flex-between gap-4 flex-wrap">
                                            <h5 class="mb-12 fw-bold">Module 1</h5>
                                            <div class="flex-align gap-8">
                                                <a href="#" style="color: #3b82f6">Edit</a>
                                                <a href="#" style="color: #ef4444">Hapus</a>
                                            </div>
                                        </div>

                                        <ul class="comment-list">
                                            <li>
                                                <div class="d-flex align-items-start gap-8 flex-xs-row flex-column">
                                                    <img src="assets/images/thumbs/mentor-img1.png" alt=""
                                                        class="w-48 h-48 rounded-circle object-fit-cover flex-shrink-0">
                                                    <div class="">
                                                        <div class="flex-align flex-wrap gap-8">
                                                            <h6 class="text-15 fw-bold mb-0">Amir Hamja </h6>
                                                            <span
                                                                class="py-0 px-8 bg-main-50 text-main-600 rounded-4 text-15 fw-medium h5 mb-0 fw-bold">You</span>
                                                            <span class="text-gray-300 text-13">8:00PM </span>
                                                        </div>
                                                        <p class="text-15 text-gray-600 mt-8">Fringilla justo mauris
                                                            cursus arcu sit urna. Nullam eu non bibendum quam mi dolor
                                                            nisi orci?</p>
                                                    </div>
                                                </div>
                                                <ul class="sub-comment-list mt-24">
                                                    <li>
                                                        <div
                                                            class="d-flex align-items-start gap-8 flex-xs-row flex-column">
                                                            <img src="assets/images/thumbs/mentor-img2.png"
                                                                alt=""
                                                                class="w-48 h-48 rounded-circle object-fit-cover flex-shrink-0">
                                                            <div class="">
                                                                <div class="flex-align flex-wrap gap-8">
                                                                    <h6 class="text-15 fw-bold mb-0">Selina Eyvi</h6>
                                                                    <span
                                                                        class="py-0 px-8 bg-main-50 text-main-600 rounded-4 text-15 fw-medium h5 mb-0 fw-bold">Mentor</span>
                                                                    <span class="text-gray-300 text-13">8:10PM</span>
                                                                </div>
                                                                <p class="text-15 text-gray-600 mt-8">Justo gravida eget
                                                                    id massa volutpat. Volutpat vehicula tortor fusce
                                                                    sed pellentesque id sagittis eu commodo. Ut ultrices
                                                                    neque faucibus morbi rhoncus. Volutpat vehicula
                                                                    tortor fusce sed pellentesque id sagittis eu commodo
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>


                                        {{-- <div class="card card-section">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center"
                                            style="margin-bottom: 20px">
                                            <p class="mb-0">Modul 1: Dasar-dasar HTML</p>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-primary">Edit</button>
                                                <button class="btn btn-sm btn-danger">Hapus</button>
                                            </div>
                                        </div>

                                        <!-- Sub-card di dalam modul -->



                                    </div>
                                </div> --}}
                                    </div>

                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
