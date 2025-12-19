<?php

namespace App\Scopes;

use App\Core\User\Domain\PermissionService;
use App\Core\User\Domain\UserType;
use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * 権限スコープ
 * ユーザーの権限設定に基づいて、コンテンツやお問い合わせ設定のフィルタリングを行う
 */
class PermissionScope extends AbstractScope
{
    protected $resourceType;
    protected $resourceId;
    protected $permission;
    protected $companyId;
    protected $facilityId;

    public function apply(Builder $builder, Model $model): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (array_key_exists(get_class($model), $this->disabled) && $this->disabled[get_class($model)] === true) {
            return;
        }

        // ContentモデルまたはContactSettingモデルの場合のみ適用
        if (!$model instanceof Content && !$model instanceof ContactSetting) {
            return;
        }

        $user = Auth::user();
        if (!$user) {
            return;
        }

        // システム管理者と企業管理者は全て許可
        if ($user->user_type === UserType::MASTER || $user->user_type === UserType::MANAGE) {
            return;
        }

        // 企業スタッフまたは施設スタッフの場合のみスコープチェック
        if ($user->user_type !== UserType::COMPANY && $user->user_type !== UserType::FACILITY) {
            return;
        }

        if (!$this->resourceType || !$this->permission) {
            return;
        }

        // ContactSettingの場合はresource_idがnullでもOK
        if ($this->resourceType !== 'contact_setting' && !$this->resourceId) {
            return;
        }

        /** @var PermissionService $permissionService */
        $permissionService = app(PermissionService::class);
        $scope = $permissionService->getPermissionScope(
            $this->resourceType,
            $this->resourceId,
            $this->permission,
            $this->companyId,
            $this->facilityId
        );

        // スコープが'own'の場合は、自分の投稿のみをフィルタリング
        if ($scope === 'own') {
            $builder->where('created_by', $user->id);
        }
    }

    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;
        return $this;
    }

    public function setResourceId(?int $resourceId): self
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    public function setPermission(string $permission): self
    {
        $this->permission = $permission;
        return $this;
    }

    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;
        return $this;
    }

    public function setFacilityId(?int $facilityId): self
    {
        $this->facilityId = $facilityId;
        return $this;
    }
}
