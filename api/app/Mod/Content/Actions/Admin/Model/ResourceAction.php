<?php

namespace App\Mod\Content\Actions\Admin\Model;

use App\Http\Actions\AbstractManageAction;
use App\Mod\ContentModel\Domain\ContentModelService as Domain;
use App\Mod\Content\Responder\Admin\Model\ResoureceResponder as Responder;
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
