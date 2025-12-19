<?php

namespace App\Mod\ContentField\Tests\Feature\Admin;

use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentFieldAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $contentModel = ContentModel::factory()->create();
        $post = ContentField::factory()->create([
            'model_id' => $contentModel->id
        ]);

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'name' => $post->name
                ]
            ]
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->getJson($this->getUrl('api.admin.content_field.detail', $params), $headers);
    }
}
