<?php

use DOMElement;
use ReflectionClass;
use ReflectionProperty;
use Zend\Soap\Exception;
use Zend\Soap\Wsdl;
use Zend\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType;
use Zend\Soap\Wsdl\DocumentationStrategy\DocumentationStrategyInterface;

class ComplexType extends DefaultComplexType
{

public function addComplexType($type)
{
    if (!class_exists($type)) {
        throw new Exception\InvalidArgumentException(sprintf(
            'Cannot add a complex type %s that is not an object or where '
            . 'class could not be found in "DefaultComplexType" strategy.',
            $type
        ));
    }

    $class = new ReflectionClass($type);
    $phpType = $class->getName();

    if (($soapType = $this->scanRegisteredTypes($phpType)) !== null) {
        return $soapType;
    }

    $dom = $this->getContext()->toDomDocument();
    $soapTypeName = $this->getContext()->translateType($phpType);
    $soapType = Wsdl::TYPES_NS . ':' . $soapTypeName;

    // Register type here to avoid recursion
    $this->getContext()->addType($phpType, $soapType);

    $defaultProperties = $class->getDefaultProperties();

    $complexType = $dom->createElementNS(Wsdl::XSD_NS_URI, 'complexType2');
    $complexType->setAttribute('name', $soapTypeName);

    $all = $dom->createElementNS(Wsdl::XSD_NS_URI, 'sequence');

    foreach ($class->getProperties() as $property) {
        if ($property->isPublic() && preg_match_all('/@var\s+([^\s]+)/m', $property->getDocComment(), $matches)) {
            /**
             * @todo check if 'xsd:element' must be used here (it may not be
             * compatible with using 'complexType' node for describing other
             * classes used as attribute types for current class
             */
            $element = $dom->createElementNS(Wsdl::XSD_NS_URI, 'element');
            $element->setAttribute('name', $propertyName = $property->getName());
            $element->setAttribute('type', $this->getContext()->getType(trim($matches[1][0])));
            $tempo = $property->getDocComment();
            if (preg_match('/___FOR_ZEND_minOccurs\s*=\s*(\d+|unbounded)/', $tempo, $matches)) {
                $element->setAttribute('minOccurs', $matches[1]);
            }
            if (preg_match('/___FOR_ZEND_maxOccurs\s*=\s*(\d+|unbounded)/', $tempo, $matches)) {
                $element->setAttribute('maxOccurs', $matches[1]);
            }

            // If the default value is null, then this property is nillable.
            if ($defaultProperties[$propertyName] === null) {
                $element->setAttribute('nillable', 'true');
            }

            $this->addPropertyDocumentation($property, $element);
            $all->appendChild($element);
        }
    }

    $complexType->appendChild($all);
    $this->addComplexTypeDocumentation($class, $complexType);
    $this->getContext()->getSchema()->appendChild($complexType);

    return $soapType;
}

/**
 * @return void
 */
private function addPropertyDocumentation(ReflectionProperty $property, DOMElement $element)
{
    if ($this->documentationStrategy instanceof DocumentationStrategyInterface) {
        $documentation = $this->documentationStrategy->getPropertyDocumentation($property);
        if ($documentation) {
            $this->getContext()->addDocumentation($element, $documentation);
        }
    }
}

/**
 * @return void
 */
private function addComplexTypeDocumentation(ReflectionClass $class, DOMElement $element)
{
    if ($this->documentationStrategy instanceof DocumentationStrategyInterface) {
        $documentation = $this->documentationStrategy->getComplexTypeDocumentation($class);
        if ($documentation) {
            $this->getContext()->addDocumentation($element, $documentation);
        }
    }
}
}
