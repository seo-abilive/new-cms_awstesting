<?php

namespace App\Mod\MediaLibrary\Tests\Feature\Admin;

use App\Mod\MediaLibrary\Domain\Models\MediaLibrary;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class MediaLibraryAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        // 登録データ
        $inputData = MediaLibrary::factory()->make()->toArray();

        $testResponse = $this->apiExec([], $inputData);
        $testResponse->assertStatus(201);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'title' => $inputData['title']
                ]
            ]
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->postJson($this->getUrl('api.media_library.admin.store'), $data, $headers);
    }
}
