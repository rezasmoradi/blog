<?php


namespace Core;


use App\resources\MediaResource;
use App\resources\PostResource;
use Bolt\structures\Node;
use Bolt\structures\Relationship;

abstract class Resource
{
    protected array $resource = [];
    protected array $result;
    protected static bool $isCollection = false;
    protected static array $items = [];
    protected static int $startNode = -1;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    abstract public function toArray();

    public static function flatten($array)
    {
        if (is_array($array) || is_object($array)) {
            foreach ($array as $key => $item) {
                if (is_object($item)) {
                    if ($item instanceof Node) {
                        self::$startNode = self::$startNode === -1 ? $item->id() : self::$startNode;
                        if (self::$startNode === $item->id()) {
                            self::$items[self::$startNode][$item->labels()[0]] = $item->properties();
                        } else {
                            self::$items[self::$startNode][][$item->labels()[0]] = $item->properties();
                        }
                    } elseif ($item instanceof Relationship) {
                        self::$startNode = $item->startNodeId();
                    }
                } else {
                    self::flatten($array[$key]);
                }
            }
        }
        return array_values(self::$items);
    }

    public static function collection($resources)
    {
        self::$isCollection = true;
        $result = [];
        $res = self::flatten($resources);
        foreach ($res as $key => $resource) {
            $model = array_keys($resource)[$key];
            $typeResource = 'App\\resources\\' . $model . 'Resource';
            $obj = new $typeResource($resource);
            $result[] = is_array($obj) ? $obj[$key]->toArray() : $obj->toArray();
        }
        return $result;
    }

    public function __toString()
    {
        return $this->toArray();
    }
}