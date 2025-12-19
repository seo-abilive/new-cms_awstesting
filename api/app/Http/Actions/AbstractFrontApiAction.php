<?php
namespace App\Http\Actions;

use App\Scopes\Collection\ScopeCollection;
use App\Scopes\PublicScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractFrontApiAction extends BaseAction
{

    public function __invoke(Request $request): Response
    {
        /** @var ScopeCollection $collection */
        $collection = app(ScopeCollection::class);

        // 表示
        $publicScope = new PublicScope();
        $publicScope->setEnabled(true);
        $collection->addScope('public', $publicScope);

        // サービスコンテナに登録してBaseModelで使用できるようにする
        app()->instance(ScopeCollection::class, $collection);

        return parent::__invoke($request);
    }
}
