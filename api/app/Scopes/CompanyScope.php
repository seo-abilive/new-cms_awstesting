<?php

namespace App\Scopes;

use App\Core\Contract\Domain\Models\ContractCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 企業スコープ
 */
class CompanyScope extends AbstractScope
{
    protected $companyAlias;

    public function apply(Builder $builder, Model $model): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (array_key_exists(get_class($model), $this->disabled) && $this->disabled[get_class($model)] === true) {
            return;
        }

        // ContractCompanyモデルの場合は、aliasで直接フィルタリング
        if ($model instanceof ContractCompany) {
            $builder->where('alias', $this->companyAlias);
            return;
        }

        // その他のモデルの場合は、companyリレーション経由でフィルタリング
        if (method_exists($model, 'company')) {
            $builder->whereHas('company', function ($query) {
                $query->where('alias', $this->companyAlias);
            });
        } else if (method_exists($model, 'companies')) {
            $builder->whereHas('companies', function ($query) {
                $query->where('alias', $this->companyAlias);
            });
        }

        return;
    }

    public function setCompanyAlias(string $companyAlias): self
    {
        $this->companyAlias = $companyAlias;
        return $this;
    }
}
