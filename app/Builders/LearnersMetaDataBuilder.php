<?php

namespace App\Builders;

use App\Providers\Interfaces\IBuilders\ILearnersMetaDataBuilder;
use App\Providers\Interfaces\ILearnersRepo;

class LearnersMetaDataBuilder implements ILearnersMetaDataBuilder
{

    private array $learnersData = [];
    private int $limits = 10;
    private int $offset = 0;
    private array $filters = [];
    private int $current_page = 1;
    private int $last_page = 1;
    private ILearnersRepo $learnersRepo;
    public function setLimit(int $limits = 10): ILearnersMetaDataBuilder{
        $this->limits = $limits;
        return $this;
    }
    public function setLearnersData(): ILearnersMetaDataBuilder{
        $this->learnersData = $this->learnersRepo->getLearnersProgress($this->limits,$this->offset,$this->filters)->toArray();
        return $this;
    }
    public function setOffset(int $offset = 0) :ILearnersMetaDataBuilder{
        $this->offset = $offset;
        return $this;
    }
    public function setFilters(array $filters = []): ILearnersMetaDataBuilder{
        $this->filters = $filters;
        return $this;
    }
    public function setCurrentPage(int $current_page = 1): ILearnersMetaDataBuilder{
        $this->offset = ($current_page - 1) * $this->limits;
        $this->current_page = $current_page;
        return $this;
    }
    public function setlearnersRepo(ILearnersRepo $learnersRepo) : ILearnersMetaDataBuilder{
        $this->learnersRepo = $learnersRepo;
        return $this;
    }
    public function getLastPage(): ILearnersMetaDataBuilder{
        $this->last_page = $this->learnersRepo->getLearnersProgressLastPage($this->limits,$this->filters);
        return $this;
    }
    public function build(): array
    {
        return [
            'data' => $this->learnersData,
            'meta' => [
                'current_page' => $this->current_page,
                'last_page' => $this->last_page
            ]
        ];
    }
}