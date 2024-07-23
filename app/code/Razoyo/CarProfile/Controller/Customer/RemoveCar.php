<?php
namespace Razoyo\CarProfile\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Razoyo\CarProfile\Model\ResourceModel\CarInfo\CollectionFactory as CarInfoCollectionFactory;
use Razoyo\CarProfile\Model\ResourceModel\CarInfo as CarInfoResource;

class RemoveCar extends Action
{
    protected $resultJsonFactory;
    protected $carInfoCollectionFactory;
    protected $carInfoResource;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CarInfoCollectionFactory $carInfoCollectionFactory,
        CarInfoResource $carInfoResource
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->carInfoCollectionFactory = $carInfoCollectionFactory;
        $this->carInfoResource = $carInfoResource;
        parent::__construct($context);
    }

    public function execute()
    {
        $carId = $this->getRequest()->getParam('car_id');
        $result = ['success' => false];

        if ($carId) {
            try {
                // Load the collection
                $collection = $this->carInfoCollectionFactory->create();
                $collection->addFieldToFilter('car_id', $carId);

                // Check if any car matches the ID
                if ($collection->getSize() > 0) {
                    foreach ($collection as $carInfoModel) {
                        // Delete the model
                        $this->carInfoResource->delete($carInfoModel);
                    }
                    $result['success'] = true;
                } else {
                    $result['message'] = 'Car ID not found.';
                }
            } catch (LocalizedException $e) {
                $result['message'] = $e->getMessage();
            } catch (\Exception $e) {
                $result['message'] = 'An error occurred while deleting the car.';
            }
        } else {
            $result['message'] = 'No car ID provided.';
        }

        $response = $this->resultJsonFactory->create();
        return $response->setData($result);
    }
}
