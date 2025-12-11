<?php

namespace App\Services;

use App\Builders\LearnersMetaDataBuilder;
use App\Helpers\ResponseHelpers;
use App\Providers\Interfaces\IBuilders\ILearnersMetaDataBuilder;
use App\Providers\Interfaces\ILearnersRepo;
use App\Providers\Interfaces\ILearnersService;
use App\Traits\InitializeBuildersTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LearnersService implements ILearnersService
{
    use InitializeBuildersTraits;
    private ILearnersRepo $learnersRepo;
    public function __construct(ILearnersRepo $learnersRepo){
        $this->learnersRepo = $learnersRepo;
        $this->initBuilders();

    }
    public function getLearnersProgress(Request $request): JsonResponse{
        try{
            $page = $request->get('page', 1);
            $page = max($page, 1);
            $limits = 5;
            $offset = ($page - 1) * $limits;
            $search = $request->get('search', '');
            $filterby = $request->get('filterby', '');
            $sortby = $request->get('sort_by', '');
            $sortDir = $request->get('sort_dir', 'asc');
            $filters = [
                'search' => $search,
                'filterby' => $filterby,
                'sort_by' => $sortby,
                'sort_dir' => $sortDir
            ];
            $data = $this->learnersMetaDataBuilder->setlearnersRepo($this->learnersRepo)
                    ->setLimit($limits)
                    ->setOffset($offset)
                    ->setFilters($filters)
                    ->setCurrentPage($page)
                    ->getLastPage()
                    ->setLearnersData()
                    ->build();

            return ResponseHelpers::successResponse($data['data'],$data['meta']);
        }
        catch (\Exception $e){
            $message = 'Failed to get learners progress. Please contact support.';
            Log::error(
                $message,
                [
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            );
            return ResponseHelpers::errorResponse($message);
        }
    }
}