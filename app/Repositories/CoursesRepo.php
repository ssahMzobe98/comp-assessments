<?php

namespace App\Repositories;

use App\Models\Course;
use App\Providers\Interfaces\ICoursesRepo;

class CoursesRepo implements ICoursesRepo
{
    public function fetchActiveCourses():array{
        return Course::select(['id','name'])->get()->toArray();
    }

}