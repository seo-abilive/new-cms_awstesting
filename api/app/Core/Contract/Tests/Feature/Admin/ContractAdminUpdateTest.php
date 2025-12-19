<?php

namespace App\Core\Contract\Tests\Feature\Admin;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContractAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        // データ作成
        $updateData = Contract::factory()->create()->toArray();
        $updateData['title'] = $updateData['title'] . ' edit';

        $testResponse = $this->apiExec(['id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = Contract::find($updateData['id']);
        $this->assertEquals($updateData['title'], $post->title);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.admin.contract.update', $params), $data, $headers);
    }
}
