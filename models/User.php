<?php

namespace app\models;

use yii\base\NotSupportedException;

class User extends databaseModels\User implements \yii\web\IdentityInterface
{
    public const USER_ROLE_MEMBER = 'user';
    public const USER_ROLE_REVIEWER = 'reviewer';
    public const USER_ROLE_PUBLISHER = 'publisher';
    public const USER_ROLE_ADMIN = 'admin';

    /**
     * Defines roles inheritances
     *
     * Format :
     * 'role1' => [
     *     'role2',
     *     'role3',
     * ] // Users having role 'role1' also implicitly have roles 'role2' and 'role3' (not recursive)
     */
    public const ROLES_INHERITANCES = [
        self::USER_ROLE_ADMIN => [
            self::USER_ROLE_PUBLISHER,
            self::USER_ROLE_REVIEWER,
            self::USER_ROLE_MEMBER,
        ],
        self::USER_ROLE_PUBLISHER => [
            self::USER_ROLE_REVIEWER,
            self::USER_ROLE_MEMBER,
        ],
        self::USER_ROLE_REVIEWER => [
            self::USER_ROLE_MEMBER,
        ],
    ];

    /**
     * Checks whether $baseRole inherits $inheritedRole
     *
     * @param $baseRole
     * @param $inheritedRole
     * @return bool
     */
    public static function isRoleInherited($baseRole, $inheritedRole): bool
    {
        if(!array_key_exists($baseRole, self::ROLES_INHERITANCES)) {
            return false;
        }

        return in_array($inheritedRole, self::ROLES_INHERITANCES[$baseRole]);
    }

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
     * Checks whether the user has the specified role, directly or inherited
     * Example: User is publisher, publisher inherits member and reviewer roles
     * So user also have member and reviewer roles. {@see isRoleInherited}
     *
     * @param $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if($this->role === $role) {
            return true;
        }

        // Role can be inherited
        return self::isRoleInherited($this->role, $role);
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
