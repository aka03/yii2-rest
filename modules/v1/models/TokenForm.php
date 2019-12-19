<?php

namespace app\modules\v1\models;

use yii\base\Model;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class TokenForm extends Model
{
    public $first_name;
    public $last_name;
    public $patronymic_name;
    public $login;
    public $email;
    public $password;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'password'], 'required'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'password'], 'trim'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'password'], 'string'],
            ['email', 'email'],
            ['password', 'validateData'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateData($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Invalid Credentials.');
            }
        }
    }

    /**
     * Finds user by login.
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByLogin($this->login);
        }

        return $this->_user;
    }

    /**
     * Return new token and delete old if exists.
     *
     * @return Token|null
     * @throws \yii\base\Exception
     */
    public function auth()
    {
        if ($this->validate()) {
            $token = new Token();
            $token->user_id = $this->getUser()->id;
            $token->generateToken(time() + 3600 * 24);

            if ($token->save()) {
                $token::deleteAll(['and', 'user_id = :user_id', ['not in', 'token', $token->token]],
                    [':user_id' => $token->user_id]);

                return $token;
            }
        }

        return null;
    }
}
