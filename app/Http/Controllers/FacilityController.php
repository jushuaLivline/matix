<?php

namespace App\Http\Controllers;
use App\Models\Code;
use App\Models\FacilityWorkDetail;
use App\Models\FacilityWorkResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacilityController extends Controller
{
    private function mapClassificationToCategory($classification)
    {
        switch ($classification) {
            case 'machine':
                return 1;
            case 'common':
                return 2;
            case 'line':
                return 3;
            case 'other':
                return 4;
            default:
                return null;
        }
    }

    public function workDetail(Request $request)
    {

        $options = Code::where('division', '作業コード')->get(['code', 'abbreviation', 'spare_1']);
        
        $items = session()->has('workItems') ? session('workItems') : [];

        $latestItem = end($items);

        return view('pages.facility.work', [
                'options' => $options,
                'items' => $items,
                'latestItem' => $latestItem
            ]);
    }

    public function storeItemWorkDetail(Request $request)
    {

        $workItemData = $request->json()->all();

        $workItemId = uniqid();

        $workItemData['id'] = $workItemId;
        $workItems = $request->session()->get('workItems', []);
        $workItems[] = $workItemData;
        $request->session()->put('workItems', $workItems);

        return response()->json(['success' => true, 'workItemId' => $workItemId]);
    }

    public function bulkStoreWorkDetails(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $workItems = $request->json('workItems');
    
            foreach ($workItems as $workItemData) {
                FacilityWorkDetail::create([
                    'working_day' => $workItemData['work_day'],
                    'employee_code' => $workItemData['employee_code'],
                    'serial_number' => 1,
                    'classification_category' => $this->mapClassificationToCategory($workItemData['classification']),
                    'project_number' => $workItemData['project_code'] ?? null,
                    'line_code' => $workItemData['line_code'] ?? null,
                    'machine_number' => $workItemData['machine_code'] ?? null,
                    'branch_number' => null,
                    'working_code' => $workItemData['work_detail'],
                    'working_hours' => $workItemData['work_hour'],
                    'remarks' => $workItemData['remark_work'],
                ]);
    
                FacilityWorkResult::create([
                    'working_day' => $workItemData['work_day'],
                    'employee_code' => $workItemData['employee_code'],
                    'department_code' => null,
                    'work_division' => $workItemData['category'],
                    'working_hours' => $workItemData['work_hour'],
                    'overtime_hours' => 1,
                ]);
            }

            DB::commit();
    
            $request->session()->forget('workItems');
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function individualMonthlyReport (Request $request) {
        $rows = [];
        $weekMap = [
            0 => '日',
            1 => '月',
            2 => '火',
            3 => '水',
            4 => '木',
            5 => '金',
            6 => '土',
        ];

        if(count($request->query()) > 0){
            $year = substr($request->year_month, 0, 4);
            $month = substr($request->year_month, -2);
            $days = Carbon::now()->year($year)->month($month)->daysInMonth;

            for ($i = 1; $i <= $days; $i++) {
                $dayOfTheWeek = Carbon::now()->year($year)->month($month)->day($i)->dayOfWeek;
                $rows[] = [
                    'day' => $i,
                    'day_of_the_week' => $weekMap[$dayOfTheWeek]
                ];
            }

            if ($month == 1) {
                $prev_month = ($year - 1) . "12";
            } else {
                $prev_month = $year . ($month - 1);
            }

            if ($month == 12) {
                $next_month = ($year + 1) . "1";
            } else {
                $next_month = $year . ($month + 1);
            }
            
        }
        return view("pages.facility.individual-monthly-report", [
            'rows' => $rows,
            'prev_month' => $prev_month ?? '',
            'next_month' => $next_month ?? '',
        ]);
    }
    
}
