<?php
  ob_start();
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$store_id = 1;
$csv_filepath = "subscribers.csv";
$csv_delimiter = ',';
$csv_enclosure = '"';
$magento_path = __DIR__;

$params = $_SERVER;

$bootstrap = Bootstrap::create(BP, $params);

$obj = $bootstrap->getObjectManager();

$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$websiteId =  $obj->get('Magento\Store\Model\StoreManagerInterface')
    ->getStore()
    ->getWebsiteId();

$customerAccountManagement =$obj
    ->create('Magento\Customer\Api\AccountManagementInterface');

$_subscriberFactory = $obj->create('Magento\Newsletter\Model\SubscriberFactory');


if (($handle = fopen($csv_filepath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // email Id
        $emailId =  $data[1];
       /* customer */
        if($customerAccountManagement->isEmailAvailable($emailId , $websiteId)){
            echo $emailId ."\n";
            $_subscriberFactory->create()->subscribe($emailId );
        }else{
            echo $emailId ."Register <br/>";
            $customerFactory = $obj->get('Magento\Customer\Model\CustomerFactory');
            $customer=$customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($emailId );// load customer by email address
            if($customer->getId()){
                $_subscriberFactory->create()->subscribeCustomerById($customer->getId());
            }

        }
    }
    fclose($handle);
}
 ob_end_flush();
?>