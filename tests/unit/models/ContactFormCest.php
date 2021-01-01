<?php

namespace tests\unit\models;

use UnitTester;
use app\models\ContactForm;
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
}
