<?php

namespace tests\models;

use app\models\LoginForm;
use app\models\User;

class LoginFormTest extends \Codeception\Test\Unit
{
    private $model;

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testLoginEmptyUsername()
    {
        $this->model = new LoginForm([
            'username' => '',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
    }

    public function testLoginLongUser()
    {
        $this->model = new LoginForm([
            'username' => '123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
    }

    public function testLoginCorrectNew()
    {
        $this->model = new LoginForm([
            'username' => 'will_be_logged_in',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
        expect(\Yii::$app->user->identity->balance)->equals(0);
    }

    public function testLoginCorrectExisted()
    {
        User::getUser('will_be_logged_in');

        $this->model = new LoginForm([
            'username' => 'will_be_logged_in',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
        expect(\Yii::$app->user->identity->balance)->equals(0);
    }
}
