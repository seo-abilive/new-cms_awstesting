<?php

namespace App\Mod\Content\Tests\Feature\Admin;

use App\Mod\Content\Domain\Models\Content;
use App\Mod\Content\Domain\Models\ContentCategory;
use App\Mod\ContentField\Domain\Models\ContentField;
use App\Mod\ContentModel\Domain\Models\ContentModel;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContentAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        $model = ContentModel::factory()->create();
        // 登録データ
        $inputData = Content::factory()->make(['model_id' => $model->id])->toArray();

        $testResponse = $this->apiExec(['model_name' => $model->alias], $inputData);
        $testResponse->assertStatus(201);
        $testResponse->assertJson([
            'payload' => [
                'data' => [
                    'title' => $inputData['title']
                ]
            ]
        ]);
    }

    public function test_store_with_custom_fields_and_categories(): void
    {
        $model = ContentModel::factory()->create();
        $field = ContentField::factory()->create(['model_id' => $model->id]);
        $category = ContentCategory::factory()->create(['model_id' => $model->id]);

        $inputData = Content::factory()->make(['model_id' => $model->id])->toArray();
        $inputData[$field->field_id] = 'custom field value';
        $inputData['categories'] = [
            ['value' => $category->id, 'label' => $category->title]
        ];

        $response = $this->apiExec(['model_name' => $model->alias], $inputData);
        $response->assertStatus(201);

        $contentId = $response->json('payload.data.id');
        $this->assertDatabaseHas('cms_content_value', [
            'content_id' => $contentId,
            'field_id' => $field->id,
            'value' => 'custom field value'
        ]);
        $this->assertDatabaseHas('cms_content_to_categories', [
            'content_id' => $contentId,
            'category_id' => $category->id
        ]);
    }

    public function test_store_with_new_category(): void
    {
        $model = ContentModel::factory()->create();
        $inputData = Content::factory()->make(['model_id' => $model->id])->toArray();
        $inputData['categories'] = [
            ['value' => 'new-cat', 'label' => 'New Category']
        ];

        $response = $this->apiExec(['model_name' => $model->alias], $inputData);
        $response->assertStatus(201);

        $this->assertDatabaseHas('cms_content_category', [
            'model_id' => $model->id,
            'title' => 'New Category'
        ]);

        $newCategory = ContentCategory::where('title', 'New Category')->first();
        $contentId = $response->json('payload.data.id');
        $this->assertDatabaseHas('cms_content_to_categories', [
            'content_id' => $contentId,
            'category_id' => $newCategory->id
        ]);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->postJson($this->getUrl('api.admin.content.model.store', $params), $data, $headers);
    }
}
