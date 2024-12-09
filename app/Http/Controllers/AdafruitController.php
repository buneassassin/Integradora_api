<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdafruitService;
class AdafruitController extends Controller
{
    protected $adafruitService;

    public function __construct(AdafruitService $adafruitService)
    {
        $this->adafruitService = $adafruitService;
    }

    public function getFeedData($feedName)
    {
        $data = $this->adafruitService->getFeedData($feedName);
        return response()->json($data);
    }
}