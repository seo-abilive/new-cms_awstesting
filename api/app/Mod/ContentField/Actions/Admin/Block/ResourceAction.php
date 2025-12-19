<?php
namespace App\Mod\ContentField\Actions\Admin\Block;

use App\Http\Actions\BaseAction;
use App\Mod\ContentField\Domain\ContentCustomBlockService as Domain;
use App\Mod\ContentField\Responder\Admin\Block\ResourceResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ResourceAction extends BaseAction
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
            'payload' => $this->domain->findAll($request, ['customField', 'contentReference.fields'])
        ];
    }
}
