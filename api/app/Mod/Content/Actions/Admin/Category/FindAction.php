<?php

namespace App\Mod\Content\Actions\Admin\Category;

use App\Http\Actions\AbstractManageAction;
use App\Mod\Content\Domain\ContentCategoryService as Domain;
use App\Mod\Content\Responder\Admin\Category\FindResponder as Responder;
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
        $this->domain->setIsFlat(true);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $this->domain->findOneBy($request)
            ]
        ];
    }
}
