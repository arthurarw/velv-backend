<?php


use App\Http\Services\ServerService;
use Tests\TestCase;

/**
 *
 */
class ServerTest extends TestCase
{
    /**
     * @var ServerService
     */
    private ServerService $service;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ServerService();
    }

    /**
     * A basic test example.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function test_get_locations(): void
    {
        $data = [
            "AmsterdamAMS-01",
            "Washington D.C.WDC-01",
            "San FranciscoSFO-12",
            "SingaporeSIN-11",
            "DallasDAL-10",
            "FrankfurtFRA-10",
            "Hong KongHKG-10"
        ];
        sort($data);

        $service = $this->service->getLocations();
        $service = $service->getData()->data;

        $this->assertEquals($data, collect($service)->toArray());
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function test_get_servers(): void
    {
        $data = [
            "id" => 1,
            "model" => "Dell R210Intel Xeon X3440",
            "ram" => "16GB",
            "ram_type" => "DDR3",
            "storage" => "2x2TBSATA2",
            "location" => "AmsterdamAMS-01",
            "price" => "€49.99",
            "converted_storage_gb" => 4000,
            "converted_storage_unity" => "TB",
            "storage_type" => "SATA",
            "original_ram" => "16GBDDR3",
        ];

        $service = $this->service->findAll(['page' => 1, 'per_page' => 1]);
        $service = $service->getData(true)['data'][0];

        $this->assertEquals($data, $service);
    }
}
