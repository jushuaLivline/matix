$(function () {
    document.addEventListener("DOMContentLoaded", function () {
        const printButtons = document.querySelectorAll('[data-print-button]');

        // Print template
        const printTemplate = `
            <style>
                table { border-collapse: collapse; }
                td {
                    border: 1px solid #000;
                    padding: 10px;
                    text-align: center;
                }
                th {
                    background-color: #e3dede;
                    border: 1px solid #000;
                    padding: 10px;
                    width: 200px;
                }
                .float-right { float: right; }
            </style>
            <b>注文書再発行</b>
            <span class="float-right">日付: {{ now()->format('m/d/Y') }}</span>
            <hr/>
            <table>
                <thead>
                    <tr>
                        <th>注文書No.</th>
                        <th>発注日</th>
                        <th>発注先 操作</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>order_form_no</td>
                        <td>order_date</td>
                        <td>supplier</td>
                    </tr>
                </tbody>
            </table>
        `;

        // Event listener to each button
        printButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const row = button.closest('tr');

                const order_form_no = row.querySelector('.tA-cn:nth-child(1)').textContent.trim();
                const order_date = row.querySelector('.tA-cn:nth-child(2)').textContent.trim();
                const supplier = row.querySelector('.tA-cn:nth-child(3)').textContent.trim();

                const formattedTemplate = printTemplate
                    .replace('order_form_no', order_form_no)
                    .replace('order_date', order_date)
                    .replace('supplier', supplier);

                const newWindow = window.open('', '_blank');
                newWindow.document.write(formattedTemplate);
                newWindow.document.close();
                newWindow.print();
                newWindow.close();
            });
        });
    });
})
