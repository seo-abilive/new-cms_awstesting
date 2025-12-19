<?php

namespace App\Mod\ContactSetting\Actions\Front;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\ContactSetting\Domain\ContactSettingService as Domain;
use App\Mod\ContactSetting\Responder\Front\StoreResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class StoreAction extends AbstractFrontApiAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $token = $request->route('token');

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                $this->domain->storeSendMail($request, $token)
            ]
        ];
    }
}
