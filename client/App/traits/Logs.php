<?php

namespace Socket\Client\traits;
trait Logs
{
    /**
     * @param array $request
     * @param string $enums
     * @return bool
     */
    public function log(array $request, string $enums = ""): bool
    {
        $fileDate = date("d-m-Y");
        $result = match ($enums) {
            "login", "logout" => ["result" => $this->authLog($request), "file" => "$enums-$fileDate.json"],
            default => ["result" => ["date" => date("Y-m-d H:i:s"), "request" => [...$request]], "file" => "$enums-$fileDate.json"],
        };
        return $this->logFile($result["result"], $result["file"]);
    }

    private function authLog($request): array
    {
        if (isset($request["request"]["Cookie"])) {

            if (isset($request["request"]["Cookie"]["PHPSESSID"])) {

                unset($request["request"]["Cookie"]["PHPSESSID"]);

            }
            if (empty($request["request"]["Cookie"])) {

                unset($request["request"]["Cookie"]);

            }
        }
        return ["date" => date("d-m-Y H:i:s"), ...$request];
    }

    /**
     * @param $result
     * @param $file
     * @return bool
     */
    private function logFile($result, $file): bool
    {
        $pathRoot = dirname(__DIR__);
        $json_file = "$pathRoot/logs/$file";
        if (file_exists($json_file)) {

            $json = json_decode(file_get_contents($json_file), true);
            $json = array_merge($json, [$result]);

        } else {

            $json = ["mailStatus" => false, $result];

        }
        $data = json_encode($json);
        touch("$json_file");
        $setting = fopen("$json_file", 'w+');
        fwrite($setting, $data);
        return fclose($setting);
    }


}