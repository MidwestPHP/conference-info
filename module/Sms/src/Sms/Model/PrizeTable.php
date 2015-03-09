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

    public function selectAvailablePrizes()
    {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->where(array('available' => 1));
        });

        return $resultSet;
    }
}