<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pet_image".
 *
 * @property int $id
 * @property int|null $pet_id
 * @property string|null $image
 */
class PetImage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pet_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pet_id'], 'integer'],
            [['image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pet_id' => 'Pet ID',
            'image' => 'Image',
        ];
    }
}
