<?php

namespace Razoyo\CarProfile\Block\Customer;

use Magento\Framework\View\Element\Template;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Customer\Model\Session as CustomerSessionFactory;
use Razoyo\CarProfile\Model\CarInfoFactory;
use Psr\Log\LoggerInterface;

class MySelectedCar extends Template
{
    /**
     * @var CustomerSessionFactory
     */
    protected $customerSession;

    /**
     * @var CarInfoFactory
     */
    protected $carInfoFactory;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * MySelectedCar constructor.
     *
     * @param Template\Context $context
     * @param CustomerSessionFactory $customerSession
     * @param CarInfoFactory $carInfoFactory
     * @param Client $httpClient
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CustomerSessionFactory $customerSession,
        CarInfoFactory $carInfoFactory,
        Client $httpClient,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->carInfoFactory = $carInfoFactory;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Fetch customer's car information
     *
     * @return array|bool
     */
    public function getCustomerCarInfo()
    {
        try {
            // Get customer ID from session
            $customerId = $this->customerSession->getCustomerId();
            if (!$customerId) {
                return false;
            }

            // Fetch all car IDs for the customer
            $carInfoCollection = $this->carInfoFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', $customerId)
                ->getColumnValues('car_id');
            if (empty($carInfoCollection)) {
                return [];
            }
            $responseData = [];

            // Iterate through each car ID and fetch data
            foreach ($carInfoCollection as $carId) {
                // Prepare API request URL
                $apiUrl = 'https://exam.razoyo.com/api/cars/' . $carId;
                // Fetch token from customer session
                $apiToken = $this->customerSession->getCartApiToken();

                // Make GET request to API with token in headers
                $response = $this->httpClient->request('GET', $apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiToken,
                    ],
                ]);
                // Check if request was successful (status code 200)
                if ($response->getStatusCode() == 200) {
                    $carData = json_decode($response->getBody()->getContents(), true);
                    $responseData[] = $carData;
                }
            }

            return $responseData;
        } catch (GuzzleException $e) {
            // Handle Guzzle exceptions (e.g., connection issues)
            $this->logger->error($e->getMessage());
        }

        return false;
    }
}
