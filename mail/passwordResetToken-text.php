<?php
/* @var $this yii\web\View */
/* @var $user common\models\User */

use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Hello <?= Html::encode($user->username) ?>

Follow the link below to reset your password

<?= $resetLink ?>