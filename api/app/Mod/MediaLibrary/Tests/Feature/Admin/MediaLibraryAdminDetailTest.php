<?php

namespace App\Mod\MediaLibrary\Tests\Feature\Admin;

use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class MediaLibraryAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = MediaLibrary::factory()->create();

        $testResponse = $this->apiExec(['id' => $post->id]);
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
        return $this->getJson($this->getUrl('api.media_library.admin.detail', $params), $headers);
    }
}
