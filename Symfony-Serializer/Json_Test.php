<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Person
{
    public $firstName;
    public $age;

}

class Json_Test extends TestCase
{

    public function testSerialization()
    {
        $person = new Person();
        $person->firstName = "Tim";
        $person->age = 42;

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        $json = $serializer->serialize($person, "json");
        $this->assertEquals('{"firstName":"Tim","age":42}', $json);
    }
}
