<?php

namespace App\Core\Contract\Domain;

use App\Domain\BaseService;
use App\Core\Contract\Domain\FacilityAlias;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\Validator;
use App\Core\Contract\Domain\Models\ContractFacility;

/**
 * @property ContractFacility $model
 */
class ContractFacilityService extends BaseService
{

    public function __construct(ContractFacility $model)
    {
        parent::__construct($model);
    }

    public function findList(Request $request, ?int $limit = null, array $with = [], string $flatMethod = 'toFlatArray'): array
    {
        if (!$limit) {
            $limit = config('contract_facility.list.limit');
        }

        $current = (int)$request->query->get('current', 1);
        $limit = (int)$request->query->get('limit', $limit);
        $userOrderBy = $this->getOrderByFromRequest($request);
        // ユーザーがソートを指定していない場合のみ、デフォルトのソート順を使用
        $orderBy = $userOrderBy ?? $this->model->orderBy();
        $isDefaultOrder = $userOrderBy === null;

        // companyテーブルのカラムでソートする場合はjoinが必要
        $needsJoin = $isDefaultOrder || ($userOrderBy && isset($userOrderBy['company_name']));

        $baseQuery = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        });

        // joinが必要な場合
        if ($needsJoin) {
            $baseQuery->join('contract_company', 'contract_facility.company_id', '=', 'contract_company.id')
                ->select('contract_facility.*');
        }

        if (method_exists($this, 'customizeListQuery')) {
            $this->customizeListQuery($request, $baseQuery);
        }

        // ソート処理
        $posts = $baseQuery
            ->when($isDefaultOrder, function ($query) use ($orderBy) {
                // デフォルトソートの場合：companyのsort_num → facilityのsort_num
                $query->orderBy('contract_company.sort_num', 'asc')
                    ->orderBy('contract_facility.sort_num', 'asc');
            })
            ->when(!$isDefaultOrder, function ($query) use ($orderBy, $needsJoin) {
                // ユーザーがソートを指定した場合：そのソート条件のみ適用
                foreach ($orderBy as $column => $direction) {
                    if ($column === 'company_name' && $needsJoin) {
                        // company_nameの場合はテーブル名を付けてソート
                        $query->orderBy('contract_company.company_name', $direction);
                    } else {
                        $query->orderBy($column, $direction);
                    }
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
        $userOrderBy = $this->getOrderByFromRequest($request);
        // ユーザーがソートを指定していない場合のみ、デフォルトのソート順を使用
        $orderBy = $userOrderBy ?? $this->model->orderBy();
        $isDefaultOrder = $userOrderBy === null;

        // companyテーブルのカラムでソートする場合はjoinが必要
        $needsJoin = $isDefaultOrder || ($userOrderBy && isset($userOrderBy['company_name']));

        $baseQuery = $this->model::where(function ($query) use ($request) {
            $criteria = $request->get('criteria', []);
            $this->appendCriteria($criteria, $query);
        });

        // joinが必要な場合
        if ($needsJoin) {
            $baseQuery->join('contract_company', 'contract_facility.company_id', '=', 'contract_company.id')
                ->select('contract_facility.*');
        }

        if (method_exists($this, 'customizeListQuery')) {
            $this->customizeListQuery($request, $baseQuery);
        }

        // ソート処理
        $posts = $baseQuery
            ->when($isDefaultOrder, function ($query) use ($orderBy) {
                // デフォルトソートの場合：companyのsort_num → facilityのsort_num
                $query->orderBy('contract_company.sort_num', 'asc')
                    ->orderBy('contract_facility.sort_num', 'asc');
            })
            ->when(!$isDefaultOrder, function ($query) use ($orderBy, $needsJoin) {
                // ユーザーがソートを指定した場合：そのソート条件のみ適用
                foreach ($orderBy as $column => $direction) {
                    if ($column === 'company_name' && $needsJoin) {
                        // company_nameの場合はテーブル名を付けてソート
                        $query->orderBy('contract_company.company_name', $direction);
                    } else {
                        $query->orderBy($column, $direction);
                    }
                }
            })
            ->with($with)
            ->get();

        return [
            'data' => $posts->map(fn($item) => method_exists($item, $flatMethod) && $this->isFlat ? $item->{$flatMethod}() : $item)->all(),
        ];
    }

    public function beforeSave(Request $request, ContractFacility $post, array &$inputs): void
    {
        $inputs['company_id'] = $request->input('company_id', [])['value'];
    }

    public function appendCriteria(?array $criteria = [], $query): void
    {
        if (isset($criteria['company_ids'])) {
            $query->whereIn('company_id', explode(',', $criteria['company_ids']));
        }
        unset($criteria['company_ids']);

        parent::appendCriteria($criteria, $query); // TODO: Change the autogenerated stub
    }

    public function validateRequest(Request $request, mixed $post = null): void
    {
        // 必要に応じてコメントアウト外し、バリデーションを追加する
        $validator = Validator::make(
            $request->all(),
            [
                "facility_name" => ['required'],
                "alias" => [
                    'required',
                    function ($attribute, $value, $fail) use ($request, $post) {
                        if (strtolower($value) === strtolower(FacilityAlias::MASTER)) {
                            $fail('「master」はエイリアスとして使用できません。');
                            return;
                        }
                        $companyId = $request->input('company_id', []);
                        $query = \App\Core\Contract\Domain\Models\ContractFacility::where('alias', $value);
                        if ($companyId) {
                            $query->where('company_id', $companyId['value']);
                        }
                        if ($post && $post->id) {
                            $query->where('id', '<>', $post->id);
                        }
                        if ($query->exists()) {
                            $fail('このエイリアスは既に使用されています。');
                        }
                    }
                ],
            ],
            [
                'facility_name.required' => '施設名は必須項目です。',
                'alias.required' => 'エイリアスは必須項目です。',
            ]
        );

        $validator->validate();
    }
}
