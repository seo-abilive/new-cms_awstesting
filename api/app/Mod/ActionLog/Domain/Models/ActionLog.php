<?php

namespace App\Mod\ActionLog\Domain\Models;

use App\Core\User\Domain\Models\User;
use App\Domain\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $title
 * @property string $body
 * @property string $ip
 * @property string $user_agent
 * @property string $path
 * @property string $method
 * @property array $params
 * @property int $http_status
 * @property string|null $error
 * @property string|null $message
 * @property float|int|null $duration
 * @property int $user_id
 */
class ActionLog extends BaseModel
{
    use HasUlids;

    protected $table = "cms_action_log";
    protected $fillable = ['ip', 'user_agent', 'path', 'method', 'params', 'http_status', 'error', 'message', 'duration', 'user_id'];
    protected $model_name = 'action_log';

    protected $casts = [
        'params' => 'array'
    ];

    public function orderBy(): array
    {
        return ['created_at' => 'desc'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
