<?php

namespace App\Core\User\Actions\Admin\Permissions\Function;

use App\Http\Actions\BaseAction;
use App\Core\User\Responder\Admin\Permissions\Function\ResourceResponder as Responder;
use App\Mod\ContentModel\Domain\ContentModelService as Domain;
use Symfony\Component\HttpFoundation\Request;

class ResourceAction extends BaseAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
    }

    protected function callback(Request $request): array
    {
        $data = $this->domain->findAll($request);

        // resource_typeを追加
        $data['data'] = \array_map(function ($item) {
            $item = $item->toArray();
            $item['resource_type'] = 'content_model';
            return $item;
        }, $data['data']);

        // お問い合わせ設定追加
        $data['data'][] = [
            'id' => null,
            'title' => 'お問い合わせ設定',
            'resource_type' => 'contact_setting',
        ];

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'payload' => $data
        ];
    }
}
