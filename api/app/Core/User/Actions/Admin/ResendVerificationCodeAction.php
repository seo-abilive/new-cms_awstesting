<?php

namespace App\Core\User\Actions\Admin;

use App\Http\Actions\BaseAction;
use App\Core\User\Domain\UserService as Domain;
use App\Core\User\Responder\Admin\ResendVerificationCodeResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ResendVerificationCodeAction extends BaseAction
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
            'payload' => $this->domain->resendVerificationCode($request)
        ];
    }
}

