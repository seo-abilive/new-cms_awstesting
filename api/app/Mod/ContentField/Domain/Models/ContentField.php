<?php
namespace App\Mod\ContentField\Domain\Models;

use App\Domain\Models\BaseModel;
use App\Mod\Content\Domain\Models\ContentValue;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $model_id
 * @property bool $is_top_field
 * @property int $parent_block_id
 * @property int $custom_field_id
 * @property string $name
 * @property string $field_id
 * @property string $field_type
 * @property bool $is_required
 * @property bool $is_list_heading
 * @property array $choices
 * @property string $placeholder
 * @property string $help_text
 * @property int $content_reference_id
 * @property array $validates
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Mod\ContentModel\Domain\Models\ContentModel $model
 */
class ContentField extends BaseModel
{
    protected $table = "cms_content_field";
    protected $fillable = [
        'model_id',
        'is_top_field',
        'parent_block_id',
        'custom_field_id',
        'name',
        'field_id',
        'field_type',
        'is_required',
        'is_list_heading',
        'choices',
        'placeholder',
        'help_text',
        'content_reference_id',
        'show_when',
        'validates',
    ];
    protected $model_name = 'content_field';

    protected $casts = [
        'is_top_field' => 'boolean',
        'is_list_heading' => 'boolean',
        'is_required' => 'boolean',
        'choices' => 'array',
        'show_when' => 'array',
        'validates' => 'array',
    ];

    // 所属モデル
    public function model(): BelongsTo
    {
        return $this->belongsTo(ContentModel::class, 'model_id');
    }

    // 参照モデル
    public function contentReference(): BelongsTo
    {
        return $this->belongsTo(ContentModel::class, 'content_reference_id');
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(ContentCustomField::class, 'custom_field_id');
    }

    public function childrenBlock(): HasMany
    {
        return $this->hasMany(ContentField::class, 'parent_block_id')->orderBy('sort_num', 'asc');
    }

    public function parentField(): BelongsTo
    {
        return $this->belongsTo(ContentField::class, 'parent_block_id');
    }


    public function values(): HasMany
    {
        return $this->hasMany(ContentValue::class, 'field_id');
    }

    public function valueForContent(int $contentId, int $fieldId, ?int $customFieldId = null): ?ContentValue
    {
        return $this->values()
            ->getRelated()
            ->where('content_id', $contentId)
            ->where('field_id', $fieldId)
            ->where('custom_content_field_id', $customFieldId)
            ->first();
    }


    public function toFlatArray(): array
    {
        $flat = $this->toArray();

        // カスタムブロック
        if (!empty($flat['children_block'])) {
            foreach ($flat['children_block'] as &$block) {
                // ラベルをセット
                $block['label'] = $block['custom_field'] ? $block['custom_field']['name'] : '';
                unset($block['custom_field']);
            }
        }

        return $flat;
    }

    public function auditFilter(): array
    {
        return ['model_id' => $this->model_id, 'is_top_field' => true];
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc'];
    }
}
