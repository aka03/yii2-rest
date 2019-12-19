<?php

namespace app\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "pet".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $breed
 * @property int $category_id
 * @property string|null $comment
 * @property float|null $price
 * @property string $user_mail
 * @property string $user_phone
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Pet extends \yii\db\ActiveRecord
{
    const SOLD_OUT = 0;
    const FOR_SALE = 10;

    const MAX_UPLOAD_IMAGES = 10;

    public static $statuses = [self::SOLD_OUT, self::FOR_SALE];
    public static $statusesWithKeys = [
        self::SOLD_OUT => 'Sold Out',
        self::FOR_SALE => 'For Sale'
    ];

    /**
     * @var UploadedFile[]
     */
    public $images;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pet';
    }

    /**
     * {@inheritdoc}
     */
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
        $maxFiles = self::MAX_UPLOAD_IMAGES - count($this->image);

        return [
            [['name', 'breed', 'category_id', 'status', 'price', 'user_email', 'user_phone'], 'required'],
            [['user_id', 'category_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['price'], 'number'],
            [['user_email'], 'email'],
            [['user_phone'], 'string', 'min' => 10],
            [['user_phone'], 'match', 'pattern' => '/^([+]?[\s0-9]+)?(\d{3}|[(]?[0-9]+[)])?([-]?[\s]?[0-9])+$/'],
            ['status', 'default', 'value' => self::FOR_SALE],
            [['status'], 'in', 'range' => self::$statuses],
            [['name', 'breed', 'comment'], 'string', 'max' => 255],
            [['images'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => $maxFiles],
            [
                ['category_id'], 'exist',
                'targetRelation' => 'category',
                'message' => 'Incorrect category.'
            ],
            [['images'], 'safe']
        ];
    }

    /**
     * Relation for PetCategory class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(PetCategory::className(), ['id' => 'category_id']);
    }

    /**
     * Relation for PetImage class.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasMany(PetImage::className(), ['pet_id' => 'id']);
    }

    /**
     * Validation.
     *
     * @return |null
     * @throws \yii\base\Exception
     */
    public function validatePet()
    {
        if (!$this->validate()) {
            return null;
        }

        $category = PetCategory::find()->where(['id' => $this->category_id])->one();

        if (!$category) {
            $this->addError('category_id', 'Incorrect category.');
        }

        if ($this->isNewRecord) {
            $this->user_id = Yii::$app->user->identity->id;
            $this->status = self::FOR_SALE;
        }

        $this->save(false);

        if (!empty($this->images)) {
            $path = Yii::getAlias('@app') . '/web/uploads/pets/' . Yii::$app->user->identity->id . '/';
            FileHelper::createDirectory($path);

            foreach ($this->images as $file) {
                $fileName = $this->getUniqueFileName($path, $file) . '.' . $file->extension;
                $file->saveAs($path . $fileName);

                $petImage = new PetImage();
                $petImage->pet_id = $this->id;
                $petImage->image = $fileName;
                $petImage->save();
            }
        }
    }

    /**
     * Unique filename for uploaded image.
     *
     * @param $path
     * @param $file
     * @return string
     */
    private function getUniqueFileName($path, $file)
    {
        $filename = md5($file->baseName . '_' . date('Y-m-d') . '_' . mt_rand(0, 99999));
        if (file_exists($path . $filename . '.' . $file->extension)) {
            return $this->getUniqueFileName($path, $file);
        }

        return $filename;
    }

    /**
     * Fields allowed to display in response.
     *
     * @return array|false
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['user_id']);

        $fields['category'] = function () {
            return $this->category->name;
        };

        $fields['status_value'] = function () {
            return self::$statusesWithKeys[$this->status];
        };

        $fields['images'] = function () {
            return $this->image;
        };

        return $fields;
    }
}
