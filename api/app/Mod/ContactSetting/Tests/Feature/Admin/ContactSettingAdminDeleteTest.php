<?php

namespace App\Mod\ContactSetting\Tests\Feature\Admin;

use App\Mod\ContactSetting\Domain\Models\ContactSetting;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContactSettingAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = ContactSetting::factory()->create();
        $this->assertEquals(1, ContactSetting::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, ContactSetting::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.contact_setting.delete', $params), $data, $headers);
    }
}
