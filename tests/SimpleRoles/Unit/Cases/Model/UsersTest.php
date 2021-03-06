<?php
/**
 * Created by PhpStorm.
 * User: bradb
 * Date: 11/4/14
 * Time: 6:06 PM
 */

namespace SimpleRoles\Unit\Model;


use SimpleRoles\Model\Users;

class UsersTest extends \PHPUnit_Framework_TestCase {
    private $db;

    public function setUp()
    {
        $this->db = new \PDO("mysql:host=127.0.0.1;dbname=simpleRoles", 'travis', '');
        parent::setUp();
    }

    public function tearDown()
    {
        $this->db = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function testGetUserInfoInvalid()
    {
        $userModel = new Users($this->db);
        $ret = $userModel->getUserInfo(-1);
        $this->assertTrue($ret === false);
    }

    /**
     * @test
     */
    public function testGetUserInfoValid()
    {
        $userModel = new Users($this->db);
        $ret = $userModel->getUserInfo(2);
        $this->assertTrue(is_array($ret));
        $this->assertEquals('balls', $ret['username']);
        $this->assertEquals('Sam Ball', $ret['name']);
        //print_r($ret);
    }

    /**
     * @test
     */
    public function testAddNewUserWhoseUserNameExists()
    {
        $userModel = new Users($this->db);
        try {
            $ret = $userModel->addNewUser('Fitzer Witzer', 'balls', 'ref');
        } catch (\Exception $e) {
            $this->assertEquals(23000, $e->getCode());
            return;
        }
        $this->fail("Missed Expected Exception");
    }

    /**
     * @test
     */
    public function testAddNewUserWhichShouldWork()
    {
        $userModel = new Users($this->db);
        $ret = $userModel->addNewUser('Fitzgerald Whitebeard', 'fitzer', 'ref');
        $this->assertTrue(is_numeric($ret));
        $this->assertTrue($ret > 0);

        $res = $userModel->getUserInfo($ret);
        $this->assertEquals('Fitzgerald Whitebeard', $res['name']);
        $this->assertEquals('fitzer', $res['username']);

        $res = $userModel->getUserByUserName('fitzer');
        $this->assertEquals('Fitzgerald Whitebeard', $res['name']);
        $this->assertEquals($ret, $res['id']);
    }
}
