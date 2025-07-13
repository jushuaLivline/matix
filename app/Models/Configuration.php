<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'configurations';

    protected $guarded = [];

    protected $materialClassification = [
        1 => '材料',
        2 => '構成部品',
    ];

    public function getMaterialClassificationAttribute()
    {
        return $this->materialClassification[$this->attributes['material_classification']] ?? 'Unknown';
    }
    
    public function parentProduct()
    {
        return $this->belongsTo(ProductNumber::class, 'parent_part_number', 'part_number');
    }

    public function childProduct()
    {
        return $this->belongsTo(ProductNumber::class, 'child_part_number', 'part_number');
    }

    /**
     * Retrieves the hierarchy of parent-child product configurations for a given part number, ensuring no duplicate entries.
     * 
     * @param string $partNumber The part number for which the hierarchy is to be retrieved.
     * @return array An array containing the hierarchy of parent products, their details, and child configurations.
     */
    public static function getHierarchy($partNumber)
    {
        $hierarchy = [];
        $processedParentNumbers = []; // Tracks processed parent part numbers to avoid duplicates

        // Fetch parent configurations where the given part number is a parent
        $parentConfigurations = self::with(['childProduct', 'parentProduct.childrenConfigurations.childProduct'])
            ->where('parent_part_number', $partNumber)
            ->get();

        // Fetch child configurations where the given part number is a child
        $childConfigurations = self::with(['parentProduct.childrenConfigurations.childProduct'])
            ->where('child_part_number', $partNumber)
            ->get();

        // Process parent configurations
        foreach ($parentConfigurations as $config) {
            $parentProduct = $config->parentProduct; // Retrieve the parent product details
            $parentPartNumber = $config->parent_part_number;

            // Check if the parent part number has already been added on array $processedParentNumbers
            if (!in_array($parentPartNumber, $processedParentNumbers)) {
                $children = self::prepareChildren($parentProduct->childrenConfigurations);

                // Add the parent and its children to the hierarchy
                $hierarchy[] = [
                    'parent_part_number' => $config->parent_part_number,
                    'part_name' => $parentProduct ? $parentProduct->product_name : null,
                    'product_category' => $parentProduct ? $parentProduct->product_category : null,
                    'children' => $children,
                ];
                $processedParentNumbers[] = $parentPartNumber; // Mark this parent part number as processed
            }
        }

        // Process child configurations
        foreach ($childConfigurations as $config) {
            $parentProduct = $config->parentProduct; // Retrieve the parent product details for the child
            if ($parentProduct) {
                $parentPartNumber = $parentProduct->part_number;

                // Check if the parent part number has already been added on array $processedParentNumbers
                if (!in_array($parentPartNumber, $processedParentNumbers)) {
                    $children = self::prepareChildren($parentProduct->childrenConfigurations);

                    $hierarchy[] = [
                        'parent_part_number' => $parentProduct->part_number,
                        'part_name' => $parentProduct->product_name,
                        'product_category' => $parentProduct->product_category,
                        'children' => $children,
                    ];
                    $processedParentNumbers[] = $parentPartNumber; // Mark this parent part number as processed
                }
            }
        }

        return $hierarchy; // Return the final hierarchy array
    }

    /**
     * Prepare children and their grandchildren if needed.
     */
    private static function prepareChildren($childrenConfigurations)
    {
        $children = [];

        foreach ($childrenConfigurations as $childConfig) {
            $childData = [
                'child_part_number' => $childConfig->childProduct ? $childConfig->childProduct->part_number : null,
                'part_name' => $childConfig->childProduct ? $childConfig->childProduct->product_name : null,
                'product_category' => $childConfig->material_classification,
                'quantity' => $childConfig->number_used,
                'grand_children' => []
            ];

            // Fetch grandchildren if the child has product_category '4' (仕掛品)
            if ($childConfig->childProduct && $childConfig->childProduct->product_category == '仕掛品') {
                $childData['grand_children'] = self::getGrandChildren($childConfig->child_part_number);
            }

            $children[] = $childData;
        }

        // Sort children to ensure those with grandchildren appear at the end
        usort($children, function ($a, $b) {
            return count($a['grand_children']) <=> count($b['grand_children']);
        });

        return $children;
    }

    /**
     * Fetch grandchildren for a given child part number.
     */
    public static function getGrandChildren($childPartNumber)
    {
        $grandChildren = [];
        $grandChildConfigurations = self::with('childProduct')
            ->where('parent_part_number', $childPartNumber)
            ->get();

        foreach ($grandChildConfigurations as $grandChildConfig) {
            $grandChildren[] = [
                'grand_child_part_number' => $grandChildConfig->child_part_number,
                'part_name' => $grandChildConfig->childProduct ? $grandChildConfig->childProduct->product_name : null,
                'product_category' => $grandChildConfig->material_classification,
                'quantity' => $grandChildConfig->number_used,
            ];
        }

        return $grandChildren;
    }
}
