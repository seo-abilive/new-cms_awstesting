<?php

namespace App\Mod\ContentField\Tests\Feature\Admin;

use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentFieldAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        $model = ContentModel::factory()->create();
        // 登録データ
        $inputData = ContentField::factory()->make(['model_id' => $model->id])->toArray();

        $testResponse = $this->apiExec([], $inputData);
        $testResponse->assertStatus(201);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'name' => $inputData['name']
                ]
            ]
        ]);
    }

    public function test_store_validation(): void
    {
        $model = ContentModel::factory()->create();

        // name is required
        $inputData = ContentField::factory()->make(['name' => '', 'model_id' => $model->id])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');

        // field_id is required
        $inputData = ContentField::factory()->make(['field_id' => '', 'model_id' => $model->id])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('field_id');

        // field_id must be unique for the same model_id
        $existingField = ContentField::factory()->create(['model_id' => $model->id]);
        $inputData = ContentField::factory()->make(['field_id' => $existingField->field_id, 'model_id' => $model->id])->toArray();
        $this->apiExec([], $inputData)
            ->assertStatus(422)
            ->assertJsonValidationErrors('field_id');

        // field_id can be the same for different model_id
        $anotherModel = ContentModel::factory()->create();
        $inputData = ContentField::factory()->make(['field_id' => $existingField->field_id, 'model_id' => $anotherModel->id])->toArray();
        $this->apiExec([], $inputData)->assertStatus(201);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->postJson($this->getUrl('api.admin.content_field.store'), $data, $headers);
    }
}
