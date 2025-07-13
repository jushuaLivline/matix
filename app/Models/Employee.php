<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\PaginateWithLimit;
use Carbon\Carbon;

class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasModelUtility, PaginateWithLimit ;

    protected $guarded = [];

    protected $fillable = [
        'employee_code',
        'employee_name',
        'department_code',
        'password',
        'authorization_code',
        'mail_address',
        'purchasing_approval_request_email_notification_flag',
        'delete_flag',
        'creator_code',
        'updater_code',
        'remember_token',
    ];

    public function department(){
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function authority(){
        return $this->belongsTo(Authority::class, 'authorization_code', 'authorization_code');
    }

    public function get_employee_by_code($employee_code){
        return Employee::where('employee_code', $employee_code)->first();
    }

    public function scopeSearch($query, $filters)
    {
        // Apply direct filters when values exist
        $filterMappings = [
            'employee_code' => 'employee_code',     // db_field => input_field name
            'department_code' => 'department_code',  
            'authorization_code' => 'authorization_code',  
            'delete_flag' => 'delete_flag',  
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            'employee_name' => 'employee_name', // db_field => input_field name
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $filters[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            // 'arrival_day' => ['arrival_day_from', 'arrival_day_to'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            $fromDate = !empty($request[$fromKey]) ? Carbon::parse($filters[$fromKey])->startOfDay() : null;
            $toDate = !empty($request[$toKey]) ? Carbon::parse($filters[$toKey])->endOfDay() : null;
        
            if ($fromDate && $toDate) {
                $query->whereBetween($column, [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where($column, '>=', $fromDate);
            } elseif ($toDate) {
                $query->where($column, '<=', $toDate);
            }
        }

        // Apply numeric range filter for order number
        $numericRanges = [
            // 'incoming_flight_number' => ['start' => 'incoming_flight_number_start', 'end' => 'incoming_flight_number_end'],
        ];
        
        foreach ($numericRanges as $column => $rangeKeys) {
            $from = $filters[$rangeKeys['start']] ?? null;
            $to = $filters[$rangeKeys['end']] ?? null;

            if ($from !== null && $to !== null) {
                $query->whereBetween($column, [$from, $to]);
            } elseif ($from !== null) {
                $query->where($column, '>=', $from);
            } elseif ($to !== null) {
                $query->where($column, '<=', $to);
            }
        }
        
        return $query;

    }
}
