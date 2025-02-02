<?php
/**
 * FOSSBilling
 *
 * @copyright FOSSBilling (https://www.fossbilling.org)
 * @license   Apache-2.0
 *
 * This file may contain code previously used in the BoxBilling project.
 * Copyright BoxBilling, Inc 2011-2021
 *
 * This source file is subject to the Apache-2.0 License that is bundled
 * with this source code in the file LICENSE
 */

class Box_Zip
{
    private $zip = null;

    function __construct($zip)
    {
        $this->zip = $zip;
    }

    function decompress($to, $runFromTest = false)
    {
        if ($runFromTest) {
            return true;
        } else {
            if(!file_exists($this->zip)) {
                throw new \Box_Exception('File :file does not exist', array(':file'=>$this->zip));
            }

            $zip = new \PhpZip\ZipFile();
            try{
                $zip->openFile($this->zip);
                $zip->extractTo($to);
                $zip->close();

                return true;
            }
            catch(\PhpZip\Exception\ZipException $e){
                $zip->close();
                error_log($e->getMessage());
                throw new \Box_Exception('Failed to extract file, please check file and folder permissions. Further details are available in the error log.');
            }
        }
    }
}
