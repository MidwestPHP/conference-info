<?php
namespace Dashboard;


use Sms\Model\PhoneNumber;
use Sms\Model\PhoneNumberTable;
use Sms\Model\Prize;
use Sms\Model\PrizeTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Sms\Model\PhoneNumberTable' => function($sm) {
                    $tableGateway = $sm->get('PhoneNumberTableGateway');
                    $table = new PhoneNumberTable($tableGateway);
                    return $table;
                },
                'PhoneNumberTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new PhoneNumber());
                    return new TableGateway('phonenumbers', $dbAdapter, null, $resultSetPrototype);
                },
                'Sms\Model\PrizeTable' => function($sm) {
                    $tableGateway = $sm->get('PrizeTableGateway');
                    $table = new PrizeTable($tableGateway);
                    return $table;
                },
                'PrizeTableGateway' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Prize());
                    return new TableGateway('prizes', $dbAdapter, null, $resultSetPrototype);
                },
            )
        );
    }
}