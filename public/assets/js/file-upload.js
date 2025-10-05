// **********************************
// Modified: Auto-assign input name from parent div attributes
// **********************************
(function ($) {
    var fileUploadCount = 0;

    $.fn.fileUpload = function () {
        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;

            // Ambil atribut name dari elemen utama (jika ada)
            var inputName =
                fileUploadDiv.attr("name") || `fileUpload_${fileUploadCount}`;

            // Buat HTML upload box
            var fileDivContent = `
                <label for="${fileUploadId}" class="file-upload image-upload__box">
                    <div class="image-upload__boxInner">
                        <i class="ph ph-image mb-8 image-upload__icon"></i>
                        <h5 class="mb-4">Drag or <span class="text-main-600"> Browse</span></h5>
                        <p class="text-13">PNG, JPEG (max 5mb size)</p>
                    </div>
                    <input type="file" id="${fileUploadId}" name="${inputName}" hidden />
                </label>
            `;

            fileUploadDiv.html(fileDivContent).addClass("file-container");

            // Fungsi untuk menangani file
            function handleFiles(files) {
                if (files.length > 0) {
                    var file = files[0];

                    var fileName = file.name;
                    var fileSize = (file.size / 1024).toFixed(2) + " KB";
                    var fileType = file.type;
                    var preview = fileType.startsWith("image")
                        ? `<img src="${URL.createObjectURL(
                              file
                          )}" alt="${fileName}" class="image-upload__image" height="30">`
                        : `<span class="image-upload__anotherFileIcon"><i class="fas fa-file"></i></span>`;

                    var fileUploadLabel =
                        fileUploadDiv.find(`label.file-upload`);
                    fileUploadLabel.find(".image-upload__boxInner").html(`
                        ${preview}
                        <button type="button" class="image-upload__deleteBtn"><i class="ph ph-x"></i></button>
                    `);

                    // Tombol hapus file
                    fileUploadLabel
                        .find(".image-upload__deleteBtn")
                        .click(function () {
                            fileUploadDiv.html(fileDivContent);
                            initializeFileUpload();
                        });
                }
            }

            // Inisialisasi event drag/drop dan file change
            function initializeFileUpload() {
                fileUploadDiv.on({
                    dragover: function (e) {
                        e.preventDefault();
                        fileUploadDiv.toggleClass(
                            "dragover",
                            e.type === "dragover"
                        );
                    },
                    drop: function (e) {
                        e.preventDefault();
                        fileUploadDiv.removeClass("dragover");
                        handleFiles(e.originalEvent.dataTransfer.files);
                    },
                });

                fileUploadDiv
                    .find(`label.file-upload input[type="file"]`)
                    .change(function () {
                        handleFiles(this.files);
                    });
            }

            initializeFileUpload();
        });
    };
})(jQuery);

// Jalankan plugin di semua elemen dengan class "fileUpload"
$(".fileUpload").fileUpload();
