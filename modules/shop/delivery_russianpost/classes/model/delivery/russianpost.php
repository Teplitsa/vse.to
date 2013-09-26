<?php defined('SYSPATH') or die('No direct script access.');

class Model_Delivery_RussianPost extends Model_Delivery
{
    /**
     * Get list of all available post types
     * 
     * @return array
     */
    public static function post_types()
    {
        return array(
            44 => 'EMS обыкновенный',
            45 => 'EMS с объявленной ценностью',
            23 => 'Заказная бандероль',
            52 => 'Заказная бандероль 1 класса',
            12 => 'Заказная карточка',
            13 => 'Заказное письмо',
            50 => 'Заказное письмо 1 класса',
            55 => 'Заказное уведомление',
            54 => 'СЕКОГРАММА',
            26 => 'Ценная бандероль',
            53 => 'Ценная бандероль 1 класса',
            36 => 'Ценная посылка',
            16 => 'Ценное письмо',
            51 => 'Ценное письмо 1 класса'
        );
    }

    /**
     * Get list of all available transfer types
     * 
     * @return array
     */
    public static function post_transfers()
    {
        return array(
            1 => 'НАЗЕМН.',
            2 => 'АВИА',
            3 => 'КОМБИН.',
            4 => 'УСКОР'
        );
    }

    /**
     * Default settings for this delivery type
     * 
     * @return array
     */
    public function default_settings()
    {
        return array(
            'viewPost' => 23,
            'typePost' => 1
        );
    }

    /**
     * Calculate delivery price
     * 
     * @param  Model_Order $order
     * @return float
     */
    public function calculate_price(Model_Order $order)
    {
        $price = 0.0;

        if ($order->postcode == '')
        {
            $this->error('Не указан индекс!');
            return $price;
        }

        if ($order->total_weight <= 0)
        {
            $this->error('Вес заказа равен 0');
            return $price;
        }

        //viewPost - вид отправления
        //typePost - способ пересылки
        //postOfficeIf - индекс получателя
        //weight - вес (в граммах)
        //value1 - Объявленная ценность

        $url = 
            'http://www.russianpost.ru/autotarif/Autotarif.aspx'
          . '?viewPost=' . (int) $this->settings['viewPost']
          . '&countryCode=643' // Российская Федерация
          . '&typePost=' . (int) $this->settings['typePost']
          . '&weight=' . (int) $order->total_weight
          . '&value1=' . round($order->sum->amount) // Объявленная ценость = стоимость заказа (сервис почты России разрешает только круглую сумму, без копеек)
          . '&postOfficeId=' . urlencode($order->postcode);

        // Peform an http request to russianpost server
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //timeout in seconds

        /*
        curl_setopt($ch, CURLOPT_PROXY, '192.168.20.99');
        curl_setopt($ch, CURLOPT_PROXYPORT, '8080');
         */

        $response = curl_exec($ch);
        
        if ( $response === FALSE)
        {
            if (Kohana::$environment === Kohana::DEVELOPMENT)
            {
                $this->error(curl_error($ch));
            }
            else
            {
                $this->error('Произошла ошибка при обращении к серверу почты России');
            }
            return $price;
        }        
        curl_close($ch);

        // ------ Parse response

        // Searh for error text
        if (preg_match('!<span\s+id="lblErrStr"\s*>([^<>]*)!i', $response, $matches))
        {
            $error = trim($matches[1]);
            if ($error != '')
            {
                $this->error($error);
                return $price;
            }
        }

        // Parse result
        if (preg_match('!<span\s+id="TarifValue"\s*>([\d\.,]*)</span>!i', $response, $matches))
        {
            $price = l10n::string_to_float($matches[1]);
        }
        else
        {
            $this->error('Ошибка при расчёте стоимости доставки');
            return $price;
        }

        return new Money($price);
    }
}