<?php


namespace app\tests\unit;



use app\models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSample()
    {
        $this->assertInstanceOf(User::class, new User());
    }
}