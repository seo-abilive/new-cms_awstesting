<?php

namespace App\Mod\ContentModel\Tests\Feature\Admin;

use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentModelAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        // データ作成
        $updateData = ContentModel::factory()->create()->toArray();
        $updateData['title'] = $updateData['title'] . ' edit';

        $testResponse = $this->apiExec(['id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = ContentModel::find($updateData['id']);
        $this->assertEquals($updateData['title'], $post->title);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.admin.content_model.update', $params), $data, $headers);
    }
}
