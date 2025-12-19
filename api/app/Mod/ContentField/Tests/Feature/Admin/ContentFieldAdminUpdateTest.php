<?php

namespace App\Mod\ContentField\Tests\Feature\Admin;

use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentFieldAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        // データ作成
        $contentModel = ContentModel::factory()->create();
        $updateData = ContentField::factory()->create([
            'model_id' => $contentModel->id
        ])->toArray();
        $updateData['name'] = $updateData['name'] . ' edit';

        $testResponse = $this->apiExec(['id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = ContentField::find($updateData['id']);
        $this->assertEquals($updateData['name'], $post->name);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.admin.content_field.update', $params), $data, $headers);
    }
}
