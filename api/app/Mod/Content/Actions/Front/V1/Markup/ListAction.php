<?php

namespace App\Mod\Content\Actions\Front\V1\Markup;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\Content\Domain\FrontContentService as Domain;
use App\Mod\Content\Responder\Front\V1\Markup\ListResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ListAction extends AbstractFrontApiAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
    }

    protected function callback(Request $request): array
    {
        $mode = $request->input('mode', 'list');
        if ($mode === 'all') {
            $data = $this->domain->findMarkupAll($request, ['values.field.parentField', 'categories'], 'toFlatFrontArray');
        } else {
            $data = $this->domain->findMarkupList($request, null, ['values.field.parentField', 'categories'], 'toFlatFrontArray');
        }

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'contents' => $data['data']
        ];
    }
}
