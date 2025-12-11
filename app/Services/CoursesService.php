<?php

namespace App\Services;

use App\Helpers\ResponseHelpers;
use App\Providers\Interfaces\ICoursesRepo;
use App\Providers\Interfaces\ICoursesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CoursesService implements ICoursesService
{
    private ICoursesRepo $coursesRepo;
    public function __construct(ICoursesRepo $coursesRepo){
        $this->coursesRepo = $coursesRepo;
    }
    public function getActiveCourses(): JsonResponse{
        try{
            $activeCourses = $this->coursesRepo->fetchActiveCourses();
            return ResponseHelpers::successResponse($activeCourses);

        }
        catch (\Exception $e){
            $message = 'Failed to get active courses. Please contact support.';
            Log::error($message, ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ResponseHelpers::errorResponse($message);

        }
    }

}