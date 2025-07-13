<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DateFormatValidatorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): JsonResponse
    {
        $reference = $request->reference;
        $format = $request->format;
        $value = $request->$reference;
        $checkPastDate = $request->checkPastDate;

        $phpFormat = match($format) {
            'YYYYMMDD' => 'Ymd',
            'YYYY-MM-DD' => 'Y-m-d',
            'YYYYMM' => 'Ym',
            'YYYYDD' => 'Yd',
            default => 'Ymd'
        };
        
        try {
            $date = Carbon::createFromFormat($phpFormat, $value);
            $now = Carbon::now()->startOfDay();
            if ($date->format($phpFormat) !== $value) {
                return Response::json(config("messages.validations.date_format"), 200);
            }
            if ($date->lt($now) && $checkPastDate == 'true') {
                return Response::json(config("messages.validations.past_date"), 200);
            }
            return Response::json(true, 200);
        } catch (\Exception $e) {
            return Response::json(false, 200);
        }
    }
}
