<?php

namespace App\Mod\Content\Tests\Feature\Admin;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $model = ContentModel::factory()->create();
        $post = Content::factory()->create(['model_id' => $model->id]);
        $this->assertEquals(1, Content::count());

        $testResponse = $this->apiExec(['model_name' => $model->alias, 'id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, Content::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.content.model.delete', $params), $data, $headers);
    }
}
