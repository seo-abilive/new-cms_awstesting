<?php

namespace App\Core\User\Tests\Feature\Admin;

use App\Core\User\Domain\Models\User;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class UserAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = User::factory()->create();
        $this->assertEquals(1, User::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, User::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.user.delete', $params), $data, $headers);
    }
}
