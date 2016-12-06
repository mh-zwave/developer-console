<?php

class Uploader {

    private $destinationPath;
    private $errorMessage;
    private $extensions;
    private $allowAll;
    private $maxSize;
    private $uploadName;
    private $uploadOriginalName;
    private $seqnence;
    public $name = 'Uploader';
    public $useTable = false;
    private $uniqueFile = false;
    private $customName = false;

    function setDir($path) {
        $this->destinationPath = $path;
        $this->allowAll = false;
    }

    function allowAllFormats() {
        $this->allowAll = true;
    }

    function setMaxSize($sizeMB) {
        $this->maxSize = $sizeMB * (1024 * 1024);
    }

    function setExtensions($options) {
        $this->extensions = $options;
    }

    function setSameFileName() {
        $this->sameFileName = true;
        $this->sameName = true;
    }

    function getExtension($string) {
        $ext = "";
        if (strpos($string, '.tar.gz') !== false) {
            $ext = 'tar.gz';
        }else{
            try {
                $parts = explode(".", $string);


                $ext = strtolower($parts[count($parts) - 1]);
            } catch (Exception $c) {
                $ext = "";
            }
        }
        return $ext;
    }

    function setMessage($message) {
        $this->errorMessage = $message;
    }

    function getMessage() {
        return $this->errorMessage;
    }

    function getUploadName() {
        return $this->uploadName;
    }

    function setUploadOriginalName($name) {
        $this->uploadOriginalName = $name;
    }

    function getUploadOriginalName() {
        return $this->uploadOriginalName;
    }

    function setSequence($seq) {
        $this->imageSeq = $seq;
    }

    function getRandom() {
        return strtotime(date('Y-m-d H:i:s')) . rand(1111, 9999) . rand(11, 99) . rand(111, 999);
    }

    function sameName($true) {
        $this->sameName = $true;
    }

    function setUniqueFile($bool = true) {
        $this->uniqueFile = $bool;
    }

    function setCustomName($name) {
        $this->customName = $name;
    }

    function uploadFile($fileBrowse) {
        $result = false;
        $size = $_FILES[$fileBrowse]["size"];
        $name = $_FILES[$fileBrowse]["name"];
        $ext = $this->getExtension($name);
        $this->setUploadOriginalName($name);
        if ($this->uniqueFile) {
            $files = scandir($this->destinationPath);
            if (in_array($name, $files)) {
                $this->setMessage("File $name already exists ");
                return false;
            }
        }
        if (!is_dir($this->destinationPath)) {
            $this->setMessage("Destination folder is not a directory ");
        } else if (!is_writable($this->destinationPath)) {
            $this->setMessage("Destination is not writable !");
        } else if (empty($name)) {
            $this->setMessage("File not selected ");
        } else if ($size > $this->maxSize) {
            $this->setMessage("Too large file! Allowed size is max: " . $this->maxSize . ' Kb');
        } else if ($this->allowAll || (!$this->allowAll && in_array($ext, $this->extensions))) {
            if ($this->customName) {
                $this->uploadName = $this->customName . "." . $ext;
            } else {
                if ($this->sameName == false) {
                    $this->uploadName = $this->imageSeq . "-" . substr(md5(rand(1111, 9999)), 0, 8) . $this->getRandom() . rand(1111, 1000) . rand(99, 9999) . "." . $ext;
                } else {
                    $this->uploadName = $name;
                }
            }


            if (move_uploaded_file($_FILES[$fileBrowse]["tmp_name"], $this->destinationPath . $this->uploadName)) {
                $result = true;
            } else {
                $this->setMessage("Upload failed , try later!");
            }
        } else {
            $this->setMessage("Invalid file format! Allowed formats are: " . implode(', ', $this->extensions));
        }
        return $result;
    }

    function deleteUploaded() {
        unlink($this->destinationPath . $this->uploadName);
    }

}
