<?php

namespace App\Traits;

use App\Providers\Interfaces\IBuilders\ILearnersMetaDataBuilder;

trait InitializeBuildersTraits
{
    protected ILearnersMetaDataBuilder $learnersMetaDataBuilder;
    public function initBuilders(): void
    {
        $this->learnersMetaDataBuilder = app(ILearnersMetaDataBuilder::class);
    }
}
