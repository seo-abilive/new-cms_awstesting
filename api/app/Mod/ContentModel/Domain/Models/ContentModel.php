<?php

namespace App\Mod\ContentModel\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\Models\ContractFacility;
use App\Domain\Models\BaseModel;
use App\Mod\ContentField\Domain\Models\ContentField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property string $api_header_key
 * @property string $description
 * @property bool $is_use_category
 * @property bool $is_use_status
 * @property bool $is_use_publish_period
 * @property int|null $max_content_count
 * @property string|null $webhook_url
 * @property bool $is_use_preview
 * @property string|null $preview_url
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $sort_num
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Core\Contract\Domain\Models\ContractCompany $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mod\ContentField\Domain\Models\ContentField[] $fields
 */
class ContentModel extends BaseModel
{

    protected $table = "cms_content_model";
    protected $fillable = ['title', 'company_id', 'max_content_count', 'webhook_url', 'is_use_preview', 'preview_url'];
    protected $model_name = 'content_model';

    protected $casts = [
        'is_use_category' => 'boolean',
        'is_use_status' => 'boolean',
        'is_use_publish_period' => 'boolean',
        'is_use_preview' => 'boolean',
        'max_content_count' => 'integer'
    ];



    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class, 'company_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ContentField::class, 'model_id')
            ->where('is_top_field', true)
            ->orderBy('sort_num')
        ;
    }

    public function cms_company(): MorphToMany
    {
        return $this->morphedByMany(ContractCompany::class, 'assignable', 'cms_content_model_assignments');
    }

    public function cms_facilities(): MorphToMany
    {
        return $this->morphedByMany(ContractFacility::class, 'assignable', 'cms_content_model_assignments');
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc'];
    }
}
