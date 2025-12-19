<?php

namespace App\Core\User\Domain\Models;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\Models\ContractFacility;
use App\Domain\Models\Traits\AuditObservable;
use App\Scopes\ScopeLoader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property datetime $publish_at
 * @property datetime $expires_at
 * @property int $sort_num
 * @property bool $status
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, AuditObservable;

    protected $table = "users";
    protected $model_name = "user";

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_master',
        'two_factor_enabled',
        'user_type',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_master' => 'boolean',
        'two_factor_enabled' => 'boolean',
        'status' => 'boolean',
    ];

    public function orderBy(): array
    {
        return ['id' => 'asc'];
    }

    public function getName(): string
    {
        return $this->model_name;
    }

    public function companies(): MorphToMany
    {
        return $this->morphedByMany(ContractCompany::class, 'assignable', 'user_assignments');
    }

    public function facilities(): MorphToMany
    {
        return $this->morphedByMany(ContractFacility::class, 'assignable', 'user_assignments');
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ScopeLoader());
    }
}
