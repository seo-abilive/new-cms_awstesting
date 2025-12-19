<?php

namespace App\Core\User\Actions\Admin\Permissions;

use App\Core\User\Domain\PermissionService;
use App\Core\Contract\Domain\FacilityAlias;
use App\Http\Actions\BaseAction;
use Symfony\Component\HttpFoundation\Request;

class CheckAction extends BaseAction
{
    public function __construct()
    {
        parent::__construct(null, null);
    }

    public function __invoke(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $permissionService = app(PermissionService::class);

        $resourceType = $request->query->get('resource_type');
        $permission = $request->query->get('permission'); // 単一の権限（後方互換性のため）
        $permissions = $request->query->get('permissions'); // 複数の権限（カンマ区切り）
        $resourceId = $request->query->get('resource_id'); // content_modelのIDなど
        $companyAlias = $request->query->get('company_alias');
        $facilityAlias = $request->query->get('facility_alias');

        $companyId = null;
        $facilityId = null;

        if ($companyAlias) {
            $company = \App\Core\Contract\Domain\Models\ContractCompany::where('alias', $companyAlias)->first();
            $companyId = $company?->id;
        }

        if ($facilityAlias && $facilityAlias !== FacilityAlias::MASTER) {
            $facility = \App\Core\Contract\Domain\Models\ContractFacility::where('alias', $facilityAlias)->first();
            $facilityId = $facility?->id;
        }

        // resource_idが指定されている場合は、そのIDを使用
        // 指定されていない場合は、resource_typeに応じてnullまたは取得
        $actualResourceId = null;
        if ($resourceId !== null) {
            $actualResourceId = (int)$resourceId;
        } elseif ($resourceType === 'contact_setting') {
            $actualResourceId = null; // お問い合わせ設定は機能レベルの権限
        } elseif ($resourceType === 'content') {
            // contentの場合は、model_nameからコンテンツモデルIDを取得
            $modelName = $request->query->get('model_name');
            if ($modelName && $companyId) {
                $contentModel = \App\Mod\ContentModel\Domain\Models\ContentModel::where('alias', $modelName)
                    ->where('company_id', $companyId)
                    ->first();
                if ($contentModel) {
                    $actualResourceId = $contentModel->id;
                    $resourceType = 'content_model'; // content_modelに対する権限をチェック
                }
            }
        }

        // 複数の権限をチェックする場合
        if ($permissions) {
            $permissionList = array_map('trim', explode(',', $permissions));
            $results = [];

            foreach ($permissionList as $perm) {
                $hasPermission = false;
                $scope = null;

                if ($resourceType === 'contact_setting') {
                    $hasPermission = $permissionService->checkPermission('contact_setting', null, $perm, $companyId, $facilityId);
                    // sort権限にはスコープがない
                    if ($perm !== 'sort') {
                        $scope = $permissionService->getPermissionScope('contact_setting', null, $perm, $companyId, $facilityId);
                    }
                } elseif ($resourceType === 'content_model' && $actualResourceId !== null) {
                    $hasPermission = $permissionService->checkPermission('content_model', $actualResourceId, $perm, $companyId, $facilityId);
                    // sort権限にはスコープがない
                    if ($perm !== 'sort') {
                        $scope = $permissionService->getPermissionScope('content_model', $actualResourceId, $perm, $companyId, $facilityId);
                    }
                }

                $results[$perm] = [
                    'has_permission' => $hasPermission,
                    'scope' => $scope,
                ];
            }

            return response()->json([
                'success' => true,
                'timestamp' => now()->timestamp,
                'payload' => $results,
            ]);
        }

        // 単一の権限をチェックする場合（後方互換性）
        $permission = $permission ?: 'read'; // デフォルトはread
        $hasPermission = false;
        $scope = null;

        $targetResourceType = $resourceType;
        if ($resourceType === 'contact_setting') {
            $hasPermission = $permissionService->checkPermission('contact_setting', null, $permission, $companyId, $facilityId);
            // sort権限にはスコープがない
            if ($permission !== 'sort') {
                $scope = $permissionService->getPermissionScope('contact_setting', null, $permission, $companyId, $facilityId);
            }
        } elseif ($resourceType === 'content_model' && $actualResourceId !== null) {
            $hasPermission = $permissionService->checkPermission('content_model', $actualResourceId, $permission, $companyId, $facilityId);
            // sort権限にはスコープがない
            if ($permission !== 'sort') {
                $scope = $permissionService->getPermissionScope('content_model', $actualResourceId, $permission, $companyId, $facilityId);
            }
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'has_permission' => $hasPermission,
                'scope' => $scope,
            ],
        ]);
    }
}
