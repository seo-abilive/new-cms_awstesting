<?php
namespace App\Mod\ActionLog\Tests\Feature\Admin;

use App\Mod\ActionLog\Domain\Models\ActionLog;
use Carbon\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ActionLogAdminListTest extends AbstractFeatureTest
{

    public function test_list(): void
    {
        // データ7件作成
        ActionLog::factory(7)->create();

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

    public function test_list_with_date_filter(): void
    {
        ActionLog::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        ActionLog::factory()->create(['created_at' => Carbon::now()->subDays(1)]);
        ActionLog::factory()->create(['created_at' => Carbon::now()]);

        // 期間開始日
        $testResponse = $this->apiExec(['criteria' => ['created_at_start' => Carbon::now()->subDays(1)->format('Y-m-d')]]);
        $testResponse->assertStatus(200);
        $testResponse->assertJsonCount(2, 'payload.data');

        // 期間終了日
        $testResponse = $this->apiExec(['criteria' => ['created_at_end' => Carbon::now()->subDays(1)->format('Y-m-d')]]);
        $testResponse->assertStatus(200);
        $testResponse->assertJsonCount(2, 'payload.data');

        // 期間
        $testResponse = $this->apiExec(['criteria' => [
            'created_at_start' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'created_at_end' => Carbon::now()->subDays(1)->format('Y-m-d'),
        ]]);
        $testResponse->assertStatus(200);
        $testResponse->assertJsonCount(1, 'payload.data');
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        return $this->getJson($this->getUrl('api.admin.action_log.list', $params), $headers);
    }

}
