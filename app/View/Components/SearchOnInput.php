<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SearchOnInput extends Component
{
    public $dataConfigs;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($dataConfigs = [])
    {
        $this->dataConfigs = $dataConfigs;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('partials.components.search_on_input');
    }
}
