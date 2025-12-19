<?php
namespace App\Mod\ContentField\Actions\Admin;

use App\Http\Actions\BaseAction;
use App\Mod\ContentField\Domain\ContentFieldService as Domain;
use App\Mod\ContentField\Responder\Admin\ListResponder as Responder;
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
        $this->domain->setIsFlat(true);
        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => $this->domain->findList($request, null, ['customField', 'contentReference'])
        ];
    }
}
