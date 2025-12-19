<?php

namespace App\Mod\Content\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Domain\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $title
 * @property int $model_id
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $sort_num
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mod\Content\Domain\Models\Content[] $contents
 */
class ContentCategory extends BaseModel
{
    use HasUlids;

    protected $table = "cms_content_category";
    protected $fillable = ['title', 'alias', 'model_id', 'company_id', 'assignable_type', 'assignable_id'];
    protected $model_name = 'content_category';

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class, 'cms_content_to_categories', 'category_id', 'content_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class, 'company_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo('assignable');
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc'];
    }

    public function auditFilter(): array
    {
        return ['model_id' => $this->model_id];
    }

    public function getContentsCountAttribute(): int
    {
        return $this->contents()->count();
    }

    public function toFrontFlatArray(): array
    {
        $flat = $this->toArray();
        $flat['contents_count'] = $this->contents_count; // 件数を追加
        $flat['id'] = $flat['seq_id'];

        unset($flat['seq_id']);
        unset($flat['model_id']);
        unset($flat['company_id']);
        unset($flat['assignable_type']);
        unset($flat['assignable_id']);
        unset($flat['pivot']);

        return $flat;
    }
}
