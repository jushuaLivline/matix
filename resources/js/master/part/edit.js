$(function () {
    (function (app) {
        app.init = function() {
            app.getNameValue();
            app.formatNumber();
            app.radio();
            app.submit();
        };

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
        
            $('.fetchQueryName').on('input change', function () {
                let query = $(this).data('query');
                let get = $(this).data('query-get');
                let model = $(this).data('model');
                let value = $(this).val();
                let name = $(this).data('reference');
                fetchQueryName(query, get, model, value, name);
            });
        }

        app.formatNumber = function(){
            $(".format-number").on("input change", function(e) {
                e.preventDefault();
                var $currentInput = $(this);
                var formatTarget = $currentInput.data("format");
            
                var number = $("#" + formatTarget).val();
            
                var format = $currentInput.val();
                var formattedNumber = number;
            
                var formatParts = format.split("");
            
                var startIndex = 0;
                formatParts.forEach(function(part) {
                    var segmentLength = parseInt(part);
                    if (segmentLength && startIndex < number.length) {
                        formattedNumber = formattedNumber.substring(0, startIndex + segmentLength) + "-" + formattedNumber.substring(startIndex + segmentLength);
                        startIndex += segmentLength + 1; 
                    }
                });
            
                // Remove the trailing dash if present
                if (formattedNumber.endsWith("-")) {
                    formattedNumber = formattedNumber.slice(0, -1);
                }
            
                // Find the next input after the current one and set the formatted value
                $currentInput.siblings('input[type="text"][readonly]').val(formattedNumber);
            });
            
        }

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

        app.submit = function(){
            $(document).on("click",".btn-register-product-number",function(e){
                e.preventDefault();
                form();
            })
            $(document).on("click",".btn-delete-action",function(e){
                e.preventDefault();
                form('delete');
            })
        }

        app.init();

        function form($action=null)
        {
            let form = $("#edit-form");
            let action = form.data('action');
            let formData = form.serializeArray();
            let method, url;

            if (action === "store") {
                method = "POST";
                url = "/master/part";

                if(!confirm("品番マスタを登録させていただきます。よろしいでしょうか？")) return false;
            } else {
                // Assume action is the ID
                url = `/master/part/${action}`;
                method = $action === "delete" ? "DELETE" : "PUT";

                if(!confirm("品番マスタ情報を更新します、よろしいでしょうか？")) return false;
            }

            
            if($action == 'delete') {
                formData.push({ name: "delete_flag", value: "1" });
            }
            
            $.ajax({
                url: url,
                method: method,
                data: $.param(formData),
                dataType: "JSON",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
                },
                beforeSend: function(){
                    $(".error-message").remove(); 
                },
                success: function(response){
                    form.find("#flash-message").remove();
                    form.before("<div id='flash-message' class='text-left'>" + response.message + "</div>");
            
                     // scroll to top
                     $('html, body').animate({
                        scrollTop: 0
                    }, 500);

                    if(action == "store"){
                        resetInputs();
                    }

                    // Refresh a specific container 
                    $("form.overlayedSubmitForm").load(location.href + " form.overlayedSubmitForm > *");
                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
    
                        Object.keys(errors).forEach(function(field) {
                            let errorMessage = errors[field][0]; 
                            let inputField = $("[name='" + field + "']");
    
                            inputField.next(".error-message").remove();
                            inputField.closest("div").append("<div class='text-red-600 mt-1 error-message'>" + errorMessage + "</div>");
                        });
                        // scroll to top page
                        $('html, body').animate({
                            scrollTop: 0 
                        }, 300)
                    }
                }
            });
        }
        function resetInputs()
        {
            $("input[type='text'], input[type='number'], input[type='email'], input[type='password'], textarea").val('');
            $("input[type='radio'], input[type='checkbox']").prop('checked', false);
            $("select").prop('selectedIndex', 0);
        }
    })(window.App || (window.App = {}));
});
