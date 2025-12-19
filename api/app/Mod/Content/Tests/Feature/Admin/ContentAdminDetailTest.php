<?php

namespace App\Mod\Content\Tests\Feature\Admin;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $model = ContentModel::factory()->create();
        $post = Content::factory()->create(['model_id' => $model->id]);

        $testResponse = $this->apiExec(['model_name' => $model->alias, 'id' => $post->id]);
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
        return $this->getJson($this->getUrl('api.admin.content.model.detail', $params), $headers);
    }
}
