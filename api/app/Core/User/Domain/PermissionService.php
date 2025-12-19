<?php

namespace App\Core\User\Domain;

use App\Core\User\Domain\Models\User;
use App\Core\User\Domain\Models\UserFacilityStaffPermissions;
use App\Core\User\Domain\UserType;
use App\Core\Contract\Domain\FacilityAlias;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class PermissionService
{
    /**
     * 権限チェック
     *
     * @param string $resourceType リソースタイプ (content_model, contact_setting)
     * @param int|null $resourceId リソースID (contact_settingの場合はnull)
     * @param string $permission 権限 (read, write, delete)
     * @param int|null $companyId 企業ID
     * @param int|null $facilityId 施設ID
     * @return bool
     */
    public function checkPermission(
        string $resourceType,
        ?int $resourceId,
        string $permission,
        ?int $companyId = null,
        ?int $facilityId = null
    ): bool {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // システム管理者は全て許可
        if ($user->user_type === UserType::MASTER) {
            return true;
        }

        // 企業管理者は全て許可
        if ($user->user_type === UserType::MANAGE) {
            return true;
        }

        // 企業スタッフまたは施設スタッフの場合のみ権限チェック
        if ($user->user_type !== UserType::COMPANY && $user->user_type !== UserType::FACILITY) {
            return false;
        }

        // 企業IDと施設IDを取得
        if (!$companyId) {
            $user->load(['companies', 'facilities']);
            if ($user->user_type === UserType::COMPANY) {
                $companyId = $user->companies->first()?->id;
            } elseif ($user->user_type === UserType::FACILITY) {
                if (!$facilityId) {
                    $facilityId = $user->facilities->first()?->id;
                }
                if ($facilityId) {
                    $facility = \App\Core\Contract\Domain\Models\ContractFacility::find($facilityId);
                    $companyId = $facility?->company_id;
                }
            }
        }

        if (!$companyId) {
            return false;
        }

        // 権限を取得（グローバルスコープを無効化）
        $query = UserFacilityStaffPermissions::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('resource_type', $resourceType);

        // resource_idがnullの場合はwhereNullを使用
        if ($resourceId === null) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $resourceId);
        }

        if ($user->user_type === UserType::COMPANY) {
            $query->where('company_id', $companyId)
                ->whereNull('facility_id');
        } elseif ($user->user_type === UserType::FACILITY && $facilityId) {
            $query->where('facility_id', $facilityId)
                ->whereNull('company_id');
        }

        $permissionRecord = $query->first();

        if (!$permissionRecord) {
            return false;
        }

        // 権限をチェック
        $permissionField = 'permission_' . $permission;
        if (!$permissionRecord->$permissionField) {
            return false;
        }

        return true;
    }

    /**
     * スコープに基づいてフィルタリング可能かチェック
     *
     * @param string $resourceType リソースタイプ
     * @param int|null $resourceId リソースID
     * @param string $permission 権限 (read, write, delete)
     * @param int|null $companyId 企業ID
     * @param int|null $facilityId 施設ID
     * @return string|null 'own' または 'all'、権限がない場合はnull
     */
    public function getPermissionScope(
        string $resourceType,
        ?int $resourceId,
        string $permission,
        ?int $companyId = null,
        ?int $facilityId = null
    ): ?string {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // システム管理者と企業管理者は全て許可
        if ($user->user_type === UserType::MASTER || $user->user_type === UserType::MANAGE) {
            return 'all';
        }

        // 企業スタッフまたは施設スタッフの場合のみ権限チェック
        if ($user->user_type !== UserType::COMPANY && $user->user_type !== UserType::FACILITY) {
            return null;
        }

        // 企業IDと施設IDを取得
        if (!$companyId) {
            $user->load(['companies', 'facilities']);
            if ($user->user_type === UserType::COMPANY) {
                $companyId = $user->companies->first()?->id;
            } elseif ($user->user_type === UserType::FACILITY) {
                if (!$facilityId) {
                    $facilityId = $user->facilities->first()?->id;
                }
                if ($facilityId) {
                    $facility = \App\Core\Contract\Domain\Models\ContractFacility::find($facilityId);
                    $companyId = $facility?->company_id;
                }
            }
        }

        if (!$companyId) {
            return null;
        }

        // 権限を取得（グローバルスコープを無効化）
        $query = UserFacilityStaffPermissions::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('resource_type', $resourceType);

        // resource_idがnullの場合はwhereNullを使用
        if ($resourceId === null) {
            $query->whereNull('resource_id');
        } else {
            $query->where('resource_id', $resourceId);
        }

        if ($user->user_type === UserType::COMPANY) {
            $query->where('company_id', $companyId)
                ->whereNull('facility_id');
        } elseif ($user->user_type === UserType::FACILITY && $facilityId) {
            $query->where('facility_id', $facilityId)
                ->whereNull('company_id');
        }

        $permissionRecord = $query->first();

        if (!$permissionRecord) {
            return null;
        }

        // 権限をチェック
        $permissionField = 'permission_' . $permission;
        if (!$permissionRecord->$permissionField) {
            return null;
        }

        // スコープを返す
        $scopeField = 'permission_' . $permission . '_scope';
        return $permissionRecord->$scopeField ?? 'all';
    }

    /**
     * アクセス可能なリソースIDのリストを取得
     *
     * @param string $resourceType リソースタイプ
     * @param string $permission 権限
     * @param int|null $companyId 企業ID
     * @param int|null $facilityId 施設ID
     * @return array|null アクセス可能なリソースIDのリスト、全てアクセス可能な場合はnull
     */
    public function getAllowedResourceIds(
        string $resourceType,
        string $permission,
        ?int $companyId = null,
        ?int $facilityId = null
    ): ?array {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // システム管理者と企業管理者は全て許可
        if ($user->user_type === UserType::MASTER || $user->user_type === UserType::MANAGE) {
            return null; // nullは全てアクセス可能を意味する
        }

        // 企業スタッフまたは施設スタッフの場合のみ権限チェック
        if ($user->user_type !== UserType::COMPANY && $user->user_type !== UserType::FACILITY) {
            return [];
        }

        // 企業IDと施設IDを取得
        if (!$companyId) {
            $user->load(['companies', 'facilities']);
            if ($user->user_type === UserType::COMPANY) {
                $companyId = $user->companies->first()?->id;
            } elseif ($user->user_type === UserType::FACILITY) {
                if (!$facilityId) {
                    $facilityId = $user->facilities->first()?->id;
                }
                if ($facilityId) {
                    $facility = \App\Core\Contract\Domain\Models\ContractFacility::find($facilityId);
                    $companyId = $facility?->company_id;
                }
            }
        }

        if (!$companyId) {
            return [];
        }

        // 権限を取得（グローバルスコープを無効化）
        $query = UserFacilityStaffPermissions::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('resource_type', $resourceType);

        if ($user->user_type === UserType::COMPANY) {
            $query->where('company_id', $companyId)
                ->whereNull('facility_id');
        } elseif ($user->user_type === UserType::FACILITY) {
            if ($facilityId) {
                $query->where('facility_id', $facilityId)
                    ->whereNull('company_id');
            } else {
                // 施設IDが指定されていない場合は、ユーザーに紐づく全ての施設の権限を取得
                $user->load('facilities');
                $userFacilityIds = $user->facilities->pluck('id')->toArray();
                if (empty($userFacilityIds)) {
                    return [];
                }
                $query->whereIn('facility_id', $userFacilityIds)
                    ->whereNull('company_id');
            }
        }

        $permissions = $query->get();

        $allowedIds = [];
        foreach ($permissions as $permissionRecord) {
            $permissionField = 'permission_' . $permission;
            if ($permissionRecord->$permissionField) {
                if ($permissionRecord->resource_id !== null) {
                    $allowedIds[] = $permissionRecord->resource_id;
                } else {
                    // resource_idがnullの場合は、全てのリソースにアクセス可能（contact_settingなど）
                    return null;
                }
            }
        }

        return empty($allowedIds) ? [] : array_unique($allowedIds);
    }

    /**
     * 権限スコープをチェック（投稿オブジェクト用）
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $post Content または ContactSetting
     * @param string $resourceType リソースタイプ (content_model, contact_setting)
     * @param int|null $resourceId リソースID (content_modelの場合はmodel_id、contact_settingの場合はnull)
     * @param string $permission 権限 (read, write, delete)
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function checkPermissionScopeForPost(
        \Symfony\Component\HttpFoundation\Request $request,
        $post,
        string $resourceType,
        ?int $resourceId,
        string $permission
    ): void {
        $user = Auth::user();

        if (!$user || !in_array($user->user_type, [UserType::COMPANY, UserType::FACILITY])) {
            return;
        }

        $companyAlias = $request->route('company_alias');
        $company = \App\Core\Contract\Domain\Models\ContractCompany::where('alias', $companyAlias)->first();
        $companyId = $company?->id;

        $facilityId = null;
        $facilityAlias = $request->route('facility_alias');
        if ($facilityAlias && $facilityAlias !== FacilityAlias::MASTER) {
            $facility = \App\Core\Contract\Domain\Models\ContractFacility::where('alias', $facilityAlias)->first();
            $facilityId = $facility?->id;
        }

        if (!$companyId) {
            return;
        }

        $scope = $this->getPermissionScope(
            $resourceType,
            $resourceId,
            $permission,
            $companyId,
            $facilityId
        );

        // スコープが'own'の場合は、自分の投稿のみアクセス可能
        if ($scope === 'own' && $post->created_by !== $user->id) {
            $messages = [
                'read' => 'このリソースへのアクセス権限がありません。',
                'write' => 'このリソースを編集する権限がありません。',
                'delete' => 'このリソースを削除する権限がありません。',
            ];
            abort(403, $messages[$permission] ?? '権限がありません。');
        }
    }
}
