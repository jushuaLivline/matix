$(function () {
    (function (app) {
        app.init = function() {
            app.reset();
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

        app.init();
    })(window.App || (window.App = {}));
});
