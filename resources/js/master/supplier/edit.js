
$(function () {
    (function (app) {
        app.init = function() {
            app.radio();
            app.phone_format();
            app.number_format();
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

        app.phone_format = function(){
            $('.phone-format').on('input', function() {
                let phoneValue = $(this).val().replace(/\D/g, '');

                if (phoneValue.length > 3 && phoneValue.length <= 7) {
                    phoneValue = phoneValue.replace(/^(\d{3})(\d{0,4})$/, '$1-$2');
                } else if (phoneValue.length > 7) {
                    phoneValue = phoneValue.replace(/^(\d{3})(\d{4})(\d{0,4})$/, '$1-$2-$3');
                }
        
                $(this).val(phoneValue);
            });           
        }

        app.number_format = function (){
            $('.number-format').on('input', function(e) {
                var cursorPos = this.selectionStart;
                
                var value = $(this).val();
                var rawValue = value.replace(/[^0-9.]/g, '');
                
                var formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        
                $(this).val(formattedValue);
                
                var diff = formattedValue.length - rawValue.length;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            });
        
            $('.number-format').each(function() {
                var value = $(this).val();
                var rawValue = value.replace(/[^0-9.]/g, '');
                var formattedValue = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                
                $(this).val(formattedValue);
            });
        }

        app.submit = function(){
            $("[type='submit']").on("click",function(e){
                e.preventDefault();
                let form = $("#submit-supplier-form");
                let action = form.data('action');
                let url = action == "store"
                    ? "/master/supplier"  // Create a new supplier
                    : `/master/supplier/${action}`;  // Update existing supplier

                let method = action == "store" ? "POST" : "PATCH"; 
                let message = action == "store"? "仕入先番号マスタを登録します、よろしいでしょうか？" : "仕入先番号マスタを更新します、よろしいでしょうか？";
                
                if(!confirm(message)) return false;

                $.ajax({
                    url: url,
                    method: method,
                    data: form.serialize(),
                    dataType: "JSON",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") 
                    },
                    beforeSend: function(){
                        $(".error-message").remove(); 
                    },
                    success: function(response){
                       // scroll to top
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);


                        form.before("<div id='flash-message' style='background-color: #fff;'>" + response.message + "</div>");
                

                        if(action == "store"){
                            resetInputs();
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
        
                            Object.keys(errors).forEach(function(field) {
                                let errorMessage = errors[field][0]; 
                                let inputField = $("[name='" + field + "']");
        
                                inputField.next(".error-message").remove();
                                inputField.closest("div").append("<div class='text-red-600 mt-1 error-message'>" + errorMessage + "</div>");

                                // scroll to top page
                                $('html, body').animate({
                                    scrollTop: 0 
                                }, 300)
                            });
                        }
                    }
                });
            })
        }

        app.init();

        function resetInputs()
        {
            $("input[type='text'], input[type='number'], input[type='email'], input[type='password'], textarea").val('');
            $("input[type='radio'], input[type='checkbox']").prop('checked', false);
            $("select").prop('selectedIndex', 0);
        }
    })(window.App || (window.App = {}));
});
