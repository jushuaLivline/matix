$(function () {
    (function (app) {
        app.init = function() {
            app.radio();
            app.getNameValue();
            app.copy();
            app.submit();
        };

        app.radio = function() {
            $("input[type='radio']").on("click", function () {
                let wasChecked = $(this).data("wasChecked") || false;
                if (wasChecked) {
                    $(this).prop("checked", false);
                    $(this).data("wasChecked", false);
                } else {
                    $(this).data("wasChecked", true);
                }
            });
        }

        app.getNameValue = function() {
            function fetchQueryName(query, get, model, value, name) {
                if (value) {
                    $.ajax({
                        url: '/outsource/defect/material/fetch-query-name',
                        type: 'POST',
                        data: {
                            query: query,
                            get: get,
                            model: model,
                            value: value,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        beforeSend: function(){
                            $("#"+name).val('');
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                $("#"+name).val(response.result);
                                console.log(response.message);
                            } else {
                                console.log(response.message);
                                $("#"+name).val('');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.log("Error:", xhr.status, error);
                            if (xhr.status === 404) {
                                console.log("Resource not found.");
                                $("#" + name).val('');
                            }
                        }
                    });
                } else {
                    $("[name='"+name+"']").val('');
                }
            }
        
            $('.fetchQueryName').on('input', function () {
                let query = $(this).data('query');
                let get = $(this).data('query-get');
                let model = $(this).data('model');
                let value = $(this).val();
                let name = $(this).data('reference');
                fetchQueryName(query, get, model, value, name);
            });
        }


        app.copy = function(){
            $("#copy-data").on("click",function(e){
                e.preventDefault();
                var form = $('#submit-kanban-form');
                $.ajax({
                    url: '/master/kanban/get_previous_input',
                    method: "GET",
                    typeType: "JSON",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
                    },
                    success: function(response){
                        console.log(response.kanban);
                        form.find("[name='management_no']").val(response.kanban.management_no);
                        form.find("[name='kanban_classification'][value='" + response.kanban.kanban_classification + "']").prop("checked", true);
                        form.find("[name='part_number']").val(response.kanban.part_number);
                        form.find("[name='product_name']").val(response.kanban.product?.product_name);
                        form.find("[name='process_code']").val(response.kanban.process_code);
                        form.find("[name='process_name']").val(response.kanban.process?.process_name);
                        form.find("[name='customer_acceptance']").val(response.kanban.customer_acceptance);
                        form.find("[name='next_process_code']").val(response.kanban.next_process_code);
                        form.find("[name='next_process_name']").val(response.kanban.next_process?.process_name);
                        form.find("[name='number_of_accomodated']").val(response.kanban.number_of_accomodated);
                        form.find("[name='box_type']").val(response.kanban.box_type);
                        form.find("[name='acceptance']").val(response.kanban.acceptance);
                        form.find("[name='printed_jersey_number']").val(response.kanban.printed_jersey_number);
                        form.find("[name='remark_1']").val(response.kanban.remark_1);
                        form.find("[name='remark_2']").val(response.kanban.remark_2);
                        form.find("[name='remark_qr_code']").val(response.kanban.remark_qr_code);
                        form.find("[name='issued_sequence_number']").val(response.kanban.issued_sequence_number);
                        form.find("[name='paid_category'][value='" + response.kanban.paid_category + "']").prop("checked", true);
                        form.find("[name='delete_flag'][value='" + response.kanban.delete_flag + "']").prop("checked", true);
                    },
                })
            });
        }

        app.submit = function(){
            $(document).on("click","#submit-add-update",function(e){
                e.preventDefault();
                if(!confirm("カンバンマスタを登録させていただきます。よろしいでしょうか？")) return false;
                form();
            })
            $(document).on("click","#update-delete",function(e){
                e.preventDefault();
                $('#delete_flag').prop("checked", true);
                form('delete');
            })
        }

        app.init();

        function form($action=null)
        {
            let form = $("#submit-kanban-form");
                let action = form.data('action');
                let formData = form.serializeArray();
                let method, url;

                if (action === "store") {
                    method = "POST";
                    url = "/master/kanban";
                } else {
                    // Assume action is the ID
                    url = `/master/kanban/${action}`;
                    method = $action === "delete" ? "DELETE" : "PUT";
                }

                if($action == 'delete') {
                    formData.push({ name: "delete_flag", value: 1 });
                }
                $.ajax({
                    url: url,
                    method: method,
                    data: form.serialize(),
                    dataType: "JSON",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
                    },
                    beforeSend: function(){
                        $(".error-message, #flash-message").remove();
                    },
                    success: function(response){
                        form.before("<div id='flash-message' style='background-color: #fff;'>" + response.message + "</div>");

                        // scroll to top
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);
                        

                         // Refresh a specific container 
                        $("form.overlayedSubmitForm").load(location.href + " form.overlayedSubmitForm > *");
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
        
                            Object.keys(errors).forEach(function(field) {
                                let errorMessage = errors[field][0];
                                // For radio buttons, manually target the wrapper
                                if (field === "kanban_classification" || field === "part_number" || field === "process_code" || field === "next_process_code") {
                                    $("[name='"+field+"'] .error-message").remove();
                                    $("[name='"+field+"']").closest('.col-10').append("<div class='error-message'>" + errorMessage + "</div>");
                                } else {
                                    let inputField = $("[name='" + field + "']");
                                    inputField.next(".error-message").remove();
                                    inputField.closest("div").append("<div class='error-message'>" + errorMessage + "</div>");
                                }

                                 // scroll to top page
                                $('html, body').animate({
                                    scrollTop: 0 
                                }, 300)
                            });
                        }
                    }
                });
        }
    })(window.App || (window.App = {}));
});
