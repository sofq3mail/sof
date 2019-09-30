<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "exchange".
 *
 * @property int $id
 * @property string $api
 * @property double $RUBUSD
 * @property double $USDEUR
 * @property string $amount_currency
 * @property string $currency
 */
class Exchange extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exchange';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['RUBUSD', 'USDEUR','amount_currency'], 'required'],
            [['RUBUSD', 'USDEUR'], 'number'],
            [['api', 'amount_currency','currency'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api' => 'Api key',
            'RUBUSD' => 'RUB/USD',
            'USDEUR' => 'USD/EUR',
            'amount_currency' => 'Сумма в $',
            'currency' => 'Валюты',
            'currency_exchange' => 'Валюты',
        ];
    }

    public function getCurrency_exchange()
    {
        $currency = unserialize($this->currency);
        if($currency){
            $html = '';
            foreach ($currency as $key => $item)
                $html .= $key.' = '.$item.PHP_EOL;
            return $html;
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if($this->isNewRecord) {
                $base_distribution = Settings::findOne(['key' => 'base_distribution']);
                $base_distribution = unserialize($base_distribution->value);

                $base_currency = Settings::findOne(['key' => 'base_currency']);
                $base_currency = $base_currency->value;

                $data = [];
                foreach ($base_distribution as $key => $value) {
                    $data[$key] = ($base_currency !== $key) ? ($this->amount_currency * ($value / 100) * $this->valByKey($key)) : $this->amount_currency * ($value / 100);
                }
                $this->currency = serialize($data);
            }
            return true;
        } else {
            return false;
        }
    }

    private function valByKey($string){
        foreach (array_keys($this->attributes) as $key){
            if(strpos($key,$string) !== false)
                $s = $this->attributes[$key];
        }
        return $s;
    }
}
