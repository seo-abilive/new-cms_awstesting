<?php

namespace App\Mod\ContentModel\Actions\Admin;

use App\Http\Actions\AbstractContentAction;
use App\Mod\ContentModel\Domain\ContentModelService as Domain;
use App\Mod\ContentModel\Responder\Admin\DeleteResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class DeleteAction extends AbstractContentAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $this->domain->delete($request, $id)
            ]
        ];
    }
}
