//common.js
import { debounce } from "lodash";
// import validationMessages from "./validation-messages";
import axios from "axios";

window.formValidationMessages = function (){
    $('.with-js-validation').each(function(){
                    
        var form = $(this);
        var rules = {}
        var messages = {}

        // Override default jQuery validator messages globally
        jQuery.extend(jQuery.validator.messages, window.validationMessages);

        $(this).find("input[data-inputautosearch-model]").each( function () {
            const model = $(this).data('inputautosearch-model')
            const column = $(this).data('inputautosearch-column')
            const columnReturn = $(this).data('inputautosearch-return')
            const reference = $(this).data('inputautosearch-reference')
            
            $(this).keyup(debounce(function(){
                    axios.post('/api/lookup-autosearch', {
                        name: columnReturn,
                        model: model,
                        column: column,
                        searchValue: $(this).val()
                    })
                    .then( response => {
                        if(reference){
                            $(`input[name=${reference}`).val(response.data.value)
                        }
                    })
                    .catch( error => {
                        console.log(error)
                    })
                }, 500)
            )
        })
        

        $(this).find("input[data-validate-date-format]").each( function () {
            rules[$(this).attr('name')] = {
                remote: {
                    url: "/api/validate-date-format",
                    type: "post",
                    data: {
                        reference: () => {
                            return $(this).attr("name");
                        },
                        format: () => {
                            return $(this).data('validate-date-format');
                        },
                        checkPastDate: () => {  
                            return  $(this).data('validate-past-date') || "false"; 
                        },
                        
                    }
                }
            }
        })

        $(this).find("input[data-validate-exist-model]").each( function () {
            rules[$(this).attr('name')] = {
                remote: {
                    url: "/api/validate-exists",
                    type: "post",
                    data: {
                        model: () => {
                            return $(this).data('validate-exist-model');
                        },
                        column: () => {
                            return $(this).data('validate-exist-column');
                        },
                        reference: () => {
                            return $(this).attr("name");
                        }
                    }
                }
            }
        })
        // for required バーコード情報は必須です
        $(this).find("input, select, textarea").each(function () {
            const name = $(this).attr('name');
            messages[name] = validationMessages
        });
        

        form.validate({
            rules: rules,
            messages: messages,
            errorElement : 'div',
            errorClass: "validation-error-message",
            // onfocusout: function(element) {
            //     replaceErrorMessage();
            // },
            // onfocusin: function(element) {
            //     replaceErrorMessage();
            // },
            submitHandler: function(form) {
                /**
                 * Handle form submission with a confirmation prompt.
                 * If a confirmation message is provided in the form's data attribute, 
                 * display a confirmation dialog before submitting the form.
                 * A submit overlay is shown during the submission process.
                 */
                const confirmationMessage = form.dataset?.confirmationMessage || null; // Get the confirmation message from the form's data attribute, if available
                const diableOverlay = form.dataset?.disabledOverlay || null; // remove overlay

                if (confirmationMessage) {
                    // Show the confirmation dialog to the user
                    if (confirm(confirmationMessage)) {
                        // If the user confirms, show the submit overlay and submit the form
                        $('.submit-overlay').css('display', "flex");
                        form.submit(); // Submit the form
                        if(diableOverlay) {
                            removeSubmitOverlay();
                        }
                    }
                    setTimeout(() => {
                        removeSubmitOverlay();
                    }, 300);
                } else {
                    form.submit(); // Submit the form
                }
            },
            errorPlacement: function(error, element) {
                var messageContainer = $(element).data("error-messsage-container");
                if(messageContainer){
                    var errorMessageContainer = $(messageContainer);
                    errorMessageContainer.html("")
                    error.appendTo( errorMessageContainer );
                }else{
                    var errorMessageContainer = $(`[data-error-container=${$(element).attr("name")}]`);
                    error.insertAfter( errorMessageContainer );
                }
            },
            invalidHandler: function(event, validator) {
                removeSubmitOverlay();

                // Scroll to first error
                if (validator.errorList.length) {
                    const scrollToElement = $(validator.errorList[0].element);
                    if (scrollToElement.length) {
                        $('html, body').animate({
                            scrollTop: 0 // scrollToElement.offset().top - 150
                        }, 300);
                    }
                }
            },
            showErrors: function(errorMap, errorList) {
                // Prepend label to required/remote messages
                errorList.forEach((errorObj) => {
                    const element = errorObj.element;
                    const fieldName = $(element).data("field-name");
                    const fieldNameAttr = $(element).attr("name");
                    const message = errorObj.message;

                    // Check if the error is for 'required' or 'remote'
                    const isRequiredOrRemote =
                        message.includes(validationMessages.required) ||
                        message.includes(validationMessages.remote);

                    if (fieldName && isRequiredOrRemote && !message.startsWith(fieldName)) {
                        errorObj.message = fieldName + message;
                    }
                });
            
                // Render the errors again
                this.defaultShowErrors();

                if (errorList.length) {
                    const scrollToElement = $(errorList[0].element);
                    if (scrollToElement.length) {
                        $('html, body').animate({
                            scrollTop: 0 // scrollToElement.offset().top - 150
                        }, 300);
                    }
                }

            }
        });
        // function replaceErrorMessage()
        // {
        //     form.find("input, select, textarea").each(function () {
        //         if ($(this).prop("required")) {
        //             var customErrorMessage = $(this).attr("data-custom-required-error-message");
        //             if(customErrorMessage) {
        //                 $(`#${$(this).attr("name")}-error`).remove()
        //                 $(`#${$(this).attr("name")}-error`).html(customErrorMessage)
        //             }
        //         }
        //     });
        // }
    })
}
formValidationMessages();

//20220920 shiota add. Labelに囲まれたradio,checkboxがcheckedの場合に、親のlabelにクラスつける処理
$(function () {
    $(document).on('change', 'label :checkbox, label :radio', function () {
        //チェック有無
        var checked = $(this).prop('checked');
        //labelにつけるクラス名
        var classname = "labelchecked";
        //親要素
        var $parent = $(this).parent();
        //親要素がlabelの場合のみ
        if ($parent.is('label')) {
            //チェック有無でクラス設定
            $parent.toggleClass(classname, checked);
            //ラジオの場合は他の要素のものはクラスを外す
            if ($(this).is(':radio')) {
                $others = $(':radio[name=' + $(this).prop('name') + ']').not($(this));
                $others.parent().removeClass(classname);
            }
        }
    });
}).trigger('change');

$(".overlayedSubmitForm").submit(function(e){
    const validator = $('.with-js-validation').validate()
    if(validator?.numberOfInvalids() > 0){
        removeSubmitOverlay();
    }else{
        if(!$(this).data('confirmation-message'))
            $('.submit-overlay').css('display', "flex");
    }
    
})

$(".with-overlay").click(function(e){
    $('.submit-overlay').css('display', "flex");
})

$('form[data-disregard-empty="true"]').submit(function (e) {
    $(this)
        .find('input[name]')
        .filter(function () {
            return !this.value;
        })
        .prop('name', '');
    
        $(this)
        .find('select[name]')
        .filter(function () {
            return !this.value;
        })
        .prop('name', '');

});

function removeSubmitOverlay(){
    $('.submit-overlay').css('display', "none");
}

//OKボタン押下時のコールバック
var okcallback = function ($modal) {
    return true;
};
/*モーダル*/
var scrollPosition, $modal;
$(function () {
    var scrollPosition;
    $(document).on('click', '.js-modal-open:not(.ajax)', function () {
        scrollPosition = $(window).scrollTop();
        let bodyFixedStyle = {}

        if ($(document).height() > $(window).height()) {
            bodyFixedStyle = {
                'top': -scrollPosition,
                'overflow-y': 'scroll'
            }
        } else {
            bodyFixedStyle = {
                'top': -scrollPosition
            }
        }
        $('body').addClass('fixed').css(bodyFixedStyle);

        var modaltarget = $(this).data('target');
        $modal = $('#' + modaltarget);
        $modal.find('.modal__content').show();
        $modal.fadeIn();
        return false;
    });
    //close
    $(document).on('click', '.js-modal-close,.js-modal-close.bColor-cancel', function () {
        $('body').removeClass('fixed').css({'top': 0});
        window.scrollTo(0, scrollPosition);
        //callback
        var res = true;
        if (typeof okcallback === 'function') {
            if ($(this).hasClass('bColor-ok'))
                res = okcallback($(this).closest('.modal.js-modal'));
        }
        if (res) {
            $(this).closest('.modal.js-modal').fadeOut();
            $(this).closest('.modal.js-modal').find('.modal__content').hide();
        }
    });

    $(document).on('click', '.js-btn-reset', function () {
        $(this).closest('form')[0].reset();
    });

    $(document).on('click', '.js-btn-reset-reload', function () {
        location.reload()
    });
});

//---- modal_ajax
//指定モーダルのmodalInner要素以下を、ajaxで指定したページの内容に置き換える。
$(function () {
    //listener
    //open
    $(document).on('click', '.js-modal-open.ajax', function () {
        //page(url)
        var url = $(this).data('url');
        //target modal
        var modaltarget = $(this).data('target');
        //modal
        $modal = $('#' + modaltarget);
        var $modalinner = $modal.find('.modalInner');
        //has modalInner
        if (url && $modalinner.length) {
            //postdata
            var postarr = $(this).data('post');
            //getdata
            var getstr = '';
            if ($(this).attr('data-get')) {
                //url query
                getstr = $(this).data('get').toString();
                //url format
                url = url.replace(/\?$/, '');
                url += (url.indexOf('?') == -1 ? '?' : '&') + getstr;
            }
            //modal open
            modal_open($modal, function () {
                //get page
                getPage(url, postarr).then(function (res) {
                    $modalinner.html(res);
                    // $modal.find('.js-modal-close').on('click', function(){
                    //     modal_close($(this));
                    // });
                });
            });
        }
    });
});
//モーダルが開くときの共通処理
var $modalloading, $modalcontent;

function modal_open($modal, callback) {
    //loading image
    $modalloading = $modal.find('.modal__bg > img');
    //content
    $modalcontent = $modal.find('.modal__content');
    //背景表示
    scrollPosition = $(window).scrollTop();
    $('body').addClass('fixed').css({'top': -scrollPosition});
    //ローディング表示
    $modalloading.show();
    //モーダルコンテンツ非表示
    $modalcontent.hide();
    //モーダル表示
    $modal.fadeIn()
    //同期処理
    $.when(
        (typeof callback === 'function') ? callback($modal) : function () {
            return false;
        } //コールバック実行
    ).then(
        $modalloading.hide(), //ローディング非表示
        $modalcontent.fadeIn() //モーダルコンテンツ表示
    );
}

//モーダルが閉じるときの共通処理
function modal_close($btn) {
    //背景非表示
    $('body').removeClass('fixed').css({'top': 0});
    window.scrollTo(0, scrollPosition);
    //callback
    var res = true;
    if (typeof okcallback === 'function') {
        if ($btn.hasClass('bColor-ok'))
            res = okcallback($btn.closest('.modal.js-modal'));
    }
    //モーダル非表示
    if (res)
        $btn.closest('.modal.js-modal').fadeOut();
}

//ページ取得
async function getPage(url, data) {
    var response = false;
    //ajax
    await $.ajax({
        cache: false,
        url: url,
        data: data,
        method: 'post',
        contextType: 'text/html',
    })
        .done(function (res) {
            response = {result: true, message: '', content: res};
        })
        .fail(function (request, status, error) {
            var msg = ['+status+'] + ' ' + request.status;
            if (error)
                msg += ' ' + error;
            var res = '<p class="error">' + msg + '</p>';
            response = {result: false, message: msg, content: res};
        })
        .always(function () {
            console.log('page[' + url + '] get -> ' + (response.result ? 'success' : 'failure ' + response.message));
        });
    return response.content;
}

//modal_ajax ----

//ソート矢印切り替え
$(function () {
    $('.list-sortbtn').click(function () {
        var $arrowobj = $(this);
        if ($arrowobj.hasClass('sortup')) {
            $arrowobj.removeClass('sortup');
            $arrowobj.addClass('sortdown');
        } else if ($arrowobj.hasClass('sortdown')) {
            $arrowobj.removeClass('sortdown');
        } else {
            $arrowobj.addClass('sortup');
        }
    });

    $(document).on('change', '.numberonly-box', function () {
        var hyphen = $(this).val();
        hyphen = hyphen.replace(/\D/g, '').replace(/\s/g, '').replace(/[０-９]/g, function (s) {
            return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
        });
        $(this).val(hyphen);
    });
});
//ショートカットボタンの開閉

$(function () {
    $('.hscOpenclose').click(function () {
        $(".headerShortcut").toggleClass('active');
//		$(".headerShortcut").slideToggle();
    });
});

//"hh:mm"→Date型へ変換
function getMillitimes(hm) {
    var arr = hm.split(':');
    if (arr.length != 2)
        return false;
    var d1 = new Date().setHours(arr[0], arr[1], 0, 0);
    var d2 = new Date().setHours(0, 0, 0, 0);
    return d1 - d2;
}

// drag and drop input file
$(function () {
    let uploadButton = document.getElementById("upload-button");
    let uploadButtonClass = document.querySelectorAll(".file-picker-custom");
    let container = document.querySelector(".containerDrag");
    let error = document.getElementById("error");
    let imageDisplay = document.getElementById("image-display");
    let inputUpload = document.getElementById("dropFiles");

    const fileHandler = (file, name, type, display = null, errorContainer = null) => {
        var acceptedMimeTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-powerpoint',
            'application/pdf',
            'image/x-citrix-jpeg',
            'image/gif',
            'image/jpeg',
            'image/x-png',
            'image/png'
        ];

        if(errorContainer){
            var error = errorContainer
        }else{
            var error = error;
        }
        
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
      
        if (!acceptedMimeTypes.includes(type)) {
            error.innerText = "「xlsx, xls, docx, doc, pptx, ppt, pdf, jpg, gif, png形式のファイルをアップロードしてください」";
            return false;
        }

        if(fileSize > 3){
            error.innerText = "「10MB以内で指定してください」";
            return false;
        }
        
        error.innerText = "";
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = () => {
            //image and file name
            let imageContainer = document.createElement("figure");
            imageContainer.innerHTML += `<div class="fileBox">${name}<span class="removeFile">x</span></div>`;
            if(display){
                display.appendChild(imageContainer);
            } else {
                imageDisplay.appendChild(imageContainer);
            }

        };
    };
    // click upload
    inputUpload && inputUpload.addEventListener("click", (ev) => {
        uploadButton.click(ev);
    });

    //Upload Button
    uploadButton && uploadButton.addEventListener("change", () => {
        imageDisplay.innerHTML = ""
        Array.from(uploadButton.files).forEach((file) => {
            fileHandler(file, file.name, file.type);
        });
    });
    //Upload Button
    uploadButtonClass.forEach(e => {
        e.addEventListener("change", () => {
            var display = getSibling(e, "image-display")
            display.innerHTML = ""
            var error = getSibling(e, "error")
            Array.from(e.files).forEach((file) => {
                fileHandler(file, file.name, file.type, display, error);
            });
        });
    })

    function getSibling(currObj, className){
        var parentofSelected = currObj.parentNode.parentNode; // gives the parent DIV
        var children = parentofSelected.childNodes;
        var myValue = null
        for (var i=0; i < children.length; i++) { 
            if (children[i].classList?.contains(className)) {
                myValue= children[i];
                break;
            }
        }
        return myValue // just to test
    } // end function
    



    container && container.addEventListener(
        "dragenter",
        (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.classList.add("active");
        },
        false
    );
    container && container.addEventListener(
        "dragleave",
        (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.classList.remove("active");
        },
        false
    );
    container && container.addEventListener(
        "dragover",
        (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.classList.add("active");
        },
        false
    );
    container && container.addEventListener(
        "drop",
        (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.classList.remove("active");
            let draggedData = e.dataTransfer;
            let files = draggedData.files;
            Array.from(files).forEach((file) => {
                fileHandler(file, file.name, file.type);
            });
        },
        false
    );
    window.onload = () => {
        if (error) {
            error.innerText = "";
        }
    };
})


$('#btn-populate-input-from-session').click(function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const source = $(this).data("source");
    $.ajax({
        url: source,
        type: 'GET',
        headers: {
          'X-CSRF-TOKEN': token
        },
        success: function(response) {
            populateInputFields(response);
            // populateSelectOptions(response);
        },
        error: function(xhr, status, error) {
          console.error('Error downloading product:', error);
        }
    });
})

function populateInputFields(response) {
    var responseData = response;

    for (var key in responseData) {
        if (responseData.hasOwnProperty(key)) {
            var value = responseData[key];
            $(`.input[name="${key}"`).prop('checked', false);
            $('input[value="' + value + '"]').prop('checked', true);

            if(key == "approval_method_category"){
                if(value == 2){
                    $("#approval-form-container").hide()
                }else{
                    $("#approval-form-container").show()
                }
            }   
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
    var selectOptions = document.getElementById("machine_division");

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


$('#clearButton').click(function () {
        // Clear input fields
    var inputFields = document.querySelectorAll('input[type="text"], input[type="number"], textarea');
    inputFields.forEach(function(input) {
        input.value = ''; 
    });

    // Reset select dropdowns
    var selectFields = document.querySelectorAll('select');
    selectFields.forEach(function(select) {
        select.selectedIndex = -1;  // Deselect any selected option (if you want to clear it entirely)
        // Or, if you want to reset to a specific default option:
        // select.selectedIndex = 0; // Resets to the first option
    });
});

document.addEventListener('DOMContentLoaded', () => {
    inputFormValiation();
});


window.inputFormValiation = function () {
    const inputFields = document.querySelectorAll('.acceptNumericOnly');

    inputFields.forEach(inputField => {
        // Clean non-numeric on input
        inputField.addEventListener('input', (event) => {
            let value = event.target.value.replace(/\D/g, '');

            if (inputField.dataset.acceptZero && value.startsWith('0') && value.length === 1) {
                value = ''; // Remove standalone 0 if needed
            }

            event.target.value = value;
        });

        // Allow pasting, but sanitize input
        inputField.addEventListener('paste', (event) => {
            event.preventDefault();
            const pasteData = (event.clipboardData || window.clipboardData).getData('text');
            const cleanData = pasteData.replace(/\D/g, '');
            event.target.value = cleanData;
            inputField.dispatchEvent(new Event('input')); // trigger input event for consistent behavior
        });

        // Optional: block keys, but allow navigation, control, etc.
        inputField.addEventListener('keydown', (event) => {
            const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'];
            const ctrlCommand = event.ctrlKey || event.metaKey;

            if (
                !/^[0-9]$/.test(event.key) &&
                !allowedKeys.includes(event.key) &&
                !ctrlCommand // allow Ctrl+V, Cmd+V, etc.
            ) {
                event.preventDefault();
            }
        });
    });

    // .acceptNineDigitTwoDecimal remains unchanged
    const decimalFields = document.querySelectorAll(".acceptNineDigitTwoDecimal");
    decimalFields.forEach((inputField) => {
        inputField.addEventListener("input", () => {
            let value = inputField.value;

            value = value.replace(/[^0-9.]/g, "");
            value = value.replace(/^(\d*\.)(.*)\./g, "$1$2");
            value = value.replace(/^(\d{9})\d+/g, "$1");
            value = value.replace(/(\.\d{2})\d+/g, "$1");

            inputField.value = value;
        });
    });

    // clearForm code below stays the same...
}


// Custom for lookup auto search
window.lookupAutoSearch = function(form){
    if (!form || !(form instanceof jQuery) || form.length === 0 || !form.is('form')) return;

    form.find("input[data-inputautosearch-model]").each( function () {
        const model = $(this).data('inputautosearch-model')
        const column = $(this).data('inputautosearch-column')
        const columnReturn = $(this).data('inputautosearch-return')
        const reference = $(this).data('inputautosearch-reference')
        
        $(this).keyup(debounce(function(){
                axios.post('/api/lookup-autosearch', {
                    name: columnReturn,
                    model: model,
                    column: column,
                    searchValue: $(this).val()
                })
                .then( response => {
                    if(reference){
                        $(`input[name=${reference}`).val(response.data.value)
                    }
                })
                .catch( error => {
                    console.log(error)
                })
            }, 500)
        )
    })
}

window.addDateValidationToRules = function (form) {
    if (!form || !(form instanceof jQuery) || form.length === 0 || !form.is('form')) return;

    form.find("input[data-validate-date-format]").each(function () {
        const name = $(this).attr('name');
        const format = $(this).data('validate-date-format');
        const checkPastDate = $(this).data('validate-past-date') || "false";
        
        // Dynamically adding remote validation rule for date format
        rules[name] = {
            remote: {
                url: "/api/validate-date-format",
                type: "post",
                data: {
                    reference: name,
                    format: format,
                    checkPastDate: checkPastDate
                }
            }
        };
    });
}

// Toggle password input
const togglePassword = document.getElementById('toggle-password');
const passwordInput = document.getElementById('password');

togglePassword?.addEventListener('click', function () {
    // Toggle the type of the password input
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;

    // Toggle the icon between 'show' and 'hide'
    togglePassword.innerHTML = type === 'password' ? '<i class="fas fa-eye-slash"></i>' : '<i class="fas fa-eye"></i>';
});

//Reload section
window.reloadSection = function(target) {
    // If no target is provided, use a default selector.
    if (!target) {
      return;
    }

    // Create an array of selectors:
    // If the target contains a comma, split by comma; otherwise, wrap it in an array.
    let selectors =
      target.indexOf(",") > -1
        ? target.split(",").map(function (sel) {
            return sel.trim();
          })
        : [target.trim()];

    // For each selector, load the content from the current URL for that specific section
    selectors.forEach(function (selector) {
      $(selector).load(window.location.href + " " + selector + " > *");
    });
  }