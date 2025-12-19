<?php

namespace App\Mod\ContentField\Tests\Feature\Admin;

use App\Mod\ContentField\Domain\Models\ContentField;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentFieldAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = ContentField::factory()->create();
        $this->assertEquals(1, ContentField::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, ContentField::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.content_field.delete', $params), $data, $headers);
    }
}
