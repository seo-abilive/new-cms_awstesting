<?php

namespace App\Mod\Content\Actions\Front\V1;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\Content\Domain\FrontContentService as Domain;
use App\Mod\Content\Responder\Front\V1\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
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
        $contents = $this->domain->findDetail($request, $id, ['categories', 'values.field.parentField'], 'toFlatFrontArray');

        // 前後の記事を取得
        $sibLings = $this->domain->findPreviousAndNext($request, $id);

        // 記事詳細の取得
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'contents' => $contents,
            'sibLings' => $sibLings
        ];
    }
}
