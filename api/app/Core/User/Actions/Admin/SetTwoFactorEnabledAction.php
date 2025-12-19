<?php

namespace App\Core\User\Actions\Admin;

use App\Http\Actions\BaseAction;
use App\Core\User\Domain\UserService as Domain;
use App\Core\User\Responder\Admin\SetTwoFactorEnabledResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class SetTwoFactorEnabledAction extends BaseAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $userId = (int) $request->route('id');
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => $this->domain->setTwoFactorEnabled($request, $userId)
        ];
    }
}

