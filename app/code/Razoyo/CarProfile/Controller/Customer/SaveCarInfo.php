<?php
namespace Razoyo\CarProfile\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Razoyo\CarProfile\Model\CarInfoFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Razoyo\CarProfile\Model\ResourceModel\CarInfo as CarInfoResource;

class SaveCarInfo extends Action
{

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * SaveCarInfo constructor.
     *
     * @param Context $context
     * @param CarInfoInterfaceFactory $carInfoFactory
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        public CarInfoFactory $carInfoFactory,
        public CarInfoResource $carInfoResource
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
    }

    /**
     * Execute method to save car info.
     */
    public function execute()
    {
        $carId = $this->getRequest()->getParam('car_id');
        $customerId = $this->customerSession->getCustomerId();

        try {
            $data = [
                'customer_id' => $customerId,
                'car_id' => $carId,
            ];
           
            $carInfoModel = $this->carInfoFactory->create();
            $carInfoModel->addData($data);
            $carInfoModel->save();

            $result = ['success' => true, 'message' => 'Car information saved successfully.'];
        } catch (\Exception $e) {
            $result = ['success' => false, 'message' => 'Error occurred while saving car information.'];
        }

        $this->getResponse()->representJson(json_encode($result));
    }



  }
