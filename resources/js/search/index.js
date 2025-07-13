$(function () {
    var currentURL = window.location.href.split('?')[0]
    
    $(document).on('click', '.searchResult li:not(.disabled)', function () {
        const value = $(this).data('value');
        let resultValueEle = $(this).closest('.searchResult').attr('data-result-value-element');
        let resultNameEle = $(this).closest('.searchResult').attr('data-result-name-element');
        const text = $(this).data('name');
        const pathname = window.location.pathname
        const errorMessageIfExist = $(`#${resultValueEle}-error`);
        const searchInputField = $(`#${resultValueEle}`);


        // Prefix 'resultValueEle' with 'update-' if '.search--result-modal-employee' exists and has 'isEditForm'.
        // Applies to the Approval route settings modal on the Purchase Requisition page.
        const searchResultFormClass = $('.search--result-modal-employee');
        if (searchResultFormClass.length && searchResultFormClass.hasClass('isEditForm')) {
            resultValueEle = `update-${resultValueEle}`;
        }

        $(`#${resultValueEle}`).val(value).trigger('input')
        $(`#${resultNameEle}`).val(text)
        $(`#${resultNameEle}`).text(text)
        
        if(errorMessageIfExist && searchInputField) {
            errorMessageIfExist.remove();
            searchInputField.removeClass('validation-error-message');
            searchInputField.removeAttr('data-gtm-form-interact-field-id aria-invalid aria-describedby data-gtm-form-interact-field-id');
        }

        localStorage.setItem(`${pathname}:${resultNameEle}`, JSON
            .stringify({ [value]: text }))

        if (typeof $(this).data('query') !== 'undefined' && typeof $(this).data('reference') !== 'undefined') {
            if (typeof(Storage) !== "undefined") {
                sessionStorage.removeItem(currentURL + '-modal-search-item')
                sessionStorage.removeItem(currentURL + '-modal-search-reference')
                sessionStorage.removeItem(currentURL + '-modal-search-value')
                sessionStorage.setItem(currentURL + '-modal-search-item', $(this).data('query'));
                sessionStorage.setItem(currentURL + '-modal-search-reference', $(this).data('reference'));
                sessionStorage.setItem(currentURL + '-modal-search-value', value);
            }
        }
        
        $(this).parents('.modal__content').find('.js-modal-close').trigger('click');

        if (typeof populateNumberOfAccomodated === 'function') {
            populateNumberOfAccomodated();
        }
        
        if (typeof populateMaterialDefect === 'function') {
            populateMaterialDefect();
        }
        // PURCHASE 69
        const errorMessages = $('.employee_error_message');
        const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
        lastErrorMessageElement.text("")

    })
    $(document).on('click', '.btnSubmitCustom', function(){
        $('.searchResult').empty();
        var model = $('#' + $(this).attr('data-target')).find('#model').val()
        var searchLabel = $('#' + $(this).attr('data-target')).find('#searchLabel').val()
        var queryData = $(this).data('query')
        var referenceData = $(this).data('reference')
        var query = ""
        var url = "/search"
        var additionalData = ""
        var hint = $('#' + $(this).attr('data-target')).find('#hint').val()
        var hintData = $(this).data('hint'); // enable product_category=0 in searchProductNumber method in SearchController
        var queryByField = $(this).data('query-field'); // add addition field to search on query

        if (typeof(Storage) !== "undefined") {
            var modalSearchItem = sessionStorage.getItem(currentURL + '-modal-search-item')
            var modalSearchReference = sessionStorage.getItem(currentURL + '-modal-search-reference')
            var modalSearchValue = sessionStorage.getItem(currentURL + '-modal-search-value')
            if(modalSearchItem == $(this).attr('data-target')){
                additionalData =` ${modalSearchReference}=${modalSearchValue}`
            }
        }

        

        $.ajax({
            url: "/search",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                model: model,
                query: '',
                'additional-data': additionalData,
                'hint': hint, // added hint data for query narrowing in search
                ...(hintData && {hint: hintData}), // Conditionally add the 'hint' property to the object if 'hintData' is truthy
                ...(queryByField && {queryByField: queryByField}) // Conditionally add the 'queryByField' property to the object if 'queryByField' is truthy
            },
            success: function(response) {
                // $('.searchResult').empty();

                // Iterate over the response and append list items
                // $.each(response, function(index, value) {
                //     var listItem = $('<li>').attr('data-value', value.code).text(value.name);
                //     $('.searchResult').append(listItem);
                // });
                var listItems = [];  // Create an array to store list items

                $.each(response, function(index, value) {

                    if (referenceData && queryData) {
                    
                        if (typeof(Storage) !== "undefined") {
                            sessionStorage.removeItem(currentURL + '-modal-search-item')
                            sessionStorage.removeItem(currentURL + '-modal-search-reference')
                            sessionStorage.removeItem(currentURL + '-modal-search-value')
                        }

                        var listItem = $('<li>')
                                        .attr({
                                            'data-value': value.code,
                                            'data-name': value.name,
                                            'data-query': queryData,
                                            'data-reference': referenceData
                                        })
                                        .text('[' + value.code + ']' + value.name);
                    }else {
                        var listItem = $('<li>')
                                        .attr({
                                            'data-value': value.code,
                                            'data-name': value.name,
                                            'data-query': queryData,
                                            'data-reference': referenceData
                                        })
                                        .text('[' + value.code + ']' + value.name);
                    }
                    
                    listItems.push(listItem);  // Add list item to the array
                    
                });

                // Append all list items to the DOM at once
                $('.searchResult').append(listItems);

                // Add the disabled list item
                var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
                $('.searchResult').prepend(disabledListItem);
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.log(error);
            }
        });
    })
    $('input[name="keyword"]').on('keyup', _.debounce(function () {
        var model = $(this).closest('.searchModal').find('#model').val();
        var searchLabel = $(this).closest('.searchModal').find('#searchLabel').val();
        var searchQuery = $(this).val();

        var queryData = $(this).closest('.searchModal').find('#query').val()
        var referenceData = $(this).closest('.searchModal').find('#reference').val()
        var additionalData = "";
        var hintData = $(this).data('hint'); // enable product_category=0 in searchProductNumber method in SearchController
        var queryByField = $(this).data('query-field'); // add addition field to search on query

        if (typeof(Storage) !== "undefined") {
            var modalSearchItem = sessionStorage.getItem(currentURL + '-modal-search-item')
            var modalSearchReference = sessionStorage.getItem(currentURL + '-modal-search-reference')
            var modalSearchValue = sessionStorage.getItem(currentURL + '-modal-search-value')
            if(modalSearchItem == $(this).attr('data-target')){
                additionalData =` ${modalSearchReference}=${modalSearchValue}`
            }
        }


        $.ajax({
            url: '/search',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                query: searchQuery,
                model: model,
                'additional-data': additionalData,
                ...(hintData && {hint: hintData}), // Conditionally add the 'hint' property to the object if 'hintData' is truthy
                ...(queryByField && {queryByField: queryByField}) // Conditionally add the 'queryByField' property to the object if 'queryByField' is truthy
            },
            success: function(response) {
                $('.searchResult').empty();

                // Iterate over the response and append list items
                $.each(response, function(index, value) {
                    // var listItem = $('<li>').attr('data-value', value.code).text(value.name);
                    if (referenceData && queryData) {
                    
                        if (typeof(Storage) !== "undefined") {
                            sessionStorage.removeItem(currentURL + '-modal-search-item')
                            sessionStorage.removeItem(currentURL + '-modal-search-reference')
                            sessionStorage.removeItem(currentURL + '-modal-search-value')
                        }

                        var listItem = $('<li>')
                                        .attr({
                                            'data-value': value.code,
                                            'data-name': value.name,
                                            'data-query': queryData,
                                            'data-reference': referenceData
                                        })
                                        .text('[' + value.code + ']' + value.name);
                    }else {
                        var listItem = $('<li>')
                                        .attr({
                                            'data-value': value.code,
                                            'data-name': value.name,
                                            'data-query': queryData,
                                            'data-reference': referenceData
                                        })
                                        .text('[' + value.code + ']' + value.name);
                    }

                    $('.searchResult').append(listItem);
                });
                // Add the disabled list item
                var disabledListItem = $('<li>').addClass('disabled').text(searchLabel);
                $('.searchResult').prepend(disabledListItem);
            },
            error: function(xhr, status, error) {
              // Handle errors
              console.log(error);
            }
        });
    }, 300)); // Debounce time: 300ms


    $(document).on('click', '.clear-button', function () {
        const resultValueEle = $(this).attr('data-result-value-element');
        const resultNameEle = $(this).attr('data-result-name-element');

        $('#' + resultValueEle).val('');
        $('#' + resultNameEle).val('');

        // $('.js-modal-close').trigger('click');
        $(this).parents('.modal__content').find('.js-modal-close').trigger('click');
    })
})
