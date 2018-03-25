<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * SendForm is the model behind the snd form.
 */
class SendForm extends Model
{
    public $username;
    public $amount;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'amount'], 'required'],
            [['username'], 'string', 'max' => 255],
            ['username', 'otherUser'],
            ['amount', 'number', 'min' => 0.01],
            ['amount', 'match', 'pattern' => "/^[0-9]+(?:\.[0-9]{2}){0,1}$/", 'message' => '{attribute} should be numeric with 2 digits after dot'],
            ['amount', 'enoughAmount'],
        ];
    }

    /**
     * Send amount of money to username
     * @return bool whether the user is logged in successfully
     */
    public function send()
    {
        if ($this->validate()) {
            User::getUser(Yii::$app->user->identity->username)
                ->sendMoney(
                    $this->username,
                    $this->amount
                );

            Yii::$app->user->identity->balance -= $this->amount;

            return true;
        }

        return false;
    }

    public function enoughAmount($attribute) {
        if (Yii::$app->user->identity->balance - $this->$attribute < -1000) {
            $this->addError($attribute, 'Not enough amount of money. You will have less than -1000 after sending money.');
        }
    }

    public function otherUser($attribute) {
        if (Yii::$app->user->identity->username == $this->$attribute) {
            $this->addError($attribute, 'You can send money only to other users');
        }
    }
}