<?php

namespace App\Services\FairScoring\Rules\Reusable;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasCCBYNCLicense extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"use CC BY NC"';
    public static $scoring = '0.5';
    public static $recommendation = 'Use License wizard to select an appropriate license';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 0.5 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        $license = data_get($metadataRecord, 'rights.license');
        $usesCCBYNCLicense = $license === 'CC BY-NC 4.0';

        return $usesCCBYNCLicense;
    }
}
