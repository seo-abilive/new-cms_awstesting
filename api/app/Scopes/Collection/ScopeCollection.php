<?php
namespace App\Scopes\Collection;

use App\Scopes\AbstractScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * スコープコレクション
 */
class ScopeCollection
{
    protected $scopes = [];

    /**
     * スコープを適用
     */
    public function apply(Builder $builder, Model $model): Builder
    {
        foreach ($this->scopes as $scope) {
            $scopeClass = get_class($scope);
            if (!in_array($scopeClass, $builder->removedScopes())) {
                $scope->apply($builder, $model);
            }
        }

        return $builder;
    }

    /**
     * スコープを追加
     */
    public function addScope(string $name, AbstractScope $scope): void
    {
        $this->scopes[$name] = $scope;
    }

    /**
     * スコープが存在するかどうか
     */
    public function hasScope(string $name): bool
    {
        return isset($this->scopes[$name]);
    }

    /**
     * スコープを取得
     */
    public function getScope(string $name): ?AbstractScope
    {
        return $this->scopes[$name] ?? null;
    }

    /**
     * スコープを削除
     */
    public function removeScope(string $name): void
    {
        unset($this->scopes[$name]);
    }
}
