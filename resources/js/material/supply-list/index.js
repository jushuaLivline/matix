$(function () {
  const excelExportButton = document.getElementById('excel-export-button');
  const excelExportForm = document.getElementById('suppliedListExcelForm');
  const pdfExportForm = document.getElementById('suppliedListPDFForm');
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const deteleButton = document.querySelectorAll('[data-button-delete]');

  // Export excel file
  excelExportButton.addEventListener('click', function () {
    excelExportForm.submit();
  })
  // Export pdf file
  excelExportButton.addEventListener('click', function () {
    pdfExportForm.submit();
  })

  deteleButton.forEach((button) => {
    button.addEventListener('click', function () {
      // Check if the button is disabled
      if (this.disabled || this.classList.contains('btn-disabled')) {
        return;
      }
      let form = this.getAttribute('data-form-id');
      $('#' + form).submit();
    }); 
  })
});
