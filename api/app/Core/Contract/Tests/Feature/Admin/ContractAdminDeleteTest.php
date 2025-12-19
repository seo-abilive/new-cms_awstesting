<?php

namespace App\Core\Contract\Tests\Feature\Admin;

use App\Core\Contract\Domain\Models\Contract;
use Illuminate\Testing\TestResponse;
use Tests\Feature\AbstractFeatureTest;

class ContractAdminDeleteTest extends AbstractFeatureTest
{
    public function test_delete(): void
    {
        $post = Contract::factory()->create();
        $this->assertEquals(1, Contract::count());

        $testResponse = $this->apiExec(['id' => $post->id]);
        $testResponse->assertStatus(204);

        $this->assertEquals(0, Contract::count());
    }

    protected function apiExec(array $params = [], array $data = [], array $headers = []): TestResponse
    {
        // TODO: Implement apiExec() method.
        return $this->deleteJson($this->getUrl('api.admin.contract.delete', $params), $data, $headers);
    }
}
