<?php

namespace App\Mod\ContactSetting\Actions\Admin;

use App\Http\Actions\AbstractManageAction;
use App\Mod\ContactSetting\Domain\ContactSettingService as Domain;
use App\Mod\ContactSetting\Responder\Admin\ListResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ListAction extends AbstractManageAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
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
