<?php

use app\models\User;

class PasswordResetFormCest
{
    private $token;

    public function _before(\FunctionalTester $I)
    {
        $user = User::findByUsername('administrator');
        $user->generatePasswordResetToken();
        $user->save(false);
        $this->token = $user->password_reset_token;
    }

    public function openPasswordResetFormWithoutToken(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password');
        $I->see('Bad Request: Missing required parameters: token');
    }

    public function openPasswordResetFormWithWrongToken(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => 'bad_token']);
        $I->see('Wrong password reset token.');
    }

    public function openPasswordResetFormWithCorrectToken(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => $this->token]);
        $I->seeElement('#reset-password-form');
    }

    public function tryToSetEmptyPassword(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => $this->token]);
        $I->submitForm('#reset-password-form', []);
        $I->expectTo('see validations errors');
        $I->see('New password cannot be blank.');
    }

    public function tryToSetTooShortPassword(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => $this->token]);
        $I->submitForm('#reset-password-form', [
            'ResetPasswordForm[password]' => '12345',
        ]);
        $I->expectTo('see validations errors');
        $I->see('New password should contain at least 6 characters.');
    }

    public function tryToSetCorrectPassword(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => $this->token]);
        $I->submitForm('#reset-password-form', [
            'ResetPasswordForm[password]' => '123456',
        ]);
        $I->see('New password was saved.');
        $I->dontSeeElement('#reset-password-form');
    }

    /**
     * @depends LoginFormCest:loginSuccessfullyUsername
     */
    public function tryToSetCorrectPasswordAndLogin(\FunctionalTester $I)
    {
        $I->amOnRoute('site/reset-password', ['token' => $this->token]);
        $I->submitForm('#reset-password-form', [
            'ResetPasswordForm[password]' => '123456',
        ]);
        $I->see('New password was saved.');
        $I->dontSeeElement('#reset-password-form');

        $I->amGoingTo("Test login with new password.");
        $I->amOnRoute('site/login');
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'administrator',
            'LoginForm[password]' => '123456',
        ]);
        $I->see('Logout (administrator)');
        $I->dontSeeElement('form#login-form');
    }
}
