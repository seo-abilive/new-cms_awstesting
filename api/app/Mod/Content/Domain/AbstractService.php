<?php

namespace App\Mod\Content\Domain;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\FacilityAlias;
use App\Domain\BaseService;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractService extends BaseService
{
    protected $contentModel;
    protected $isCategory = false;

    public function __construct($model)
    {
        parent::__construct($model);
    }

    /**
     * リストクエリからorderByを取得
     */
    protected function getOrderByFromRequest(Request $request): ?array
    {
        if ($this->isCategory) {
            return null;
        }

        $sort = $request->query->get('sort', null);
        $direction = $request->query->get('direction', null);
        if (!$sort || !$direction) {
            return $this->contentModel->orderBy();
        }

        $field = $this->contentModel->fields->firstWhere('field_id', $sort);
        if ($field) {
            return ['sort_value' => $direction];
        }

        return parent::getOrderByFromRequest($request);
    }

    /**
     * リストクエリをカスタマイズ
     */
    protected function customizeListQuery(Request $request, $query): void
    {
        if ($this->isCategory) {
            return;
        }

        $sort = $request->query->get('sort');
        $direction = $request->query->get('direction');

        if (!$sort || !$direction) {
            return;
        }

        $field = $this->contentModel->fields->firstWhere('field_id', $sort);
        if ($field) {
            $query->select('cms_content.*'); // 明示しておくと安心
            $query->selectSub(function ($q) use ($field) {
                $q->from('cms_content_value as cv')
                    ->select('cv.value')
                    ->whereColumn('cv.content_id', 'cms_content.id')
                    ->where('cv.field_id', $field->id);
            }, 'sort_value');
        }
    }

    protected function beforeSave($request, $post, &$inputs): void
    {
        // model idの追加
        $inputs['model_id'] = $this->contentModel->id;
    }

    /**
     * Content Modelをセット
     */
    protected function setContentModel(): void
    {
        // Content Modelの取得
        $route = Route::current();
        $modelName = $route->parameter('model_name');
        if (!$modelName) {
            abort(404);
        }

        $comapnyAndFacility = $this->findCompanyAndFacility(request());
        $company = $comapnyAndFacility['company'];
        $contentModel = ContentModel::where('alias', $modelName)->where('company_id', $company->id)->first();
        if (!$contentModel) {
            abort(404);
        }
        $this->contentModel = $contentModel;
    }

    /**
     * ヘッダートークンを使用してContentModelをセット
     */
    protected function setContentModelFromToken(): void
    {
        // ヘッダートークンの取得
        $headerCmsToken = request()->header('X-CMS-API-KEY');
        if (!$headerCmsToken) {
            abort(403, 'トークンがありません');
        }

        // ヘッダートークンを使用してContentModelを取得
        $contentModel = ContentModel::where('api_header_key', $headerCmsToken)->first();
        if (!$contentModel) {
            abort(403, 'トークンが間違っています');
        }

        $this->contentModel = $contentModel;
    }

    protected function appendCriteria($criteria = [], $query): void
    {
        // デフォルトでmodelでフィルター
        $query->where('model_id', $this->contentModel->id);

        // カテゴリ
        if (isset($criteria['category'])) {
            $query->whereHas('categories', function ($query) use ($criteria) {
                $query->where('seq_id', $criteria['category']);
            });
        }
        unset($criteria['category']);

        // contentValueのフィールド
        foreach ($criteria as $key => $value) {
            if ($value === null || $value === '') {
                unset($criteria[$key]);
                continue;
            }

            // field_idがkeyと一致するContentFieldを取得
            $field = $this->contentModel->fields->firstWhere('field_id', $key);
            if ($field) {
                $query->whereHas('values', function ($query) use ($field, $value) {
                    $query->where('field_id', $field->id)
                        ->where('value', $value);
                });
            }

            // 処理したキーは必ずunsetする
            unset($criteria[$key]);
        }

        parent::appendCriteria($criteria, $query);
    }

    protected function appendFacilityCriteria(&$criteria = [], $query): void
    {
        // facilityでフィルター
        if (isset($criteria['facility_alias'])) {

            $query->where(function ($query) use ($criteria) {
                // 複数の施設でフィルター
                $facilityAliases = explode(',', $criteria['facility_alias']);
                $isFirst = true;
                foreach ($facilityAliases as $facilityAlias) {
                    $facilityAlias = trim($facilityAlias);
                    if (empty($facilityAlias)) {
                        continue;
                    }

                    if ($facilityAlias === FacilityAlias::MASTER) {
                        // 本部（master）の場合はcompanyでフィルター
                        if ($isFirst) {
                            $query->where('assignable_type', ContractCompany::class);
                            $isFirst = false;
                        } else {
                            $query->orWhere('assignable_type', ContractCompany::class);
                        }
                    } else {
                        // 施設の場合はfacilityのaliasでフィルター
                        if ($isFirst) {
                            $query->whereHas('assignable', function ($query) use ($facilityAlias) {
                                $query->where('alias', $facilityAlias);
                            });
                            $isFirst = false;
                        } else {
                            $query->orWhereHas('assignable', function ($query) use ($facilityAlias) {
                                $query->where('alias', $facilityAlias);
                            });
                        }
                    }
                }
            });
        }

        unset($criteria['facility_alias']);
    }
}
