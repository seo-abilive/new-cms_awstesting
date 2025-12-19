<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 公開スコープ
 */
class PublicScope extends AbstractScope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (array_key_exists(get_class($model), $this->disabled) && $this->disabled[get_class($model)] === true) {
            return;
        }

        // ステータス
        $builder->where(function ($query) {
            $query->where('status', true)
                ->orWhere('status', null);
        });

        // 公開期間
        $builder->where(function ($query) {
            $query->whereNull('publish_at')
                ->orWhere('publish_at', '<=', now());
        });

        $builder->where(function ($query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>=', now());
        });
    }
}
