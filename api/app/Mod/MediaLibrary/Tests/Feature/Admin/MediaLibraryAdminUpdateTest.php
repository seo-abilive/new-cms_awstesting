<?php

namespace App\Mod\MediaLibrary\Tests\Feature\Admin;

use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class MediaLibraryAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        // データ作成
        $updateData = MediaLibrary::factory()->create()->toArray();
        $updateData['title'] = $updateData['title'] . ' edit';

        $testResponse = $this->apiExec(['id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = MediaLibrary::find($updateData['id']);
        $this->assertEquals($updateData['title'], $post->title);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.media_library.admin.update', $params), $data, $headers);
    }
}
