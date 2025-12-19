<?php

namespace App\Mod\Content\Tests\Feature\Admin;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        $model = ContentModel::factory()->create();
        // データ作成
        $updateData = Content::factory()->create(['model_id' => $model->id])->toArray();
        $updateData['title'] = $updateData['title'] . ' edit';

        $testResponse = $this->apiExec(['model_name' => $model->alias, 'id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = Content::find($updateData['id']);
        $this->assertEquals($updateData['title'], $post->title);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.admin.content.model.update', $params), $data, $headers);
    }
}
