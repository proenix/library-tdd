<?php

namespace tests\unit\models;

use UnitTester;
use app\models\ContactForm;
use app\models\User;
use yii\mail\MessageInterface;

class ContactFormCest
{
    public function testEmailIsSentOnContact(UnitTester $I)
    {
        $model = new ContactForm();

        $model->attributes = [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
            'verifyCode' => 'testme',
        ];

        expect_that($model->contact('admin@example.com'));

        // using Yii2 module actions to check email was sent
        $I->seeEmailIsSent();

        /** @var MessageInterface $emailMessage */
        $emailMessage = $I->grabLastSentEmail();
        expect('valid email is sent', $emailMessage)->isInstanceOf('yii\mail\MessageInterface');
        expect($emailMessage->getTo())->hasKey('admin@example.com');
        expect($emailMessage->getFrom())->hasKey('noreply@example.com');
        expect($emailMessage->getReplyTo())->hasKey('tester@example.com');
        expect($emailMessage->getSubject())->equals('very important letter subject');
        expect($emailMessage->toString())->stringContainsString('body of current message');
    }

    public function testEmailSendByLoggedInUserAutoSetAddress(UnitTester $I)
    {
        $I->amGoingTo('Set own identity to administrator.');
        $user = User::findByEmail('test@test.test');
        \Yii::$app->user->setIdentity($user);

        $model = new ContactForm();
        $model->attributes = [
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
            'verifyCode' => 'testme',
        ];
        $I->assertTrue($model->contact('admin@example.com'));

        // using Yii2 module actions to check email was sent
        $I->seeEmailIsSent();

        /** @var MessageInterface $emailMessage */
        $I->amGoingTo('Verify if email was sent.');
        $emailMessage = $I->grabLastSentEmail();
        $I->assertInstanceOf('yii\mail\MessageInterface', $emailMessage);
        $I->amGoingTo('Email content.');
        $I->assertArrayHasKey('admin@example.com', $emailMessage->getTo());
        $I->assertArrayHasKey('noreply@example.com', $emailMessage->getFrom());
        $I->assertArrayHasKey($user->email, $emailMessage->getReplyTo());
        $I->assertContains('very important letter subject', $emailMessage->getSubject());
        $I->assertContains('body of current message', $emailMessage->toString());
    }
}
