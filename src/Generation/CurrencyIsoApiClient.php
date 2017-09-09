<?php

namespace IsoCurrency\Generation;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;

class CurrencyIsoApiClient
{
    const URL = 'http://www.currency-iso.org/dam/downloads/lists/list_one.xml';

    /** @var HttpClient */
    private $httpClient;

    /** @var RequestFactory */
    private $requestFactory;

    public function __construct(HttpClient $httpClient, RequestFactory $requestFactory)
    {

        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return array
     * @throws \Http\Client\Exception
     * @throws \Exception
     */
    public function fetch()
    {
        $countries = [];

        $request = $this->requestFactory->createRequest('GET', self::URL);
        $response = $this->httpClient->sendRequest($request);
        $xml = new \SimpleXMLElement($response->getBody());

        foreach ($xml->{'CcyTbl'}->{'CcyNtry'} as $countryXml) {
            $code = (string)$countryXml->{'Ccy'};

            if (empty($code)) {
                continue;
            }

            $countries[$code] = new Country(
                (string)$countryXml->{'CtryNm'},
                (string)$countryXml->{'CcyNm'},
                (string)$countryXml->{'Ccy'},
                (int)$countryXml->{'CcyNbr'},
                (int)$countryXml->{'CcyMnrUnts'}
            );
        }

        return $countries;
    }
}