<?php

namespace App\Services\FairScoring\Rules\Interoperable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasOpenFormats extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = 'RESOURCE files use ONLY domain-relevant community open formats';
    public static $scoring = '3.5 points';
    public static $recommendation = 'Avoid using proprietary formats when possible';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 3.5 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        return true;
    }
}
