<?php

namespace app\models;

use yii\base\NotSupportedException;

class User extends databaseModels\User implements \yii\web\IdentityInterface
{
    public const USER_ROLE_MEMBER = 'member';
    public const USER_ROLE_REVIEWER = 'reviewer';
    public const USER_ROLE_PUBLISHER = 'publisher';
    public const USER_ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException("This function is not implemented.");
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        throw new NotSupportedException("This function is not implemented");
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException("This function is not implemented");
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
