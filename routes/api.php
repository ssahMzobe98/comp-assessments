<?php

use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LearnersController;
use Illuminate\Support\Facades\Route;

Route::get('/learner-progress',[LearnersController::class,'getLearnersProgress']);
Route::get('/active-courses/list',[CoursesController::class,'getActiveCourses']);
Route::get('/learner-courses/{learner_id}',[LearnersController::class,'getLearnerCourses']);
Route::get('/learner-progress/{learner_id}/{course_id}',[LearnersController::class,'getLearnerCourseProgress']);

