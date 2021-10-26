<?php

namespace App\Services\FairScoring\Rules\Interoperable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasProprietaryFormats extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = 'if not, RESOURCE files use formats that are proprietary, but can be recognized and used by freely available tools';
    public static $scoring = '2 points';
    public static $recommendation = 'Avoid using proprietary formats when possible';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 2 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        return false;
    }
}
