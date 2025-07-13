$(function () {
    var clickedPartNumber;

    function resetModalInputs() {
        const modalContent = document.getElementById('modalContent');
        const inputs = modalContent.querySelectorAll('input');

        console.log(inputs);

        inputs.forEach((input) => {
            if (input.id !== 'parent_part_number' && input.id !== 'product_name_selected' && input.id !== 'material_classification1' && input.id !== 'material_classification2') {
                input.value = ''; // Reset the value to an empty string
            }

        });
        $('#material_classification1').prop('checked', true);
    }

    function removeHighlightClass(selected) {
        $(".parentLabel, .childLabel").removeClass("highlight");
        clickedPartNumber = selected;

        // if (clickedPartNumber == 'child') {
        //     console.log('enabled')
        //     $(".btnAction.button-product.disabled.btn-edit").prop("disabled", false);
        //     $(".btnAction.button-product.disabled.btn-edit").removeClass('disabled');
        // } else {
        //     console.log('disabled')
        //     $(".btnAction.button-product.disabled.btn-edit").prop("disabled", true);
        //     $(".btnAction.button-product.disabled.btn-edit").addClass('disabled');
        // }
    }

    function toggleButtonState(enable) {
        var button = $(".btnAction.button-product.btn-edit");

        if (enable) {
            button.removeClass('disabled').prop('disabled', false);
        } else {
            button.addClass('disabled').prop('disabled', true);
        }
    }

    function parentToggleButtonState(enable) {
        var button = $(".btnAction.button-product.btn-new");

        if (enable) {
            button.removeClass('disabled').prop('disabled', false);
        } else {
            button.addClass('disabled').prop('disabled', true);
        }
    }

    $('#show_remark_1_modal').on('click', function () {
        resetModalInputs();
    });

    $('#show_second_conf_settings_modal').on('click', function () {
        resetModalInputs();
    });

    $(document).on('click', '.clear-button', function () {
        const resultValueEle = $(this).attr('data-result-value-element');
        const resultNameEle = $(this).attr('data-result-name-element');

        $('#' + resultValueEle).val('');
        $('#' + resultNameEle).val('');

        $('.js-modal-close-second').trigger('click');
    })

    $(document).on('click', '.searchResultSecond li:not(.disabled)', function () {
        const child_part_number = $('#child_part_number').value;
        const value = $(this).data('value');
        const resultValueEle = $(this).closest('.searchResultSecond').attr('data-result-value-element');
        const resultNameEle = $(this).closest('.searchResultSecond').attr('data-result-name-element');
        const text = $(this).data('name');
        

        $('#' + resultValueEle).val(value);
        $('#' + resultNameEle).val(text);

        // if (child_part_number != 'value') {
        //     $('input[name=delete_flag]').prop('checked', true);
        // } else {
        //     $('input[name=delete_flag]').prop('checked', false);
        // }

        $('.js-modal-close-second').trigger('click');
    })

    $('.btnSubmitCustomSecond').on('click',function () {
        var model = $('#' + $(this).attr('data-target')).find('#model').val()
        var hint = $('#' + $(this).attr('data-target')).find('#hint').val()
        var searchLabel = $('#' + $(this).attr('data-target')).find('#searchLabel').val()
        $.ajax({
            url: '/search',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                model: model,
                query: '',
                hint: hint
            },
            success: function(response) {
                $('.searchResultSecond').empty();

                // Iterate over the response and append list items
                $.each(response, function(index, value) {
                    var listItem = $('<li>').attr({
                        'data-value': value.code,
                        'data-name': value.name,
                    }
                        ).text('[' + value.code + ']' + value.name);
                    $('.searchResultSecond').append(listItem);
                });

                // Add the disabled list item
                var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
                $('.searchResultSecond').prepend(disabledListItem);
            },
            error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
            }
        });
    })

    $('input[name="keyword"]').on('keyup',function() {
        var model = $(this).closest('.searchModal').find('#model').val();
        var searchLabel = $(this).closest('.searchModal').find('#searchLabel').val();
        var searchQuery = $(this).val();
        var hint = $('#' + $(this).attr('data-target')).find('#hint').val()

        $.ajax({
            url: '/search',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                query: searchQuery,
                model: model,
                hint: hint
            },
            success: function(response) {
                $('.searchResultSecond').empty();

                // Iterate over the response and append list items
                $.each(response, function(index, value) {
                    var listItem = $('<li>').attr({
                        'data-value': value.code,
                        'data-name': value.name,
                    }).text('[' + value.code + ']' + value.name);
                    $('.searchResultSecond').append(listItem);
                });
                // Add the disabled list item
                var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
                $('.searchResultSecond').prepend(disabledListItem);
            },
            error: function(xhr, status, error) {
            // Handle errors
            console.log(error);
            }
        });
    });

    $(document).on('click', '.js-modal-close-first', function () {
        console.log('close first');

        $('body').removeClass('fixed').css({'top': 0});
        window.scrollTo(0, scrollPosition);

        //callback
        var res = true;
        if (typeof okcallback === 'function') {
            if ($(this).hasClass('bColor-ok'))
                res = okcallback($(this).closest('.modal.js-modal-first'));
        }

        if (res) {
            $(this).closest('.modal.js-modal-first').fadeOut();
            $(this).closest('.modal.js-modal-first').find('.modal__content').hide();
        }
    });

    var scrollPosition = $(window).scrollTop();
    $(document).on('click', '.second-conf-close,.js-modal-close-second,.js-modal-close-second.bColor-cancel,#back_', function () {
        console.log('close second');

        // resetModalInputs();

        $('body').removeClass('fixed').css({'top': 0});
        window.scrollTo(0, scrollPosition);

        //callback
        var res = true;
        if (typeof okcallback === 'function') {
            if ($(this).hasClass('bColor-ok'))
                res = okcallback($(this).closest('.modal.js-modal-second'));
        }

        if (res) {
            $(this).closest('.modal.js-modal-second').fadeOut();
            $(this).closest('.modal.js-modal-second').find('.modal__content').hide();
        }

        removeHighlightClass('child');
        toggleButtonState(false);
    });

    $('.js-btn-reset-reload-second').on('click',function () {
        resetModalInputs();
    });

    // Highlight parent label on click
    $(".parentLabel").on('click',function() {
        removeHighlightClass('parent');
        $(this).addClass("highlight");
        parentToggleButtonState(true);
        toggleButtonState(false);
    });

    // Highlight child label on click
    $(document).on("click", ".childLabel", function() {
        removeHighlightClass('child');
        $(this).addClass("highlight");
        toggleButtonState(true);
    });

    $(document).on('click', '#openConfigModal', function() {
        removeHighlightClass('child');
        toggleButtonState(false);
    });

    $('#btnNew').on('click',function() {
        $('#modalTitle').text('構成マスタメンテ：材料・構成部品を追加')
        resetModalInputs();
    });

    $(document).on('click', '#btn-edit', function() {
        console.log('edit show');
        var highlightedLabel = $(".parentLabel.highlight, .childLabel.highlight");

        var selectedID = highlightedLabel.attr("data-id");
        var selecteddNumberUsed = highlightedLabel.attr("data-numberUsed");
        var selectedMaterialClassification = highlightedLabel.attr("data-materialClassification");
        var selectedChildPartNumber = highlightedLabel.attr("data-childPartNumber");
        var selectedProductName = highlightedLabel.attr("data-productName");

        if (selectedID !== undefined) {
            console.log("Highlighted Value (data-id):", selectedID);
        } else {
            console.log("No label is highlighted.");
        }
        if (clickedPartNumber == 'child') {
            console.log('here');
            $('#orig_child_part_number').val(selectedChildPartNumber);

            $('#number_used').val(selecteddNumberUsed);
            $('#child_part_number').val(selectedChildPartNumber);
            $('#child_product_name').val(selectedProductName);
            $("input[name='material_classification'][value='" + selectedMaterialClassification + "']").attr("checked", "checked");

            $('#config_id').val(selectedID);
            $('#modalTitle').text('構成マスタメンテ：構成編集')
        }

    });

    $('#btn_saveConfiguration').on('click',function () {
        console.log('save conf');
        var form_data = $('#configuration-form-id').serialize();

        console.log(form_data)

        $.ajax({
            url: '/master/configuration/create',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: form_data,
            success: function(response) {
                var radioLabel = '';
                if (response.material_classification  == 1) {
                    radioLabel = '（材料）';
                } else {
                    radioLabel = '（構成部品）';
                }

                var newConfigurationHTML = `
                    <div class="item-configuration">
                        - <label type="text" class="childLabel no-outline"
                            data-id="${response.id}"
                            data-numberUsed="${response.number_used}"
                            data-materialClassification="${response.material_classification}"
                            data-childPartNumber="${response.child_part_number}"
                            data-productName="${response.child_product_name}">
                                ${response.child_part_number} ${response.child_product_name} ${radioLabel} X ${response.number_used}
                            </label>
                    </div>
                `;

                $('.childLabels').append(newConfigurationHTML);

                $('.second-conf-close').trigger('click');
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.log(error);
            }
        });
    });

    $('#btn_editChildConfiguration').on('click',function() {
        console.log('update conf');
        var form_data = $('#configuration-form-id').serialize();

        $.ajax({
            url: '/master/configuration/edit',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: form_data,
            success: function(updatedRecords) {
                var radioLabel = '';
                $('.childLabels').empty();

                updatedRecords.forEach(function(record) {
                    if (record.material_classification  == 1) {
                        radioLabel = '（材料）';
                    } else {
                        radioLabel = '（構成部品）';
                    }
                    var newConfigurationHTML = `
                        <div class="item-configuration">
                            - <label type="text" class="childLabel no-outline"
                                data-id="${record.id}"
                                data-numberUsed="${record.number_used}"
                                data-materialClassification="${record.material_classification}"
                                data-childPartNumber="${record.child_part_number}"
                                data-productName="${record.child_product_name}">
                                    ${record.child_part_number} ${record.child_product_name} ${radioLabel} X ${parseInt(record.number_used)}
                                </label>
                        </div>
                    `;
                    $('.childLabels').append(newConfigurationHTML);
                });

                $('.second-conf-close').trigger('click');
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.log(error);
            }
        });
    });

    $('#delte_child_config').on('click',function() {
        var conf = confirm("Are you sure you want to delete?");
        if(conf === true) {
            var form_data = $('#configuration-form-id').serialize();

            $.ajax({
                url: '/master/configuration/delete',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: form_data,
                success: function(updatedRecords) {
                    if (updatedRecords.length === 0) {
                        var btn = document.getElementById('btnNew');
                        btn.setAttribute('disabled', 'true');
                        btn.classList.add('disabled');
                    }

                    var radioLabel = '';
                    console.log('success soft delete');

                    $('.childLabels').empty();

                    updatedRecords.forEach(function(record) {
                        if (record.material_classification  == 1) {
                            radioLabel = '（材料）';
                        } else {
                            radioLabel = '（構成部品）';
                        }
                        var newConfigurationHTML = `
                            <div class="item-configuration">
                                - <label type="text" class="childLabel no-outline"
                                    data-id="${record.id}"
                                    data-numberUsed="${record.number_used}"
                                    data-materialClassification="${record.material_classification}"
                                    data-childPartNumber="${record.child_part_number}"
                                    data-productName="${record.child_product_name}">
                                        ${record.child_part_number} ${record.child_product_name}${radioLabel} X ${parseInt(record.number_used)}
                                    </label>
                            </div>
                        `;
                        $('.childLabels').append(newConfigurationHTML);
                    });

                    $('.second-conf-close').trigger('click');
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.log(error);
                }
            });
        }
    });

    $('#editInNewTab').on('click',function() {
        var baseUrl = window.location.origin;

        var id = $('#editInNewTab').attr('data-id');
        var url = baseUrl + "/master/products/"+id+"/edit";
        window.open(url, '_blank');
    });

    $('#openUnitPriceSettingModal').on('click',function() {
        $('#unitPriceSettingModal').removeClass('js-modal');
        $('#unitPriceSettingModal').addClass('js-modal-second');

        $('#btnModalClose').removeClass('js-modal-close');
        $('#btnModalClose').addClass('second-conf-close');

        $('#btnModalClose').off('click');
    });

    $('#openSequenceSettingModal').on('click',function() {
        $('#sequenceSettingModal').removeClass('js-modal');
        $('#sequenceSettingModal').addClass('js-modal-second');
        $('#sequenceSettingModal').css('z-index', '99999');


        $('#closeSequenceSettingModal').removeClass('js-modal-close');
        $('#closeSequenceSettingModal').addClass('second-conf-close');

        $('#closeSequenceSettingModal').off('click');
    });

    $('.js-modal-close-first').on('click',function() {
        location.reload();
    })

})