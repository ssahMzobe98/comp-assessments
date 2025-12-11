<?php

namespace App\Providers\Interfaces;

interface ILearnersRepo
{
    public function getLearnersProgress(int $limit = 10, int $offset = 0,array $filters = []): object;

    public function getLearnersProgressLastPage(int $limit = 10,array $filters = []): int;
}