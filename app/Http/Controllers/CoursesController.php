<?php

namespace App\Http\Controllers;

use App\Providers\Interfaces\ICoursesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    private ICoursesService $coursesService;
    public function __construct(ICoursesService $coursesService){
        $this->coursesService = $coursesService;
    }
    public function getActiveCourses(Request $request): JsonResponse{
        return $this->coursesService->getActiveCourses();
    }
}
