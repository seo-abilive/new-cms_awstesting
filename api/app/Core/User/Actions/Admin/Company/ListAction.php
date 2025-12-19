<?php

namespace App\Core\User\Actions\Admin\Company;

use App\Core\User\Domain\UserService as Domain;
use App\Core\User\Responder\Admin\Company\ListResponder as Responder;
use App\Http\Actions\AbstractContentAction;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ListAction extends AbstractContentAction
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
            'payload' => $this->domain->findList($request)
        ];
    }
}
