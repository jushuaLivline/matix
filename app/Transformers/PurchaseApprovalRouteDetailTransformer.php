<?php

namespace App\Transformers;

use App\Models\PurchaseApprovalRouteDetail;
use League\Fractal\TransformerAbstract;

class PurchaseApprovalRouteDetailTransformer extends TransformerAbstract
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
        'employee'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PurchaseApprovalRouteDetail $row)
    {
        return [
            'id' => $row->id,
            'employee_code' => $row->approver_employee_code,
            'order_of_approval' => $row->order_of_approval,
        ];
    }

    public function includeEmployee(PurchaseApprovalRouteDetail $row)
    {
        $employee = $row->employee;
        
        return $this->item($employee, new EmployeeTransformer);
    }
}
