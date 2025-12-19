<?php

namespace App\Core\Contract\Tests\Feature\Admin;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContractAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        // 登録データ
        $inputData = Contract::factory()->make()->toArray();

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
        return $this->postJson($this->getUrl('api.admin.contract.store'), $data, $headers);
    }
}
