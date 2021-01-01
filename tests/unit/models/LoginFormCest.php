<?php

namespace tests\unit\models;

use UnitTester;
use app\models\LoginForm;

class LoginFormCest
{
    private $model;

    protected function _after(UnitTester $I)
    {
        \Yii::$app->user->logout();
    }

    public function testLoginNoUser(UnitTester $I)
    {
        $this->model = new LoginForm([
            'username' => 'not_existing_username',
            'password' => 'not_existing_password',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
    }

    public function testLoginWrongPassword(UnitTester $I)
    {
        $this->model = new LoginForm([
            'username' => 'administrator',
            'password' => 'wrong_password',
        ]);

        expect_not($this->model->login());
        expect_that(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasKey('password');
    }

    public function testLoginCorrectUsername(UnitTester $I)
    {
        $this->model = new LoginForm([
            'username' => 'administrator',
            'password' => 'administrator',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasntKey('password');
    }

    public function testLoginCorrectEmail()
    {
        $this->model = new LoginForm([
            'username' => 'test@test.test',
            'password' => 'administrator',
        ]);

        expect_that($this->model->login());
        expect_not(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasntKey('password');
    }
}
