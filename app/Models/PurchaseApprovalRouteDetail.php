<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseApprovalRouteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'approval_route_no',
        'order_of_approval',
        'approver_employee_code',
    ];

    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'approver_employee_code', 'employee_code');
    }

    public function createApprovalRouteDetail($approval_route, $value, $request, $isLoginUser = false)
    {
        // Get the last display order detail for the given approval route
        $lastDisplayOrderDetail = $this->getLastDisplayOrderDetail($approval_route->approval_route_no);
    
        // Determine the employee code to use
        $employeeCode = $isLoginUser ? $request->user()->employee_code : $approval_route->employee_code;
    
        // Determine the order of approval
        $orderOfApproval = $lastDisplayOrderDetail ? $lastDisplayOrderDetail->order_of_approval + 1 : 1;
    
        // Prepare the data for creating the approval route detail
        $approvalRouteDetailData = [
            'employee_code' => $employeeCode,
            'approval_route_no' => $approval_route->approval_route_no,
            'order_of_approval' => $orderOfApproval,
            'approver_employee_code' => $value,
        ];
    
        // Create and return the approval route detail
        return $this->create($approvalRouteDetailData);
    }
    
    protected function getLastDisplayOrderDetail($approvalRouteNo)
    {
        return $this->where('approval_route_no', $approvalRouteNo)
                    ->orderBy('order_of_approval', 'desc')
                    ->first();
    }
    
}
