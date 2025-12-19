<?php

namespace App\Http\Middleware;

use App\Core\User\Domain\PermissionService;
use App\Core\Contract\Domain\FacilityAlias;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $resourceType リソースタイプ (content_model, contact_setting)
     */
    public function handle(Request $request, Closure $next, string $resourceType): Response
    {
        $permissionService = app(PermissionService::class);

        // リクエストメソッドから権限タイプを判定
        // 並び替えの場合は特別に処理
        $routeName = $request->route()->getName();
        $isSortRequest = strpos($routeName, '.sort') !== false && $request->method() === 'POST';
        $permission = $isSortRequest ? 'sort' : $this->getPermissionFromMethod($request->method());

        // リソースIDを取得
        $resourceId = null;
        $routeId = $request->route('id');

        // contentの場合は、model_nameからコンテンツモデルIDを取得
        if ($resourceType === 'content') {
            $modelName = $request->route('model_name');
            if ($modelName) {
                $companyAlias = $request->route('company_alias');
                $company = \App\Core\Contract\Domain\Models\ContractCompany::where('alias', $companyAlias)->first();
                if ($company) {
                    $contentModel = \App\Mod\ContentModel\Domain\Models\ContentModel::where('alias', $modelName)
                        ->where('company_id', $company->id)
                        ->first();
                    if ($contentModel) {
                        $resourceId = $contentModel->id;
                    }
                }
            }
        } elseif ($resourceType === 'contact_setting') {
            // お問い合わせ設定は機能レベルの権限なので、resource_idは常にnull
            $resourceId = null;
        } else {
            // content_modelの場合は、route('id')から取得
            $resourceId = $routeId ? (int)$routeId : null;
        }

        // 企業IDと施設IDを取得
        $companyId = null;
        $facilityId = null;

        $companyAlias = $request->route('company_alias');
        if ($companyAlias) {
            $company = \App\Core\Contract\Domain\Models\ContractCompany::where('alias', $companyAlias)->first();
            $companyId = $company?->id;
        }

        $facilityAlias = $request->route('facility_alias');
        if ($facilityAlias && $facilityAlias !== FacilityAlias::MASTER) {
            $facility = \App\Core\Contract\Domain\Models\ContractFacility::where('alias', $facilityAlias)->first();
            $facilityId = $facility?->id;
        }

        // 一覧取得（GET /）の場合は、特定のリソースIDがないので権限チェックをスキップ
        // フィルタリングはサービス側で行う
        // contact_settingの場合は、詳細ページでもresourceIdはnullなので、route('id')で判定
        $isListRequest = $request->method() === 'GET' && !$routeId;
        if ($resourceId === null && $isListRequest) {
            // 一覧取得の場合は権限チェックをスキップ（サービス側でフィルタリング）
            // ただし、企業スタッフ・施設スタッフの場合は、権限が全くない場合は403を返す
            $user = Auth::user();
            if ($user && in_array($user->user_type, [\App\Core\User\Domain\UserType::COMPANY, \App\Core\User\Domain\UserType::FACILITY])) {
                // contentの場合は、model_nameからコンテンツモデルIDを取得してチェック
                if ($resourceType === 'content') {
                    $modelName = $request->route('model_name');
                    if ($modelName && $companyId) {
                        $contentModel = \App\Mod\ContentModel\Domain\Models\ContentModel::where('alias', $modelName)
                            ->where('company_id', $companyId)
                            ->first();
                        if ($contentModel) {
                            // このコンテンツモデルへの権限をチェック
                            if (!$permissionService->checkPermission('content_model', $contentModel->id, 'read', $companyId, $facilityId)) {
                                abort(403, 'このコンテンツへのアクセス権限がありません。');
                            }
                        }
                    }
                } else {
                    $allowedIds = $permissionService->getAllowedResourceIds($resourceType, 'read', $companyId, $facilityId);
                    // 権限が全くない場合（空配列）は403を返す
                    if ($allowedIds !== null && empty($allowedIds)) {
                        abort(403, 'このリソースへのアクセス権限がありません。');
                    }
                }
            }
            return $next($request);
        }

        // 権限チェック
        // contentの場合は、content_modelに対する権限をチェック
        if ($resourceType === 'content') {
            if (!$resourceId) {
                abort(403, 'コンテンツモデルが見つかりません。');
            }
            if (!$permissionService->checkPermission('content_model', $resourceId, $permission, $companyId, $facilityId)) {
                abort(403, 'このコンテンツへのアクセス権限がありません。');
            }
        } elseif ($resourceType === 'contact_setting') {
            // お問い合わせ設定は機能レベルの権限なので、resource_idはnull
            if (!$permissionService->checkPermission('contact_setting', null, $permission, $companyId, $facilityId)) {
                abort(403, 'このお問い合わせ設定へのアクセス権限がありません。');
            }
        } else {
            if (!$permissionService->checkPermission($resourceType, $resourceId, $permission, $companyId, $facilityId)) {
                abort(403, 'このリソースへのアクセス権限がありません。');
            }
        }

        return $next($request);
    }

    /**
     * HTTPメソッドから権限タイプを取得
     */
    private function getPermissionFromMethod(string $method): string
    {
        return match (strtoupper($method)) {
            'GET' => 'read',
            'POST', 'PUT', 'PATCH' => 'write',
            'DELETE' => 'delete',
            default => 'read',
        };
    }
}
