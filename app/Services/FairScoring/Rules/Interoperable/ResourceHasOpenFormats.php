<?php

namespace App\Services\FairScoring\Rules\Interoperable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasOpenFormats extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"Make use ONLY of domain-relevant community open formats/standards"';
    public static $scoring = '2';
    public static $recommendation = 'Avoid using proprietary formats when possible';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 2 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        return true;
    }
}
