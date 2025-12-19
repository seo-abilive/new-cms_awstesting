<?php

namespace App\Core\Contract\Domain\Models;

use App\Core\User\Domain\Models\User;
use App\Domain\Models\BaseModel;
use App\Domain\Models\Traits\AuditObservable;
use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use App\Mod\Content\Domain\Models\Content;
use App\Mod\Content\Domain\Models\ContentCategory;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class ContractFacility extends BaseModel
{
    use AuditObservable;

    protected $table = "contract_facility";
    protected $model_name = "contract_facility";

    protected $fillable = [
        'company_id',
        'facility_name',
        'alias',
        'zip_code',
        'address',
        'phone',
        'website',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class);
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
