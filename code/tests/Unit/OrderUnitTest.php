<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
class OrderUnitTest extends Tests\TestCase
{
    use WithoutMiddleware;

    function testGetDistance()
    {
        $origin = '44.968046,-94.420307';
        $destination = '44.33328,-29.132006';

        $stub = $this->createMock(\App\Library\Distance\GoogleDistanceMatrix::class);
        $stub->method('getDistance')->with($origin, $destination)->willReturn(100);

        $helper = new \App\Helpers\DistanceHelper($stub);

        $this->assertEquals(100, $helper->getDistance($origin,$destination));
    }

    function testDistanceValidatorPositive()
    {
        $validData = [
            'initialLatitude' => '44.968046',
            'initialLongitude' => '-94.420307',
            'finalLatitude' => '44.33328',
            'finalLongitude' => '-29.132006',
        ];

        $stub = $this->createMock(\App\Validators\DistanceValidator::class);

        $stub->method('validate')->with(
            $validData['initialLatitude'],
            $validData['initialLongitude'],
            $validData['finalLatitude'],
            $validData['finalLongitude']
        )->willReturn(true);


        $this->assertEquals(true, $stub->validate(
            $validData['initialLatitude'],
            $validData['initialLongitude'],
            $validData['finalLatitude'],
            $validData['finalLongitude']
        ));
    }

    function testDistanceValidatorFailed()
    {
        $inValidData = [
            'initialLatitude' => '400.968046',
            'initialLongitude' => '-904.420307',
            'finalLatitude' => '44.33328',
            'finalLongitude' => '-29.132006',
        ];

        $stub = $this->createMock(\App\Validators\DistanceValidator::class);

        $stub->method('validate')->with(
            $inValidData['initialLatitude'],
            $inValidData['initialLongitude'],
            $inValidData['finalLatitude'],
            $inValidData['finalLongitude']
        )->willReturn(false);

        $this->assertEquals(false, $stub->validate(
            $inValidData['initialLatitude'],
            $inValidData['initialLongitude'],
            $inValidData['finalLatitude'],
            $inValidData['finalLongitude']
        ));
    }
}
