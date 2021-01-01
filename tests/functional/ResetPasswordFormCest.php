<?php

class ResetPasswordFormCest
{
    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/request-password-reset');
    }

    public function openResetPasswordPage(\FunctionalTester $I)
    {
        $I->see('Request password reset', 'h1');
    }

    public function loggedInUserResetForbidden(\FunctionalTester $I)
    {
        $I->amLoggedInAs(\app\models\User::findByUsername('administrator'));
        $I->amOnPage('/site/request-password-reset');
        $I->see('Forbidden');
    }

    public function resetWithEmptyEmail(\FunctionalTester $I)
    {
        $I->submitForm('#request-password-reset-form', []);
        $I->expectTo('see validation errors');
        $I->see('Email cannot be blank.');
    }

    public function resetWithNotVaidEmail(\FunctionalTester $I)
    {
        $I->submitForm('#request-password-reset-form', [
            'PasswordResetRequestForm[email]' => 'thatsnotanemail'
        ]);
        $I->expectTo('see validation errors');
        $I->see('Email is not a valid email address.');
    }

    public function resetWithWrongEmail(\FunctionalTester $I)
    {
        $I->submitForm('#request-password-reset-form', [
            'PasswordResetRequestForm[email]' => 'this.user.is.not.in.db@example.com'
        ]);
        $I->expectTo('see validation errors');
        $I->see('There is no user with such email.');
    }

    public function resetWithCorrectEmail(\FunctionalTester $I)
    {
        $I->submitForm('#request-password-reset-form', [
            'PasswordResetRequestForm[email]' => 'test@test.test'
        ]);
        $I->expectTo('Succeed and get email.');
        $I->seeEmailIsSent();
        $I->dontSeeElement('#request-password-reset-form');
        $I->see('Check your email for further instructions.');
    }

    public function canGoToResetFromLoginPage(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
        $I->see('If you forgot your password you can reset it.');
        $I->click('//*[@id="login-form"]/div[4]/a');
        $I->see('Request password reset', 'h1');
    }
}
