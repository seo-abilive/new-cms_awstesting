<?php
namespace App\Mod\Content\Tests\Feature\Admin;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentAdminListTest extends AbstractFeatureTest
{

    public function test_list(): void
    {
        $model = ContentModel::factory()->create();

        // データ7件作成
        Content::factory(7)->create(['model_id' => $model->id]);

        // 1ページ目テスト
        $testResponse = $this->apiExec(['model_name' => $model->alias, 'current' => 1, 'limit' => 5]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson(['payload' => ['total' => 7]]);
        $testResponse->assertJsonCount(5, 'payload.data');

        // 2ページ目テスト
        $testResponse = $this->apiExec(['model_name' => $model->alias, 'current' => 2, 'limit' => 5]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson(['payload' => ['total' => 7]]);
        $testResponse->assertJsonCount(2, 'payload.data');
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        return $this->getJson($this->getUrl('api.admin.content.model.list', $params), $headers);
    }

}
