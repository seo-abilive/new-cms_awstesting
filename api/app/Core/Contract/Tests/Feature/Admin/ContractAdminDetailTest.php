<?php

namespace App\Core\Contract\Tests\Feature\Admin;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContractAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = Contract::factory()->create();

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
        return $this->getJson($this->getUrl('api.admin.contract.detail', $params), $headers);
    }
}
