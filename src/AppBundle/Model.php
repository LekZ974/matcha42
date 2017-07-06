<?php

namespace App\AppBundle;


class Model
{
    protected $app;
    public $name;

    public function __construct($c)
    {
        $this->app = $c;
        $this->name = strtolower(get_called_class());
    }

//    /*
//     *  SPECIAL SQL
//     */
//
//    public function addition($id, $col, $int)
//    {
//        $pdo = $this->app->db->prepare("UPDATE $this->name SET $col = $col + $int WHERE id = ?");
//        $pdo->execute(array($id));
//    }

    /*
    *	INSERT / UPDATE / DELETE FUNCTION
    */
    public function insertFillDB($name, $values)
    {
        $int = null;
        foreach ($values as $key => $v)
        {
            $table[] = $key;
            $int = $int . "?,";
            $val[] = $v;
        }
        $int = $int . "?,";
        $int = $int . "?,";
        $table[] = "created_at";
        $table[] = "updated_at";
        $val[] = date("d/m/Y H:i:s");
        $val[] = date("d/m/Y H:i:s");
        $col = implode(',', $table);
        $int = substr($int, 0, -1);
        $pdo = $this->app->db->prepare("INSERT INTO $name($col) VALUES($int)");
        $pdo->execute($val);

        return $this->app->db->lastInsertId();
    }

    public function insert($values)
    {
        $values['created_at'] = date("Y-m-d H:i:s");
        $values['updated_at'] = date("Y-m-d H:i:s");
        $strValue = null;
        foreach ($values as $elem)
        {
            $strValue = $strValue.'?,';
        }
        $strValue = substr($strValue, 0, -1);
        $field = array_keys($values);
        $db = $this->name;
        $tab = explode('\\', $db);
        $dbName = array_pop($tab);
        $field = implode($field, ', ');
        $pdo = $this->app->db->prepare("INSERT INTO $dbName($field) VALUES($strValue)");
        if ($pdo->execute(array_values($values)))
            return true;
        else
            return false;
    }

    public function update($id, $values)
    {
        foreach ($values as $key => $v)
        {
            $table[] = $key . " = ?";
            $val[] = $v;
        }
        $table[] = "updated_at = ?";
        $val[] = date("d/m/Y H:i:s");
        $val[] = $id;
        $col = implode(',', $table);
        $pdo = $this->app->db->prepare("UPDATE $this->name SET $col WHERE id = ?");
        $pdo->execute($val);
    }

    public function updateLink($colonne, $id, $values)
    {
        foreach ($values as $key => $v)
        {
            $table[] = $key . " = ?";
            $val[] = $v;
        }
        $table[] = "updated_at = ?";
        $val[] = date("d/m/Y H:i:s");
        $val[] = $id;
        $col = implode(',', $table);
        $pdo = $this->app->db->prepare("UPDATE $this->name SET $col WHERE $colonne = ?");
        $pdo->execute($val);
    }

    public function deleteSpecial($col, $id)
    {
        $pdo = $this->app->db->prepare("DELETE FROM $this->name WHERE $col = :id");
        $pdo->execute(array(
            'id' => $id
        ));
    }

    public function delete($id)
    {
        $pdo = $this->app->db->prepare("DELETE FROM $this->name WHERE id = :id");
        $pdo->execute(array(
            'id' => $id
        ));

    }

    /*
    *	FIND FUNCTIONS
    */

    public function findById($id)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM $this->name WHERE id = :id");
        $pdo->execute(array(
            'id' => $id
        ));

        return $pdo->fetch();

    }

    public function findOne($col, $id)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM $this->name WHERE $col = :id");
        $pdo->execute(array(
            'id' => $id
        ));

        return $pdo->fetch();
    }

    public function find($col, $id)
    {
        $pdo = $this->app->db->prepare("SELECT * FROM $this->name WHERE $col = :id");
        $pdo->execute(array(
            'id' => $id
        ));

        return $pdo->fetchAll();
    }

    public function findAll()
    {
        $pdo = $this->app->db->prepare("SELECT * FROM $this->name");
        $pdo->execute();

        return $pdo->fetchAll();
    }

    public function findLast()
    {
        $pdo = $this->app->db->prepare("SELECT * FROM $this->name ORDER BY id DESC LIMIT 1");
        $pdo->execute();

        return $pdo->fetch();
    }

    /*
    *  USEFULL FUNCTION
    */

    public function isSingle($field, $value)
    {
        $db = $this->name;
        $tab = explode('\\', $db);
        $dbName = array_pop($tab);
        $pdo = $this->app->db->prepare("SELECT $field FROM $dbName WHERE $field = ?");
        $pdo->execute([$value]);
        if (empty($pdo->fetchAll()))
            return true;

        return false;
    }

    public function downloadFromServer($request, $fileName, $dest)
    {
        $files = $request->getUploadedFiles()[$fileName];
        if (empty($files)) {
            throw new \Exception('Expected a newfile');
        }
//        print_r($newfile);

        if ($files->getError() === UPLOAD_ERR_OK) {
            $newFile = md5(microtime(TRUE)*100000).'.jpg';
            $files->moveTo($dest.$newFile);
        }
        return $newFile;
    }
}