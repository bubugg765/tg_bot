

<?php

$OnKeyFully = "";
function RCry($encryptedText,$key) {
    global $OnKeyFully;
    $encryptedText = hex2bin($encryptedText);
    $iv = substr($encryptedText, 0, 16);
    $ciphertext = substr($encryptedText, 16);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}
$db_host = "localhost";
$db_username = "";
$db_password = "";
$db_database = "";

$chatid1 = 0;

$encryption_key = '';
$db_conn_err = 0;

$conn = mysqli_connect($db_host,$db_username,$db_password,$db_database) or $db_conn_err = 1;

$reason = "tgbot";
$humanDate = date('Y-m-d H:i:s', time());

header('Content-Type: text/html; charset=utf-8');

$bot_token = "";
$chatida = 0;
$data = file_get_contents('php://input'); 


$data = json_decode($data, true); 
//file_put_contents(__DIR__ . '/log.txt', print_r($data, true));

if (!empty($data['message'])) {
    $chat_id = $data['message']['from']['id'];
    $realchat_id = $data['message']['chat']['id'];
    $supergroup = $data['message']['chat']['type'] == "supergroup";
    $is_topic = $data['message']['reply_to_message']['is_topic_message'];
    $topic_name = $data['message']['reply_to_message']['forum_topic_created']['name'];
    $topic_id = $data['message']['message_thread_id'];
    $user_name = $data['message']['from']['username'];
    $first_name = $data['message']['from']['first_name'];
    $last_name = $data['message']['from']['last_name'];
    $text = trim($data['message']['text']);
    $text_array = explode(" ", $text);
    $filename = $data['message']['document']['file_name'];
    $file_id = $data['message']['document']['file_id'];
    $caption = $data['message']['caption'];
    
    
    if(!empty($text))
        {    
        if ($text == '/myid') {
            message_to_telegram($bot_token, $chat_id, $chat_id,$supergroup,$topic_id, $realchat_id);
            die();
            
        }
            if ($chat_id == 0)
       {


    if ($text == '/help') {
        $text_return = "Привет, $first_name! Вот мои команды: 


Блокировка:

/ipban - забанить пользователя по ip.
/hwban - забанить пользователя по hwid.
/userban - забанить пользователя по никнейму.
/delkey -  удалить ключ.


Разблокировка:

/ipunban - разбанить пользователя по ip.
/hwunban - разбанить пользователя по hwid.
/userunban - разбанить пользователя по никнейму.
/keyunban - разбанить пользователя по ключу.

Другое:

/resethash - сбросить хэш лоадера
/addkey - сгенерировать ключ (кол-во,товар,дни,комментарий)
/texraboti - установить статус тех работ (1/0)
/checkban - Проверка находится ли в бане ключ / ip / hwid / никнейм
/keyinfo - выдать информацию о ключе
/hwreset - сброс хвида ключа
";
        message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
    }
    elseif (strpos($text, '/resethash') === 0) {
    $habza = "";
    $reqresult = mysqli_query($conn, "UPDATE `loaderinfo` SET `CurrHash` = '".$habza."'");
    if($reqresult)
        $text_return = "Хэш успешно сброшен.";
    if($text_return)
        message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
elseif (strpos($text, '/version') === 0) {
    $habza = isset($text_array[1]) ? $text_array[1] : '';
    if($habza){
        $reqresult = mysqli_query($conn, "UPDATE `loaderinfo` SET `CurrVersion` = '".$habza."'");
        if($reqresult)
            $text_return = "Версия успешно изменена.";
        if($text_return)
            message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
    }
}
elseif (strpos($text, '/addkey') === 0) {
    if(isset($text_array[1])&&isset($text_array[2])&&isset($text_array[3]))
    {
        $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
        if ($ip_to_ban != "") {
            $keys = "";
            if($ip_to_ban>50)
            $ip_to_ban = 50;
            for($i = 0; $i != $ip_to_ban; $i++)
            {
                $key = sha1(uniqid() . time() . $i);
                $pipisa = $text_array[2] * 1440;
                $game = $text_array[3];
                $comm = isset($text_array[4]) ? $text_array[4] : '';
                if($game == "CS2")
                    $game = "CS2 Ext";
                $result = mysqli_query($conn, "INSERT INTO `keys` (product_key, hwid, is_used, minutes, game, commentus, status, activation_timestamp, initial_value, hwid_reset_times) VALUES ('".$key."', 0, 0, '".$pipisa."', '".$game."', '".$comm."', 'none', 0, '".$pipisa."', 2)");
                if(!$result)
                {
                    $text_return = 'Ошибка: ' . mysqli_error($conn);
                    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
                    die();
                }
                else
                    $keys .= $key . "\n"; 
            }
            message_to_telegram($bot_token, $chat_id, $keys,$supergroup,$topic_id, $realchat_id);
            die();
        }
        else
        {
            $text_return = "Введите команду в формате /addkey `значение`";
            message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
        }
    }
    die();
}
    
elseif (strpos($text, '/ipban') === 0) {
        $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip_to_ban)) {
         
        $result = mysqli_query($conn, "INSERT INTO bans (reason, ip, ban_timestamp) VALUES ('".$reason."', '".$ip_to_ban."', '".$humanDate."')");

        if ($result) {
             $text_return = "IP-адрес заблокирован";
        } else {
             $text_return = 'Ошибка: ' . mysqli_error($conn);
        }
        
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `ip`= '".$ip_to_ban."'");
        
        if (mysqli_num_rows($queryt) > 0) {
    	mysqli_query($conn, "UPDATE `keys` SET `status` = 'banned' WHERE `ip` = '".$ip_to_ban."'");
        }
        
       
     } else {
        $text_return = "IP-адрес некорректен";
     }
  } else {
      $text_return = "Введите команду в формате:
/ipban 'необходимый ip адрес'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
elseif (strpos($text, '/hwban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match("/MFP/", $ip_to_ban)) {
         
        mysqli_query($conn, "INSERT INTO bans (reason, hwid, ban_timestamp) VALUES ('".$reason."', '".$ip_to_ban."', '".$humanDate."')");
        
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `hwid`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0) {
    	mysqli_query($conn, "UPDATE `keys` SET `status` = 'banned' WHERE `hwid` = '".$ip_to_ban."'");
        }
    	
        $text_return = "HWID заблокирован";
     } else {
        $text_return = "HWID некорректен";
     }
  } else {
      $text_return = "Введите команду в формате:
/hwban 'необходимый hwid'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
elseif (strpos($text, '/userban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
        mysqli_query($conn, "INSERT INTO bans (reason, username, ban_timestamp) VALUES ('".$reason."', '".$ip_to_ban."', '".$humanDate."')");
        
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `login`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0) {
    	mysqli_query($conn, "UPDATE `keys` SET `status` = 'banned' WHERE `login` = '".$ip_to_ban."'");
        }
    	
        $text_return = "Никнейм заблокирован";
  } else {
      $text_return = "Введите команду в формате:
/userban 'необходимый никнейм'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

elseif (strpos($text, '/decode') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
        try {
            $encrypted_query = base64_decode( $ip_to_ban );
        } catch (Exception $e) {
            $text_return = "ошибка base64";
            message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
            die();
        }
    
        try {
            $decrypted_query = RCry($encrypted_query,$OnKeyFully);
        } catch (Exception $e) {
            $text_return = "ошибка ssl";
            message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
            die();
        }
        
        $text_return = $decrypted_query;
  } else {
      $text_return = "Введите команду в формате:
/decode 'необходимый текст'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
elseif (strpos($text, '/delkey') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."' AND `status` = 'none'");
        if (mysqli_num_rows($queryt) > 0) {
           mysqli_query($conn, "INSERT INTO bans (reason, product_key, ban_timestamp) VALUES ('".$reason."', '".$ip_to_ban."', '".$humanDate."')");
           
    	   $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."'");
           if (mysqli_num_rows($queryt) > 0) {
        	mysqli_query($conn, "DELETE FROM `keys` WHERE `product_key` = '".$ip_to_ban."'");
           }
    	   
           $text_return = "Ключ удален";
        }
        else {
            $text_return = "Ключ не найден";
        }
  } else {
      $text_return = "Введите команду в формате:
/delkey 'необходимый ключ'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}


elseif (strpos($text, '/ipunban') === 0) {
        $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip_to_ban)) {
        mysqli_query($conn, "DELETE FROM bans WHERE `ip` = '".$ip_to_ban."'");
        
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `ip`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0) {
        mysqli_query($conn, "UPDATE `keys` SET `status` = 'none' WHERE `ip` = '".$ip_to_ban."'");
        }
    	
        $text_return = "IP-адрес разблокирован";
     } else {
        $text_return = "IP-адрес некорректен";
     }
  } else {
      $text_return = "Введите команду в формате:
/ipunban 'необходимый ip адрес'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
elseif (strpos($text, '/hwunban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match("/MFP/", $ip_to_ban)) {
        mysqli_query($conn, "DELETE FROM bans WHERE `hwid` = '".$ip_to_ban."'");
        
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `hwid`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0) {
        mysqli_query($conn, "UPDATE `keys` SET `status` = 'none' WHERE `hwid` = '".$ip_to_ban."'");
        }
    	
        $text_return = "HWID разблокирован";
     } else {
        $text_return = "HWID некорректен";
     }
  } else {
      $text_return = "Введите команду в формате:
/hwunban 'необходимый hwid'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
elseif (strpos($text, '/userunban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
        mysqli_query($conn, "DELETE FROM bans WHERE `username` = '".$ip_to_ban."'");
        
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `login`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0) {
        mysqli_query($conn, "UPDATE `keys` SET `status` = 'none' WHERE `login` = '".$ip_to_ban."'");
        }
    	
        $text_return = "Никнейм разблокирован";
  } else {
      $text_return = "Введите команду в формате:
/userunban 'необходимый никнейм'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
elseif (strpos($text, '/keyunban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."' AND `status` = 'banned'");
        if (mysqli_num_rows($queryt) > 0) {
           mysqli_query($conn, "DELETE FROM `bans` WHERE `product_key` = '".$ip_to_ban."'");
    	   mysqli_query($conn, "UPDATE `keys` SET `status` = 'none' WHERE `product_key` = '".$ip_to_ban."'");
           $text_return = "Ключ разблокирован";
        }
        else {
            $text_return = "Ключ не найден";
        }
  } else {
      $text_return = "Введите команду в формате:
/keyunban 'необходимый ключ'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
elseif (strpos($text, '/unfreeze') === 0) {
    $zapros;
    if(isset($text_array[1])&&isset($text_array[2]))
    {
        if($text_array[1] == "all")
            $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'none' WHERE `status`= 'freezed' AND `is_used` = '1' AND `commentus` = '".$text_array[2]."'");
        else
            $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'none' WHERE `status`= 'freezed' AND `is_used` = '1' AND `game` = '".$text_array[1]."' AND `commentus` = '".$text_array[2]."'");
    }
    else if(isset($text_array[1]))
        $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'none' WHERE `status`= 'freezed' AND `is_used` = '1' AND `game` = '".$text_array[1]."'");
    else
        $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'none' WHERE `status`= 'freezed' AND `is_used` = '1'");
    
    if($zapros)
        message_to_telegram($bot_token, $chat_id, "Ключи успешно разморожены",$supergroup,$topic_id, $realchat_id);
    else
        message_to_telegram($bot_token, $chat_id, "Ошибка: ". mysqli_error($conn),$supergroup,$topic_id, $realchat_id);
    die();
}
elseif (strpos($text, '/freeze') === 0) {
    $zapros;
    if(isset($text_array[1])&&isset($text_array[2]))
    {
        if($text_array[1] == "all")
            $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'freezed' WHERE `status`= 'none' AND `is_used` = '1' AND `commentus` = '".$text_array[2]."'");
        else
            $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'freezed' WHERE `status`= 'none' AND `is_used` = '1' AND `game` = '".$text_array[1]."' AND `commentus` = '".$text_array[2]."'");
    }
    else if(isset($text_array[1]))
        $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'freezed' WHERE `status`= 'none' AND `is_used` = '1' AND `game` = '".$text_array[1]."'");
    else
        $zapros = mysqli_query($conn,"UPDATE `keys` SET `status` = 'freezed' WHERE `status`= 'none' AND `is_used` = '1'");
    
    if($zapros)
        message_to_telegram($bot_token, $chat_id, "Ключи успешно заморожены",$supergroup,$topic_id, $realchat_id);
    else
        message_to_telegram($bot_token, $chat_id, "Ошибка: ". mysqli_error($conn),$supergroup,$topic_id, $realchat_id);
    die();
}
elseif (strpos($text, '/checkban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match("/MFP/", $ip_to_ban)) {
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `hwid`= '".$ip_to_ban."' AND `status` = 'banned'");
    	$queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `hwid`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "HWID находится в бане";
        }
        else {
           $text_return = "HWID не находится в бане";
        }
     }
     else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip_to_ban)) {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `ip`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `ip`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "Ip находится в бане";
        }
        else {
           $text_return = "Ip не находится в бане";
        }
     }
     else {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `login`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `username`= '".$ip_to_ban."'");
        $queryt3 = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt4 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `product_key`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "Никнейм находится в бане";
        }
        else if (mysqli_num_rows($queryt3) > 0 || mysqli_num_rows($queryt4) > 0) {
           $text_return = "Ключ находится в бане";
        }
        else {
           $text_return = "Никнейм или Ключ не находится в бане";
        }
     }
  } else {
      $text_return = "Введите команду в формате:
/checkban 'ключ / ip / hwid / username'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

elseif (strpos($text, '/texraboti') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {


    $sreqresult = mysqli_query($conn, "SELECT * FROM `loaderinfo`");
    if($sreqresult)
        {
            $datat = $sreqresult->fetch_assoc();
            $status = $datat["CurrTexrab"];
            if($status != $ip_to_ban)
            {
                $reqresult = mysqli_query($conn, "UPDATE `loaderinfo` SET `CurrTexrab` = '".$ip_to_ban."'");
                if($reqresult)
                    $text_return = "Установлено значение техработ: ".$ip_to_ban;
                else
                    $text_return = "Ошибка подключения к базе данных";
            }
            else
            {
                $text_return = "Значение техработ итак ".$ip_to_ban;
            }
        }
        else
        {
            $text_return = "Ошибка подключения к базе данных";
        }

  } else {
      $text_return = "Введите команду в формате:
/texraboti '0/1'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

elseif (strpos($text, '/keyinfo') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {

     $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."'");
     $datat = $queryt->fetch_assoc();
     if ($datat)
     {
     $status = $datat["status"];
     $minutes = $datat["minutes"];
     $activatetime = $datat["activation_timestamp"];
     $username = $datat["login"];
     $isused = $datat["is_used"];
     if ($isused == "1" && $status == "none")
     {
         $status = "used";
     }
     if ($isused == "0" && $status == "none" && $username == "")
     {
         $status = "not used";
     }
     if ($isused == "0" && $status == "none" && $username != "")
     {
         $status = "hwid reseted";
     }
     
     $text_return = "Информация о ключе:
Статус: $status
Активирован: $activatetime
Минуты: $minutes
Ник: $username";
     }
     else
     {
        $text_return = "Ключ не найден!";
     }
  } else {
      $text_return = "Введите команду в формате:
/keyinfo 'необходимый ключ'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

elseif (strpos($text, '/hwreset') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {

     $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."'");
     $datat = $queryt->fetch_assoc();
     if ($datat)
     {
         $status = $datat["status"];
         $minutes = $datat["minutes"];
         $activatetime = $datat["activation_timestamp"];
         $username = $datat["login"];
         $isused = $datat["is_used"];
         if ($isused == "1" && $status == "none")
         {
             $status = "used";
         }
         if ($isused == "0" && $status == "none" && $username == "")
         {
             $status = "not used";
         }
         if ($isused == "0" && $status == "none" && $username != "")
         {
             $status = "hwid reseted";
         }
         if ($status == "used")
         {
             $query = mysqli_query($conn, "UPDATE `keys` SET `hwid` = '0' WHERE `keys`.`product_key` = '".$ip_to_ban."'");
	         $query_ = mysqli_query($conn, "UPDATE `keys` SET `is_used` = '0' WHERE `keys`.`product_key` = '".$ip_to_ban."'");
             $text_return = "HWID успешно сброшен!";
         }
         if ($status == "not used")
         {
             $text_return = "ключ не активирован!";
         }
         if ($status == "hwid reseted")
         {
             $text_return = "HWID итак сброшен!";
         }
    }
    else
    {
        $text_return = "Ключ не найден!";
     }
  } else {
      $text_return = "Введите команду в формате:
/hwreset 'необходимый ключ'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

else {
    $text_return = "Команда не найдена!
Используйте /help для отображения доступных комманд.
";
        message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
    
}
else
    {
//        message_to_telegram($bot_token, $chat_id, $chat_id);
//        die();
//       }
    
    if ($text == '/help') {
        $text_return = "Привет, [$chat_id]  $first_name! Вот мои команды: 

/checkban - Проверка находится ли в бане ключ / ip / hwid / никнейм
/keyinfo - выдать информацию о ключе
";
        message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

elseif (strpos($text, '/checkban') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {
     if (preg_match("/MFP/", $ip_to_ban)) {
    	$queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `hwid`= '".$ip_to_ban."' AND `status` = 'banned'");
    	$queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `hwid`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "HWID находится в бане";
        }
        else {
           $text_return = "HWID не находится в бане";
        }
     }
     else if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip_to_ban)) {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `ip`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `ip`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "Ip находится в бане";
        }
        else {
           $text_return = "Ip не находится в бане";
        }
     }
     else {
        $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `login`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt2 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `username`= '".$ip_to_ban."'");
        $queryt3 = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."' AND `status` = 'banned'");
        $queryt4 = mysqli_query($conn,"SELECT * FROM `bans` WHERE `product_key`= '".$ip_to_ban."'");
        if (mysqli_num_rows($queryt) > 0 || mysqli_num_rows($queryt2) > 0) {
           $text_return = "Никнейм находится в бане";
        }
        else if (mysqli_num_rows($queryt3) > 0 || mysqli_num_rows($queryt4) > 0) {
           $text_return = "Ключ находится в бане";
        }
        else {
           $text_return = "Никнейм или Ключ не находится в бане";
        }
     }
  } else {
      $text_return = "Введите команду в формате:
/checkban 'ключ / ip / hwid / username'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}
elseif (strpos($text, '/keyinfo') === 0) {
    $ip_to_ban = isset($text_array[1]) ? $text_array[1] : '';
  if ($ip_to_ban != "") {

     $queryt = mysqli_query($conn,"SELECT * FROM `keys` WHERE `product_key`= '".$ip_to_ban."'");
     $datat = $queryt->fetch_assoc();
     if ($datat)
     {
     $status = $datat["status"];
     $minutes = $datat["minutes"];
     $activatetime = $datat["activation_timestamp"];
     $username = $datat["login"];
     $isused = $datat["is_used"];
     if ($isused == "1" && $status == "none")
     {
         $status = "used";
     }
     if ($isused == "0" && $status == "none" && $username == "")
     {
         $status = "not used";
     }
     if ($isused == "0" && $status == "none" && $username != "")
     {
         $status = "hwid reseted";
     }
     
     $text_return = "Информация о ключе:
Статус: $status
Активирован: $activatetime
Минуты: $minutes
Ник: $username";
     }
     else
     {
        $text_return = "Ключ не найден!";
     }
  } else {
      $text_return = "Введите команду в формате:
/keyinfo 'необходимый ключ'";
  }
    message_to_telegram($bot_token, $chat_id, $text_return,$supergroup,$topic_id, $realchat_id);
}

}
        }
    else
        {
            if(!$filename)
                die("");
            $file_info = json_decode(file_get_contents("https://api.telegram.org/bot{$bot_token}/getFile?file_id={$file_id}"), true);
            $file_path = $file_info['result']['file_path'];
            $file_url = "https://api.telegram.org/file/bot{$bot_token}/{$file_path}";
            $file_contents = file_get_contents($file_url);
            $hex_string = bin2hex($file_contents);
            $hex_string[0] = 4;
            $hex_string[1] = 6;
            $encrypted_hex_string = openssl_encrypt($hex_string, 'aes-256-cbc', $encryption_key);
            $Gquery2 = mysqli_query($conn, "SELECT * FROM `p_data` WHERE `Game`='".$caption."'");
            $zapros;
            if(mysqli_num_rows($Gquery2) != 0)
            {
                $Grow = mysqli_fetch_assoc($Gquery2);
                $oldver = $Grow['Version'];
                $newver = 1+$oldver;
                $zapros = mysqli_query($conn, "UPDATE `p_data` SET `Data` = '".$encrypted_hex_string."', `Version` = '".$newver."' WHERE `Game`='".$caption."'");
                if($zapros)
                    message_to_telegram($bot_token, $chat_id, "Файл обновлен",$supergroup,$topic_id, $realchat_id);
                else
                    message_to_telegram($bot_token, $chat_id, "Ошибка обновления файла: ". mysqli_error($conn),$supergroup,$topic_id, $realchat_id);
            }
            else
                {
                    $zapros = mysqli_query($conn, "INSERT INTO `p_data` (Name, Data, Game, Version) VALUES ('$filename','$encrypted_hex_string','$caption',1)");
                    if($zapros)
                        message_to_telegram($bot_token, $chat_id, "Файл сохранен",$supergroup,$topic_id, $realchat_id);
                    else
                        message_to_telegram($bot_token, $chat_id, "Ошибка записи файла: ". mysqli_error($conn),$supergroup,$topic_id, $realchat_id);
                }
        }
        
    die();
}

function message_to_telegram($bot_token, $chat_id, $message, $supergroup, $topic_id, $realchat_id) {
    $url = "https://api.telegram.org/bot$bot_token/sendMessage";
    $data;
    if(!$supergroup)
    {
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
        ];
    }
    else
    {
        $data = [
                'message_thread_id' => $topic_id,
                'chat_id' => $realchat_id,
                'text' => $message
            ];
    }
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $ans = file_get_contents($url, false, $context);

}
