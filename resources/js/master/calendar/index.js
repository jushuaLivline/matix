$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var selectedDates = [];
    var unselectedId = [];
    var yearData = { year: new Date().getFullYear() };
    var calendarEl = document.getElementById('calendar');
    var total_working_days = document.getElementById('total_working_days');
    var actual_working_hours = document.getElementById('actual_working_hours');
    var total_holidays = document.getElementById('total_holidays');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ja',
        //locale: 'en',
        initialView: 'multiMonthYear',
        multiMonthMaxColumns: 6,
        editable: true,
        eventSources: [{
            events: function(info, successCallback, failureCallback) {
                const date = new Date(info.start);
                yearData.year = new Date(info.start).getFullYear();
                const newTitle = yearData.year + '年度カレンダー';
                $.ajax({
                    url: "calendar",
                    type: "GET",
                    data: yearData,
                    success: function(data) {
                        console.log(data);
                        successCallback(data.data);
                        document.getElementById("fc-dom-1").textContent = newTitle;
                        total_holidays.value = data.total_holidays+"日";
                        computeInputs(total_holidays.value, yearData.year);
                        
                        let holidayCounts = {};
                        // Iterate over the items and count them by month
                        data.data.forEach(function(item) {
                            let date = new Date(item.calendar_date);
                            let month = date.getMonth(); // getMonth() returns month index (0 for January, 1 for February, etc.)
                            
                            if (!holidayCounts[month]) {
                                holidayCounts[month] = 0;
                            }
                            // Increment the count for the month
                            holidayCounts[month]++;
                            // Update the background color
                            document.querySelector('.fc-day[data-date="' + item.calendar_date + '"]').style.background = 'radial-gradient(ellipse at center,  #f6e6f5 0%,#f6e6f5 47%,#f6e6f5 47%,#ffffff 47%,#ffffff 48%)';
                        });

                        const dayCells = document.querySelectorAll('.fc-daygrid-day-number');
                        dayCells.forEach((cell) => {
                            let dayNumber = cell.textContent.trim();
                            if (dayNumber.includes("日")) {
                                dayNumber = dayNumber.replace("日", "").trim();
                            }
                            cell.textContent = dayNumber;
                        });

                        document.querySelectorAll('.fc-multimonth-title').forEach(function(element, index) {
                            const startYear = info.start.getFullYear();
                            const startMonth = info.start.getMonth(); // Index of first visible month
                            const thisMonth = startMonth + index;
                        
                            // Calculate correct year/month even if it spills into next year
                            const displayYear = startYear + Math.floor(thisMonth / 12);
                            const displayMonth = (thisMonth % 12) + 1;
                        
                            // Update title text
                            element.textContent = `${displayMonth}月`;
                        
                            // Calculate the number of days in the month
                            const totalDays = new Date(displayYear, displayMonth, 0).getDate();
                        
                            // Create a new div element for the holiday counter
                            let counterDiv = document.createElement('div');
                            counterDiv.className = 'working_days_counters';
                            counterDiv.id = 'month_counter_' + index;
                            if (holidayCounts[index] === undefined) {
                                holidayCounts[index] = 0;
                            }
                        
                            counterDiv.innerHTML = totalDays -  holidayCounts[index];
                            element.parentNode.insertBefore(counterDiv, element.nextSibling);
                        });
                    }
                });
            },
        }],
        windowResize: function(view) {
            // Prevent re-rendering or add custom logic here
            console.log('Window resized, but no re-rendering.');
        },
        selectable: true,
        dayHeaders: true,
        aspectRatio: 4,
        headerToolbar: {
            start: '',
            center: 'prevYear title nextYear',
            end: ''
        },
        buttonIcons: {
            prevYear: 'chevrons-left', // double chevron
            nextYear: 'chevrons-right' // double chevron
        },
        dateClick: function(info) {
            if (info.dayEl.style.backgroundColor == 'initial') {
                // check DB if exists
                $.ajax({
                    type: 'post',
                    data: {
                        date: info.dateStr,
                    },
                    url: 'calendar/check-exists',
                    success: function(data) {
                        if (data.status === true) {
                            // add to dates that will be deleted in DB 
                            unselectedId.push(data.id)
                        } else {
                            // remove from pre-selected Dates
                            selectedDates = selectedDates.filter((element) => element !== info.dateStr);
                        }
                        // Log unselectedId and selectedDates
                        console.log('Unselected IDs:', unselectedId);
                        console.log('Selected Dates:', selectedDates);
                        
                        total_holidays.value = (parseInt(total_holidays.value) - 1)+"日";
                        computeInputs(total_holidays.value, yearData.year);
                                        
                        updateCounter(info, 'plus');  // To increment the counter
                    }
                });
                info.dayEl.style.background = '#ffffff';
            } else {
                info.dayEl.style.background = 'radial-gradient(ellipse at center,  #f6e6f5 0%,#f6e6f5 47%,#f6e6f5 47%,#ffffff 47%,#ffffff 48%)';
                selectedDates.push(info.dateStr);
                // Log unselectedId and selectedDates
                console.log('Unselected IDs:', unselectedId);
                console.log('Selected Dates:', selectedDates);
                total_holidays.value = (parseInt(total_holidays.value) + 1)+"日";
                computeInputs(total_holidays.value, yearData.year);
                
                updateCounter(info, 'minus');
            
            }
        },
    });
    calendar.render();

    $('#saveAll').on('click', function() {
        var currentDate = calendar.getDate();
        var conf = confirm('この内容で登録しますか？');
        if (conf) {
            if (selectedDates.length > 0) {
                $.ajax({
                    url: "calendar/calendar-operations",
                    data: {
                        selectedDates: selectedDates,
                        type: 'create'
                    },
                    type: "POST",
                    success: function(data) {},
                });
            }
            if (unselectedId.length > 0) {
                var confirmDelete = confirm('削除される日付があります。確認しますか？')
                if (confirmDelete) {
                    $.ajax({
                        url: "calendar/calendar-operations",
                        data: {
                            unselectedId: unselectedId,
                            type: 'delete'
                        },
                        type: "POST",
                        success: function(data) {},
                    });
                }
            }
            $.ajax({
                url: "calendar/calendar-operations",
                data: {
                    year: currentDate.getFullYear(),
                    type: 'create'
                },
                type: "POST",
                success: function(data) {
                $('<div id="flash-message" class="mb-4">データは正常に登録されました</div>').insertAfter('.accordion');

                 // scroll to top
                 $('html, body').animate({
                    scrollTop: 0
                }, 500);
        
                },
            });
        }
    });

    function computeInputs(total_holidays_db, currentYear) {
        // Total number
        const holidays = parseInt(total_holidays.value) || 0;
        const isLeap = (currentYear % 4 === 0 && currentYear % 100 !== 0) || (currentYear % 400 === 0);
        const totalDaysInYear = isLeap ? 366 : 365;
        const totalDays = totalDaysInYear - holidays;

        total_working_days.value = totalDays+"日";
        actual_working_hours.value = (totalDays * 8)+"時間";
    }

    function updateCounter(info, operation) {
        // Parse the date string to get the month index
        let date = new Date(info.dateStr);
        let monthIndex = date.getMonth(); // getMonth() returns month index (0 for January, 1 for February, etc.)

        // Find the element by id and update its inner text
        let counterElement = document.getElementById('month_counter_' + monthIndex);
        if (counterElement) {
            let currentCount = parseInt(counterElement.innerText);
            if (operation === 'plus') {
                counterElement.innerText = currentCount + 1;
            } else if (operation === 'minus') {
                counterElement.innerText = currentCount - 1;
            }
        }
    }
});