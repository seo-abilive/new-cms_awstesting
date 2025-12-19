<?php

namespace App\Mod\ContentModel\Domain\Models;

use App\Domain\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $model_id
 * @property string $markup_type
 * @property string $template_json
 * @property \Carbon\Carbon|null $publish_at
 * @property \Carbon\Carbon|null $expires_at
 * @property mixed $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Mod\ContentModel\Domain\Models\ContentModel $model
 */
class ContentModelMarkup extends BaseModel
{
    protected $table = "cms_content_model_markup";
    protected $fillable = ['model_id', 'markup_type', 'template_json'];

    public function model(): BelongsTo
    {
        return $this->belongsTo(ContentModel::class, 'model_id');
    }
}
