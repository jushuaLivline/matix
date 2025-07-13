$(function () {
    var base_url = window.location.origin;
    $(document).ready(function() {
        $('input:radio[name=product_category]:first').attr('checked', true);
        $('input:radio[name=instruction_class]:first').attr('checked', true);
        $('input:radio[name=production_division]:first').attr('checked', true);

        var part_number = $('#part_number').val();
        var part_number_editing_format = $('#part_number_editing_format').val();
        var positions = part_number_editing_format.split("").map(function(digit) {
            return parseInt(digit);
        });

        var formattedString = insertDashes(part_number, positions);
        $('#edited_part_number').val(formattedString);


        var customer_part_number = $('#customer_part_number').val();
        var customer_part_number_edit_format = $('#customer_part_number_edit_format').val();
        var positions_customer = customer_part_number_edit_format.split("").map(function(digit) {
            return parseInt(digit);
        });

        var formattedStringCustomer = insertDashes(customer_part_number, positions_customer);
        $('#customer_edited_product_number').val(formattedStringCustomer);
    })
    $('#part_number').change(function() {
        var positions = $('#part_number_editing_format').val().split("").map(function(digit) {
            return parseInt(digit);
        });

        var formattedString = insertDashes($(this).val(), positions);
        $('#edited_part_number').val(formattedString);
    });
    $('#part_number_editing_format').change(function() {
        var positions = $(this).val().split("").map(function(digit) {
            return parseInt(digit);
        });

        var part_number = $('#part_number').val();
        var formattedString = insertDashes(part_number, positions);
        $('#edited_part_number').val(formattedString);
    });

    $('#customer_part_number').change(function() {
        var positions = $('#customer_part_number_edit_format').val().split("").map(function(digit) {
            return parseInt(digit);
        });

        var formattedString = insertDashes($(this).val(), positions);
        $('#customer_edited_product_number').val(formattedString);
    });
    $('#customer_part_number_edit_format').change(function() {
        var positions = $(this).val().split("").map(function(digit) {
            return parseInt(digit);
        });

        var customer_part_number = $('#customer_part_number').val();
        var formattedString = insertDashes(customer_part_number, positions);
        $('#customer_edited_product_number').val(formattedString);
    });
    $(document).on('click', '.removeFile', function () {
        $(this).closest('figure').remove();
    })

    $('#createReqFrm').validate({
        rules: {
            part_number: {
                required: true,
                maxlength: 20
            },
            product_name: {
                required: true,
                maxlength: 40
            },
            name_abbreviation: {
                required: true,
                maxlength: 20
            },
            customer_code: {
                maxlength: 6
            },
            supplier_code: {
                maxlength: 6
            },
            department_code: {
                required: true,
                maxlength: 6
            },
            line_code: {
                maxlength: 6
            },
            standard: {
                maxlength: 6
            },
            material_manufacturer_code: {
                maxlength: 4
            },
            unit_code: {
                maxlength: 10
            },
            customer_part_number: {
                maxlength: 20
            },
            customer_part_number_edit_format: {
                maxlength: 5
            },
            customer_edited_product_number: {
                maxlength: 24
            },
            customer_edited_product_number: {
                maxlength: 24
            },

        },
        messages: {
            part_number: {
                required: '入力してください',
                maxlength: '得意先依頼内容の入力は20文字以内にしてください'
            },
            product_name: {
                required: '入力してください',
                maxlength: '得意先依頼内容の入力は40文字以内にしてください'
            },
            name_abbreviation: {
                required: '入力してください',
                maxlength: '得意先依頼内容の入力は20文字以内にしてください'
            },
            customer_code: {
                maxlength: '得意先依頼内容の入力は6文字以内にしてください'
            },
            supplier_code: {
                maxlength: '得意先依頼内容の入力は6文字以内にしてください'
            },
            department_code: {
                required: '入力してください',
                maxlength: '得意先依頼内容の入力は6文字以内にしてください'
            },
            line_code: {
                maxlength: '得意先依頼内容の入力は6文字以内にしてください'
            },
            standard: {
                maxlength: '得意先依頼内容の入力は6文字以内にしてください'
            },
            material_manufacturer_code: {
                maxlength: '得意先依頼内容の入力は4文字以内にしてください'
            },
            unit_code: {
                maxlength: '得意先依頼内容の入力は10文字以内にしてください'
            },
            customer_part_number: {
                maxlength: '得意先依頼内容の入力は20文字以内にしてください'
            },
            customer_part_number_edit_format: {
                maxlength: '得意先依頼内容の入力は5文字以内にしてください'
            },
            customer_edited_product_number: {
                maxlength: '得意先依頼内容の入力は24文字以内にしてください'
            },
            customer_edited_product_number: {
                maxlength: '得意先依頼内容の入力は24文字以内にしてください'
            },
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            $(element).closest('.row-field').find('.error_msg').html(error);
        },
        invalidHandler: function(event, validator) {
            $('.submit-overlay').css('display', "none");
        }
    })

    const productId = $('#product_id').val();

    $('#delete_product').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
              url: '/master/products/' + productId + '/delete',
              type: 'POST',
              headers: {
                'X-CSRF-TOKEN': token
              },
              success: function(response) {
                console.log('Product deleted successfully!');
                // Handle the response or perform any additional tasks
                window.location.href = '/master/products';
              },
              error: function(xhr, status, error) {
                console.error('Error deleting product:', error);
                // Handle the error
              }
            });
        }
    });

    $('#hard_delete_product').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        if (confirm('Are you sure you want to permanently delete this product?')) {
            $.ajax({
              url: '/master/products/' + productId + '/hard-delete',
              type: 'POST',
              headers: {
                'X-CSRF-TOKEN': token
              },
              success: function(response) {
                console.log('Product deleted successfully!');
                // Handle the response or perform any additional tasks
                window.location.href = '/master/products';
              },
              error: function(xhr, status, error) {
                console.error('Error deleting product:', error);
                // Handle the error
              }
            });
        }
    });

    $('#btn-copy').click(function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $.ajax({
            url: '/master/products/create/duplicate',
            type: 'POST',
            headers: {
              'X-CSRF-TOKEN': token
            },
            success: function(response) {
                populateInputFields(response);
                populateSelectOptions(response);
                //   selectCheckboxes(response);
                selectRadioButton(response);
            },
            error: function(xhr, status, error) {
              console.error('Error downloading product:', error);
            }
        });
    })
    function populateInputFields(response) {
        var responseData = response;

        delete responseData.part_number;
        delete responseData.edited_part_number;
        for (var key in responseData) {
            if (responseData.hasOwnProperty(key)) {
                var value = responseData[key];
                var inputField = document.getElementById(key);
                if (inputField) {
                    inputField.value = value;
                }
            }
        }
    }
    function populateSelectOptions(response) {
        // Assuming the response is already a JavaScript object (not JSON)
        var responseData = response;

        // Get the select element
        var selectOptions = document.getElementById("unit_code");

        var existingOptionIDs = Array.from(selectOptions.options).map(option => option.value);

        // Loop through the response data and select the matching option, if it exists
        for (var key in responseData) {
            if (responseData.hasOwnProperty(key)) {
                var value = responseData[key];
                if (existingOptionIDs.includes(key)) {
                    selectOptions.value = key; // Set the select element value to the matched ID
                }
            }
        }
    }

    // function selectCheckboxes(response) {
    //     // Assuming the response is already a JavaScript object (not JSON)
    //     var responseData = response;

    //     // Get the container to hold checkboxes
    //     var checkboxContainer = document.getElementById("checkboxContainer");

    //     // Loop through the checkboxes in the container and check/uncheck them based on the matched IDs
    //     for (var i = 0; i < checkboxContainer.children.length; i++) {
    //         var checkbox = checkboxContainer.children[i];
    //         if (checkbox.type === "checkbox" && responseData.hasOwnProperty(checkbox.value)) {
    //             checkbox.checked = true; // Check the checkbox if the ID is matched
    //         } else {
    //             checkbox.checked = false; // Uncheck the checkbox if the ID is not matched
    //         }
    //     }
    // }

    function selectRadioButton(response) {
        // Assuming the response is already a JavaScript object (not JSON)
        var responseData = response;

        var productionDivision = responseData.production_division;
        selectRadioByValue(productionDivision, "production_division");

        // Handle the second set of radio buttons
        var productCategory = responseData.product_category;
        selectRadioByValue(productCategory, "product_category");

        var instructionClass = responseData.product_category;
        selectRadioByValue(instructionClass, "instruction_class");
    }

    function selectRadioByValue(value, setName) {
        var radioButtons = document.getElementsByName(setName);

        // Loop through the radio buttons and check the one with the matched value
        for (var i = 0; i < radioButtons.length; i++) {
            var radioBtn = radioButtons[i];
            var radioBtnValue = radioBtn.value;

            if (radioBtnValue === value) {
                radioBtn.checked = true; // Check the radio button if the value is matched
                break; // Break after finding the matched value
            }
        }
    }

    function insertDashes(inputStr, positions) {
        var result = "";
        var dashIndex = 0;
        for (var i = 0; i < inputStr.length; i++) {
            result += inputStr[i];
            if (dashIndex < positions.length && (i + 1) === positions[dashIndex]) {
            result += "-";
            dashIndex++;
            }
        }

        return result;
    }

    $('#sequenceSetting').click(function() {
        $('#sequenceSettingModal').css('z-index', '');

        $('#sequenceSettingModal').addClass('js-modal');
        $('#sequenceSettingModal').removeClass('js-modal-second');


        $('#closeSequenceSettingModal').addClass('js-modal-close');
        $('#closeSequenceSettingModal').removeClass('second-conf-close');
    });
})