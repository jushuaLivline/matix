<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductMaterialHierarchyModal extends Component
{
    public $modalId;

    public function __construct($modalId = 'product-material-hierarchy-modal')
    {
        $this->modalId = $modalId;
    }

    public function render()
    {
        return view('partials.components.product-material-hierarchy-modal');
    }
}
