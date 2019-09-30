<?php

namespace console\controllers;

use common\models\Exchange;
use common\models\Settings;
use yii\console\Controller;
use yii\httpclient\Client;

class CurrencyController extends Controller
{
    const CURRENCY_PARE = [
        'RUB' => 'RUBUSD',
        'EUR' => 'USDEUR',
    ];

    private $url = 'http://test.loc/exchange/';
    private $base_distribution;
    private $new_distribution;
    private $base_currency;


    public function actionIndex()
    {
        $exchange = Exchange::find()->where(['not', ['amount_currency' => null]])->all();

        $base_distribution = Settings::findOne(['key' => 'base_distribution']);
        $this->base_distribution = unserialize($base_distribution->value);

        $base_currency = Settings::findOne(['key' => 'base_currency']);
        $this->base_currency = $base_currency->value;

        $new_distribution = Settings::findOne(['key' => 'new_distribution']);
        $this->new_distribution = unserialize($new_distribution->value);

        if ($this->new_distribution) {
            foreach ($exchange as $item) {
                $balance = $this->getBalances($item->id);
                $all_price = $this->getAllPrice($item->id);
                $balance_on_base = $this->balanceOnBaseCurrency($balance, $all_price);
                $amount_on_base = array_sum($balance_on_base);
                $newBalance = $this->newBalance($amount_on_base);

                $currency = [];
                foreach ($this->new_distribution as $key => $val){
                    $difference =  $newBalance[$key] - $balance_on_base[$key];
                    if($difference < 0){
                        echo 'продать '.$key.' = '.$difference.PHP_EOL; // отправляем запрос на продажу (buy("USDRUB", 15))
                    }
                    if($difference > 0 && $key !== $this->base_currency){
                        echo 'купить '.$key.' = '.$difference.PHP_EOL; // отправляем запрос на покупку (buy("RUBUSD", 15))
                    }
                    $currency[$key] = ($balance_on_base[$key] + $difference);
                }

                $item->currency = serialize($this->saveBalance($currency, $all_price));
                if($item->save()){
                    echo 'Данные о бирже №'.$item->id.' обновлены'.PHP_EOL;
                }
            }
            $new_distribution->value = null;
            $new_distribution->save();
            $base_distribution->value = serialize($this->new_distribution);
            $base_distribution->save();
        }else{
            echo 'Задайте новое распределение валют';
        }
    }

    /**
     * Новый баланс с новым распределением
     * @param $balance
     * @param $all_price
     * @return array
     */
    private function saveBalance($balance, $all_price){
        $data = [];
        foreach ($balance as $key => $item){
            $data[$key] = ($key != $this->base_currency) ? $item * $all_price[$key] : $item;
        }
        return $data;
    }

    /**
     * Баланс в основной валюте из нового распределения
     * @param $amount
     * @return array
     */
    private function newBalance($amount){
        $data = [];
        foreach ($this->new_distribution as $key => $value) {
            $data[$key] = intval($amount * ($value / 100));
        }
        return $data;
    }

    /**
     * Баланс на бирже в базовой валюте
     * @param $balance
     * @param $all_price
     * @return array
     */
    private function balanceOnBaseCurrency($balance, $all_price)
    {
        $data = [];
        foreach ($balance as $key => $item) {
            $data[$key] = ($key == $this->base_currency) ? $item : $item / $all_price[$key];
        }
        return $data;
    }


    /**
     * Получаем балан с биржи
     * @param $id
     * @return mixed
     */
    private function getBalances($id)
    {
        $exchange = $this->sendApi($id);
        $balance = json_decode($exchange->content);
        return unserialize($balance->currency);
    }

    /**
     * Получаем курсы всех валют на одной бирже
     * @param $id
     * @return array
     */
    private function getAllPrice($id)
    {
        $all_currency = self::CURRENCY_PARE;
        $data = [];
        foreach ($all_currency as $key => $item) {
            $data[$key] = $this->getPrice($id, $all_currency[$key]);
        }
        return $data;
    }

    /**
     * Получаем курс одной валюты
     * @param $id
     * @param $currency
     * @return mixed
     */
    private function getPrice($id, $currency)
    {
        $exchange = $this->sendApi($id);
        $price = json_decode($exchange->content);
        return $price->$currency;
    }


    /**
     * Отправляем запрос на биржу.
     * @param $action
     * @return \yii\httpclient\Response
     */
    private function sendApi($action)
    {
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setFormat(Client::FORMAT_JSON)
            ->setUrl($this->url . $action)
            ->send();
        return $response;
    }


}