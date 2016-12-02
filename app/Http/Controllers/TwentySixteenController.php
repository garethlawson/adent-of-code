<?php

namespace App\Http\Controllers;

use App\Services\TwentySixteen\DayOneService;

class TwentySixteenController extends Controller
{
    /**
     * Puzzle one HTML
     *
     * @param DayOneService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aocDayOnePuzzleOne(DayOneService $service)
    {
        $service->findEasterBunnyHq1();
        $html = $service->getPathHtml();
        return view('aoc_day_one', compact('html'));
    }
}
