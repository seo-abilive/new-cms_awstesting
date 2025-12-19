<?php

namespace App\Mod\MediaLibrary\Actions\Admin;

use App\Http\Actions\AbstractManageAction;
use App\Mod\MediaLibrary\Domain\MediaLibraryService as Domain;
use App\Mod\MediaLibrary\Responder\Admin\ResourceResponder as Responder;
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
