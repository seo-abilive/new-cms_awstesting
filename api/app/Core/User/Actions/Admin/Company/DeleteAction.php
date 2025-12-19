<?php

namespace App\Core\User\Actions\Admin\Company;

use App\Core\User\Domain\UserService as Domain;
use App\Core\User\Responder\Admin\Company\DeleteResponder as Responder;
use App\Http\Actions\AbstractContentAction;
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
