<?php

namespace Socket\Client\traits;

use Verot\Upload\Upload;

trait VerotUpload
{
    public function fileUpload($files, $options, $lang = "en_GB", $debug = false): array
    {
        $handle = new Upload($files, $lang);
        if (!$debug) {
            $uploadPath = $options["uploadPath"];
            if ($handle->uploaded) {
                (isset($options["tittle"]) ? (!empty($options["tittle"]) ? $handle->file_new_name_body = $options["tittle"] : "") : "");
                if ($options["resize"]["boolean"]) { // görsel boyutlandırma
                    $resize = $options["resize"];
                    $handle->image_resize = true;
                    $handle->image_ratio_crop = true;
                    $handle->image_x = $resize["image_x"];
                    $handle->image_y = $resize["image_y"];
                    $handle->image_ratio_x = true;
                    $handle->image_ratio_y = true;
                }
                (isset($options["type"]) ? (!empty($options["type"]) ? $handle->image_convert = $options["type"] : "") : "");
                if (!empty($options["allowed"])) {
                    $handle->allowed = $options["allowed"]; // dosya türü seçimi
                }
                $handle->process("upload/$uploadPath"); // dosya kayıt edileceği nokta
                if ($handle->processed) {
                    $filePath = $handle->file_dst_path . $handle->file_dst_name;
                    $path = str_replace("../upload", "../app/upload", $filePath);
                    $path = str_replace("\\", "/", $path);
                    $result = ["boolean" => true, "path" => $path, "fileName" => $handle->file_dst_name_body];
                    $handle->clean();
                } else {
                    $result = ["boolean" => false, "error" => $handle->error];
                }
            } else {
                $result = ["boolean" => false, "debug" => $handle];
            }
        } else {
            $result = ["boolean" => false, "debug" => $handle];
        }
        return $result;
    }
}