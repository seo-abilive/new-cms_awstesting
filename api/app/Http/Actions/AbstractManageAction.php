<?php

namespace App\Http\Actions;

use App\Scopes\Collection\ScopeCollection;
use App\Scopes\ContentUsageScope;
use App\Scopes\PermissionScope;
use App\Core\Contract\Domain\FacilityAlias;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractManageAction extends BaseAction
{

    public function __invoke(Request $request): Response
    {
        /** @var ScopeCollection $collection */
        $collection = app(ScopeCollection::class);

        $companyAlias = $request->route('company_alias');
        $facilityAlias = $request->route('facility_alias');

        if (!$companyAlias || !$facilityAlias) {
            abort(404);
        }

        // コンテンツ使用スコープ
        $contentUsageScope = new ContentUsageScope();
        $contentUsageScope->setEnabled(true)->setCompanyAlias($companyAlias)->setFacilityAlias($facilityAlias);
        $collection->addScope('content_usage', $contentUsageScope);

        $company = \App\Core\Contract\Domain\Models\ContractCompany::where('alias', $companyAlias)->first();
        $companyId = $company?->id;

        $facilityId = null;
        if ($facilityAlias && $facilityAlias !== FacilityAlias::MASTER) {
            $facility = \App\Core\Contract\Domain\Models\ContractFacility::where('alias', $facilityAlias)->first();
            $facilityId = $facility?->id;
        }

        // 権限スコープ（contentのルートの場合）
        // 並び替え関連のルート（.resource, .sort）の場合はスコープを適用しない（全件表示）
        $routeName = $request->route()->getName();
        $isSortRelatedRoute = strpos($routeName, '.resource') !== false || strpos($routeName, '.sort') !== false;
        
        $modelName = $request->route('model_name');
        if ($modelName && $companyId && !$isSortRelatedRoute) {
            $contentModel = \App\Mod\ContentModel\Domain\Models\ContentModel::where('alias', $modelName)
                ->where('company_id', $companyId)
                ->first();

            if ($contentModel) {
                $permissionScope = new PermissionScope();
                $permissionScope->setEnabled(true)
                    ->setResourceType('content_model')
                    ->setResourceId($contentModel->id)
                    ->setPermission('read')
                    ->setCompanyId($companyId)
                    ->setFacilityId($facilityId);
                $collection->addScope('permission', $permissionScope);
            }
        }

        // 権限スコープ（contact_settingのルートの場合）
        // 並び替え関連のルート（.resource, .sort）の場合はスコープを適用しない（全件表示）
        if (strpos($routeName, '.contact_setting.') !== false && $companyId && !$isSortRelatedRoute) {
            $permissionScope = new PermissionScope();
            $permissionScope->setEnabled(true)
                ->setResourceType('contact_setting')
                ->setResourceId(null) // お問い合わせ設定は機能レベルの権限
                ->setPermission('read')
                ->setCompanyId($companyId)
                ->setFacilityId($facilityId);
            $collection->addScope('permission', $permissionScope);
        }

        // サービスコンテナに登録してBaseModelで使用できるようにする
        app()->instance(ScopeCollection::class, $collection);

        return parent::__invoke($request);
    }
}
