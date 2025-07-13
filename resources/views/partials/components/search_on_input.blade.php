<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        const dataConfigs = @json($dataConfigs);
        const token = "{{ csrf_token() }}";
        const url = "{{ route('search') }}";

        if (dataConfigs && Object.keys(dataConfigs).length > 0) {
            initializeSearchInputs({
                dataConfigs: dataConfigs,
                token: token,
                url: url
            });

            // Check if any input field has a pre-filled value and trigger search
            for (const key in dataConfigs) {
                if (dataConfigs.hasOwnProperty(key)) {
                    const inputElement = document.querySelector(`.${key}`);
                    if (inputElement && inputElement.value.trim() !== '') {
                        // Trigger search for elements with a value
                        performSearch(inputElement, dataConfigs, token, url, false);
                    }
                }
            }
        } else {
            console.warn('No dataConfigs provided or dataConfigs is empty.');
        }
    });

    function initializeSearchInputs({ dataConfigs, token, url }) {
        const previousValues = {}; // Store previous values of each input

        const debouncedSearch = debounce(function (inputElement, isFocus) {
            const inputId = inputElement.id || inputElement.name; // Use ID or name as key
            const currentValue = inputElement.value.trim();

            // Only perform search if the value has changed
            if (previousValues[inputId] !== currentValue) {
                previousValues[inputId] = currentValue; // Update the stored value
                performSearch(inputElement, dataConfigs, token, url, isFocus);
            }
        }, 1000);

        $(document).on('input', '.searchOnInput', function () {
            debouncedSearch(this, true);
        });

        $(document).on('blur', '.searchOnInput', function () {
            debouncedSearch(this, false);
        });
    }

    function debounce(func, delay) {
        let debounceTimer;
        return function () {
            const context = this;
            const args = arguments;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(context, args), delay);
        };
    }

    function performSearch(inputElement, dataConfigs, token, url, isFocus) {
        const query = inputElement.value.trim();
        const config = getConfigForElement(inputElement, dataConfigs);

        if (config && query !== "") { // Ensure query isn't empty
            $.ajax({
                url: url,
                method: "POST",
                data: {
                    model: config.model,
                    query: query,
                    _token: token
                },
                success: function (response) {
                    handleSearchResponse(response, inputElement, config.reference, isFocus);
                },
                error: function () {
                    console.error('Error during AJAX request');
                }
            });
        }
    }

    function getConfigForElement(element, dataConfigs) {
        const elementClasses = Array.from(element.classList);
        let config = null;

        Object.keys(dataConfigs).some(key => {
            if (elementClasses.includes(key)) {
                config = dataConfigs[key];
                return true;
            }
            return false;
        });

        return config;
    }

    function handleSearchResponse(response, inputElement, referenceId, isFocus) {
        let correspondingCode = inputElement.value.trim();
        let correspondingText = '';

        response.forEach(function (item) {
            if (item.code === correspondingCode || correspondingCode === parseInt(item.code, 10).toString()) {
                correspondingCode = item.code;
                correspondingText = item.name;
            }
        });

        const nameElement = document.getElementById(referenceId);
        if (isFocus && correspondingText != '') {
            nameElement.value = correspondingText;
        }

        if (!isFocus || !correspondingCode) {
            inputElement.value = correspondingCode;
            if (!correspondingCode) {
                nameElement.value = '';
            }
        }
    }
</script>