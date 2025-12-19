<?php

namespace App\Mod\ContactSetting\Tests\Feature\Admin;

use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContactSettingAdminUpdateTest extends AbstractFeatureTest
{

    public function test_update(): void
    {
        // データ作成
        $updateData = ContactSetting::factory()->create()->toArray();
        $updateData['title'] = $updateData['title'] . ' edit';

        $testResponse = $this->apiExec(['id' => $updateData['id']], $updateData);
        $testResponse->assertStatus(204);

        // 更新確認
        $post = ContactSetting::find($updateData['id']);
        $this->assertEquals($updateData['title'], $post->title);
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->putJson($this->getUrl('api.admin.contact_setting.update', $params), $data, $headers);
    }
}
