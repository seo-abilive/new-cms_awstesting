<?php

namespace App\Mod\Content\Actions\Admin;

use App\Http\Actions\AbstractManageAction;
use App\Mod\Content\Domain\ContentService as Domain;
use App\Mod\Content\Responder\Admin\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class DetailAction extends AbstractManageAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $this->domain->findDetail($request, $id, ['categories', 'values.field.parentField'])
            ]
        ];
    }
}
