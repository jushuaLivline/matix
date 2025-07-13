$(function () {
  const printButton = document.getElementById("printButton");
  const confirmOrderButton = document.querySelector("#confirmOrder");
  const confirmOrderForm = document.querySelector("#confirmOrderForm");

  confirmOrderButton.addEventListener("click", function () {
    if(!confirm('表示されている発注待ち情報を発注情報として登録します、よろしいでしょうか？')) return;
    confirmOrderForm.submit();
  });


  printButton.addEventListener("click", function () {
    printContent();
  });

  function printContent() {
    var contentToPrint = document.querySelector(".tableWrap");
    var printContent = contentToPrint.cloneNode(true);

    var newWindow = window.open("", "_blank");
    newWindow.document.open();

    newWindow.document.write(`
        <html>
        <head>
            <style>
                @media print {
                    @page { size: landscape; margin: 0; }
                    body::before { content: ""; display: none; }
                }
            </style>
        </head>
        <body onload="window.print(); window.onafterprint = function() { window.close(); }">
            ${printContent.outerHTML}
        </body>
        </html>
    `);

    newWindow.document.close();
    newWindow.focus();
  }
});
