<?php
namespace App\Core\Contract\Tests\Feature\Admin;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContractAdminListTest extends AbstractFeatureTest
{

    public function test_list(): void
    {
        // データ7件作成
        Contract::factory(7)->create();

        // 1ページ目テスト
        $testResponse = $this->apiExec(['current' => 1, 'limit' => 5]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson(['payload' => ['total' => 7]]);
        $testResponse->assertJsonCount(5, 'payload.data');

        // 2ページ目テスト
        $testResponse = $this->apiExec(['current' => 2, 'limit' => 5]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson(['payload' => ['total' => 7]]);
        $testResponse->assertJsonCount(2, 'payload.data');
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        return $this->getJson($this->getUrl('api.admin.contract.list', $params), $headers);
    }

}
