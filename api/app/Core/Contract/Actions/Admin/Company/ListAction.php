<?php
namespace App\Core\Contract\Actions\Admin\Company;

use App\Http\Actions\BaseAction;
use App\Core\Contract\Domain\ContractCompanyService as Domain;
use App\Core\Contract\Responder\Admin\Company\ListResponder as Responder;
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
            'payload' => $this->domain->findList($request)
        ];
    }
}
