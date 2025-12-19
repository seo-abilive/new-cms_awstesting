<?php

namespace App\Core\User\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\Models\ContractFacility;
use App\Domain\Models\BaseModel;
use App\Domain\Models\Traits\AuditObservable;
use App\Scopes\ScopeLoader;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFacilityStaffPermissions extends BaseModel
{
    use AuditObservable;

    protected $table = "user_facility_staff_permissions";
    protected $model_name = "user_facility_staff_permissions";


    protected $fillable = [
        'resource_type',
        'resource_id',
        'user_id',
        'facility_id',
        'company_id',
        'permission_read',
        'permission_read_scope',
        'permission_write',
        'permission_write_scope',
        'permission_delete',
        'permission_delete_scope',
        'permission_sort',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(ContractFacility::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(ContractCompany::class);
    }
}
