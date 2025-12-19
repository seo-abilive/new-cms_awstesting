<?php
namespace App\Http\Actions;

use App\Scopes\Collection\ScopeCollection;
use App\Scopes\CompanyScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractContentAction extends BaseAction
{

    public function __invoke(Request $request): Response
    {
        /** @var ScopeCollection $collection */
        $collection = app(ScopeCollection::class);

        $companyAlias = $request->route('company_alias');
        if (!$companyAlias) {
            abort(404);
        }

        // 企業スコープ
        $companyScope = new CompanyScope();
        $companyScope->setEnabled(true)->setCompanyAlias($companyAlias);
        $collection->addScope('company', $companyScope);

        // サービスコンテナに登録してBaseModelで使用できるようにする
        app()->instance(ScopeCollection::class, $collection);

        return parent::__invoke($request);
    }
}
