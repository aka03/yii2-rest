<?php

namespace app\modules\v1\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PetSearch represents the model behind the search form of `app\modules\v1\models\Pet`.
 */
class PetSearch extends Pet
{
    public $category;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'breed'], 'safe'],
            ['comment', 'string', 'max' => 5000],
            [['price'], 'number'],
            [['category'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Pet::find();

        $query->joinWith(['category', 'image'])->groupBy('pet.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                    'name' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $dataProvider->sort->attributes['category'] = [
            'asc' => ['pet_category.name' => SORT_ASC],
            'desc' => ['pet_category.name' => SORT_DESC],
        ];

        $query->andFilterWhere([
            'price' => $this->price,
            'status' => $this->status,
        ]);

        $query
            ->andFilterWhere(['like', 'breed', $this->breed])
            ->andFilterWhere(['like', 'pet_category.name', $this->category]);

        return $dataProvider;
    }
}
