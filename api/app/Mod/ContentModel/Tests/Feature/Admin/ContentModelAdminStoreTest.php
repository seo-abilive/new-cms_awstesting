<?php

namespace App\Mod\ContentModel\Tests\Feature\Admin;

use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentModelAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        // 登録データ
        $inputData = ContentModel::factory()->make()->toArray();

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

    public function test_store_validation(): void
    {
        // title is required
        $inputData = ContentModel::factory()->make(['title' => ''])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');

        // alias is required
        $inputData = ContentModel::factory()->make(['alias' => ''])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('alias');

        // alias must be unique
        $existing = ContentModel::factory()->create();
        $inputData = ContentModel::factory()->make(['alias' => $existing->alias])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('alias');
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->postJson($this->getUrl('api.admin.content_model.store'), $data, $headers);
    }
}