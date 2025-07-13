<?php

namespace App\Transformers;

use App\Models\Employee;
use League\Fractal\TransformerAbstract;

class EmployeeTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Employee $row)
    {
        return [
            'name' => $row->employee_name,
            'code' => $row->employee_code,
        ];
    }
}
