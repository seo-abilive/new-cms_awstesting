<?php

namespace App\Core\Contract\Domain\Models;

use App\Casts\Encrypted;
use App\Core\User\Domain\Models\User;
use App\Domain\Models\BaseModel;
use App\Domain\Models\Traits\AuditObservable;
use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use App\Mod\Content\Domain\Models\Content;
use App\Mod\Content\Domain\Models\ContentCategory;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property string $company_name
 * @property string $alias
 * @property string $zip_code
 * @property text $address
 * @property string $phone
 * @property text $website
 * @property text $ai_api_key
 * @property datetime $publish_at
 * @property datetime $expires_at
 * @property int $sort_num
 * @property bool $status
 */
class ContractCompany extends BaseModel
{
    use AuditObservable;

    protected $table = "contract_company";
    protected $model_name = "contract_company";

    protected $fillable = [
        'company_name',
        'alias',
        'zip_code',
        'address',
        'phone',
        'website',
        'ai_api_key',
        'status',
    ];

    protected $casts = [
        'ai_api_key' => Encrypted::class,
    ];

    public function facilities(): HasMany
    {
        return $this->hasMany(ContractFacility::class)->orderBy('sort_num', 'asc');
    }

    public function users(): MorphToMany
    {
        return $this->morphToMany(User::class, 'assignable', 'user_assignments');
    }

    public function content_models(): MorphToMany
    {
        return $this->morphToMany(ContentModel::class, 'assignable', 'cms_content_model_assignments');
    }

    public function media_libraries(): MorphMany
    {
        return $this->morphMany(MediaLibrary::class, 'assignable', 'cms_media_library_assignments');
    }

    public function contact_settings(): MorphMany
    {
        return $this->morphMany(ContactSetting::class, 'assignable', 'cms_contact_setting_assignments');
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(Content::class, 'assignable', 'cms_content_assignments');
    }

    public function content_categories(): MorphMany
    {
        return $this->morphMany(ContentCategory::class, 'assignable', 'cms_content_category_assignments');
    }

    public function orderBy(): array
    {
        return ['sort_num' => 'asc'];
    }
}
