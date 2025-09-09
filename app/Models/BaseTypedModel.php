<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;

class BaseTypedModel extends Model
{
    /**
     * Aktifkan konversi otomatis tipe data (int, float, datetime, dsb)
     */
    protected bool $autoTypeCast = true;

    /**
     * Mapping tipe data berdasarkan metadata dari database
     */
    protected function typeCastResults(array $data): array
    {
        if (!$this->autoTypeCast || empty($data)) {
            return $data;
        }

        /** @var ConnectionInterface $db */
        $db = \Config\Database::connect();
        $fields = $db->getFieldData($this->table);

        $fieldTypes = [];
        foreach ($fields as $field) {
            $fieldTypes[$field->name] = $field->type;
        }

        foreach ($data as &$row) {
            foreach ($row as $key => &$value) {
                if (!isset($fieldTypes[$key])) continue;

                switch ($fieldTypes[$key]) {
                    case 'int':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                        $value = (int) $value;
                        break;

                    case 'float':
                    case 'double':
                    case 'decimal':
                        $value = (float) $value;
                        break;

                    case 'boolean':
                    case 'bool':
                        $value = (bool) $value;
                        break;

                    case 'datetime':
                    case 'timestamp':
                        $value = $value ? date('Y-m-d H:i:s', strtotime($value)) : null;
                        break;
                }
            }
        }

        return $data;
    }

    // Override find()
    public function find($id = null)
    {
        $result = parent::find($id);
        return is_array($result) ? $this->typeCastResults([$result])[0] : $result;
    }

    // Override findAll()
    public function findAll(?int $limit = null, int $offset = 0)
    {
        $results = parent::findAll($limit, $offset);
        return $this->typeCastResults($results);
    }

    // Untuk query builder biasa: $model->where(...)->getTypedResults()
    public function getTypedResults(): array
    {
        $results = $this->get()->getResultArray();
        return $this->typeCastResults($results);
    }
}
