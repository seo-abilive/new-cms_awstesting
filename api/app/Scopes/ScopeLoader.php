<?php
namespace App\Scopes;

use App\Scopes\Collection\ScopeCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ScopeLoader extends AbstractScope
{
    protected $enabled = true;

    public function apply(Builder $builder, Model $model): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        if (array_key_exists(get_class($model), $this->disabled) && $this->disabled[get_class($model)] === true) {
            return;
        }

        /** @var ScopeCollection $collection */
        $collection = app(ScopeCollection::class);
        $collection->apply($builder, $model);
    }
}
