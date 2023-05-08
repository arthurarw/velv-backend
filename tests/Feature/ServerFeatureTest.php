<?php


// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServerFeatureTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_get_locations(): void
    {
        $response = $this->getJson('/api/servers/locations');

        $response->assertStatus(200);
    }

    public function test_not_found_locations(): void
    {
        $response = $this->getJson('/api/servers/location');

        $response->assertStatus(404);
    }

    public function test_get_servers()
    {
        $response = $this->getJson('/api/servers');

        $response->assertStatus(200);
    }

    public function test_not_found_servers()
    {
        $response = $this->getJson('/api/servers?ram=2GB&location=AmsterdamAMS-01&storage=72000&hard_disk_type=SATA&page=1&per_page=100');

        $response->assertStatus(404);
    }
}
