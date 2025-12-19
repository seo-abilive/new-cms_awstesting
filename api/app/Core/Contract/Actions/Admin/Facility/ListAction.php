<?php

namespace App\Core\Contract\Actions\Admin\Facility;

use App\Http\Actions\BaseAction;
use App\Core\Contract\Domain\ContractFacilityService as Domain;
use App\Core\Contract\Responder\Admin\Facility\ListResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ListAction extends BaseAction
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
            'payload' => $this->domain->findList($request, null, ['company'])
        ];
    }
}
