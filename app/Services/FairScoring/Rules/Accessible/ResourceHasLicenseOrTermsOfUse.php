<?php

namespace App\Services\FairScoring\Rules\Accessible;

use App\Services\FairScoring\Interfaces\FairScoreRule;
use App\Services\FairScoring\Rules\BaseRule;

class ResourceHasLicenseOrTermsOfUse extends BaseRule implements FairScoreRule
{
    public static $metadataCondition = '"resource has License or Terms of Use"';
    public static $scoring = '1';
    public static $recommendation = 'Specify the Licence or Terms of Use of the resource';

    public static function calculateScore($metadataRecord)
    {
        return self::meetsCondition($metadataRecord) ? 1 : 0;
    }

    public static function meetsCondition($metadataRecord)
    {
        $hasLicense = !empty(data_get($metadataRecord, 'rights.license'));
        $hasTermsOfUse = !empty(data_get($metadataRecord, 'rights.terms_of_use.0.value'));
        return $hasLicense || $hasTermsOfUse;
    }
}
