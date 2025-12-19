<?php

namespace App\Mod\ContactSetting\Actions\Admin;

use App\Http\Actions\AbstractManageAction;
use App\Mod\ContactSetting\Domain\ContactSettingService as Domain;
use App\Mod\ContactSetting\Responder\Admin\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class DetailAction extends AbstractManageAction
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
                'data' => $this->domain->findDetail($request, $id)
            ]
        ];
    }
}
