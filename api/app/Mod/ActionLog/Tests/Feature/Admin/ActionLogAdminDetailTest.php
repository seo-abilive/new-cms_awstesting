<?php

namespace App\Mod\ActionLog\Tests\Feature\Admin;

use App\Mod\ActionLog\Domain\Models\ActionLog;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ActionLogAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = ActionLog::factory()->create();

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'title' => $post->title
                ]
            ]
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->getJson($this->getUrl('api.admin.action_log.detail', $params), $headers);
    }
}
