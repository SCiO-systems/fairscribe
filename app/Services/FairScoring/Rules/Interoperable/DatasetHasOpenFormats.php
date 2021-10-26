<?php

namespace App\Services\FairScoring\Rules\Interoperable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class DatasetHasOpenFormats extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = 'DATASET files use ONLY domain-relevant community open formats';
    public static $scoring = '2';
    public static $recommendation = 'Avoid using proprietary formats when possible';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 2 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        if (!empty($resource_files = data_get($metadataRecord, 'dataCORE.resource_files'))) {
            foreach ($resource_files as $file) {
                if (!array_key_exists('mime_type', $file) || empty($file['mime_type'])) {
                    return false;
                }
            }
        }
        return true;
    }
}
