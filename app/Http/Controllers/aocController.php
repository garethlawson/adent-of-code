<?php

namespace App\Http\Controllers;

use App\Services\aocDayOneService;

class aocController extends Controller
{
    /**
     * Puzzle one HTML
     *
     * @param aocDayOneService $service
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function aocDayOnePuzzleOne(aocDayOneService $service)
    {
        $service->findEasterBunnyHq1();
        $html = $service->getPathHtml();
        return view('aoc_day_one', compact('html'));
    }
}
