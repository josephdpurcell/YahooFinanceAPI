<?php

class YahooFinanceAPI
{
    public $api_url = 'http://query.yahooapis.com/v1/public/yql';

    /**
     * @param array $tickers The array of ticker symbols
     * @param array|bool $fields Array of fields to get from the returned XML
     * document, or if true use default fields, or if false return XML
     *
     * @return array|string The array of data or the XML document
     */
    public function api ($tickers,$fields=true) {
        // set url
        $url = $this->api_url;
        $url .= '?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20%28%22'.implode(',',$tickers).'%22%29&env=store://datatables.org/alltableswithkeys';

        // set fields
        if ($fields===true || empty($fields)) {
            $fields = array(
                    'Symbol','Name','Change','ChangeRealtime','PERatio',
                    'PERatioRealtime','Volume','PercentChange','DividendYield',
                    'LastTradeRealtimeWithTime','LastTradeWithTime','LastTradePriceOnly','LastTradeTime',
                    'LastTradeDate'
                    );
        }

        // make request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $resp = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 

        // parse response
        if (!empty($fields)) {
            $xml = new SimpleXMLElement($resp);
            $data = array();
            $row = array();
            $time = time();
            if(is_object($xml)){
                foreach($xml->results->quote as $quote){
                    $row = array();
                    foreach ($fields as $field) {
                        $row[$field] = (string) $quote->$field;
                    }
                    $data[] = $row;
                }
            }
        } else {
            $data = $resp;
        }

        return $data;
    }
}
