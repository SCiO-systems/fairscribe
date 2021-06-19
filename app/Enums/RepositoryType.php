<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * The repository types.
 */
final class RepositoryType extends Enum
{
    const CKAN = 'CKAN';
    const DKAN = 'DKAN';
    const DSpace = 'DSpace';
    const Dataverse = 'Dataverse';
    const GeoNetwork = 'GeoNetwork';
    const GeoNode = 'GeoNode';
}
