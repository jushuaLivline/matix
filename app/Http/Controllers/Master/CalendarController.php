<?php
namespace App\Http\Controllers\Master;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Calendar;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()) {
            $startDate = $request->start ?? Carbon::createFromDate($request->year, 1, 1)->toDateString();
            $endDate = $request->end ?? Carbon::createFromDate($request->year, 12, 31)->toDateString();

            $dataQuery = Calendar::whereBetween('calendar_date', [$startDate, $endDate])
            ->select(['id', 'calendar_date']);

            $data = $dataQuery->get();

            $total = clone $dataQuery;
            $totalHolidays = $total->count();    

            return response()->json([
                'data' => $data,
                'total_holidays' => $totalHolidays,
            ]);
        }

        return view('pages.master.calendar.index');
    }

    public function calendarOperations(Request $request)
    {
        switch ($request->type) {
            case 'create':
                $data = [];
                $dataWeekend = [];
                $displayedYear = $request->year;
                $currentYear = date('Y');

                if (!empty($request->selectedDates)) {
                    foreach ($request->selectedDates as $dateString) {
                        $date = Carbon::parse($dateString);

                        // for selected dates
                        $dateStringExists = DB::table('calendars')->where('calendar_date', $dateString)->exists();
                        if (!$dateStringExists) {
                            $data[] = [
                                'calendar_date' => $date,
                                'creator_code' => Auth::user()->id,
                                'created_at' => now(),
                            ];
                        }
                    }
                }

                $event = Calendar::insert($data);

                return response()->json($event);
                break;
            case 'delete':
                $event = Calendar::whereIn('id', $request->unselectedId)->delete();

                return response()->json($event);
                break;

            default:
                break;
        }
    }

    public function checkExists (Request $request)
    {
        $exists = Calendar::where('calendar_date', $request->date)->exists();

        if ($exists) {
            $record = Calendar::where('calendar_date', $request->date)->first();
            $dateId = $record->id;
        }

        $data = [
            'status' => $exists,
            'id' => $dateId ?? null,
        ];

        return response()->json($data);
    }
}