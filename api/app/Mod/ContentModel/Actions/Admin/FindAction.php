<?php

namespace App\Mod\ContentModel\Actions\Admin;

use App\Http\Actions\AbstractContentAction;
use App\Mod\ContentModel\Domain\ContentModelService as Domain;
use App\Mod\ContentModel\Responder\Admin\FindResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class FindAction extends AbstractContentAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $this->domain->findOneBy($request, ['fields.contentReference.fields'])
            ]
        ];
    }
}
