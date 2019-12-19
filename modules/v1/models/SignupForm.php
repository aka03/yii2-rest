<?php

namespace app\modules\v1\models;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;
use app\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $first_name;
    public $last_name;
    public $patronymic_name;
    public $login;
    public $email;
    public $password;

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'password'], 'required'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'password'], 'trim'],
            [['first_name', 'last_name', 'patronymic_name', 'login'], 'match', 'pattern' => '/^[a-zA-Zа-яА-Я]+$/u'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            [
                ['login'], 'unique',
                'targetClass' => '\app\models\User',
                'message' => 'This login has already been taken.'
            ],
            [
                ['email'], 'unique',
                'targetClass' => '\app\models\User',
                'message' => 'This email address has already been taken.'
            ],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->patronymic_name = $this->patronymic_name;
        $user->login = $this->login;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->save(false);

        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole('author');
        $auth->assign($authorRole, $user->getId());

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'patronymic_name' => 'Patronymic Name',
            'login' => 'Login',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }
}
