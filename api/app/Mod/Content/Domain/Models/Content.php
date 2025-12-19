<?php

namespace App\Mod\Content\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Domain\Models\BaseModel;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read \App\Mod\ContentModel\Domain\Models\ContentModel $model
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mod\Content\Domain\Models\ContentValue[] $values
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mod\Content\Domain\Models\ContentCategory[] $categories
 */
class Content extends BaseModel
{
    use HasUlids;

    protected $table = "cms_content";
    protected $fillable = [];
    protected $model_name = 'content';

    public function model(): BelongsTo
    {
        return $this->belongsTo(ContentModel::class, 'model_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ContentValue::class, 'content_id')->orderBy('sort_num', 'asc');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ContentCategory::class, 'cms_content_to_categories', 'content_id', 'category_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class, 'company_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo('assignable');
    }

    public function toFlatArray(): array
    {
        // created_by, updated_byを可視化（toArray()の前に呼ぶ必要がある）
        $this->makeVisible(['created_by', 'updated_by']);
        $flat = $this->toArray();

        foreach ($this->values as $value) {
            $flat = $value->formatValueForAdmin($flat);
        }

        unset($flat['values']); // values自体はいらなければ削除
        return $flat;
    }

    public function toFlatFrontArray(): array
    {
        /** @param ContentCategory $category */
        $categories = $this->categories->map(function ($category) {
            return $category->toFrontFlatArray();
        });

        // 企業、施設
        $assignable = $this->assignable->toArray();
        unset($assignable['id']);
        unset($assignable['publish_at']);
        unset($assignable['expires_at']);
        unset($assignable['sort_num']);
        unset($assignable['status']);
        unset($assignable['created_at']);
        unset($assignable['updated_at']);


        $flat = $this->toArray();
        $flat['id'] = $flat['seq_id'];
        $flat['categories'] = $categories;
        $flat['assignable'] = $assignable;

        unset($flat['seq_id']);
        unset($flat['model_id']);
        unset($flat['company_id']);
        unset($flat['assignable_type']);
        unset($flat['assignable_id']);

        foreach ($this->values as $value) {
            $flat = $value->formatValueForFront($flat);
        }

        unset($flat['values']);
        return $flat;
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc', 'created_at' => 'desc'];
    }

    public function auditFilter(): array
    {
        return ['model_id' => $this->model_id];
    }

    /**
     * Eager Loadingを使用してContentとその関連データを効率的に取得
     */
    public function scopeWithMedia($query)
    {
        // 階層ラッパーはValueとして保存されないため、values.fieldのEagerで十分
        return $query->with(['values.field']);
    }
}
