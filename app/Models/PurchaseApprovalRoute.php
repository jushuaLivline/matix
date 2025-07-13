<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseApprovalRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_code',
        'approval_route_no',
        'approval_route_name',
        'display_order',
        "creator",
        "updator",
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()->user()->id;
        });

        self::updating(function ($model) {
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()->user()->id;
        });
    }

    public function details()
    {
        return $this->hasMany(PurchaseApprovalRouteDetail::class, 'approval_route_no', 'approval_route_no');
    }


    public function createApprovalRoute($request)
    {
        // Get the last approval route number and display order for the given employee_code
        $approvalRouteData = $this->generateApprovalRouteData($request->employee_code, $request->name, $request->user()->employee_code);

        // Create and return the new approval route
        return $this->create($approvalRouteData);
    }

    public function updateApprovalRoute($request)
    {
        // Find the approval route by ID and update its name
        $approvalRoute = $this->findOrFail($request->update_id);
        $approvalRoute->update(['approval_route_name' => $request->name]);

        // Delete existing details for this approval route
        $approvalRoute->details()->delete();

        return $approvalRoute;
    }

    public function deleteApprovalRoute($id)
    {
        // Find the approval route by ID
        $approvalRoute = $this->find($id);

        // If route exists, proceed to delete related details and update remaining routes
        if ($approvalRoute) {
            // Delete related details and the approval route itself
            $approvalRoute->details()->delete();
            $approvalRoute->delete();

            // Reorder the remaining approval routes for the employee
            return $this->reorderApprovalRoutes($approvalRoute->employee_code);
        }
    }

    private function generateApprovalRouteData($employee_code, $route_name, $creator_code)
    {
        // Get the last approval route number and display order
        $lastApprovalRoute = $this->orderBy('approval_route_no', 'desc')->first();
        $lastDisplayOrder = $this->orderBy('display_order', 'desc')->where('employee_code', $employee_code)->first();

        // Prepare approval route data
        return [
            'employee_code' => $employee_code,
            'approval_route_no' => $lastApprovalRoute ? (int) $lastApprovalRoute->approval_route_no + 1 : 1,
            'approval_route_name' => $route_name,
            'display_order' => $lastDisplayOrder ? (int) $lastDisplayOrder->display_order + 1 : 1,
            'creator' => $creator_code,
        ];
    }

    private function reorderApprovalRoutes($employee_code)
    {
        // Fetch approval routes for the given employee_code, ordered by display_order
        $purchaseApprovalRoutes = $this->where('employee_code', $employee_code)->orderBy('display_order', 'ASC')->get();

        // Update the display order for each route
        foreach ($purchaseApprovalRoutes as $index => $route) {
            $route->display_order = $index + 1;
            $route->save();
        }

        return $purchaseApprovalRoutes;
    }

}