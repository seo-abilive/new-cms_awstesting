<?php
namespace App\Mod\ContentField\Domain\Models;

use App\Domain\Models\BaseModel;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $model_id
 * @property string $name
 * @property string $field_id
 */
class ContentCustomField extends BaseModel
{
    protected $table = "cms_content_custom_field";
    protected $fillable = ['model_id', 'name', 'field_id'];
    protected $model_name = 'content_custom_field';

    public function model(): BelongsTo
    {
        return $this->belongsTo(ContentModel::class, 'model_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ContentField::class, 'custom_field_id')
            ->where('is_top_field', false)
            ->whereNull('parent_block_id')
            ->orderBy('sort_num', 'asc');
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc'];
    }

    public function auditFilter(): array
    {
        return ['model_id' => $this->model_id];
    }

    public function toFlatArray(): array
    {
        $flat = $this->toArray();
        return $flat;
    }
}
