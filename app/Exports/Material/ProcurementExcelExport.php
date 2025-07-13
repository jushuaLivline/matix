<?php

namespace App\Exports\Material;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProcurementExcelExport implements FromView
{
    protected $procurementPlanLists, $firstDateOfMonth, $lastDateOfMonth, $dates;

    public function __construct($procurementPlanLists, $firstDateOfMonth, $lastDateOfMonth, $dates)
    {
        $this->procurementPlanLists = $procurementPlanLists;
        $this->firstDateOfMonth = $firstDateOfMonth;
        $this->lastDateOfMonth = $lastDateOfMonth;
        $this->dates = $dates;
    }

    public function view(): View
    {
        return view('pages.material.procurement.excel_export', [
            'procurementPlanLists' => $this->procurementPlanLists,
            'firstDateOfMonth' => $this->firstDateOfMonth,
            'lastDateOfMonth' => $this->lastDateOfMonth,
            'dates' => $this->dates,
        ]);
    }

}
