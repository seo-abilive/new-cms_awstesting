<?php

namespace App\Mod\Content\Actions\Front\V1\Categories;

use App\Http\Actions\AbstractFrontApiAction;
use App\Mod\Content\Domain\FrontContentCategoryService as Domain;
use App\Mod\Content\Responder\Front\V1\Categories\ListResponder as Responder;
use Symfony\Component\HttpFoundation\Request;

/**
 * @property Domain $domain
 * @property Responder $responder
 */
class ListAction extends AbstractFrontApiAction
{
    public function __construct(Domain $domain, Responder $responder)
    {
        parent::__construct($domain, $responder);
        $this->domain->setIsFlat(true);
    }

    protected function callback(Request $request): array
    {
        $list = $this->domain->findAll($request, [], 'toFrontFlatArray');

        // contentsの件数を追加
        $data = collect($list['data'])->map(function ($category) {
            $category['contents_count'] = $category['contents_count'] ?? 0;
            return $category;
        })->toArray();

        return [
            'success' => true,
            'timestamp' => now()->timestamp,
            'contents' => $data
        ];
    }
}
