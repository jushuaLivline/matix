$(function () {
    (function (app) {
        app.init = function() {
            app.reset();
            app.check();
            app.buttonState();
        };

        app.reset = function() {
            $(document).ready(function () {
                $("[type='reset']").on("click", function(e){
                    e.preventDefault();
                    var form = $(this).closest('form');

                    form.find("[type='text']").val('');
                })
            });
        };

        app.check = function(){
            $('#checkAll').on("click",function(){
                if (this.checked) {
                    $(".checkboxes").prop("checked", true);
                } else {
                    $(".checkboxes").prop("checked", false);
                }	
            });

            $("#unapprove-button").on('click', function () {
                let confirmation = confirm("現在選択されている購買依頼申請の承認を取り消します、よろしいでしょうか？");
                if (!confirmation) {
                    return;
                }
                $("input[name=approval_type]").val("unapprove");
                $("#approval-form").submit();
            });
        
            $("#approve-button").on('click', function () {
                var confirmation = confirm("現在選択されている購買依頼申請を承認します、よろしいでしょうか？");
                if (!confirmation) {
                    return;
                }
                console.log('clicked')
        
                $("input[name=approval_type]").val("approve");
                $("#approval-form").submit();
            });
        }

        app.buttonState = function(){
            function updateButtonStates() {
                const $checkedBoxes = $('.checkboxes:checked');
                const $approveButton = $('#approve-button');
                const $unapproveButton = $('#unapprove-button');
            
                if ($checkedBoxes.length === 0) {
                    $approveButton.prop('disabled', true).addClass('btn-disabled');
                    if ($unapproveButton.length) {
                        $unapproveButton.prop('disabled', true).addClass('btn-disabled');
                    }
                } else {
                    $approveButton.prop('disabled', false).removeClass('btn-disabled');
                    if ($unapproveButton.length) {
                        $unapproveButton.prop('disabled', false).removeClass('btn-disabled');
                    }
                }
            }

            updateButtonStates();

            // 個別のチェックボックスの変更を監視
            $('.checkboxes').on('change', updateButtonStates);

            // 全選択チェックボックスの変更を監視
            $('#checkAll').on('change', updateButtonStates);
        }

        app.init();
    })(window.App || (window.App = {}));
});
