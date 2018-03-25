<?php

namespace tests\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testNewUser()
    {
        expect_that($user = User::getUser('get_user'));
        expect($user->username)->equals('get_user');
        expect($user->balance)->equals(0);
    }

    public function testAddBalance()
    {
        expect_that($user = User::getUser('add_bal_user'));
        $user->addBalance(100.23);
        expect($user->balance)->equals(100.23);
    }

    public function testDelBalance()
    {
        expect_that($user = User::getUser('del_bal_user'));
        $user->addBalance(-10.23);
        expect($user->balance)->equals(-10.23);
    }

    public function testDelBalanceTooMuch()
    {
        $this->expectException('yii\base\UserException');
        expect_that($user = User::getUser('too_much_user'));
        expect($user->addBalance(-1001));
        expect($user->balance)->equals(0);
    }

    public function testDelBalanceThousand()
    {
        expect_that($user = User::getUser('user_1000'));
        expect($user->addBalance(-1000));
        expect($user->balance)->equals(-1000);
    }

    public function testSendMoneyOnExistsUser()
    {
        expect_that($user1 = User::getUser('user_1'));
        expect_that($user2 = User::getUser('user_2'));
        $user1->sendMoney($user2->username, 10.55);
        expect($user1->balance)->equals(-10.55);
        $user2 = User::getUser('user_2');
        expect($user2->balance)->equals(10.55);
    }

    public function testSendMoneyOnNonExistsUser()
    {
        expect_that($user1 = User::getUser('user_1'));
        $user1->sendMoney('user_2', 10.55);
        expect($user1->balance)->equals(-10.55);
        $user2 = User::getUser('user_2');
        expect($user2->balance)->equals(10.55);
    }
}
