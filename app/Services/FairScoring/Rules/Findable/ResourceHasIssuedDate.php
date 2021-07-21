<?php

namespace App\Services\FairScoring\Rules\Findable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasIssuedDate extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"resource has ISSUED DATE"';
    public static $scoring = '0.25';
    public static $recommendation = 'Provide the Issued Date of the resource';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 0.25 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        if (!empty(data_get($metadataRecord, 'dataCORE.release_date'))) {
            return true;
        }
        return false;
    }
}
