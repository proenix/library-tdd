<?php

namespace tests\unit\models;

use app\models\User;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        expect_that($user = User::findIdentity(1));
        expect($user->username)->equals('administrator');

        expect_not(User::findIdentity(999));
    }

    public function testFindUserByUsername()
    {
        expect_that($user = User::findByUsername('administrator'));
        expect_not(User::findByUsername('not-admin'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser($user)
    {
        $user = User::findByUsername('administrator');
        expect_that($user->validateAuthKey('auth_key_administrator'));
        expect_not($user->validateAuthKey('non-existing'));

        expect_that($user->validatePassword('administrator'));
        expect_not($user->validatePassword('123456'));
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testByPasswordResetToken()
    {
        $user = User::findByUsername('administrator');
        $user->generatePasswordResetToken();
        $user->save(false);

        $token = $user->password_reset_token;

        expect_that(!empty($token));

        $user = User::findByPasswordResetToken($token);
        expect_that($user->username == 'administrator');
    }
}
