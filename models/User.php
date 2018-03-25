<?php

namespace app\models;

use yii\base\UserException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{users}}';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return User::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by [[username]] or create it
     *
     * @return User|null
     */
    public static function getUser($username)
    {
        if (empty($username)) {
            throw new UserException("Unable to get user with empty username");
        }

        $user = User::findByUsername($username);
        if (is_null($user)) {
            $user = new User();
            $user->username = $username;
            $user->balance = 0;
            $user->save();
        }

        return $user;
    }

    public function addBalance($amount) {
        $this->balance += $amount;

        $this->update();

        return $this;
    }

    public function beforeSave($insert) {
        if (!parent::beforeSave($insert))
            return false;

        if ($this->balance < -1000) {
            throw new UserException("Unable to set balance {$this->balance} to user {$this->username}");
        }

        return true;
    }

    /**
     * Send money to another user
     * @param $username string
     * @param $amount double
     * @throws UserException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function sendMoney($username, $amount) {
        if ($username == $this->username) {
            throw new UserException("Unable to send money to yourself");
        }

        $transaction = User::getDb()->beginTransaction();

        try {
            $this->addBalance(-$amount);

            $toUser = User::getUser($username)
                ->addBalance($amount);

            \Yii::$app->db->createCommand()->batchInsert(
                History::tableName(),
                ['user', 'amount', 'balance'],
                [
                    [$this->id, -$amount, $this->balance],
                    [$toUser->id, $amount, $toUser->balance]
                ]
            )->execute();

            $transaction->commit();
        } catch(\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
