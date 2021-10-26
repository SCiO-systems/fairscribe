<?php

namespace App\Services\FairScoring\Rules\Interoperable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasResourceMetadata extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"DATA is additionally linked to other data to provide context"';
    public static $scoring = '1';
    public static $recommendation = 'Provide in Metadata links from datasets to relevant publications or vice versa';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 1 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        $relatedResources = data_get($metadataRecord, 'related_resources');

        foreach ($relatedResources as $related) {
            if (empty($related['id'])) {
                return false;
            }
        }

        return true;
    }
}
