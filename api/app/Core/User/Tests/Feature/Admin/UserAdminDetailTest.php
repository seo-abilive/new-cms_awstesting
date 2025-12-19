<?php

namespace App\Core\User\Tests\Feature\Admin;

use App\Core\User\Domain\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class UserAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = User::factory()->create();

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
        return $this->getJson($this->getUrl('api.admin.user.detail', $params), $headers);
    }
}
