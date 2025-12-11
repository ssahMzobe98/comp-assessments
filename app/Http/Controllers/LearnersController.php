<?php

namespace App\Http\Controllers;

use App\Providers\Interfaces\ILearnersService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearnersController extends Controller
{
    private ILearnersService $learnersService;
    public function __construct(ILearnersService $learnersService)
    {
        $this->learnersService = $learnersService;
    }
    public  function getLearnersProgress(Request $request):JsonResponse{
        return $this->learnersService->getLearnersProgress($request);
    }
}
