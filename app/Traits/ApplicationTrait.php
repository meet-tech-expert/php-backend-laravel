<?php

namespace App\Traits;

use App\Models\Applications;
use Carbon\Carbon;

trait ApplicationTrait
{
    public function getApplicationsGraphData($startDate, $endDate)
    {
        $previousMonthStartDate = Carbon::parse($startDate)->subMonth(1)->format('Y-m-d');
        $previousMonthEndDate = Carbon::parse($endDate)->subMonth(1)->format('Y-m-d');

        $applications = Applications::selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(*) as application_count")->groupBy('date')->orderBy('created_at', 'ASC')->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();
        $applicationCount = collect($applications)->sum('application_count');
        $previousMonthApplicationCount = Applications::select('created_at')->whereDate('created_at', '>=', $previousMonthStartDate)->whereDate('created_at', '<=', $previousMonthEndDate)->count();

        $applicationDifference = $applicationCount - $previousMonthApplicationCount;
        $applicationDifference = $applicationDifference < 0 ? $applicationDifference : "+$applicationDifference";

        $period = Carbon::parse($startDate)->daysUntil($endDate);

        foreach ($period as $index => $date) {
            $dateFormatted = $date->format('Y-m-d');
            $applicationPeriods[$index] = $applications->firstWhere('date', $dateFormatted) ?? ['date' => $dateFormatted, 'application_count' => 0];
        }

        return [
            'previous_month_start_date' => $previousMonthStartDate,
            'previous_month_end_date' => $previousMonthEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'number_of_month_days' => Carbon::parse($startDate)->daysInMonth,
            'application_difference' => $applicationDifference,
            'application_count' => $applicationCount,
            'previous_month_application_count' => $previousMonthApplicationCount,
            'applications' => $applicationPeriods,
        ];
    }
}
