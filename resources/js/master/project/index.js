$(function () {
    (function (app) {
        app.init = function() {
            app.reset();
            app.submit();
        };

        app.reset = function() {
            $("[type='reset']").on("click", function(e){
                e.preventDefault();
                var form = $(this).closest('form');

                // Clear text inputs
                form.find("[type='text']").val('');

                form.find("select").each(function () {
                    $(this).prop("selectedIndex", 0);
                });
            });
        };

        app.submit = function() {
            $('[type="submit"]').on("click", function(e) {
                e.preventDefault();
        
                $("#search-form").validate({
                    rules: {
                        project_name: { required: true }
                    },
                    messages: {
                        project_name: { required: "プロジェクト名は必須です。" }
                    },
                    errorPlacement: function(error, element) {
                        if (element.attr("name") == "project_name") {
                            error.insertAfter(".search-group");
                        }
                    },
                    submitHandler: function(form) {
                        form.submit();
                    }
                });
        
                if ($("#search-form").valid()) {
                    $("#search-form").submit();
                }
            });
        };
        

        app.init();
    })(window.App || (window.App = {}));
});
