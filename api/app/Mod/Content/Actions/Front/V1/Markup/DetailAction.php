<?php

namespace App\Mod\Content\Actions\Front\V1\Markup;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\Content\Domain\FrontContentService as Domain;
use App\Mod\Content\Responder\Front\V1\Markup\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder5
 */
class DetailAction extends AbstractFrontApiAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');

        // 記事詳細の取得
        $contents = $this->domain->findMarkupDetail($request, $id, ['categories', 'values.field.parentField'], 'toFlatFrontArray');

        // 記事詳細の取得
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'contents' => $contents['data'],
        ];
    }
}
