<?php

namespace App\Mod\ContactSetting\Tests\Feature\Admin;

use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContactSettingAdminDetailTest extends AbstractFeatureTest
{
    public function test_detail(): void
    {
        // データ作成
        $post = ContactSetting::factory()->create();

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
        return $this->getJson($this->getUrl('api.admin.contact_setting.detail', $params), $headers);
    }
}
