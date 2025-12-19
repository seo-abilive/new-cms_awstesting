<?php

namespace App\Mod\ContentModel\Tests\Feature\Admin;

use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentModelAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = ContentModel::factory()->create();
        $this->assertEquals(1, ContentModel::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, ContentModel::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.content_model.delete', $params), $data, $headers);
    }
}
