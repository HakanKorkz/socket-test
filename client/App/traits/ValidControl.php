<?php

namespace Socket\Client\traits;
trait ValidControl
{
    use ValidationConfig;
    use Logs;

    public function ValidControl(array $request, array $labels): array
    {
        $Info = [];
        $op = "";
        $enums = "";
        $validation = $this->validation($request);
        if ($validation["boolean"]) {
            $Info['operation'] = 'warning';
            $Info['subject'] = "Validasyon hatası";
            $Info['messages'] = $this->validInfo($validation[0], $labels);
            $Info['path'] = "";
            $await = true;
            $op = "validasyon kontrolü";
            $enums = "validation";
        } else {
            $await = false;
        }

        if ($await) {
            $this->log(["request" => [...$Info, "operation" => $op]], "$enums");
        }
        return ["boolean" => $await, ...$Info];
    }
}