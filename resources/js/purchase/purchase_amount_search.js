document.addEventListener("DOMContentLoaded", function () {
    let activeInputId = '';
    let selectedYear = new Date().getFullYear();
    let customButton = document.querySelectorAll('.year-month-date-picker');
    
    if(customButton.length > 0){
        customButton.forEach(function (button) {
            button.addEventListener('click', function () {
                const inputId = this.getAttribute('data-target');
                openCalendarModal(inputId);
            });
        }); 
    }



    // Open the calendar modal with a specific input
    window.openCalendarModal = function (inputId) { 
        activeInputId = inputId;
        const inputValue = document.getElementById(activeInputId)?.value || '';

        if (inputValue) {
            selectedYear = parseInt(inputValue.substring(0, 4)) || new Date().getFullYear();
        } else {
            selectedYear = new Date().getFullYear();
        }

        document.getElementById('calendarModal').style.display = 'flex';
        populateCalendar(selectedYear);
    };

    // Close the calendar modal
    window.closeCalendarModal = function () {
        document.getElementById('calendarModal').style.display = 'none';
    };

    // Change the year and update the calendar (Fix: prevent page reload)
    window.changeYear = function (offset, event) {
        event.preventDefault(); // Prevents page reload
        selectedYear += offset;
        populateCalendar(selectedYear);
    };

    // Populate the calendar with months for the selected year
    function populateCalendar(year) {
        const calendar = document.getElementById('calendar');
        if (!calendar) return;

        calendar.innerHTML = ''; // Clear previous content
        document.getElementById('calendarYear').textContent = `${year}年`;

        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;

        for (let month = 1; month <= 12; month++) {
            const formattedMonth = `${year}${month.toString().padStart(2, '0')}`;
            const monthName = `${month}月`;

            const monthElement = document.createElement('div');
            monthElement.className = 'calendar-month';
            monthElement.textContent = monthName;

            // Highlight current month only if it's the current year
            if (year === currentYear && month === currentMonth) {
                monthElement.classList.add('current-month');
            }

            monthElement.addEventListener('click', () => selectMonth(formattedMonth));
            calendar.appendChild(monthElement);
        }
    }

    // Update the corresponding input with the selected month
    function selectMonth(formattedDate) {
        if (activeInputId) {
            document.getElementById(activeInputId).value = formattedDate;
        }
        closeCalendarModal();
    }
});
