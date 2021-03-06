<?php

use App\Services\WSService;
use Zend\Soap\AutoDiscover;
use Zend\Soap\Server;

require 'vendor/autoload.php';
require 'pacjentT.php';
require 'kaoz.php';
require 'WSService.php';
require 'ComplexType.php';

class Soap
{

    /**
     * @return mixed
     */
    public function initServer()
    {
        return isset($_GET['wsdl']) ? $this->generateWSDL() : $this->handleServer();
    }

    /**
     * @return mixed
     */
    protected function generateWSDL()
    {
        $serverUrl = 'http://localhost:8000';
        $soapAutoDiscover = new AutoDiscover(new ComplexType());
        /*        $soapAutoDiscover->setClassMap([
                    pacjentT::class,
                ]);*/
        $soapAutoDiscover->setBindingStyle(['style' => 'document']);
        $soapAutoDiscover->setOperationBodyStyle(['use' => 'literal']);
        $soapAutoDiscover->setClass('WSService');
        $soapAutoDiscover->setUri($serverUrl);
        $soapAutoDiscover->setServiceName('WSService');

        $wsdl = $soapAutoDiscover->generate();
        $wsdl->addType('pacjent', 'pacjentT');
        //$wsdl->addComplexType('\pacjentT');
        //$wsdl->addComplexType(pacjentT::class);
        header('Content-type: application/xml');
        $xml = $wsdl->toXML();
        $xml = str_replace(
            [
                'xsd:',
                'xsd=',
                '<definitions',
                '</definitions>',
                '<types>',
                '</types>',
            ],
            [
                'xs:',
                'xs=',
                '<wsdl:definitions',
                '</wsdl:definitions>',
                '<wsdl:types>',
                '</wsdl:types>',
            ],
            $xml
        );
        return $xml;
    }

    /**
     * @return mixed
     */
    protected function handleServer()
    {
        $classmap = [];
        $options = [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'uri' => $this->wsdl,
            'soap_version' => SOAP_1_1,
            'encoding' => 'UTF-8',
            'classmap' => $classmap,
        ];
        $soap = new Server(null, $options);
        $soap->setClass(new WSService());
        $soap->handle();
    }

}


$instance = new Soap();
echo $instance->initServer();