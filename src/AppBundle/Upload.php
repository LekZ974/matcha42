<?php
/**
 * Created by PhpStorm.
 * User: lekz
 * Date: 10/07/17
 * Time: 10:45
 */

namespace App\AppBundle;


class Upload
{
    public $error;
    protected $files;
    protected $extension = ['image/jpg', 'image/jpeg', 'image/bmp', 'image/png'];

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function uploadIsValid($path, $maxSize)
    {
        foreach ($this->files as $file)
        {
            if ($this->checkFile($file, $maxSize) === true)
            {
                $name = uniqid().'.'.explode("/", $file->getClientMediaType())[1];
                $file->moveTo($path.$name);

                return $name;
            }
            else
                return false;
        }
    }

    protected function checkFile($file, $maxSize)
    {
        if ($file->getError() === UPLOAD_ERR_NO_FILE)
            return $this->error[] = 'Expected a file';
        else if ($file->getError() === UPLOAD_ERR_FORM_SIZE || $file->getSize() > $maxSize)
            return $this->error[] = 'File too big (max 5Mo)';
        else if (!in_array($file->getClientMediaType(), $this->extension))
            return $this->error[] = 'Wrong file extension! (only jpg/jpeg/bmp/png)';
        return true;
    }
}