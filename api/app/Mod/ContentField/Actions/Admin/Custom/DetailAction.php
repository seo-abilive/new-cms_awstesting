<?php

namespace App\Mod\ContentField\Actions\Admin\Custom;

use App\Http\Actions\BaseAction;
use App\Mod\ContentField\Domain\ContentCustomFieldService as Domain;
use App\Mod\ContentField\Responder\Admin\Custom\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class DetailAction extends BaseAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');
        $this->domain->setIsFlat(true);
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $this->domain->findDetail($request, $id, ['fields.contentReference.fields'])
            ]
        ];
    }
}
