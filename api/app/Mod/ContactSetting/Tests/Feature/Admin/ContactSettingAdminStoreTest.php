<?php

namespace App\Mod\ContactSetting\Tests\Feature\Admin;

use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContactSettingAdminStoreTest extends AbstractFeatureTest
{

    public function test_store(): void
    {
        // 登録データ
        $inputData = ContactSetting::factory()->make()->toArray();

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
        return $this->postJson($this->getUrl('api.admin.contact_setting.store'), $data, $headers);
    }
}
