<?php

namespace App\Mod\Content\Domain\Models;

use App\Casts\DynamicContentValueCast;
use App\Domain\Models\BaseModel;
use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property int $content_id
 * @property int $field_id
 * @property string $block_seq_id
 * @property string $value
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $sort_num
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Mod\Content\Domain\Models\Content $content
 * @property-read \App\Mod\ContentField\Domain\Models\ContentField $field
 */
class ContentValue extends BaseModel
{
    use HasUlids;

    protected $table = "cms_content_value";
    protected $fillable = [
        'content_id',
        'field_id',
        'block_id',
        'block_seq_id',
        'value',
        'sort_num'
    ];
    protected $model_name = 'content_value';

    protected $casts = [
        'value' => DynamicContentValueCast::class
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ContentField::class, 'field_id');
    }

    public function customContentField(): BelongsTo
    {
        return $this->belongsTo(ContentField::class, 'custom_content_field_id');
    }

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    /**
     * 管理画面用に値を整形する
     */
    public function formatValueForAdmin(array $flat = []): array
    {
        $fieldType = $this->getFieldType();
        $fieldId = $this->getFieldId();

        switch ($fieldType) {
            case 'custom_block':
                $flat[$fieldId][] = [
                    'field_id' => $this->field_id,
                    'block_seq_id' => $this->block_seq_id,
                    'values' => [$this->field?->field_id => $this->value]
                ];
                break;
            case 'date':
                $flat[$fieldId] = $this->value ? $this->value->format('Y-m-d') : null;
                break;
            case 'media_image':
                // メディア取得
                $media = MediaLibrary::find($this->value);
                $flat[$fieldId] = $media ? $media->toArray() : null;
                break;
            case 'radio':
            case 'select':
                $field = $this->getFeild();
                $choices = $field?->choices;
                $selectedChoice = null;
                if (is_array($choices)) {
                    foreach ($choices as $choice) {
                        if (isset($choice['value']) && $choice['value'] == $this->value) {
                            $selectedChoice = $choice;
                            break;
                        }
                    }
                }
                $flat[$fieldId] = $selectedChoice;
                break;
            case 'checkbox':
                $field = $this->getFeild();
                $choices = $field?->choices;
                $flat[$fieldId] = array_filter($choices, function ($choice) {
                    return $choice['value'] == $this->value;
                });
                break;
            default:
                $flat[$fieldId] = $this->value;
                break;
        }

        return $flat;
    }

    /**
     * フロント用に値を整形する
     */
    public function formatValueForFront(array $flat = [], ?string $fieldType = null, ?string $fieldId = null, ?array $value = null): array
    {
        $fieldType = $fieldType ?? $this->getFieldType();
        $fieldId = $fieldId ?? $this->getFieldId();

        $value = $value ? $value : $this->toArray();

        if (!$fieldType || !$fieldId) {
            return $flat;
        }

        switch ($fieldType) {
            case 'custom_block':
                // カスタムブロック
                $blockData = [];
                $field = ContentField::where('id', $this->field_id)->first();
                $blockData = $this->formatValueForFront($blockData, $field?->field_type, $field?->field_id, $value);
                $flat[$fieldId][] = $blockData;
                break;
            case 'custom_field':
                // カスタムフィールド
                $fieldData = [];
                if (is_array($value['value'])) {
                    foreach ($value['value'] as $val) {
                        $field = ContentField::where('id', $val['custom_content_field_id'])->first();
                        $fieldData = $this->formatValueForFront($fieldData, $field?->field_type, $field?->field_id, $val);
                    }
                    $flat[$fieldId] = $fieldData;
                }
                break;
            case 'media_image':
            case 'media_file':
                // メディア取得
                /** @var MediaLibrary $media */
                $media = MediaLibrary::find($value['value']);
                $flat[$fieldId] = $media ? $media->toFrontFlatArray() : null;
                break;
            case 'media_image_multi':
                // 複数メディア取得
                $medias = collect($value['value'])
                    ->map(function ($id) {
                        return MediaLibrary::find($id);
                    })
                    ->filter();

                $flat[$fieldId] = $medias->map(function ($media) {
                    /** @var MediaLibrary $media */
                    return $media->toFrontFlatArray();
                });
                break;
            case 'date':
                $flat[$fieldId] = $value['value'] ? Carbon::parse($value['value'])->format('Y-m-d') : null;
                break;
            case 'content_reference':
                // コンテンツ参照取得
                $referencedContent = Content::where('id', $value['value'])->with(['values.field.parentField', 'values.field.contentReference', 'categories'])->first();
                $flat[$fieldId] = $referencedContent ? $referencedContent->toFlatFrontArray() : null;
                break;
            default:
                $flat[$fieldId] = $value['value'];
                break;
        }

        return $flat;
    }

    /**
     * メディア系のアイテムを取得する
     */
    public function mediaItems(Collection $values): array
    {
        $ids = [];
        foreach ($values as $value) {
            $ids = $value->getMediaIds(null, $ids);
        }

        $items = MediaLibrary::whereIn('id', $ids)->get();
        $mediaItems = [];
        foreach ($items as $item) {
            $mediaItems[$item->id] = $item;
        }
        return $mediaItems;
    }

    /**
     * 使用しているメディアのIDを取得する
     */
    public function getMediaIds(?string $fieldType = null, array $ids = [], ?array $value = null): array
    {
        $fieldType = $fieldType ?? $this->field?->field_type;
        switch ($fieldType) {
            case 'custom_field':
                foreach ($this->value as $value) {
                    $field = ContentField::where('id', $value['custom_content_field_id'])->first();
                    $ids = $this->getMediaIds($field?->field_type, $ids, $value);
                }
                break;
            case 'media_image':
            case 'media_file':
                $ids[] = $value ? $value['value'] : $this->value;
                break;
            case 'media_image_multi':
                $ids = array_merge($ids, $this->value);
                break;
            default:
                break;
        }

        return $ids;
    }

    protected function getFeild(): ?ContentField
    {
        return $this->field?->parentField ? $this->field?->parentField : $this->field;
    }

    protected function getFieldType(): ?string
    {
        return $this->field?->parentField ? $this->field?->parentField?->field_type : $this->field?->field_type;
    }

    protected function getFieldId(): ?string
    {
        return $this->field?->parentField ? $this->field?->parentField?->field_id : $this->field?->field_id;
    }
}
