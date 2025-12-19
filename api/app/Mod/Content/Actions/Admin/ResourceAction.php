<?php

namespace App\Mod\Content\Actions\Admin;

use App\Http\Actions\AbstractManageAction;
use App\Mod\Content\Domain\ContentService as Domain;
use App\Mod\Content\Responder\Admin\ResourceResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ResourceAction extends AbstractManageAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
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
