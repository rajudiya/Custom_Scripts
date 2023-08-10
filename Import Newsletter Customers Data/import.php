<?php
/**
 * News Letter Customer Impor Script
 * @developer Rohan Ajudiya
 */
ob_start();
use Magento\Framework\App\Bootstrap;
require __DIR__ . '/app/bootstrap.php';
$csv_filepath = "subscribers.csv"; // Your Csv File path
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
if (($handle = fopen($csv_filepath, "r")) !== FALSE) { // Read Your Csv File
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $emailId =  $data[1]; // email Id
       // Fatch customer Data 
        if($customerAccountManagement->isEmailAvailable($emailId , $websiteId)){
            echo $emailId."=> Imported Successfully" ."\n";
            $_subscriberFactory->create()->subscribe($emailId );
        }else{
            echo $emailId ."Register"."\n";
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
