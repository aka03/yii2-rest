<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\TokenForm;
use app\modules\v1\models\ProfileForm;
use Yii;
use app\modules\v1\models\SignupForm;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use app\modules\v1\models\Token;

/**
 * @OA\Info(
 *   version="1.0",
 *   title="Simple API",
 * ),
 * @OA\Server(
 *   url="/api/v1",
 *   description="main server",
 * ),
 * @OA\SecurityScheme(
 *      securityScheme="Bearer",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */
class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['options', 'token', 'signup']
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['token', 'signup', 'index', 'view', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['token', 'signup'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update', 'delete'],
                        'matchCallback' => function() {
                            return array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId()))[0] =='admin';
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * Remove all default actions.
     *
     * @return array|void
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update']);

        return $actions;
    }

    /**
     * User list.
     *
     * @OA\Get(path="/users",
     *     tags={"User"},
     *     summary="Get User list. (Admin only)",
     *     @OA\Response(
     *         response = 200,
     *         description = "User list",
     *         @OA\Schema(ref = "#/components/schemas/User")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     security={{"Bearer":{}}}
     * )
     */
    public function actionIndex()
    {

    }

    /**
     * Get user by ID.
     *
     * @OA\Get(path="/users/{id}",
     *     tags={"User"},
     *     summary="Get user by ID. (Admin only)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Get user by ID",
     *         @OA\Schema(ref = "#/components/schemas/User")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     */
    public function actionView()
    {

    }

    /**
     * Signs user up.
     *
     * @OA\Post(path="/users/signup",
     *     tags={"User"},
     *     summary="User sign up.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Created user object",
     *         @OA\JsonContent(ref="#/components/schemas/UserAuth")
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "User sign up",
     *         @OA\Schema(ref="#/components/schemas/UserAuth")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     *
     * @return array
     * @throws \Exception
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->bodyParams, '')) {
            if (!$model->signup()) {
                return ['errors' => $model->getErrors()];
            }

            return ['success' => 'Thank you for registration.'];
        }

        return ['errors' => 'Invalid Credentials'];
    }

    /**
     * Get user token action.
     *
     * @OA\Post(path="/users/token",
     *     tags={"User"},
     *     summary="Get token.",
     *     @OA\RequestBody(
     *       required=true,
     *       description="Created user object",
     *       @OA\JsonContent(ref="#/components/schemas/UserAuth")
     *   ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Get token",
     *         @OA\Schema(ref = "#/components/schemas/Token")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     *
     * @return Token|array|null
     */
    public function actionToken()
    {
        $model = new TokenForm();
        $model->load(Yii::$app->request->bodyParams, '');

        if (!$model->validate()) {
            return ['errors' => $model->getErrors()];
        }

        if ($token = $model->auth()) {
            return $token;
        }
    }

    /**
     * Update user credentials.
     *
     * @OA\Put(path="/users/{id}",
     *     tags={"User"},
     *     summary="Update user credentials. (Admin only)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Update user info",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Update user credentials",
     *         @OA\Schema(ref = "#/components/schemas/User")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @param $id
     * @return ProfileForm|array|null
     */
    public function actionUpdate($id)
    {
        $model = ProfileForm::findOne($id);

        if (Yii::$app->request->isPut) {
            $model->load(Yii::$app->request->bodyParams, '');

            if (!$model->save()) {
                return ['errors' => $model->getErrors()];
            }

            return $model;
        }
    }

    /**
     * Delete user action.
     *
     * @OA\Delete(path="/users/{id}",
     *     tags={"User"},
     *     summary="Delete user. (Admin only)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Delete user",
     *         @OA\Schema(ref = "#/components/schemas/User")
     *     ),
     *     @OA\Response(response=204, description="Delete success"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     */
    public function actionDelete($id)
    {

    }
}
