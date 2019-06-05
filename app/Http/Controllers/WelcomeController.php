<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\City;
use App\Forecast;

class WelcomeController extends Controller
{
    public function sunshine(Request $request, $id)
    {
        $scope = [];

        $startMicroTime = microtime(true);

        $city = City::find($id);

        if (! $city) {
            $scope['error'] = 'city_not_found';

            return response()->json($scope);
        }

        // Historical longest period of sunny days in given city

        // The article on Gaps and islands problem was used:
        // https://www.sqlservercentral.com/articles/group-islands-of-contiguous-dates-sql-spackle

        /*
        SELECT MAX(c) max
        FROM (
            SELECT condition, COUNT(grp) c
            FROM (
                SELECT
                    *,  
                    ROW_NUMBER() OVER(ORDER BY date) - ROW_NUMBER() OVER(PARTITION BY condition ORDER BY date) grp
                FROM forecasts
                WHERE city_id = 1
            ) t1
            GROUP BY grp, condition
        ) t2
        WHERE condition = 'Sunny';
        */

        $longestSunshinePeriod = cache()->remember("longest_sunshine_{$city->id}", 86400, function() use ($city) {
            $query = 
                'SELECT MAX(c) max FROM ('
                .'SELECT condition, COUNT(grp) c FROM ('
                .'SELECT *, ROW_NUMBER() OVER(ORDER BY date) - ROW_NUMBER() OVER(PARTITION BY condition ORDER BY date) grp '
                .'FROM forecasts WHERE city_id = :city_id) t1 '
                .'GROUP BY grp, condition) t2 '
                .'WHERE condition = :condition';

            $max = DB::select($query, ['city_id' => $city->id, 'condition' => 'Sunny']);

            return $max[0]->max;
        });

        // Longest period in current month

        $monthMaxSunshinePeriod = cache()->remember("month_sunshine_{$city->id}", 86400, function() use ($city) {
            $startOfMonth = Carbon::today()->startOfMonth();

            $currentMonthForecasts = Forecast::where('city_id', $city->id)->
                where('condition', 'Sunny')->
                where('date', '>', $startOfMonth)->
                orderBy('date', 'desc')->
                get(['date']);

            $maxPeriod = 0;
            $count = 0;
            $prev = null;

            foreach ($currentMonthForecasts as $forecast) {
                if (! $prev) {
                    $count++;
                } elseif ($prev->diffInDays($forecast->date) == 1) {
                    $count++;
                } elseif ($prev->diffInDays($forecast->date) > 1) {
                    $count = 1;
                }

                if ($count > $maxPeriod) {
                    $maxPeriod = $count;
                }

                $prev = $forecast->date;
            }

            return $maxPeriod;
        });

        // Length of current period of sunshine

        $currentSunshinePeriod = cache()->remember("", 86400, function() use ($city) {
            $today = Carbon::today();

            $lastOvercastDay = Forecast::where('city_id', $city->id)->
                where('condition', '<>', 'Sunny')->
                orderBy('date', 'desc')->
                first(['date']);
            
            return $today->diffInDays($lastOvercastDay->date);
        });

        // Script execution time

        $finishMicroTime = microtime(true);
		$deltaMicroTime = round($finishMicroTime - $startMicroTime, 6); 
        
        $scope['longestSunshinePeriod'] = $longestSunshinePeriod;
        $scope['monthMaxSunshinePeriod'] = $monthMaxSunshinePeriod;
        $scope['currentSunshinePeriod'] = $currentSunshinePeriod;
        $scope['time'] = $deltaMicroTime;

		return response()->json($scope);
    }

    public function index(Request $request)
    {
        $scope = [];

        $cities = City::orderBy('name')->get();
        
        $scope['cities'] = $cities;

		return view('welcome', $scope);
    }
}
