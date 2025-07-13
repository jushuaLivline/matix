$(function () {
    (function (app) {
        app.init = function() {
            app.submit();
        };

        app.submit = function(){
            $("#submit-add-update").on("click",function(e){
                e.preventDefault();
                let form = $("#submit-kanban-form");
                let action = form.data('action');
                let url = action == "store"
                    ? "/master/project"
                    : `/master/project/${action}`;

                let method = action == "store" ? "POST" : "PATCH"; 

                let message = action == "store"? "プロジェクト番号マスタを登録します。よろしいでしょうか？" : "プロジェクトマスタ情報を更新します、よろしいでしょうか？";
                
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
                        $(".error-message, #flash-message").remove();
                    },
                    success: function(response){
                        form.find("#flash-message").remove();
                        form.before("<div id='flash-message' style='background-color: #fff;'>" + response.message + "</div>");
                        
                       // scroll to top
                        $('html, body').animate({
                            scrollTop: 0
                        }, 500);

                        if(action == "store")
                            resetInputs();

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
