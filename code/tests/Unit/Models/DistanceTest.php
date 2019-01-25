<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class DistanceTest extends Tests\TestCase
{
    protected $distanceHelper;

    use WithoutMiddleware;

    protected function setUp()
    {
        $this->distanceHelper = $this->createMock(\App\Helpers\DistanceHelper::class);

        parent::setUp();
    }

    function testGetOrSetDistanceWithExistsData()
    {
        echo "\n *** Unit Test - Models::Distance - Method:GetOrSetDistance - with already exists data *** \n";

        $distanceCoordinates = [
            'initialLatitude' => '44.968046',
            'initialLongitude' => '-94.420307',
            'finalLatitude' => '44.33328',
            'finalLongitude' => '-29.132006',
        ];

        $origin = sprintf('%s,%s', $distanceCoordinates['initialLatitude'], $distanceCoordinates['initialLongitude']);;
        $destination = sprintf('%s,%s', $distanceCoordinates['finalLatitude'], $distanceCoordinates['finalLongitude']);;

        $this->distanceHelper->method('getDistance')->with($origin, $destination)
            ->willReturn(100);

        $model = \App\Http\Factory\DistanceFactory::create($this->distanceHelper);

        $this->assertInstanceOf('\App\Http\Models\Distance', $model->getOrSetDistance(
            $distanceCoordinates['initialLatitude'],
            $distanceCoordinates['initialLongitude'],
            $distanceCoordinates['finalLatitude'],
            $distanceCoordinates['finalLongitude']
        ));
    }

    function testGetOrSetDistanceWithNewData()
    {
        echo "\n *** Unit Test - Models::Distance - Method:GetOrSetDistance -  with dynamically generated data *** \n";

        $distanceCoordinates = $this->generateGeoCordinates();

        $origin = sprintf('%s,%s', $distanceCoordinates['initialLatitude'], $distanceCoordinates['initialLongitude']);;
        $destination = sprintf('%s,%s', $distanceCoordinates['finalLatitude'], $distanceCoordinates['finalLongitude']);;

        $this->distanceHelper->method('getDistance')->with($origin, $destination)
            ->willReturn($distanceCoordinates['distance']);

        $model = \App\Http\Factory\DistanceFactory::create($this->distanceHelper);

        $this->assertInstanceOf('\App\Http\Models\Distance', $model->getOrSetDistance(
            $distanceCoordinates['initialLatitude'],
            $distanceCoordinates['initialLongitude'],
            $distanceCoordinates['finalLatitude'],
            $distanceCoordinates['finalLongitude']
        ));
    }

    function testGetOrSetDistanceWithInvalidData()
    {
        echo "\n *** Unit Test - Models::Distance - Method:GetOrSetDistance -  with invalid data *** \n";

         $distanceCoordinates = [
            'initialLatitude' => '1144.968046',
            'initialLongitude' => '-1194.420307',
            'finalLatitude' => '44.33328',
            'finalLongitude' => '-29.132006',
        ];

        $origin = sprintf('%s,%s', $distanceCoordinates['initialLatitude'], $distanceCoordinates['initialLongitude']);;
        $destination = sprintf('%s,%s', $distanceCoordinates['finalLatitude'], $distanceCoordinates['finalLongitude']);;

        $this->distanceHelper->method('getDistance')->with($origin, $destination)
            ->willReturn('GOOGLE_API_NULL_RESPONSE');

        $model = \App\Http\Factory\DistanceFactory::create($this->distanceHelper);

        $this->assertEquals('GOOGLE_API_NULL_RESPONSE', $model->getOrSetDistance(
            $distanceCoordinates['initialLatitude'],
            $distanceCoordinates['initialLongitude'],
            $distanceCoordinates['finalLatitude'],
            $distanceCoordinates['finalLongitude']
        ));
    }

    /**
     * @return array
     */
    protected function generateGeoCordinates()
    {
        $faker = Faker\Factory::create();

        $initialLatitude = $faker->latitude();
        $initialLongitude = $faker->latitude();
        $finalLatitude = $faker->longitude();
        $finalLongitude = $faker->longitude();

        $distance = $this->distance($initialLatitude, $initialLongitude, $finalLatitude, $finalLongitude);

        return [
            'initialLatitude' => $initialLatitude,
            'initialLongitude' => $initialLongitude,
            'finalLatitude' => $finalLatitude,
            'finalLongitude' => $finalLongitude,
            'distance' => $distance
        ];
    }

    /**
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     *
     * @return int
     */
    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distanceInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return (int) $distanceInMetre;
    }
}
