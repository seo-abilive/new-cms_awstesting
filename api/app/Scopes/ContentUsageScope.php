<?php

namespace App\Scopes;

use App\Core\Contract\Domain\Models\ContractCompany;
use App\Core\Contract\Domain\Models\ContractFacility;
use App\Core\Contract\Domain\FacilityAlias;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentUsageScope extends AbstractScope
{

    protected $companyAlias;
    protected $facilityAlias;

    public function apply(Builder $builder, Model $model): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (array_key_exists(get_class($model), $this->disabled) && $this->disabled[get_class($model)] === true) {
            return;
        }

        // 企業エイリアスで絞り込み
        if ($this->companyAlias && method_exists($model, 'company')) {
            $builder->whereHas('company', function ($query) {
                $query->where('alias', $this->companyAlias);
            });
        }

        // 施設エイリアスで絞り込み
        if ($this->facilityAlias) {
            switch (true) {
                case $this->facilityAlias === FacilityAlias::MASTER:
                    // 本部（master）の場合：contract_companyのaliasで絞り込み
                    if (method_exists($model, 'cms_company')) {
                        // MorphToManyリレーション：中間テーブル経由で自動的にフィルタリングされる
                        $builder->whereHas('cms_company', function ($query) {
                            $query->where('alias', $this->companyAlias);
                        });
                    } else if (method_exists($model, 'assignable')) {
                        // MorphToリレーション：モデルテーブルのassignable_typeでフィルタリング
                        $builder->where('assignable_type', ContractCompany::class)
                            ->whereHas('assignable', function ($query) {
                                $query->where('alias', $this->companyAlias);
                            });
                    }

                    break;
                default:
                    // その他の場合：contract_facilityのaliasで絞り込み
                    if (method_exists($model, 'cms_facilities')) {
                        // MorphToManyリレーション：中間テーブル経由で自動的にフィルタリングされる
                        $builder->whereHas('cms_facilities', function ($query) {
                            $query->where('alias', $this->facilityAlias);
                        });
                    } else if (method_exists($model, 'assignable')) {
                        // MorphToリレーション：モデルテーブルのassignable_typeでフィルタリング
                        $builder->where('assignable_type', ContractFacility::class)
                            ->whereHas('assignable', function ($query) {
                                $query->where('alias', $this->facilityAlias);
                            });
                    }
                    break;
            }
        }
    }

    /**
     * 企業エイリアスを設定
     */
    public function setCompanyAlias(string $companyAlias): self
    {
        $this->companyAlias = $companyAlias;
        return $this;
    }

    /**
     * 施設エイリアスを設定
     */
    public function setFacilityAlias(string $facilityAlias): self
    {
        $this->facilityAlias = $facilityAlias;
        return $this;
    }
}
