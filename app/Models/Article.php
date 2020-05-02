<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Laravel\Scout\Searchable;

/**
 * App\Models\Article
 *
 * @property string $id
 * @property string $title
 * @property string $article_id
 * @property string|null $article_parent_id
 * @property string|null $text
 * @property string|null $description
 * @property string|null $redirect
 * @property string|null $comment
 * @property int|null $contributor_id
 * @property string|null $contributor_username
 * @property \Illuminate\Support\Carbon $revision_time
 * @property string $sha1
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereArticleParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereContributorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereContributorUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereRedirect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereRevisionTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereSha1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Article withCacheCooldownSeconds($seconds = null)
 * @mixin \Eloquent
 */
class Article extends AbstractModel
{
    use Cachable, Searchable;

    protected $fillable = [
        'article_id',
        'article_parent_id',
        'title',
        'description',
        'comment',
        'contributor_id',
        'contributor_username',
        'redirect',
        'revision_time',
        'sha1',
        'text',
    ];

    protected $casts = [
        'revision_time' => 'datetime',
    ];

    public function searchableAs(): string
    {
        return 'articles';
    }

    public function toSearchableArray(): array
    {
        $a = $this->toArray();

        return [
            'title'       => $a['title'],
            'description' => $a['description'],
        ];
    }
}
