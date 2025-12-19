<?php

namespace App\Mod\Content\Tests\Feature\Front;

use App\Mod\Content\Domain\Models\Content;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentFrontDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = Content::factory()->create();

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(200);
        $testResponse->assertJson([
            'contents' => [
                'title' => $post->title
            ]
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->getJson($this->getUrl('api.front.content.detail', $params), $headers);
    }
}
