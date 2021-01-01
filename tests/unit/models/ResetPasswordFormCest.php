<?php

namespace tests\unit\models;

use UnitTester;
use app\models\ResetPasswordForm;
use app\models\User;
use yii\base\InvalidArgumentException;

class ResetPasswordFormCest
{
    private $model;
    private $token;

    /**
     * @depends PasswordResetFormCest
     */
    public function _before(UnitTester $I)
    {
        $user = User::findIdentity(1);
        $user->generatePasswordResetToken();
        $user->save(false);
        $this->token = $user->password_reset_token;
    }

    public function testEmptyToken(UnitTester $I)
    {
        $I->expectThrowable(
            new InvalidArgumentException('Password reset token cannot be blank.'),
            function () {
                $this->model = new ResetPasswordForm('');
            }
        );
    }

    public function testWrongToken(UnitTester $I)
    {
        $I->expectThrowable(
            new InvalidArgumentException('Wrong password reset token.'),
            function () {
                $this->model = new ResetPasswordForm('wrong_token');
            }
        );
    }

    public function testCorrectTokenWrongPassword(UnitTester $I)
    {
        $this->model = new ResetPasswordForm($this->token);
        $I->expect($this->model->validate());
        expect($this->model->errors)->hasKey('password');
    }

    public function testCorrectTokenCorrectPassword(UnitTester $I)
    {
        $this->model = new ResetPasswordForm($this->token, ['password' => 'new_password']);
        $I->expect($this->model->validate());
        $I->expect($this->model->resetPassword());
        expect($this->model->errors)->hasntKey('password');

        $user = User::findIdentity(1);
        $I->expect(empty($user->password_reset_token));
        $I->assertFalse($user->validatePassword('administrator'));
        $I->expect($user->validatePassword('new_password'));
    }
}
