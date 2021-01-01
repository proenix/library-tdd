<?php

namespace tests\unit\models;

use UnitTester;
use app\models\PasswordResetRequestForm;
use app\models\User;

class PasswordResetRequestFormCest
{
    private $model;

    public function _after(UnitTester $I)
    {
    }

    public function testRequestPasswordResetNoEmail(UnitTester $I)
    {
        $this->model = new PasswordResetRequestForm([
            'email' => 'not_existing_email',
        ]);

        expect_not($this->model->validate());
        expect_not($this->model->sendEmail());
        expect($this->model->errors)->hasKey('email');
    }

    public function testRequestPasswordResetCorrectEmail(UnitTester $I)
    {
        $this->model = new PasswordResetRequestForm([
            'email' => 'test@test.test',
        ]);

        expect_that($this->model->sendEmail());

        $user = User::findByEmail('test@test.test');
        expect_that(isset($user->password_reset_token));

        // using Yii2 module actions to check email was sent
        $I->seeEmailIsSent();

        /** @var MessageInterface $emailMessage */
        $emailMessage = $I->grabLastSentEmail();
        expect('valid email is sent', $emailMessage)->isInstanceOf('yii\mail\MessageInterface');
        expect($emailMessage->getTo())->hasKey('test@test.test');
        expect($emailMessage->getSubject())->stringContainsString('Password reset for ');
        expect($emailMessage->toString())->stringContainsString('Follow the link below to reset your password');
    }
}
