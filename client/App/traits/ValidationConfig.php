<?php

namespace Socket\Client\traits;

use Valitron\Validator as Validator;

trait ValidationConfig
{

    public function validation($required): array
    {
        $langPath = dirname(__DIR__, 2) . "/vendor/vlucas/valitron/lang";
        Validator::langDir("$langPath");
        Validator::lang($required["lang"]);
        $v = new Validator($required);
//        switch ($required["operation"]) {
//            case "userCreate":
//            case "login":
//                $v->rule('required', ['username', 'password']);
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                $v->rule('lengthMin', 'password', 6);
//                break;
//            case "profileEdit":
//                $v->rule('required', 'username');
//                $v->rule('optional', 'password_after');
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                $v->rule('lengthMin', 'password_after', 6);
//                break;
//            case "settingEdit":
//                $v->rule('required', ["tittle", "keyword", "description", "author"]);
//                $v->rule('optional', ["email", "phone", "footer_text"]);
//                $v->rule('email', "email");
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                break;
//            case "settingLogo":
//                $v->rule('required', ["logo", "file_languages"]);
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                break;
//            case "settingFavicon":
//                $v->rule('required', ["favicon", "file_languages"]);
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                break;
//
//            case "refAdd":
//                $v->rule('required', ["referenceLogo"]);
//                $v->rule('optional', ["brandName_tr","brandDescription_tr","brandName_en","brandDescription_en"]);
//                $v->rules([
//                    'lengthMin' => [
//                        ['brandName_tr',2],
//                        ['brandDescription_tr',2],
//                        ['brandName_en',2],
//                        ['brandDescription_en',2]
//                    ]
//                ]);
//
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                break;
//                case "refUpdate":
//                $v->rule('optional', ["referenceLogo"]);
//                $v->rule('optional', ["brandName_tr","brandDescription_tr","brandName_en","brandDescription_en"]);
//                $v->rules([
//                    'lengthMin' => [
//                        ['brandName_tr',2],
//                        ['brandDescription_tr',2],
//                        ['brandName_en',2],
//                        ['brandDescription_en',2]
//                    ]
//                ]);
//
//                $v->labels($this->langValidation($required["location"], $required["page"], $required["lang"]));
//                break;
//        }
        switch ($required["operation"]) {
            case "login":
                $v->rule('required', ['kadi', 'password']);
                $v->labels(["kadi"=>"Kullanıcı adı","password"=>"Şifre"]);
                $v->rule('lengthMin', 'password', 6);
                break;
        }
        if ($v->validate()) {
            $result = ["boolean" => false];
        } else {
            // Errors
            $result = ["boolean" => true, $v->errors()];
        }
        return $result;

    }

    public function validInfo($response, array $labels): array
    {

        $results = [];
        foreach ($labels as $label) {
           if (!empty($response["$label"])) {
               $results = array_merge($results, $response[$label]);
           }
        }
        return $results;
    }

}