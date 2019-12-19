<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Pet;
use app\modules\v1\models\PetSearch;
use app\modules\v1\models\PetCategory;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\filters\Cors;

/**
 * PetController implements the CRUD actions for Pet model.
 */
class PetController extends ActiveController
{
    public $modelClass = 'app\modules\v1\models\Pet';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'except' => ['options']
            ],
            'corsFilter' => [
                'class' => Cors::className(),
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'categories', 'statuses'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['createPost'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'delete'],
                        'roles' => ['updatePost'],
                        'roleParams' => function() {
                            return ['model' => Pet::findOne(['id' => Yii::$app->request->get('id')])];
                        },
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['index'], $actions['update']);

        $actions['error']['class'] = 'app\components\ErrorAction';

        return $actions;
    }

    /**
     * Lists all Pet models.
     *
     * @OA\Get(path="/pets",
     *     tags={"Pet"},
     *     summary="Retrieves the collection of Pet resources.",
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="breed",
     *         in="query",
     *         description="Filter by breed",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         description="Filter by price",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status. [0: 'Sold Out', 10: 'For Sale']",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Pet collection response",
     *         @OA\Schema(ref = "#/components/schemas/Pet")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, '');

        return $dataProvider;
    }

    /**
     * Displays a single Pet model.
     *
     * @OA\Get(path="/pets/{id}",
     *     tags={"Pet"},
     *     summary="Displays a single Pet model.",
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
     *         description = "Displays a single Pet model",
     *         @OA\Schema(ref = "#/components/schemas/Pet")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     @OA\Response(response=404, description="Resource Not Found"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Pet model.
     *
     * @OA\Post(path="/pets",
     *     tags={"Pet"},
     *     summary="Add pet.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Add pet",
     *         @OA\JsonContent(ref="#/components/schemas/Pet"),
     *         @OA\Schema(ref="#/components/schemas/Pet")
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Add pet",
     *         @OA\Schema(ref = "#/components/schemas/Pet")
     *     ),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=401, description="Invalid credentials"),
     *     security={{"Bearer":{}}}
     * )
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Pet();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->bodyParams, '');
            $model->images = UploadedFile::getInstancesByName('images');

            $model->validatePet();
            if (!$model->getErrors()) {
                return ['success' => 'Pet has been added.'];
            }

            return ['errors' => $model->getErrors()];
        }
    }

    /**
     * Updates an existing Pet model.
     *
     * @OA\Put(path="/pets/{id}",
     *     tags={"Pet"},
     *     summary="Update pet info.",
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
     *         description="Update pet info",
     *         @OA\JsonContent(ref="#/components/schemas/Pet")
     *     ),
     *     @OA\Response(
     *         response = 200,
     *         description = "Update pet info",
     *         @OA\Schema(ref = "#/components/schemas/Pet")
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
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPut) {
            $model->load(Yii::$app->request->bodyParams, '');
            $model->images = UploadedFile::getInstancesByName('images');

            $model->validatePet();
            if (!$model->getErrors()) {
                return ['success' => 'Pet has been updated.'];
            }

            return ['errors' => $model->getErrors()];
        }
    }

    /**
     * Deletes an existing Pet model.
     *
     * @OA\Delete(path="/pets/{id}",
     *     tags={"Pet"},
     *     summary="Delete pet.",
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
     *         description = "Delete pet.",
     *         @OA\Schema(ref = "#/components/schemas/Pet")
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
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Relation for PetCategory model.
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function actionCategories()
    {
        return PetCategory::find()->all();
    }

    /**
     * Return statuses as key:value.
     *
     * @return array
     */
    public function actionStatuses()
    {
        return Pet::$statusesWithKeys;
    }

    /**
     * Finds the Pet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Pet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Pet::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
