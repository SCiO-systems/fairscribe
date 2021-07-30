<?php

namespace App\Services\FairScoring\Rules\Accessible;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasUrlsOfPhysicalFiles extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"Provide URLs of physical files"';
    public static $scoring = '2';
    public static $recommendation = 'Provide physical files or relevant URLs';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 2 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        $files = data_get($metadataRecord, 'dataCORE.resource_files');

        if (empty($files)) {
            return false;
        }

        foreach ($files as $file) {
            if (empty($file['path'])) {
                return false;
            }
        }

        return true;
    }
}
