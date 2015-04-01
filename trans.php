<?php


    switch($argc){
        case 1:
            echo "请输入参数， -help 为帮助";
            break;
        case 2:
            $key = trim($argv[1]);
            if($key == '-help' || $key == '--help'){
                printHelp();
            }
            else
                trans($key);
            break;
        case 3:
            $key = trim($argv[1]);
            $arg = trim($argv[2]);
            if ($arg == '-a' || $arg == '-s' || $arg == '-r' || $arg == '-e'){
                trans($key,$arg);
            }

    }

    function printHelp(){
        echo "我是帮助";
    }
    function trans($key,$arg=""){
        $key = mb_convert_encoding($key, "UTF-8");
        $url = 'http://fanyi.youdao.com/openapi.do?keyfrom=everyun&key=1376377100&type=data&doctype=json&version=1.1&q='.urlencode($key);
        $content = file_get_contents($url);
        $wordArr = json_decode($content,true);
        if($wordArr['errorCode'] != 0){
            echo "\n\t出错了:\t";
            switch($wordArr['errorCode']){
                case 20:
                    echo "要翻译的文本过长";
                    break;
                case 30:
                    echo "无法进行有效的翻译";
                    break;
                case 40:
                    echo "不支持这种语言";
                    break;
                case 50:
                    echo "无效的 Key";
                    break;
                case 60:
                    echo "未找到改词";
            }
        }
        else{
            if($arg == '-a'){
                print_r($wordArr);
            }
            elseif($arg == '-s'){
                print_r($wordArr['translation'][0]);
                echo "\n";
            }
            elseif($arg == '-r'){
                //trans($key);
                $read = array();
                $read['de'] = array('默认发音',$wordArr['basic']['phonetic']);
                if(isset($wordArr['basic']['us-phonetic'])){
                    $read['us'] = array('美式发音',$wordArr['basic']['us-phonetic']);
                }
                if(isset($wordArr['basic']['uk-phonetic'])) {
                    $read['uk'] = array('英式发音', $wordArr['basic']['uk-phonetic']);
                }

                foreach($read as $key){
                    echo "\t".$key[0].": [ ".$key[1]." ]\n";
                }
            }
            elseif($arg == '-e'){
                if(isset($wordArr['web'])){
                    $example = $wordArr['web'];
                    echo "\t例句: \n";
                    foreach($example as $key){
                        echo "\n\t".$key['key'].": \n";
                        foreach($key['value'] as $value){
                            echo "\t  ".$value."\n";
                        }
                    }
                }
                else
                {
                    echo "\t这个单词没有例句\n";
                }
            }
            else{
                echo "  ".$key."的释义为:\n";
                $basic = $wordArr['basic']['explains'];
                foreach($basic as $key){
                    echo "\t->".$key."\n";
                }
            }
        }
    }
?>