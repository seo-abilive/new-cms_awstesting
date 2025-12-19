<?php

namespace App\Domain;

use App\Core\Contract\Domain\FacilityAlias;
use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\Models\ContractFacility;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
class BaseService
{
    protected $model = null;
    protected $isFlat = false;

    public function __construct(mixed $model)
    {
        $this->model = $model;
    }

    public function setIsFlat(bool $val): void
    {
        $this->isFlat = $val;
    }

    public function findList(Request $request, ?int $limit = 10, array $with = [], string $flatMethod = 'toFlatArray'): array
    {
        $current = (int)$request->query->get('current', 1);
        $limit = (int)$request->query->get('limit', $limit);
        $orderBy = $this->getOrderByFromRequest($request) ?? $this->model->orderBy();

        $baseQuery = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        });

        if (method_exists($this, 'customizeListQuery')) {
            $this->customizeListQuery($request, $baseQuery);
        }

        $posts = $baseQuery
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                foreach ($orderBy as $column => $direction) {
                    $query->orderBy($column, $direction);
                }
            })
            ->with($with)
            ->paginate($limit, ['*'], 'page', $current);

        return [
            'total' => $posts->total(),
            'current' => $posts->currentPage(),
            'pages' => $posts->lastPage(),
            'limit' => $posts->perPage(),
            'data' => array_map(fn($item) => method_exists($item, $flatMethod) && $this->isFlat ? $item->{$flatMethod}() : $item, $posts->items()),
        ];
    }

    public function findAll(Request $request, array $with = [], string $flatMethod = 'toFlatArray'): array
    {
        $orderBy = $this->getOrderByFromRequest($request) ?? $this->model->orderBy();
        $posts = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        })->with($with)
            ->when(!empty($orderBy), function ($query) use ($orderBy) {
                foreach ($orderBy as $column => $direction) {
                    $query->orderBy($column, $direction);
                }
            })
            ->get();

        return [
            'data' => $posts->map(fn($item) => method_exists($item, $flatMethod) && $this->isFlat ? $item->{$flatMethod}() : $item)->all(),
        ];
    }

    public function findDetail(Request $request, mixed $id, array $with = [], string $flatMethod = 'toFlatArray'): mixed
    {
        $post = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        })->with($with)->findOrFail($id);

        return method_exists($post, $flatMethod) && $this->isFlat ? $post->{$flatMethod}() : $post;
    }

    public function findOneBy(Request $request, array $with = [], string $flatMethod = 'toFlatArray'): mixed
    {
        $post = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        })->with($with)->first();

        return method_exists($post, $flatMethod) && $this->isFlat ? $post->{$flatMethod}() : $post;
    }

    public function getModel(): mixed
    {
        return $this->model;
    }

    public function save(?Request $request, mixed $id = null): mixed
    {
        return DB::transaction(function () use ($request, $id) {
            $modelName = $this->model;
            $post = $id ? $this->findDetail($request, $id) : new $modelName();
            $this->validateRequest($request, $post);

            $inputs = $request->request->all();

            // 保存前処理
            if (method_exists($this, 'beforeSave')) {
                $this->beforeSave($request, $post, $inputs);
            }

            foreach ($inputs as $key => $val) {
                $post->{$key} = $val !== '' && $val !== null ? $val : null;
            }
            $post->save();

            // 保存後処理
            if (method_exists($this, 'afterSave')) {
                $this->afterSave($request, $post, $id);
            }

            return $post;
        });
    }

    public function delete(Request $request, mixed $id = null): array
    {
        $model = $this->findDetail($request, $id);

        // 削除前処理
        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete($request, $model);
        }

        $model->delete();

        // 削除後処理
        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete($request, $model);
        }

        return [
            'id' => $model->id,
            'result' => true
        ];
    }

    public function sort(Request $request): array
    {
        return DB::transaction(function () use ($request) {
            $sortIds = $request->input('sort_ids', []);
            foreach ($sortIds as $key => $id) {
                $post = $this->findDetail($request, $id);
                $post->sort_num = ($key + 1);
                $post->save();
            }
            return ['result' => true];
        });
    }

    protected function appendCriteria(?array $criteria = [], $query): void
    {
        // フリー検索
        if (!empty($criteria['freeword'])) {
            // フリーワードをスペース（全角・半角）で分割し、AND条件でcontentsにLIKE検索をかける
            $freewords = preg_split('/[\s　]+/u', $criteria['freeword'], -1, PREG_SPLIT_NO_EMPTY);
            foreach ($freewords as $word) {
                $query->where('free_search', 'like', '%' . $word . '%');
            }
        }
        unset($criteria['freeword']);

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
    }

    protected function getOrderByFromRequest(Request $request): ?array
    {
        $sort = $request->query->get('sort', null);
        $direction = $request->query->get('direction', null);
        if (!$sort || !$direction) {
            return null;
        }

        return [$sort => $direction];
    }

    protected function validateRequest(Request $request, mixed $post = null): void
    {
        // Default does nothing. Override in subclass.
    }

    /**
     * 企業と施設を取得
     */
    protected function findCompanyAndFacility(Request $request): array
    {

        $companyAlias = $request->route('company_alias');
        $facilityAlias = $request->route('facility_alias');

        $company = ContractCompany::where('alias', $companyAlias)->first();
        if ($facilityAlias === FacilityAlias::MASTER) {
            $facility = $company;
        } else {
            $facility = ContractFacility::where('alias', $facilityAlias)->first();
        }

        if (!$company || !$facility) {
            abort(404);
        }

        return ['company' => $company, 'facility' => $facility];
    }
}
