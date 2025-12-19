<?php

namespace App\Mod\Content\Actions\Front\V1;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\Content\Domain\FrontContentService as Domain;
use App\Mod\Content\Responder\Front\V1\ListResponder as Responder;
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
            $list = $this->domain->findAll($request, ['values.field.parentField', 'categories', 'assignable'], 'toFlatFrontArray');
            return [
                'success' => true,
                'timestamp' => now()->timestamp,
                'contents' => $list['data']
            ];
        } else {
            $list = $this->domain->findList($request, null, ['values.field.parentField', 'categories', 'assignable'], 'toFlatFrontArray');
            return [
                'success' => true,
                'timestamp' => now()->timestamp,
                'all' => $list['total'],
                'current' => $list['current'],
                'limit' => $list['limit'],
                'pages' => $list['pages'],
                'contents' => $list['data']
            ];
        }
    }
}
