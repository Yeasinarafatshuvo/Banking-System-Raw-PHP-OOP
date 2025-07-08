<?php
namespace BankingSystem\Abstracts;

abstract class BaseModel {
    protected $id;

    public function getId() {
        return $this->id;
    }

    abstract public function validate(): bool;
}