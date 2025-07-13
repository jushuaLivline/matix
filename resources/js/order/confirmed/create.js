$(function () {
    (function (app) {
        app.init = function() {
            app.reset();
            app.radio();
            app.getNameValue();
            app.deleteRow();
            app.register();
        };

        app.reset = function() {
            $(document).on("click","[type='reset']",function(e){
                e.preventDefault();
                var form = $(this).closest('form');

                // Clear text inputs
                form.find("[type='text']").val('');

                // Uncheck radio buttons
                form.find("[type='radio']").prop('checked', false);
            })
        };

        app.radio = function() {
            $(document).on("click","input[type='radio'][name='classification']", function(){
                if ($(this).prop("checked")) {
                    if ($(this).data("wasChecked")) {
                        $(this).prop("checked", false);
                        $(this).data("wasChecked", false);
                    } else {
                        $("input[type='radio'][name='classification']").data("wasChecked", false);
                        $(this).data("wasChecked", true);
                    }
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

        app.deleteRow = function(){
            $(document).on("click", ".delete-btn", function(e){
                e.preventDefault();
                let confirmation = confirm("確定受注情報を削除します、よろしいでしょうか？");
                if (confirmation) {
                    var id = $(this).closest("tr").data("tr-id");
                    $.ajax({
                        url: "/order/confirmed/delete",
                        type: "DELETE",
                        data: {
                            id: id,
                            _token: $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                alert(response.message);
                                let tr = $("tr[data-tr-id='" + id + "']");
                                tr.fadeOut(500, function() { 
                                    $(this).remove(); 
                                });
                            } else {
                                alert("削除に失敗しました。");
                            }
                        },
                        error: function() {
                            alert("削除に失敗しました。");
                        }
                    });
                }
                });
        }

        app.register = function(){
            $(document).on("click", "#register", function(e){
                e.preventDefault();
        
                let orders = [];
                let tableRows = $(".list-table tbody tr");

                if (tableRows.length === 0) {
                    alert("テーブルにレコードが見つかりません");
                    return;
                }

                // Show confirmation prompt
                if (!confirm("確定受注情報を登録します、よろしいでしょうか？")) {
                    return;
                }

                tableRows.each(function(){
                    let tr = $(this);
                    let trId = tr.data("tr-id"); // Get data-tr-id

                    let numberOfAccommodatedInput = tr.find("[name='number-of-accommodated']");
                    let numberOfAccommodated = numberOfAccommodatedInput.length && numberOfAccommodatedInput.val() 
                        ? numberOfAccommodatedInput.val().trim() 
                        : "0";

                    let kanbanNumberInput = tr.find("[name='kanban-number']");
                    let kanbanNumber = kanbanNumberInput.length && kanbanNumberInput.val() 
                        ? kanbanNumberInput.val().trim() 
                        : (tr.find(".kanban-number").text().trim() || "0");
        
                    orders.push({
                        id: trId,
                        number_of_accommodated: numberOfAccommodated,
                        kanban_number: kanbanNumber
                    });
                });
        
                $.ajax({
                    url: "/order/confirmed/bulk-register",
                    type: "POST",
                    data: {
                        orders: orders,
                        _token: $('meta[name="csrf-token"]').attr("content")
                    },
                    dataType: "JSON",
                    success: function(response) {
                        if (response.status == "success") {
                            $("#firm-order-form").before(
                                "<div id='flash-message'>" + 
                                    response.message + 
                                "</div>"
                            );
                    

                            $(".list-table").find("input").each(function(){
                                if (!$(this).val().trim()) {
                                    $(this).val("0");
                                }
                            });

                            // scroll to top
                            $('html, body').animate({
                                scrollTop: 0
                            }, 500);
                        } else {
                            alert("一括データを登録できません");
                        }
                    },
                    error: function() {
                        alert("一括データを登録できません");
                    }
                });
            });          
        }

        app.init();
    })(window.App || (window.App = {}));
});
