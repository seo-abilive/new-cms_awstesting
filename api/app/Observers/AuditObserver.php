<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * データ登録時共通処理
 */
class AuditObserver
{
    /**
     * 保存時
     */
    public function saving(Model $model): void
    {
        $modelName = (string)$model->getName();

        // seq_id
        if (config("{$modelName}.saving.seq_id", false) && \is_null($model->seq_id)) {
            $maxSeqId = $model->getMaxSeqId($model->auditFilter(), $model->id, $model->company_id ?? null);
            $model->seq_id = $maxSeqId;
        }

        // sort_num
        if (config("{$modelName}.saving.sort_num", true) && \is_null($model->sort_num)) {
            $maxSortNum = $model->getMaxSortNum($model->auditFilter());
            $model->sort_num = $maxSortNum;
        }

        // status
        if (config("{$modelName}.saving.status", true) && \is_null($model->status)) {
            $model->status = true;
        }

        // publish_at, expires_at
        if (config("{$modelName}.saving.publish_period", true)) {
            if (!\is_null($model->publish_at) && empty($model->publish_at)) {
                $model->publish_at = null;
            }
            if (!\is_null($model->expires_at) && empty($model->expires_at)) {
                $model->expires_at = null;
            }
        }

        // created_by, updated_by
        $user = auth()->user();
        if (config("{$modelName}.saving.created_by", true) && \is_null($model->created_by) && $user) {
            $model->created_by = $user->id;
        }
        if (config("{$modelName}.saving.updated_by", true) && $user) {
            $model->updated_by = $user->id;
        }

        // free_search登録処理
        if (config("{$modelName}.saving.update_free_search", true)) {
            $ignoreFields = ['password', 'free_search', 'created_by_roles', 'updated_by_roles', 'created_at', 'updated_at'];
            $modelData = $model->toArray();
            $freeSearch = $this->getFreeWordContents([], $ignoreFields, $modelData);
            $model->free_search = serialize($freeSearch);
        }
    }

    protected function getFreeWordContents(array $freeSearch = [], array $ignoreFields = [], array $modelData = []): array
    {
        foreach ($modelData as $key => $value) {
            if (is_array($value)) {
                $freeSearch = $this->getFreeWordContents($freeSearch, $ignoreFields, $value);
            } else if (is_string($value)) {
                if (in_array($key, $ignoreFields)) {
                    continue;
                }

                $freeSearch[] = $value;
            }
        }

        return $freeSearch;
    }
}
