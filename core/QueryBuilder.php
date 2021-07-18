<?php


namespace Core;


use PDO;

class QueryBuilder
{
    protected static $connection = null;
    protected static $model = '';
    protected string $table = '';
    protected static $sql = '';
    protected static $conditions = [];
    protected static $conditionString = '';

    public function __construct()
    {
        if (is_null(self::$connection)) {
            self::$connection = DB::getInstance();
        }

        return self::$connection;
    }

    /**
     * get mysql connection
     *
     * @return null|PDO
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
        self::$model = $class;

        if (class_exists($class)) {
            $builder->table = get_class_vars($class)['table'];
        } else {
            $builder->table = $class;
            $class = explode(DIRECTORY_SEPARATOR, $builder->table);
            $builder->table = lcfirst(end($class)) . 's';
        }

        return $builder;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function toSql(): string
    {
        return self::$sql;
    }

    /**
     * @param string|array $columns
     * @return \stdClass
     */
    public function first($columns = '*')
    {
        if ($columns !== '*' && is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        if (!empty(self::$conditions)) {
            self::$sql = 'SELECT ' . $columns . ' FROM ' . $this->getTable() . ' WHERE ' . self::$conditionString . ' LIMIT 1';
        } else {
            self::$sql = 'SELECT ' . $columns . ' FROM ' . $this->getTable() . ' LIMIT 1';
        }
        $stmt = $this->getConnection()->prepare(self::$sql);
        $stmt->execute(self::$conditions);

        return $stmt->fetchObject(self::$model);
    }

    /**
     * @param string|array $columns
     * @return array|null
     */
    public function get($columns = '*'): array
    {
        if ($columns !== '*' && is_array($columns)) {
            $columns = implode(', ', $columns);
        }
        if (!empty(self::$conditions)) {
            $sql = 'SELECT ' . $columns . ' FROM ' . $this->getTable() . ' WHERE ' . self::$conditionString;
        } else {
            $sql = 'SELECT ' . $columns . ' FROM ' . $this->getTable();
        }
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(self::$conditions);

        self::$sql = $stmt->queryString;

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::$model);
    }

    /**
     * @param array $values
     * @return bool
     */
    public function create($values)
    {
        $keys = array_keys($values);
        $columns = implode(', ', $keys);
        $keys = array_map(function ($key) {
            return ":$key";
        }, $keys);

        $valuesColumns = implode(', ', $keys);
        $values = array_values($values);

        self::$sql = 'INSERT INTO ' . $this->getTable() . "($columns) VALUES($valuesColumns)";
        $stmt = $this->getConnection()->prepare(self::$sql);
        foreach ($keys as $index => $key) {
            $stmt->bindParam($key, $values[$index]);
        }
        return $stmt->execute();
    }

    /**
     * @param array $values
     * @return bool
     */
    public function update($values)
    {
        $valuesKeys = array_map(function ($key) {
            return "$key=:$key";
        }, array_keys($values));

        $valuesColumns = implode(', ', $valuesKeys);

        $cond = explode('=?', self::$conditionString);

        if (!empty(self::$conditionString)) {
            self::$sql = 'UPDATE ' . $this->getTable() . ' SET ' . $valuesColumns . ' WHERE ' . "{$cond[0]} = :{$cond[0]}";
        } else {
            self::$sql = 'UPDATE ' . $this->getTable() . ' SET ' . $valuesColumns;
        }

        $stmt = $this->getConnection()->prepare(self::$sql);

        foreach ($valuesKeys as $key => $valuesKey) {
            $k = explode('=', $valuesKey);
            $stmt->bindParam($k[1], array_values($values)[$key]);
        }
        $id = (int)self::$conditions[0];
        $stmt->bindParam(":{$cond[0]}", $id);

        self::$sql = $stmt->queryString;

        return $stmt->execute();
    }

    /**
     * @param array $filters
     * @param string $aggregate
     * @return QueryBuilder
     */
    public function where($filters, $aggregate = 'AND')
    {
        foreach ($filters as $filterValue) {
            self::$conditions[] = $filterValue;
        }

        $filterKeys = array_map(function ($key) {
            return "$key=?";
        }, array_keys($filters));

        if (count($filters) > 1) {
            self::$conditionString = implode(" $aggregate ", $filterKeys);
        } else {
            self::$conditionString = implode(' ', $filterKeys);
        }
        return $this;
    }

    public function delete()
    {
        if (!empty(self::$conditionString)) {
            self::$sql = 'DELETE FROM ' . $this->getTable() . ' WHERE ' . self::$conditionString;
        } else {
            self::$sql = 'DELETE FROM ' . $this->getTable();

        }

        $stmt = $this->getConnection()->prepare(self::$sql);

        return $stmt->execute(!empty(self::$conditionString) ? self::$conditions : null);
    }

    /**
     * @param string $stmt
     * @return array
     */
    public function rawQuery($stmt)
    {
        $stmt = $this->getConnection()->prepare($stmt);
        $stmt->execute();
        self::$sql = $stmt->queryString;
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function find($id)
    {
        $sql = 'SELECT * FROM ' . $this->getTable() . ' WHERE :id=' . $id . ' LIMIT 1';

        $stmt = $this->getConnection()->prepare($sql);
        $stmt->bindParam(':id', $id);
        self::$sql = $stmt->queryString;

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, self::$model)[0];
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
            return $this->create($model);
        }
    }


}