<!-- resources/views/partials/components/product-material-hierarchy-modal.blade.php -->
<div id="{{ $modalId }}" class="modal product-material-hierarchy-modal modal__bg js-modal" style="z-index: 100;">
    <div class="modal__content meduim-modal" style="max-width: 470px !important;">
        <div class="modal-header">
            <h5 class="modalTitle" style="background-color: #a8a8a897 !important; width: 450px !important">構成検索</h5>
            <button type="button" class="modalCloseBtn js-modal-close" aria-label="Close">x</button>
        </div>
        <div class="modal-body">
            <div class="boxModal mb-1" style="margin-top: 30px;">
                <div class="mr-0">
                    <form id="product-material-form">
                        <div class="row-group-content mb-2">
                            <!-- 品番 -->
                            <div class="flex-row gx-2">
                                <label for="part_number" class="label_for">品番</label>
                                <div class="search-group">
                                    <input type="text" 
                                        id="part_number" 
                                        name="part_number" 
                                        value="" 
                                        class="searchOnInput Product" 
                                        style="width: 140px">

                                    <input type="text" 
                                        readonly
                                        id="product_name" 
                                        name="product_name" 
                                        value="" 
                                        class="middle-name" 
                                        style="width: 190px">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductNumberModal"
                                            data-querys="searchProductNumberModal"
                                            data-references="product_name"
                                            data-api-product-material-hierarchy-disabled>
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="clearForm()">クリア</button>
                                    <button type="button" class="btn btn-primary btn-sm" id="search-button">検索</button>
                            </div>
                            <div class="col-md-6 text-end mb-4" style="margin-top: -35px;">
                                <button type="button" class="btn btn-success btn-sm"  id="export-excel">Excel出力</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-scroll p-2">
                <ul id="tree-container" class="tree-view"></ul>
            </div>
        </div>
    </div>
</div>

@php
    $dataConfigs['Product'] = [
        'model' => 'ProductMaterial',
        'reference' => 'product_name'
    ];
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@push('styles')
<style>
    /* Modal size and positioning */
    .medium-modal {
        max-width: 500px; /* Adjust width as needed for medium size */
    }

    .modal-body {
        padding: 1rem;
    }
    .button-group {
        display: flex;  
        justify-content: center; 
        gap: 10px;
        margin: 0 auto;
        width: 100%;

    }

    /* Styling for the buttons */
    .button-group .btn {
        max-width: 100px;
        width: 100%;
    }

    .container-scroll {
        max-height: 55vh;
        overflow-y: auto;
        border: 2px solid gray;
        border-radius: 8px;
        background-color: #fff;
        padding: 5px !important;
    }
    .tree-view {
        list-style-type: none;
        padding-left: 0;
        margin-left: 0;
        border-radius: 8px;
        transition: background-color 0.3s, padding 0.3s;
    }

    .tree-node {
        margin-left: 20px;
        position: relative;
        padding-left: 18px; 
    }

    .tree-node .btn-toggle {
        cursor: pointer;
        background-color: #007bff;
        border: none;
        color: white;
        width: 17px;
        height: 17px; 
        display: inline-block;
        padding: 0;
        font-size: 1em;
        text-align: center;
        line-height: 1;
        margin-right: 8px;
        position: absolute;
        left: -4px;
        top: 2px;
        border-radius: 10%; /* Rounded button */
    }

    .tree-node ul {
        font-size: 14px;
        margin-top: 5px; 
        margin-bottom: 5px; 
        margin-left: -18px; 
        margin-bottom: 20px;
    }

    .collapse {
        display: none;
    }

    .tree-node .folder {
        font-weight: bold;
        cursor: pointer;
    }

    .tree-node .file {
        font-style: italic; 
        cursor: default;
    }
    .tree-node:hover {
        background-color: rgba(200, 230, 255, 0.5);
        border-radius: 8px;
    }

    .selected {
        background-color: #bababa;
    }

    .red-text {
        color: red;
    }
</style>
@endpush

@push('scripts')
<script>

    /**
     * Clears the form by resetting all input fields to their default values.
     * @return void
     */
    function clearForm() {
        document.getElementById('product-material-form').reset();
        $('#tree-container').empty().html('<li class="text-muted text-center">検索結果はありません</li>');
    }

    // export product material excel file button
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('export-excel').addEventListener('click', function() {
            const partNumber = document.getElementById('part_number').value.trim();
            if (partNumber) {
                window.location.href = `{{ route('export.product-material.hierarchy') }}?partNumber=${encodeURIComponent(partNumber)}`;
            } else {
                $('#part_number').css('border', '2px solid red');
                return;
            }
        });
    });

    $(document).ready(function() {
        
        function fetchHierarchy(partNumber) {
            $.ajax({
                url: `/api/product-material-hierarchy`,
                method: 'GET',
                data: { partNumber: partNumber },
                success: function(data) {
                    const treeContainer = $('#tree-container');
                    treeContainer.empty();

                    // Retrieve input values
                    const inputProductNumber = $('[input-product-number]').val();
                    const inputPartNumber = $('[input-part-number]').val();

                    // Select target fields in the modal
                    const targetProductNumber = $('.open-material-hierarchy-modal #product_name');
                    const targetPartNumber = $('.open-material-hierarchy-modal #part_number');

                    // Update product_number & part_number field if empty
                    if (!targetProductNumber.val()) {
                        const fallbackProductNumber = inputProductNumber || data[0]?.part_name;
                        targetProductNumber.val(fallbackProductNumber);
                    }
                    if (!targetPartNumber.val()) {
                        targetPartNumber.val(inputPartNumber);
                    }


                    if (Array.isArray(data) && data.length === 0) {
                        treeContainer.html('<li class="text-muted text-center">検索結果はありません</li>');
                    } else if (Array.isArray(data)) {
                        createTree(data, treeContainer, partNumber);
                    } else {
                        treeContainer.html('<li class="text-muted text-center">検索結果はありません</li>');
                    }
                },
                error: function() {
                    $('#tree-container').html('<li class="text-muted text-center">検索結果はありません</li>');
                }
            });
        }

        function createTree(data, parentElement, highlightedPartNumber) {
            data.forEach((item, index) => {
                const isFolder = item.children && item.children.length > 0;
                const isLastItem = index === data.length - 1;
                const parentText = `<span class="${isFolder ? 'folder' : 'file'} toggle" style="${item.parent_part_number == highlightedPartNumber ? 'color: red !important;' : ''}">
                    ${item.parent_part_number || ''} - ${item.part_name || ''} (${item.product_category || ''})
                </span>`;
                const li = $('<li>').html(parentText).addClass('tree-node');

                if (isFolder) {
                    const ul = $('<ul>').addClass('');
                    item.children.forEach((child, childIndex) => {
                        const isLastChild = childIndex === item.children.length - 1;
                        const folderIcon = isLastChild ? '└─' : '├─';
                        const childText = `<span class="file" style="${child.child_part_number == highlightedPartNumber ? 'color: red !important;' : ''}">
                            ${folderIcon} ${child.child_part_number || ''} - ${child.part_name || ''} (${child.product_category || ''}) x ${Math.floor(child.quantity || 0)}
                        </span>`;
                        const childLi = $('<li>').html(childText).addClass('tree-node');

                        if (child.grand_children && child.grand_children.length > 0) {
                            const grandUl = $('<ul>').addClass('');
                            child.grand_children.forEach((grandChild, grandChildIndex) => {
                                const isLastGrandChild = grandChildIndex === child.grand_children.length - 1;
                                const grandChildIcon = isLastGrandChild ? '└─' : '├─';
                                const grandChildText = `<span class="file" style="${grandChild.grand_child_part_number == highlightedPartNumber ? 'color: red !important;' : ''}">
                                    ${grandChildIcon} ${grandChild.grand_child_part_number || ''} - ${grandChild.part_name || ''} (${grandChild.product_category || ''}) x ${Math.floor(grandChild.quantity || 0)}
                                </span>`;
                                grandUl.append($('<li>').html(grandChildText).addClass('tree-node'));
                            });
                            childLi.append(grandUl).prepend('<button class="btn btn-toggle" type="button">-</button>');
                        }
                        ul.append(childLi);
                    });
                    li.append(ul).prepend('<button class="btn btn-toggle" type="button">-</button>');
                }
                parentElement.append(li);
            });

            parentElement.off('click', '.btn-toggle').on('click', '.btn-toggle', function() {
                const ul = $(this).siblings('ul');
                ul.toggleClass('collapse');
                $(this).text(ul.hasClass('collapse') ? '+' : '-');
            });

            parentElement.off('click', '.toggle').on('click', '.toggle', function() {
                const ul = $(this).siblings('ul');
                ul.toggleClass('collapse');
            });
        }


        $(document).on('click', '.js-modal-open', function() {
            // Retrieve data attributes from the current element triggering the event
            const modalTriggerData = $(this).attr('data-open-material-hierarchy-modal');
            const apiDisabledData = $(this).attr('data-api-product-material-hierarchy-disabled');

            // Retrieve values from input fields for part and product numbers
            const inputPartNumber = $('[input-part-number]').val();
            const inputProductNumber = $('[input-product-number]').val();

            // Select the target element for displaying the product number in the modal
            const modalProductNumberField = $('.open-material-hierarchy-modal #product_name');

            // Flag to determine if the hierarchy fetch method should be disabled
            let isFetchHierarchyDisabled = false;

            // Check if the modal trigger data attribute exists
            if (modalTriggerData !== undefined) {
                // Open the material hierarchy modal by adding the relevant class
                $('.product-material-hierarchy-modal').addClass('open-material-hierarchy-modal');
                // Set the product number value in the modal's input field
                $(modalProductNumberField).val(inputProductNumber);
            }

            // Check if the API disable data attribute exists
            if (apiDisabledData !== undefined) {
                // Set the flag to disable the fetch hierarchy method
                isFetchHierarchyDisabled = true;
            }

            // Fetch the part_number from the URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const requestPartNumber = (inputPartNumber !== undefined) ? inputPartNumber : urlParams.get('part_number');

            // Get the data-part-number from the clicked element
            var partNumber = $(this).attr('data-part-number') || '';

            // Prioritize data-part-number if available, else fallback to requestPartNumber
            if (requestPartNumber && requestPartNumber !== partNumber) {
                partNumber = $(this).attr('data-part-number') || requestPartNumber;
            } else if (!partNumber) {
                partNumber = requestPartNumber;
            }

            const modalId = $(this).data('target');
            
            if(!isFetchHierarchyDisabled) {
                fetchHierarchy(partNumber);
            }
            
            // Set the partNumber value in the modal input field
            const $partNumberInput = $(`#${modalId}`).find('#part_number');
            $partNumberInput.val(partNumber);
            $partNumberInput.trigger('input');

            // Show the modal
            $(`#${modalId}`).fadeIn();
        });

        $(document).on('click', '.js-modal-close', function() {
            const modalId = $(this).data('target');
            const $partNumberInput = $(`#${modalId}`).find('#part_number');
            const $partNameInput = $(`#${modalId}`).find('#product_name');
            $partNumberInput.val('');
            $partNameInput.val('');

            // Check if the element has the 'open-material-hierarchy-modal' class
            // disable the auto close of modal product-material-hierarchy-modal
            $('.product-material-hierarchy-modal:not(.open-material-hierarchy-modal)').fadeOut();
        });

        $(document).on('click', '.product-material-hierarchy-modal', function(e) {
            if ($(e.target).is('.product-material-hierarchy-modal')) {
                $(this).fadeOut();
            }
        });

        $('#search-button').on('click', function() {
            const partNumber = $('#part_number').val();
            if(partNumber == ''){
                $('#part_number').css('border', '2px solid red');
            }else{
                $('#part_number').css('border', '2px solid #bfbfbf');
                fetchHierarchy(partNumber);
            }
        });
    });
</script>
@endpush