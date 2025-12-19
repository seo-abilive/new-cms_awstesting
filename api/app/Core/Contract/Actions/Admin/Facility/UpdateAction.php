<?php

namespace App\Core\Contract\Actions\Admin\Facility;

use App\Http\Actions\BaseAction;
use App\Core\Contract\Domain\ContractFacilityService as Domain;
use App\Core\Contract\Responder\Admin\Facility\UpdateResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class UpdateAction extends BaseAction
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
                'data' => $this->domain->save($request, $id)
            ]
        ];
    }
}
