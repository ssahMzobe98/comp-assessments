<?php

namespace App\Providers;

use App\Builders\LearnersMetaDataBuilder;
use App\Providers\Interfaces\IBuilders\ILearnersMetaDataBuilder;
use App\Providers\Interfaces\ICoursesRepo;
use App\Providers\Interfaces\ICoursesService;
use App\Providers\Interfaces\ILearnersRepo;
use App\Providers\Interfaces\ILearnersService;
use App\Repositories\CoursesRepo;
use App\Repositories\LearnersRepo;
use App\Services\CoursesService;
use App\Services\LearnersService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $data = [
            ILearnersService::class => LearnersService::class,
            ILearnersRepo::class => LearnersRepo::class,
            ICoursesService::class => CoursesService::class,
            ICoursesRepo::class => CoursesRepo::class,
            ILearnersMetaDataBuilder::class => LearnersMetaDataBuilder::class,
        ];
        foreach ($data as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
