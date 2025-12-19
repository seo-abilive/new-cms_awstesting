<?php

namespace App\Mod\Content\Actions\Admin\Model;

use App\Http\Actions\AbstractManageAction;
use App\Mod\ContentModel\Domain\ContentModelService as Domain;
use App\Mod\Content\Responder\Admin\Model\FindResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class FindAction extends AbstractManageAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $post = $this->domain->findOneBy($request, ['fields.contentReference.fields']);
        $post = $this->domain->addCurrentContentCount($request, $post);

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $post
            ]
        ];
    }
}
