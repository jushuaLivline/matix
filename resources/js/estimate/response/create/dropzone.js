$(function () {

  window.dropzoneInt = function (
    target = ".dropzone",
    maxFile = 1,
    getExistingFiles = typeof existingFiles !== "undefined" ? existingFiles : []
  ) {
    console.log("Initializing Dropzone on:", target);
    Dropzone.autoDiscover = false;

    const submitButton = document.querySelector(".submit-button");
    let targetElems = document.querySelectorAll(target);
    if (!targetElems.length) {
      console.error("Dropzone target not found:", target);
      return;
    }

    targetElems.forEach((targetElem, index) => {
      const file = getExistingFiles[index]; // get matching file for this dropzone

      if (targetElem.dropzone) {
        targetElem.dropzone.destroy(); // Destroy Dropzone only on the specific element
      }

      let myDropzone = new Dropzone(targetElem, {
        url: "/estimate/response/store_temp_file", // Ensure this is a valid route
        addRemoveLinks: true,
        createImageThumbnails: false,
        dictDefaultMessage: "クリックまたはドロップしてファイルをアップロードしてください。",
        dictRemoveFile:
          "<span style='color:white; background-color:red; padding:3px 5px; border-radius: 50%; cursor:pointer'>X</span>",
        maxFilesize: 10, // 10MB max file size
        maxFiles: maxFile, // Limit number of files
        paramName: "file", // Must match the backend request field name
        dictFileTooBig: "10MB 以内のファイルをアップロードしてください",
        dictInvalidFileType:
          "無効なファイル形式です。許可されている形式: xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png",
        acceptedFiles:
          "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint,application/pdf,image/gif,image/jpeg,image/png",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
        },
        init: function () {
          console.log("Dropzone initialized on:", target);

          const dz = this;

          if (file && file.name && file.path) {
            let mockFile = {
              name: file.name,
              size: file.size,
              serverId: file.name,
            };

            const isImage = /\.(jpg|jpeg|png|gif)$/i.test(file.name);

            dz.emit("addedfile", mockFile);
            // dz.emit(
            //   "thumbnail",
            //   mockFile,
            //   isImage ? file.path : "/images/icons/file-icon.png"
            // );

            if (isImage) {
              // Let Dropzone handle thumbnail generation from the URL
              mockFile.previewElement.querySelector("img").src = file.path;
            }
           
            dz.emit("complete", mockFile);

            // Add hidden input if not already present
            if (
              !document.querySelector(
                `input[name="existing_files[]"][value="${file.name}"]`
              )
            ) {
              const hiddenInput = document.createElement("input");
              hiddenInput.type = "hidden";
              hiddenInput.name = "existing_files[]";
              hiddenInput.value = file.name;
              hiddenInput.setAttribute("data-file-name", file.name);
              document.getElementById("hiddenInputs").appendChild(hiddenInput);
            }
          }

          dz.on("addedfile", function (file) {
            if (this.files.length > maxFile) {
              this.removeFile(this.files[0]);
            }
          });
        },
        success: function (file, response) {
          targetElem.nextElementSibling.textContent = "";
          if (!response.name) {
            alert("Error uploading file!");
            this.removeFile(file);
            return;
          }

          // **Store uploaded file name**
          const hiddenInput = document.createElement("input");
          hiddenInput.type = "hidden";
          hiddenInput.name = "uploaded_files[]";
          hiddenInput.value = response.name;
          hiddenInput.setAttribute("data-file-name", file.name);
          hiddenInput.setAttribute("required", true);

          document.getElementById("hiddenInputs").appendChild(hiddenInput);

          if (submitButton) {
            toggleSubmitButton(submitButton, false);
          }
        },
        error: function (file, message) {
          targetElem.nextElementSibling.textContent = message;
          this.removeFile(file);

          if (submitButton) {
            toggleSubmitButton(submitButton, true);
          }
        },
        removedfile: function (file) {
          const fileName = file.name;

          // **Check if it's a new file**
          let newFile = document.querySelector(
            `input[name="uploaded_files[]"][value="${fileName}"]`
          );
          if (newFile) {
            newFile.remove();
          }

          // **Check if it's an existing file**
          let existingFile = document.querySelector(
            `input[name="existing_files[]"][value="${fileName}"]`
          );
          if (existingFile) {
            fetch("/estimate/response/remove_temp_file", {
              method: "POST",
              headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]')
                  .value,
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ filename: fileName }),
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  console.log("File deleted:", fileName);
                  existingFile.remove();
                } else {
                  console.error("Failed to delete file:", data.message);
                }
              })
              .catch((error) => console.error("Error:", error));
          }

          file.previewElement.remove();

          if (submitButton && this.files.length === 0) {
            toggleSubmitButton(submitButton, false);
          }
        },
      });
    });
  };



  function toggleSubmitButton(button, isDisabled) {
    button.disabled = isDisabled;
    button.classList.toggle("btn-disabled", isDisabled);
    console.log(isDisabled ? "Button disabled" : "Button enabled");
}
});
