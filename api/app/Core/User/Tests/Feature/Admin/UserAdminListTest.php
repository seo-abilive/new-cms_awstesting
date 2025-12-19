<?php
namespace App\Core\User\Tests\Feature\Admin;

use App\Core\User\Domain\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class UserAdminListTest extends AbstractFeatureTest
{

    public function test_list(): void
    {
        // データ7件作成
        User::factory(7)->create();

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
        return $this->getJson($this->getUrl('api.admin.user.list', $params), $headers);
    }

}
