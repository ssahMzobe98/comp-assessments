<?php

namespace App\Providers\Interfaces\IBuilders;

use App\Providers\Interfaces\ILearnersRepo;

interface ILearnersMetaDataBuilder
{

    public function setLimit(int $limits = 10): ILearnersMetaDataBuilder;
    public function setLearnersData(): ILearnersMetaDataBuilder;
    public function build(): array;
    public function setOffset(int $offset = 0) :ILearnersMetaDataBuilder;
    public function setFilters(array $filters = []): ILearnersMetaDataBuilder;
    public function setCurrentPage(int $current_page = 1): ILearnersMetaDataBuilder;
    public function setlearnersRepo(ILearnersRepo $learnersRepo) : ILearnersMetaDataBuilder;

    public function getLastPage(): ILearnersMetaDataBuilder;
}