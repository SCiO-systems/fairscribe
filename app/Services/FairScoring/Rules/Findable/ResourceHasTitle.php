<?php

namespace App\Services\FairScoring\Rules\Findable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasTitle extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"resource has TITLE"';
    public static $scoring = '0.125';
    public static $recommendation = 'Provide a Resource Title';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 0.125 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        if (!empty($title_array = data_get($metadataRecord, 'title'))) {
            if (!empty(head($title_array)['value'])) {
                return true;
            }
        }
        return false;
    }
}
