<?php
/**
 * Client library to communicate with Product API
 */
namespace App\Lib\Network;

use GuzzleHttp\Client,
    App\Lib\Utility\Hash;

class CompareEngineClient extends Client {
    
    private $countryCode;
    private $languageCode;
    
    /**
     * Instantiate CompareEngineClient with locale configuration
     * 
     * Example:
     * $ce = new ComparisonEngineClient(array(
     *     'locale' => array(
     *         'countryCode' => 'hk',
     *         'languageCode' => 'zh'
     *     ),
     *     'oauth' => array(
     *         'token'
     *     )
     * ));
     * 
     * @param type $config
     */
    public function __construct($config=array())
    {
        try {
            $this->countryCode = $config['locale']['countryCode'];
            $this->languageCode = $config['locale']['languageCode'];
            $this->oauthToken = $config['oauth']['token'];
            unset($config['locale']);
        } catch (Exception $ex) {
            throw new Exception('Locale not set', 123, $ex);
        }
        
        parent::__construct($config);
    }
    
    /**
     * getChannels
     * 
     * Retrieve an array of available channels for this website
     * 
     * @param type $params Filter response by properties
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getChannels($params=array(), $options=array())
    {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('channels', $options);
        return $response->json();
    }
    
    /**
     * getChannelDetails
     * 
     * Retrieve information about a channel by ID or Alias
     * 
     * Example:
     * $ccInfo = $compareEngineClient->getChannelDetails('credit-cards');
     * 
     * @param type $ref Channel ID or Alias
     * @param type $params
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getChannelDetail($ref=null, $params=array(), $options=array())
    {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('channels/' . $ref, $options);
        return $response->json();
    }
    
    /**
     * getProducts
     * 
     * Retrieve an array of products matching the provided parameters
     * 
     * Example:
     * $creditCardList = $compareEngineClient->getProducts(array(
     *     'channel' => 'credit-cards',
     *     'query' => array(
     *         'companyName' => array(
     *             '$in' => array('HSBC')
     *         ),
     *         'cardProvider' => array(
     *             '$in' => array('Mastercard')
     *         )
     *     ),
     *     'sort' => array(
     *         'lowestMonthlyFlatRate' => 1,
     *         'computedLaprAverage' => -1
     *     ),
     *     'limit' => 15,
     *     'offset' => 30
     * ));
     * 
     * @param type $params Filter response by properties
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getProducts($params=array(), $options=array()) {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('products', $options);
        return $response->json();
    }
    
    /**
     * getProductDetails
     * 
     * Retrieve information about a product by ID
     * 
     * Example:
     * $productInfo = $compareEngineClient->getProductDetail(4826);
     * 
     * @param type $id Product ID
     * @param type $params
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getProductDetail($id=null, $params=array(), $options=array()) {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('products/' . $id, $options);
        return $response->json();
    }
    
    /**
     * getCompanies
     * 
     * Retrieve an array of companies matching the provided parameters
     * 
     * Example:
     * $creditCardList = $compareEngineClient->getProducts(array(
     *     'channel' => 'credit-cards'
     * ));
     * 
     * @param type $params Filter response by properties
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getCompanies($params=array(), $options=array())
    {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('companies', $options);
        return $response->json();
    }
    
    /**
     * getCompanyDetails
     * 
     * Retrieve information about a company by ID or Alias
     * 
     * Example:
     * $hsbcInfo = $compareEngineClient->getCompanyDetail('hsbc');
     * 
     * @param type $ref Channel ID or Alias
     * @param type $params
     * @param type $options GuzzleHttp Request options
     * @return GuzzleHttp\Message\Response
     */
    public function getCompanyDetail($ref=null, $params=array(), $options=array())
    {
        $options = $this->populateRequestOptions($params, $options);
        $response = $this->get('companies/' . $ref, $options);
        return $response->json();
    }
    
    public function captureLead($params=array())
    {
        
    }
    
    /**
     * Prepopulate query parameters with countryCode, languageCode etc
     * 
     * @param type $params
     * @param type $options GuzzleHttp Request options
     * @return array GuzzleHttp Request options
     */
    protected function populateRequestOptions($params=array(), $options=array())
    {
        $query = Hash::merge((array) $params, array(
            'countryCode'           => $this->countryCode,
            'language'              => $this->languageCode,
            'oauth2_access_token'   => $this->oauthToken
        ));
        return Hash::merge($options, array('query' => $query));
    }
    
}
