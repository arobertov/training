<?php
/**
 * Created by PhpStorm.
 * User: Angel
 * Date: 23.6.2017 г.
 * Time: 0:21
 */

namespace AppBundle\Services;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDir;

    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move($this->targetDir, $fileName);

        return $fileName;
    }

    public function removeFile($path)
    {
        $file_path = $this->getTargetDir().'/'.$path;
        if(file_exists($file_path)) unlink($file_path);
        return true;
    }

    public function getTargetDir()
    {
        return $this->targetDir;
    }



}