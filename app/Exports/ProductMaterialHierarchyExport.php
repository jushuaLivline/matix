<?php

namespace App\Exports;

use App\Models\ProductNumber;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductMaterialHierarchyExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $partNumber;

    public function __construct($partNumber)
    {
        $this->partNumber = $partNumber;
    }

    /**
     * Return headings for the export.
     */
    public function headings(): array
    {
        										
        return [
            //parent::head
            '製品品番',
            '製品品名',
            '製品区分',
            //child::head
            '品番',
            '品名',
            '材料区分',
            '使用個数',
            //grandchild::head
            '品番',
            '品名',
            '材料区分',
            '使用個数'
        ];
    }

    /**
     * Map the hierarchy data to the required format for Excel.
     */
    public function map($row): array
    {
        $parent = $row['parent'];
        $child = $row['child'];
        $grandChild = $row['grand_child'];

        return [
            $parent['parent_part_number'],
            $parent['parent_part_name'],
            $parent['product_category'],
            $child['child_part_number'],
            $child['child_part_name'],
            $child['material_classification'],
            $child['quantity'],
            $grandChild['grand_child_part_number'] ?? null,
            $grandChild['grand_child_part_name'] ?? null,
            $grandChild['grand_child_product_category'] ?? null,
            $grandChild['grand_child_quantity'] ?? null,
        ];
    }

    /**
     * Return collection of hierarchy data.
     */
    public function collection()
    {
        $product = ProductNumber::where('part_number', $this->partNumber)->first();

        if (!$product) {
            return collect([]);
        }

        $hierarchy = $product->getHierarchy();
        $exportData = [];

        foreach ($hierarchy as $parent) {
            // Prepare parent information for the first row
            $parentInfo = [
                'parent_part_number' => $parent['parent_part_number'],
                'parent_part_name' => $parent['part_name'],
                'product_category' => $parent['product_category'],
            ];

            foreach ($parent['children'] as $child) {
                $grandChildren = $child['grand_children'] ?? [];

                // If there are grandchildren, loop through them and create separate rows for each
                if (count($grandChildren)) {
                    foreach ($grandChildren as $grandChild) {
                        $exportData[] = [
                            'parent' => $parentInfo,
                            'child' => [
                                'child_part_number' => $child['child_part_number'],
                                'child_part_name' => $child['part_name'],
                                'material_classification' => $child['product_category'],
                                'quantity' => $child['quantity'],
                            ],
                            'grand_child' => [
                                'grand_child_part_number' => $grandChild['grand_child_part_number'],
                                'grand_child_part_name' => $grandChild['part_name'],
                                'grand_child_product_category' => $grandChild['product_category'],
                                'grand_child_quantity' => $grandChild['quantity'],
                            ],
                        ];

                        // After processing the first row with the parent info, clear parent info to avoid repetition
                        $parentInfo = [
                            'parent_part_number' => '',
                            'parent_part_name' => '',
                            'product_category' => '',
                        ];
                    }
                } else {
                    // If there are no grandchildren, add a row with null values for the grandchild fields
                    $exportData[] = [
                        'parent' => $parentInfo,
                        'child' => [
                            'child_part_number' => $child['child_part_number'],
                            'child_part_name' => $child['part_name'],
                            'material_classification' => $child['product_category'],
                            'quantity' => $child['quantity'],
                        ],
                        'grand_child' => [
                            'grand_child_part_number' => null,
                            'grand_child_part_name' => null,
                            'grand_child_product_category' => null,
                            'grand_child_quantity' => null,
                        ]
                    ];

                    // Clear parent info after the first entry
                    $parentInfo = [
                        'parent_part_number' => '',
                        'parent_part_name' => '',
                        'product_category' => '',
                    ];
                }
            }
        }

        return collect($exportData);
    }

    /**
     * Apply styles to the worksheet, like coloring the target part number.
     */
    public function styles(Worksheet $sheet)
    {
        // Check if the sheet contains data
        $highestRow = $sheet->getHighestRow();

        if ($highestRow == 1) { // No data rows exist beyond the headings
            $sheet->mergeCells('A2:K2');
            $sheet->setCellValue('A2', '検索結果はありません');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        } else {
            $partNumber = $this->partNumber;

            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();

                // Skip the header row
                if ($rowIndex === 1) {
                    continue;
                }

                // Get the values of the parent, child, and grandchild part number cells
                $parentPartCell = $sheet->getCell('A' . $rowIndex);
                $childPartCell = $sheet->getCell('D' . $rowIndex);
                $grandChildPartCell = $sheet->getCell('H' . $rowIndex);

                // Apply styles if part numbers match
                if ($parentPartCell->getValue() == $partNumber) {
                    $sheet->getStyle('A' . $rowIndex)->getFont()->getColor()->setARGB('FFFF0000');
                }

                if ($childPartCell->getValue() == $partNumber) {
                    $sheet->getStyle('D' . $rowIndex)->getFont()->getColor()->setARGB('FFFF0000');
                }

                if ($grandChildPartCell && $grandChildPartCell->getValue() == $partNumber) {
                    $sheet->getStyle('H' . $rowIndex)->getFont()->getColor()->setARGB('FFFF0000');
                }
            }
        }
    }
}