<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Resource
 *
 * @property int $id
 * @property string $status
 * @property float $findable_score
 * @property float $accessible_score
 * @property float $interoperable_score
 * @property float $reusable_score
 * @property float $fair_scoring
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Collection[] $collections
 * @property-read int|null $collections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource query()
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereAccessibleScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereFairScoring($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereFindableScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereInteroperableScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereReusableScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Resource whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Resource extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'resources';

    public function collections()
    {
        return $this->belongsToMany(
            Collection::class,
            'collection_resources',
            'collection_id',
            'resource_id'
        );
    }
}
