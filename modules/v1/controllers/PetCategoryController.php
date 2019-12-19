<?php

namespace app\modules\v1\controllers;

use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\Response;

/**
 * PetCategoryController implements the CRUD actions for PetCategory model.
 */
class PetCategoryController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\PetCategory';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['options']
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
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['updatePost'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all Pet categories.
     *
     * @OA\Get(path="/pet-categories",
     *     tags={"PetCategory"},
     *     summary="Retrieves the collection of Pet categories.",
     *     @OA\Response(
     *         response = 200,
     *         description = "Pet categories collection response",
     *         @OA\Schema(ref = "#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @return mixed
     */
    public function actionIndex()
    {

    }

    /**
     * Displays a single Pet category.
     *
     * @OA\Get(path="/pet-categories/{id}",
     *     tags={"PetCategory"},
     *     summary="Displays a single Pet category.",
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
     *         description = "Displays a single Pet category",
     *         @OA\Schema(ref = "#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

    }

    /**
     * Creates a new Pet category.
     *
     * @OA\Post(path="/pet-categories",
     *     tags={"PetCategory"},
     *     summary="Add pet category. (Admin only)",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add pet category",
     *         @OA\JsonContent(ref="#/components/schemas/PetCategory")
     *   ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Add pet category",
     *         @OA\Schema(ref = "#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @return mixed
     */
    public function actionCreate()
    {

    }

    /**
     * Updates an existing Pet category.
     *
     * @OA\Put(path="/pet-categories/{id}",
     *     tags={"PetCategory"},
     *     summary="Update pet category info. (Admin only)",
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
     *         description="Update pet category info",
     *         @OA\JsonContent(ref="#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Update pet category info",
     *         @OA\Schema(ref = "#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

    }

    /**
     * Deletes an existing Pet category.
     *
     * @OA\Delete(path="/pet-categories/{id}",
     *     tags={"PetCategory"},
     *     summary="Delete pet category. (Admin only)",
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
     *         description = "Delete pet category.",
     *         @OA\Schema(ref = "#/components/schemas/PetCategory")
     *     ),
     *     @OA\Response(response=204, description="Delete success"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @param $id
     */
    public function actionDelete($id)
    {

    }
}
