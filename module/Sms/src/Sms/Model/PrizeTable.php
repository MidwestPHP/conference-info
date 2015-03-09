<?php
namespace Sms\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;

class PrizeTable {
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getPrize($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function savePrize(Prize $prize)
    {
        $data = array(
            'name' => $prize->name,
            'available' => $prize->available
        );

        $id = (int) $prize->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getPrize($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Prize id does not exist');
            }
        }
    }

    public function deletePrize($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function selectRandomPrize()
    {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->columns(array('maxId' => new Expression('MAX(id)'), 'minId' => new Expression('MIN(id)')));
            $select->where(array('available' => 1));
        });

        $row = $resultSet->current();

        return $this->getRandomRecord($row->minId, $row->maxId);
    }

    public function getRandomRecord($minId, $maxId)
    {
        $number = mt_rand($minId, $maxId);

        try {
            $result = $this->getPrize($number);
            if (0 === $result->available) {
                self::getRandomRecord($minId, $maxId);
            }
            return $result;
        } catch (\Exception $e) {
            self::getRandomRecord($minId, $maxId);
        }
        return false;
    }
}