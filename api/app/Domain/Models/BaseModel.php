<?php

namespace App\Domain\Models;

use App\Domain\Models\Traits\AuditObservable;
use App\Scopes\ScopeLoader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    use HasFactory;
    use AuditObservable;

    protected $baseFillable = [
        'publish_at',
        'expires_at',
        'sort_num',
        'status'
    ];

    protected $baseCasts = [
        'publish_at' => 'datetime',
        'expires_at' => 'datetime',
        'sort_num' => 'int',
        'status' => 'boolean'
    ];

    protected $baseHidden = [
        'created_by',
        'updated_by',
        'free_search',
    ];

    protected $model_name = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = array_merge($this->fillable, $this->baseFillable);
        $this->casts = array_merge($this->casts, $this->baseCasts);
        $this->hidden = array_merge($this->hidden, $this->baseHidden);
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ScopeLoader());
    }

    public function orderBy(): array
    {
        return ['id' => 'asc'];
    }

    public function getName(): ?string
    {
        return $this->model_name;
    }

    public function getMaxSortNum(array $criteria = []): int
    {
        // クエリビルダを初期化
        $query = static::query();
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        $max = $query->max('sort_num');

        // null（レコードなし）の場合は 1 を返す
        return ($max ?? 0) + 1;
    }

    public function getMaxSeqId(array $criteria = [], mixed $model_id = null, ?int $companyId = null): int
    {
        // クエリビルダを初期化
        $query = static::query();
        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        if ($model_id) {
            $query->where('model_id', '=', $model_id);
        }
        $max = $query->max('seq_id');

        // null（レコードなし）の場合は 1 を返す
        return ($max ?? 0) + 1;
    }

    public function auditFilter(): array
    {
        return [];
    }
}
