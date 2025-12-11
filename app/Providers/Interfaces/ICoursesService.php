<?php

namespace App\Providers\Interfaces;

use Illuminate\Http\JsonResponse;

interface ICoursesService
{
    public function getActiveCourses(): JsonResponse;
}