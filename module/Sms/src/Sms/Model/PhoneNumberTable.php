<?php
namespace Sms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class PhoneNumberTable
{
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

    public function getNumber($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function lookUpByNumber($number)
    {
        $rowset = $this->tableGateway->select(array('number' => $number));
        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Could not find $number");
        }
        return $row;
    }

    public function selectAvailableNumbers()
    {
        $resultSet = $this->tableGateway->select(function (Select $select) {
            $select->where(array('available' => 1));
        });

        return $resultSet;
    }

    public function saveNumber(PhoneNumber $phoneNumber)
    {
        $data = array(
            'number' => $phoneNumber->number,
            'available' => $phoneNumber->available
        );

        $id = (int)$phoneNumber->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getNumber($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Phone Number id does not exist');
            }
        }
    }

    public function deleteNumber($id)
    {
        $this->tableGateway->delete(array('id' => (int)$id));
    }
}