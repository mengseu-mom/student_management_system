<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    public function getHolidays($year)
    {
        $apiKey = env('HOLIDAY_API_KEY');

        $response = Http::get("https://holidayapi.com/v1/holidays", [
            'key' => $apiKey,
            'country' => 'KH',
            'year' => $year,
            'public' => true, // only public holidays
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to fetch holidays'], 500);
        }

        $data = $response->json();

        // Transform to { "YYYY-MM-DD": "Holiday Name" }
        $holidays = [];
        if (isset($data['holidays'])) {
            foreach ($data['holidays'] as $holiday) {
                $holidays[$holiday['date']] = $holiday['name'];
            }
        }

        return response()->json(['holidays' => $holidays]);
    }
}
