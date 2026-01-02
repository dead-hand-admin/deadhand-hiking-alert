<?php

namespace Models;

use PDO;

abstract class BaseModel {
    
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Получить подключение к БД
     */
    protected function db() {
        return db();
    }
    
    /**
     * Найти по ID
     */
    public function find($id) {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Найти все записи
     */
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db()->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Найти по условию
     */
    public function where($column, $value, $operator = '=') {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Найти одну запись по условию
     */
    public function first($column, $value, $operator = '=') {
        $stmt = $this->db()->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }
    
    /**
     * Создать запись
     */
    public function create($data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->db()->lastInsertId();
    }
    
    /**
     * Обновить запись
     */
    public function update($id, $data) {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = ?";
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " 
                WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($values);
    }
    
    /**
     * Удалить запись
     */
    public function delete($id) {
        $stmt = $this->db()->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Подсчёт записей
     */
    public function count($column = null, $value = null) {
        if ($column && $value) {
            $stmt = $this->db()->prepare("SELECT COUNT(*) as cnt FROM {$this->table} WHERE {$column} = ?");
            $stmt->execute([$value]);
        } else {
            $stmt = $this->db()->query("SELECT COUNT(*) as cnt FROM {$this->table}");
        }
        return $stmt->fetch()['cnt'];
    }
}