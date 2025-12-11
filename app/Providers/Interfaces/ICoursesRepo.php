<?php

namespace App\Providers\Interfaces;

interface ICoursesRepo
{

    public function fetchActiveCourses():array;
}