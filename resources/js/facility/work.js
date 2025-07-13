document.addEventListener("DOMContentLoaded", function () {
    var workDayInput = document.getElementById("work_day");
    var prevDayButton = document.getElementById("prevDayButton");
    var nextDayButton = document.getElementById("nextDayButton");

    //event listener for the "前日" button
    prevDayButton.addEventListener("click", function () {
        updateDay(-1);
    });

    //event listener for the "翌日" button
    nextDayButton.addEventListener("click", function () {
        updateDay(1);
    });

    function updateDay(offset) {
        var currentDate = new Date(workDayInput.value.replace(/(\d{4})(\d{2})(\d{2})/, "$1-$2-$3"));

        currentDate.setDate(currentDate.getDate() + offset);

        var newDate = currentDate.getFullYear() +
            ("0" + (currentDate.getMonth() + 1)).slice(-2) +
            ("0" + currentDate.getDate()).slice(-2);

        workDayInput.value = newDate;
    }
});

$(document).ready(function() {
   // Initial state
   updateClassificationInputs();

   // Event handler for classification change
   $("#classification").change(function() {
       updateClassificationInputs();
   });

   function updateClassificationInputs() {
       var classification = $("#classification").val();
       var inputContainer = $("#classification_input");
       var codeInput = $("#project_code");
       var nameInput = $("#project_name");
       var searchModalButton = $("#buttonModal");
       var searchModalLabel = $("#searchLabel");

       $("#classification_input input, #machine_input input").prop("disabled", false);
       $("#classification_input, #machine_input").show();

       if (classification === "machine") {
           $("#classification_input input").prop("disabled", true);
           $("#machine_input").show();
       } else if (classification === "other") {
           $("#machine_input").hide();
           $("#classification_input").hide();
       } else {
           $("#machine_input").hide();
       }

       if (classification === "line") {
           codeInput.attr("id", "project_code").attr("name", "line_code");
           nameInput.attr("id", "project_name").attr("name", "line_name").attr("readonly", true);
           searchModalButton.attr("data-target", "searchLineModal");
           searchModalLabel.text("ライン");
       } else {
           codeInput.attr("id", "project_code").attr("name", "project_code");
           nameInput.attr("id", "project_name").attr("name", "project_name");
           searchModalButton.attr("data-target", "searchProjectModal");
           searchModalLabel.text("コード");
       }


   }

   var classificationSelect = $('#classification');
   var workDetailSelect = $('#work_detail');

   function updateWorkDetailOptions() {
       var selectedClassification = classificationSelect.val();

       workDetailSelect.empty();

       var spareId;
       switch (selectedClassification) {
           case 'machine':
               spareId = '1';
               break;
           case 'common':
               spareId = '2';
               break;
           case 'line':
               spareId = '3';
               break;
           case 'other':
               spareId = '4';
               break;
           default:
               break;
       }

       optionsData.forEach(function (option) {
            if (option.spare_1 === spareId) {
                workDetailSelect.append('<option value="' + option.code + '" id="' + option.spare_1 + '">' + option.abbreviation + '</option>');
            }
        });
   }

   classificationSelect.on('change', updateWorkDetailOptions);

   updateWorkDetailOptions();
});
