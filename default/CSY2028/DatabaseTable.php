<?php

namespace Core;
use PDOException;

Class DatabaseTable {
    private $pdo;
    private $primaryKey;
    private $table;
    private $className;
    private $constructorArgs;


    public function __construct($pdo, $table, $primaryKey, string $className = '\stdClass', array $constructorArgs =[]) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        $this->className = $className;
        $this->constructorArgs = $constructorArgs;
    }

    private function query($sql, $parameters = [])
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($parameters);
        return $query;

    }

    public function find($field, $value) {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' = :value');

        $criteria = [
            'value' => $value
        ];

        $stmt->execute($criteria);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
    }


    public function findAll() {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table);

        $stmt->execute();

        //return $stmt->fetchAll();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
    }

    public function findById($value)
    {
        $query = 'SELECT * FROM '.$this->table.' WHERE '.$this->primaryKey.' = :value';

        $parameters = [
            'value'=> $value
        ];
        $query = $this->query($query, $parameters);

        return $query->fetchObject($this->className, $this->constructorArgs);

    }

    public function findMultipleConditions($parameters = []){

        if(empty($parameters))
            return $this->findAll();

        $query = 'SELECT * FROM '.$this->table.' WHERE';

        $i = 0;
        foreach($parameters as $key => $value){
            if ($i==0) {$query .= ' '.$key. ' =  :'.$key;}
            else {$query .= ' AND '.$key. ' =  :'.$key;}
            $i++;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
        $stmt->execute($parameters);


        return $stmt->fetchAll();

    }

    public function findAllOptions($field, $operator = null,$value, $orderBy = null , $order = null, $limit = null)
    {
        $query = 'SELECT * FROM '.$this->table;

        $parameters = [
            'value' => $value
        ];

        if ($field!=null) {
            if ($operator !== null) $query .= ' WHERE '.$field.' ' . $operator. ' :value';
            else $query .= ' WHERE '.$field.' = :value';
        }
        if($orderBy !== null) $query .= ' ORDER BY '.$orderBy;

        if($order !== null) $query .= ' '.$order;

        if($limit !== null) $query .= ' LIMIT '.$limit;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($parameters);

        return $stmt->fetchAll(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);

    }

    public function insert($record) {
        $keys = array_keys($record);

        $values = implode(', ', $keys);
        $valuesWithColon = implode(', :', $keys);

        $query = 'INSERT INTO ' . $this->table . ' (' . $values . ') VALUES (:' . $valuesWithColon . ')';

        $stmt = $this->pdo->prepare($query);

        $stmt->execute($record);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare('DELETE FROM '.$this->table.' WHERE '.$this->primaryKey.' = :id LIMIT 1');
        $criteria = [
            'id' => $id
        ];
        $stmt->execute($criteria);
    }

    public function save($record)
    {
        try {
            $this->insert($record);
        } catch (PDOException $e) {
            $this->update($record);
        }
    }

    private function update($record)
    {
        $query = 'UPDATE ' .$this->table. ' SET ';

        $parameters = [];

        foreach($record as $key => $value) {
            $parameters[] = $key.' = :'.$key;
        }

        $query .= implode(', ', $parameters);
        $query .= ' WHERE ' .$this->primaryKey. ' = :primaryKey';

        $record['primaryKey'] = $record[$this->primaryKey];

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($record);

    }

    public function count($field, $value)
    {
        $query = 'SELECT count(*) FROM '.$this->table.' WHERE '.$field.' = :value';
        $criteria = [
            'value' => $value
        ];
        $result = $this->query($query, $criteria);
        return $result->fetch();
    }


    public function distinct($column)
    {
        $stmt = $this->pdo->prepare('SELECT DISTINCT '.$column.' FROM '.$this->table);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function column($column)
    {
        $stmt = $this->pdo->prepare('SELECT '.$column.' FROM '.$this->table);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->className, $this->constructorArgs);
        $stmt->execute();
        return $stmt->fetchAll();

    }

}