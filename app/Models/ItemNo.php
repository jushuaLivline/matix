<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'item_name',
        'product_name_abbreviation',
        'product_category',
        'customer_code',
        'supplier_code',
        'department_code',
        'line_code',
        'sub_line_code',
        'standard',
        'material_manufacturer_code',
        'unit_code',
        'back_number',
        'part_number_edit_format',
        'edit_part_number',
        'instruction_classification',
        'customer_part_number',
        'customer_part_number_edit_format',
        'customer_edited_part_number',
        'production_classification',
        'delete_flag',
    ];
}
