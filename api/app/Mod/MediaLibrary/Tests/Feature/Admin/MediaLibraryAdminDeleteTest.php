<?php

namespace App\Mod\MediaLibrary\Tests\Feature\Admin;

use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class MediaLibraryAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = MediaLibrary::factory()->create();
        $this->assertEquals(1, MediaLibrary::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, MediaLibrary::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.media_library.admin.delete', $params), $data, $headers);
    }
}
