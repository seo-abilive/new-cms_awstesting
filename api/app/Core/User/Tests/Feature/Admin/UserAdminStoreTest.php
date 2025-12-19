<?php

namespace App\Core\User\Tests\Feature\Admin;

use App\Core\User\Domain\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class UserAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        // 登録データ
        $inputData = User::factory()->make()->toArray();

        $testResponse = $this->apiExec([], $inputData);
        $testResponse->assertStatus(201);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'title' => $inputData['title']
                ]
            ]
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->postJson($this->getUrl('api.admin.user.store'), $data, $headers);
    }
}
