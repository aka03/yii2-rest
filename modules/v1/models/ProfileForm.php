<?php

namespace app\modules\v1\models;

use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use app\models\User;

/**
 * Signup form
 */
class ProfileForm extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = parent::rules();

        return ArrayHelper::merge($rules, [
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'status'], 'required'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email', 'status'], 'trim'],
            [['first_name', 'last_name', 'patronymic_name', 'login'], 'match', 'pattern' => '/^[a-zA-Zа-яА-Я]+$/u'],
            [['first_name', 'last_name', 'patronymic_name', 'login', 'email'], 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
        ]);
    }

    /**
     * Signs user up.
     *
     * @return bool|null
     * @throws \Exception
     */
    public static function getFormForUser($id)
    {
        $userModel = User::findOne(['id' => $id]);

        if (!$userModel) {
            return null;
        }

        $form = new self();
        $form->first_name = $userModel->first_name;
        $form->last_name = $userModel->last_name;
        $form->patronymic_name = $userModel->patronymic_name;
        $form->login = $userModel->login;
        $form->email = $userModel->email;
        $form->status = $userModel->status;

        return $form;
    }

    /**
     * Update user credentials.
     *
     * @return mixed|null
     */
    public function updateUserWithFormData()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = $this->_user;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->patronymic_name = $this->patronymic_name;
        $user->login = $this->login;
        $user->email = $this->email;
        $user->status = $this->status;

        return $user->save() ? $user : null;
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
    protected static function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }
}
