<?php

namespace App\Services\FairScoring\Rules\Findable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasAtLeastOneURL extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = 'RESOURCE has at least one URL defined in metadata';
    public static $scoring = '0.5 points ( =A )';
    public static $recommendation = '* This can be evaluated after publishing in a repository';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 0.5 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        return false;
    }
}
