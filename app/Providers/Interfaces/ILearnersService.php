<?php

namespace App\Providers\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ILearnersService
{
    public function getLearnersProgress(Request $request): JsonResponse;
}