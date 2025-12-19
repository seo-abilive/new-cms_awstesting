<?php

namespace App\Mod\Content\Actions\Admin;

use App\Http\Actions\BaseAction;
use App\Mod\Content\Responder\Admin\PreviewProxyResponder as Responder;
use App\Mod\Content\Domain\PreviewProxyService as Domain;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class PreviewProxyAction extends BaseAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        return $this->domain->fetchPreview($request);
    }
}

