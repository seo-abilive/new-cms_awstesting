<?php

namespace App\Core\User\Actions\Admin\Company;

use App\Http\Actions\AbstractContentAction;
use App\Core\User\Domain\UserService as Domain;
use App\Core\User\Responder\Admin\Company\DetailResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class DetailAction extends AbstractContentAction
{

    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $id = $request->route('id');
        $user = $this->domain->findDetail($request, $id, ['companies', 'facilities']);

        // 権限設定を取得
        $permissions = $this->domain->getUserPermissions($id);
        $user->permissions = $permissions;

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => [
                'data' => $user
            ]
        ];
    }
}
