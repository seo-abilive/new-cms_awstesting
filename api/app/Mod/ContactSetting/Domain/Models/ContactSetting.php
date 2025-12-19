<?php

namespace App\Mod\ContactSetting\Domain\Models;

use App\Casts\Encrypted;
use App\Core\Contract\Domain\Models\ContractCompany;
use App\Domain\Models\BaseModel;
use App\Domain\Models\Traits\AuditObservable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $id
 * @property string $title
 * @property string $token
 * @property string $from_address
 * @property string $from_name
 * @property string $to_address
 * @property string $subject
 * @property string $body
 * @property boolean $is_return
 * @property string $return_field
 * @property string $return_subject
 * @property string $return_body
 * @property array $fields
 * @property boolean $is_recaptcha
 * @property string $recaptcha_site_key
 * @property string $recaptcha_secret_key
 * @property string $thanks_page
 * @property datetime $publish_at
 * @property datetime $expires_at
 * @property int $sort_num
 * @property string $status
 */
class ContactSetting extends BaseModel
{
    use AuditObservable;
    use HasUlids;

    protected $table = "cms_contact_setting";
    protected $fillable = [
        'title',
        'from_address',
        'from_name',
        'to_address',
        'subject',
        'body',
        'is_return',
        'return_field',
        'return_subject',
        'return_body',
        'fields',
        'is_recaptcha',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'thanks_page',
        'assignable_type',
        'assignable_id',
    ];

    protected $casts = [
        'is_return' => 'boolean',
        'fields' => 'array',
        'is_recaptcha' => 'boolean',
        'recaptcha_site_key' => Encrypted::class,
        'recaptcha_secret_key' => Encrypted::class,
    ];

    protected $model_name = "contact_setting";

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
        return $this->toArray();
    }
}
