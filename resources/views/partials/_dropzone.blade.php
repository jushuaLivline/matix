@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .dz-size{
            display: none !important;
        }

        .dropzone .dz-preview .dz-image {
            height: 70px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        window.initializeDropzone = function(target=null, maxFile = null){
            console.log('dropzoneInit')
            Dropzone.autoDiscover = false;
            var targetElem = (target) ? target : 'div.dropzone';

            $(targetElem).dropzone({
                url: "/temporary-upload",
                addRemoveLinks: true,
                createImageThumbnails: false,
                dictDefaultMessage: "クリックまたはドロップしてファイルをアップロードしてください。",
                dictRemoveFile: "<span style='color:white; background-color:red; padding:3px 5px; border-radius: 50%; cursor:pointer'>X</span>",
                maxFilesize: 10,
                maxFiles: null,
                acceptedFiles: "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint,application/pdf,image/x-citrix-jpeg,image/gif,image/jpeg,image/jpg,image/x-png,image/png,",
                init: function() {
                    myDropzone = this;
                    if(maxFile){
                        myDropzone.on("addedfile", function (file) {
                            if (this.files.length > maxFile) {
                                this.removeFile(this.files[0]);
                            }
                        });
                    }
                   
                    $.ajax({
                        url: '/temporary-upload',
                        type: 'get',
                        data: {
                            '_token': '{!! csrf_token() !!}',
                            'form': '{{ url()->current() }}',
                            'user_id': '{{ Auth::user()->id }}',
                        },
                        dataType: 'json',
                        success: function(response) {
                            $.each(response, function(key, value) {
                                var mockFile = {
                                    name: value.name,
                                    size: value.size
                                };
                                myDropzone.emit("addedfile", mockFile);
                                myDropzone.emit("complete", mockFile);
                            });

                        }
                    });
                },
                removedfile: function(file) {
                    $.ajax({
                        url: "/temporary-upload/0",
                        type: 'DELETE',
                        success: function() {
                            //
                        },
                        data: {
                            file_name: file.name,
                            user_id: '{{ Auth::user()->id }}',
                            '_token': '{!! csrf_token() !!}',
                        },
                    })
                    file.previewElement.remove();
                },
                params: {
                    '_token': '{!! csrf_token() !!}',
                    'form': '{{ url()->current() }}',
                    'user_id': '{{ Auth::user()->id }}',
                }
            });

        }
        initializeDropzone();
    </script>
@endpush
