<?php

namespace App\Mod\ContentModel\Actions\Admin\Markup;

use App\Http\Actions\BaseAction;
use App\Mod\ContentModel\Domain\ContentModelMarkupService as Domain;
use App\Mod\ContentModel\Responder\Admin\Markup\ResourceResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ResourceAction extends BaseAction
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
            'payload' => $this->domain->findAll($request)
        ];
    }
}
