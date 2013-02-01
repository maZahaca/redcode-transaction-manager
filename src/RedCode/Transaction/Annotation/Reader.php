<?php
/**
 * @author maZahaca
 */
namespace RedCode\Transaction\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * @author maZahaca
 */
class Reader
{
    private $annotationReader;

    static private $reflected = array();

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * Get Reflection by object
     * @param $object Object of some class
     * @return ReflectionObject
     */
    protected function getReflection($object)
    {
        if(!isset(self::$reflected[get_class($object)])) {
            self::$reflected[get_class($object)] = new ReflectionObject($object);
        }

        return self::$reflected[get_class($object)];
    }

    /**
     * Get fields with annotation
     * @param object $object Some object of class
     * @param string $annotation annotation class string
     * @return array fields with annotation
     */
    public function getFields($object, $annotation)
    {
        $fields = array();
        /** @var $reflectionProperty ReflectionProperty */
        foreach($this->getReflection($object)->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $annotations = $this->annotationReader->getPropertyAnnotations($reflectionProperty);
            foreach ($annotations as $annotationType) {
                if(preg_match('/' . $annotation . '/u', get_class($annotationType))) {
                    $fields[] = $reflectionProperty->getName();
                }
            }
        }

        return $fields;
    }
}
