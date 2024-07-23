<?php
namespace Razoyo\CarProfile\Block\Customer;

use Magento\Framework\View\Element\Template;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Razoyo\CarProfile\Model\CarInfoFactory;
use Magento\Customer\Model\Session as CustomerSession;

class SelectCar extends Template
{
        /**
     * @var Client
     */
    private $httpClient;

    /**
     * View constructor.
     *
     * @param Template\Context $context
     * @param Client $httpClient
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Client $httpClient,
        public CustomerSession $customerSession,
        public CarInfoFactory $carInfoFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpClient = $httpClient;
    }

    /**
     * Fetch cars data from API
     *
     * @return array|bool
     */
    public function getCarsData()
    {
        try {
            // Make GET request to API
            $response = $this->httpClient->request('GET', 'https://exam.razoyo.com/api/cars');
            // Check if request was successful (status code 200)
            if ($response->getStatusCode() == 200) {
                $headers = $response->getHeaders();
                $yourToken = isset($headers['your-token'][0]) ? $headers['your-token'][0] : null;
                if ($yourToken) {
                    $this->customerSession->setCartApiToken($yourToken);
                }
                
                $responseData = json_decode($response->getBody()->getContents(), true);
                return $responseData;
            }
        } catch (GuzzleException $e) {
            // Handle Guzzle exceptions (e.g., connection issues)
            $this->_logger->error($e->getMessage());
        }
        
        return false;
    }

      /**
     * Fetch car info based on customerId using factory model
     *
     * @return array
     */
    public function getCarInfoByCustomerId()
    {
        $carInfoData = [];
        $customerId = $this->customerSession->getCustomerId();

        try {
            // Load car info collection by customerId using factory model
            $carInfoCollection = $this->carInfoFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
            foreach ($carInfoCollection as $carInfo) {
                // Example: Extract required fields and format as needed
                $carInfoData[] = [
                    'car_id' => $carInfo->getCarId(),
                    'car_name' => $carInfo->getCarName(),
                    // Add more fields as per your requirement
                ];
            }
        } catch (\Exception $e) {
            // Handle exception if necessary
            $this->_logger->error($e->getMessage());
        }

        return $carInfoData;
    }
}
