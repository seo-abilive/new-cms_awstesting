<?php

namespace App\Mod\MediaLibrary\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Domain\Models\BaseModel;
use App\Domain\Models\Traits\AuditObservable;
use App\Mod\Content\Domain\Models\ContentValue;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $file_name
 * @property string $file_path
 * @property string $file_url
 * @property string $mime_type
 * @property int $file_size
 * @property string $image_size
 * @property string $alt_text
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property int|null $sort_num
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Mod\Content\Domain\Models\ContentValue[] $contentValues
 */
class MediaLibrary extends BaseModel
{
    use AuditObservable;
    use HasUlids;

    protected $table = "cms_media_library";
    protected $fillable = [
        'file_name',
        'file_path',
        'file_url',
        'mime_type',
        'file_size',
        'image_size',
        'alt_text',
        'assignable_type',
        'company_id',
        'assignable_id',
        'contentValues'
    ];
    protected $model_name = "media_library";

    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class, 'company_id');
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo('assignable');
    }

    /**
     * このメディアを参照している ContentValue（メディア系フィールドのみ）
     * value カラムに media_library.id が入っている前提
     */
    public function contentValues(): HasMany
    {
        return $this->hasMany(ContentValue::class, 'value', 'id')
            ->whereHas('field', function ($q) {
                $q->whereIn('field_type', ['media_image', 'media_file']);
            });
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc', 'id' => 'desc'];
    }

    public function toFrontFlatArray(): array
    {
        $flat = $this->toArray();
        unset($flat['id']);
        unset($flat['company_id']);
        unset($flat['assignable_type']);
        unset($flat['assignable_id']);
        return $flat;
    }
}
