<?php

namespace App\Transformers;

use App\Models\PurchaseApprovalRoute;
use League\Fractal\TransformerAbstract;

class PurchaseApprovalRouteTransformer extends TransformerAbstract
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
        'details'
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(PurchaseApprovalRoute $row)
    {
        return [
            'approval_route_name' => $row->approval_route_name,
        ];
    }

    public function includeDetails(PurchaseApprovalRoute $row)
    {
        $details = $row->details;

        return $this->item($details, new PurchaseApprovalRouteDetailTransformer);
    }
}
