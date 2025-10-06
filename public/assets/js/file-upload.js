(function ($) {
    var fileUploadCount = 0;

    $.fn.fileUpload = function () {
        return this.each(function () {
            var fileUploadDiv = $(this);
            var fileUploadId = `fileUpload-${++fileUploadCount}`;
            var inputName =
                fileUploadDiv.attr("name") || `fileUpload_${fileUploadCount}`;
            var oldPreview = fileUploadDiv.data("preview"); // ✅ ambil data-preview dari Blade

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

            function handleFiles(files) {
                if (files.length > 0) {
                    var file = files[0];
                    var fileType = file.type;

                    var preview = fileType.startsWith("image")
                        ? `<img src="${URL.createObjectURL(file)}" alt="${file.name
                        }" class="image-upload__image">`
                        : `<span class="image-upload__anotherFileIcon"><i class="fas fa-file"></i></span>`;

                    renderPreview(preview);
                }
            }

            function renderPreview(previewHTML) {
                var fileUploadLabel = fileUploadDiv.find("label.file-upload");
                fileUploadLabel.find(".image-upload__boxInner").html(`
                    ${previewHTML}
                    <button type="button" class="image-upload__deleteBtn"><i class="ph ph-x"></i></button>
                `);

                fileUploadLabel
                    .find(".image-upload__deleteBtn")
                    .click(function () {
                        fileUploadDiv.html(fileDivContent);
                        initializeFileUpload();
                    });
            }

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

                fileUploadDiv.find(`input[type="file"]`).change(function () {
                    handleFiles(this.files);
                });

                // ✅ tampilkan preview lama jika ada
                if (oldPreview) {
                    renderPreview(
                        `<img src="${oldPreview}" alt="Old Photo" class="image-upload__image">`
                    );
                }
            }

            initializeFileUpload();
        });
    };
})(jQuery);

$(".fileUpload").fileUpload();
