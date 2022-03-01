<?php


namespace Core;

use Bolt\protocol\AProtocol;

class QueryBuilder
{
    protected static ?AProtocol $connection = null;
    protected static string $cypher = '';
    protected static array $conditions = [];
    protected static string $conditionString = '';
    protected static array $fillable = [];
    protected static string $paginate = '';
    protected static array $matches = [];
    protected static array $creates = [];
    protected static array $relations = [];
    protected static array $hidden = [];
    protected static array $nodes = [];
    protected string $label = '';

    public function __construct()
    {
        self::$connection = DB::getInstance();

        return self::$connection;
    }

    /**
     * get connection
     *
     * @return AProtocol
     */
    public function getConnection()
    {
        return self::$connection;
    }

    /**
     *
     * @return QueryBuilder
     */
    public static function builder()
    {
        $builder = new QueryBuilder();
        $class = get_called_class();
        self::$fillable = get_class_vars($class)['fillables'];

        if (class_exists($class)) {
            $builder->label = get_class_vars($class)['label'];
        }

        return $builder;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function toCypher(): string
    {
        if (empty(self::$matches) && empty(self::$creates)) {
            return 'MATCH (' . 'n:' . $this->getLabel() . ') ' . self::$conditionString . self::$paginate;
        } else {
            $this->build();
            return self::$cypher;
        }
    }

    /**
     * @param string $node
     * @param string $model
     * @param array $conditions
     * @param string $relCondition
     * @return QueryBuilder
     */
    public function match($node, $model, $conditions, $relCondition = '')
    {
        $params = [];
        foreach ($conditions as $key => $condition) {
            $params[] = $key . ':' . "\"{$condition}\"";
        }

        self::$nodes[] = $node;
        self::$matches[] = '(' . $node . ':' . $model . ' { ' . implode(', ', $params) . ' })' . $relCondition;
        return $this;
    }

    /**
     * @param string $node
     * @param string $model
     * @param array $properties
     * @param string $relation
     * @return QueryBuilder
     */
    public function createConstraint($node, $model, $properties, $relation = '')
    {
        $params = [];
        $time = time();
        if (!array_key_exists('uuid', $properties)) {
            $params[] = 'uuid: apoc.create.uuid()';
        }
        $params[] = 'created_at: ' . $time;
        $params[] = 'updated_at: ' . $time;
        if (!empty($properties)) {
            foreach ($properties as $key => $property) {
                $params[] = $key . ':' . "\"{$property}\"";
            }
        }
        self::$nodes[] = $node;
        self::$creates[] = ' CREATE (' . $node . ':' . $model . ' { ' . implode(', ', $params) . '})' . $relation;
        return $this;
    }

    public function createRelation($firstNode, $relation, $secondNode, $rel_properties = [])
    {
        $rel = '';
        if (!empty($rel_properties)) {
            $rel = '{ ';
            $params = [];
            $time = time();
            $params[] = 'created_at: ' . $time;
            $params[] = 'updated_at: ' . $time;
            foreach ($rel_properties as $key => $property) {
                $params[] = $key . ': ' . $property;
            }
            $rel .= implode(',', $params) . ' }';
        }
        self::$creates[] = ' CREATE (' . $firstNode . ')' . $relation . $rel. '(' . $secondNode . ')';
        return $this;
    }

    /**
     * @param string $properties
     * @return object
     */
    public function first($properties = '*')
    {
        if (is_array($properties)) {
            $props = [];
            foreach ($properties as $property) {
                $props[] = 'n.' . $property;
            }
            $props = implode(', ', $props);
        } else {
            $props = $properties === '*' ? 'n, r, b' : $properties;
        }
        if ($this->getLabel() === 'User') {
            $props = 'n';
            $query = $this->getLabel() . ')';
        } else {
            $query = $this->getLabel() . ')-[r]->(b) ';
        }
        self::$cypher = 'MATCH (n:' . $query . self::$conditionString . ' RETURN ' . $props;
        $this->getConnection()->run(self::$cypher);
        self::$conditionString = '';
        self::$conditions = [];
        return $this->getConnection()->pull();
    }

    /**
     * @return array
     */
    public function get()
    {
        self::$cypher = 'MATCH (n:' . $this->getLabel() . ')-[r]->(b) ' . self::$conditionString . ' RETURN n,r,b' . self::$paginate;
        $this->getConnection()->run(self::$cypher);
        self::$conditionString = '';
        self::$conditions = [];
        return $this->getConnection()->pull();
    }

    /**
     * @return QueryBuilder
     */
    public function build()
    {
        $matches = '';
        $creates = '';
        $rel = '';
        if (!empty(self::$matches)) {
            foreach (self::$matches as $key => $match) {
                if (strpos($match, '(') === 0 && $key !== 0) {
                    $matches .= ', ' . $match;
                } else {
                    $matches .= $match;
                }
            }
            self::$cypher = 'MATCH ';
        }
        if (!empty(self::$creates)) {
            $creates = implode(' ', self::$creates);
        }
        if (!empty(self::$relations)) {
            $rel = ' MERGE ' . implode(', ', self::$relations);
        }
        self::$cypher .= $matches . $creates . $rel . self::$conditionString . self::$paginate;
        return $this;
    }

    /**
     * @param array|string $properties
     * @return mixed
     */
    public function return($properties = '*')
    {
        if ($properties === '*' && !empty(self::$nodes)) {
            $returns = implode(', ', self::$nodes);
        } else {
            $returns = is_array($properties) ? implode(', ', $properties) : $properties;
        }
        self::$cypher .= ' RETURN ' . $returns;

        $this->getConnection()->run(self::$cypher);

        self::$cypher = '';
        self::$nodes = [];
        self::$matches = [];
        self::$creates = [];

        return $this->getConnection()->pull()[0];
    }

    /**
     * @param array $relations
     * @return array
     */
    public function deleteRelation($relations)
    {
        self::$cypher .= ' DELETE ' . implode(', ', $relations);
        $this->getConnection()->run(self::$cypher);
        return $this->getConnection()->pull();
    }

    /**
     * @param int $limit
     * @param int $skip
     * @return QueryBuilder
     */
    public function paginate($limit, $skip = 0)
    {
        if ($skip === 0) {
            self::$paginate = ' LIMIT ' . $limit;
        } else {
            self::$paginate = ' SKIP ' . $skip . ' LIMIT ' . $limit;
        }
        return $this;
    }

    /**
     * @param array $values
     * @return array
     */
    public function update($values)
    {
        DB::beginTransaction();
        $params = [];
        foreach ($values as $key => $value) {
            if (in_array($key, self::$fillable)) {
                $params[] = $key . ':' . "\"{$value}\"";
            }
        }
        $params[] = 'updated_at:' . time();
        $params = implode(',', $params);

        $this->getConnection()->run($this->toCypher() . ' SET ' . self::$nodes[0] . ' += {' . $params . '} RETURN p');
        $result = $this->getConnection()->pull();
        DB::commit();
        self::$cypher = '';
        self::$matches = [];
        self::$creates = [];
        self::$nodes = [];
        return $result[0][0]->properties();
    }

    /**
     * @param array $filters
     * @param string $aggregate
     * @return QueryBuilder
     */
    public function where($filters, $aggregate = 'AND')
    {
        self::$conditions = $filters;

        $query = [];
        foreach ($filters as $key => $filter) {
            if (is_numeric($filter)) {
                $query[] = 'n.' . $key . ' = ' . "\"$filter\"" . '';
            } else {
                $query[] = 'n.' . $key . ' =~ ' . "\"(?i)$filter\"" . '';
            }
        }

        if (count($filters) > 1) {
            self::$conditionString = ' WHERE ' . implode(" $aggregate ", $query);
        } else {
            self::$conditionString = ' WHERE ' . implode(' ', $query);
        }
        return $this;
    }

    /**
     * @param array $nodes
     * @return mixed
     */
    public function delete($nodes)
    {
        self::$cypher = $this->toCypher() . ' DETACH DELETE ' . implode(', ', $nodes);

        $this->getConnection()->run(self::$cypher);
        return $this->getConnection()->pull();
    }

    /**
     * @param string $stmt
     * @return array
     */
    public function rawQuery($stmt)
    {
        $stmt = $this->getConnection()->run($stmt);
        self::$cypher = $stmt;
        return $this->getConnection()->pull();
    }

    /**
     * @param string $id
     * @return array
     */
    public function find($id)
    {
        self::$cypher = 'MATCH (n:' . $this->getLabel() . ' {uuid: ' . "\"$id\"" . '}) RETURN n';
        $this->getConnection()->run(self::$cypher);
        return $this->getConnection()->pull();
    }

    public function saveModel($model)
    {
        $user = $this->find($model->id);
        $model = (array)$model;
        if ($user) {
            $this->where(['id' => $model['id']]);
            $model = array_slice($model, 2);
            return $this->update($model);
        } else {
            return $this->update($model);
        }
    }
}