<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $key
 * @property string $value
 */
class Settings extends \yii\db\ActiveRecord
{
    public $usd;
    public $eur;
    public $rub;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'string', 'max' => 255],
            [['usd', 'eur', 'rub'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }


    public function getCurrency($currency)
    {
        if (($this->key == 'base_distribution' || $this->key == 'new_distribution') && $this->value) {
            $data = unserialize($this->value);
            return $data[$currency];
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
//            var_dump(empty($this->eur));
            if (($this->key == 'base_distribution' || $this->key == 'new_distribution') && ((
                    empty($this->eur) !== true ||
                    empty($this->rub) !== true ||
                    empty($this->usd) !== true
                ))) {
                if(
                    ($this->usd + $this->eur + $this->rub !== 100)){
                    $this->addError('eur', 'Сумма значений должна равняться 100%');
                    return false;
                }

                $data = ['USD' => $this->usd, 'EUR' => $this->eur, 'RUB' => $this->rub];
                $this->value = serialize($data);
            }
            return true;
        } else {
            return false;
        }
    }

    public function getValues()
    {
        if (($this->key == 'base_distribution' || $this->key == 'new_distribution') && $this->value) {
            $currency = unserialize($this->value);
            $html = '';
            foreach ($currency as $key => $item)
                $html .= $key . ' = ' . $item . '%' . PHP_EOL;
            return $html;
        }
        return $this->value;

    }
}
