<?php
error_reporting(0);
function captcha_bitmoon() {
    $eol = "\n";
    $boundary = "------WebKitFormBoundary";
    $content = 'Content-Disposition: form-data; name="payload"';
    
   #h while (true) {
        $code = az_num(16);
        $data = '';
        $data .= $boundary . $code . $eol;
        $data .= $content . $eol . $eol;
        $data .= base64_encode(json_encode(["i" => 1, "a" => 1, "t" => "dark", "ts" => round(time() * 1000)])) . $eol;
        $data .= $boundary . $code . '--';
        
        $r = base_run(host . "system/libs/captcha/request.php", $data, 1, $code);
        
        if ($r["status"] == 403) {
            print m . "there is an error!!";
            sleep(1);
            r();
            return "";
        }
        
        $r = base_run(host . "system/libs/captcha/request.php?payload=" . base64_encode(json_encode(["i" => 1, "ts" => round(time() * 1000)])));
        
        if ($r["status"] == 403) {
            print p . "no captcha wait!";
            L(60);
            r();
            return "";
        }
        
        for ($i = 0; $i < 5; $i++) {
            $coordinate = coordinate($r["res"], $i);
            if ($coordinate["x"]) {
                break;
            }
        }
        
        if (!$coordinate["x"]) {
            return "";
        }
        
        $microtime = ["ts" => round(time() * 1000)];
        $load = ["i", "x", "y", "w", "a"];
        $pay = [1, $coordinate["x"], $coordinate["y"], 314.661, 2];
        
        $answer = array_combine($load, $pay);
        $answer_enc = json_encode(array_merge($answer, $microtime));

        $code1 = az_num(16);
        $data1 = '';
        $data1 .= $boundary . $code1 . $eol;
        $data1 .= $content . $eol . $eol;
        $data1 .= base64_encode($answer_enc) . $eol;
        $data1 .= $boundary . $code1 . '--';
        
        $r = base_run(host . "system/libs/captcha/request.php", $data1, 1, $code1);
        
        if ($r["status"] == 200) {
            return join(',', [$answer["x"], $answer["y"], $answer["w"]]);
        } else {
          print p . "error captcha not solve";
          sleep(2);
          r();
          return "";
        #}
        
    }
}


function count_key($iconPath, $count) {
  for ($o = 0; $o < count($iconPath); $o++) {
    $image = imagecreatefromstring($iconPath[$o]);
    $width = imagesx($image);
    $height = imagesy($image);
    $pixel_count = 0;

    for ($x = 0; $x < $width; $x++) {
      for ($y = 0; $y < $height; $y++) {
        $color = imagecolorat($image, $x, $y);

        if ($color == 0) {
          $pixel_count++;
        }
      }
    }

    imagedestroy($image);
    $array_pixel_count[] = $pixel_count;
    unset($pixel_count);
  }

  $values_count = array_count_values($array_pixel_count);

  if ($count == count($values_count)) {
    return "";
  }

  for ($i = 0; $i < count($array_pixel_count); $i++) {
    if (!$array_pixel_count[$i] || 10 >= $array_pixel_count[$i]) {
      return "";
    }
  }

  for ($i = 0; $i < count($array_pixel_count); $i++) {
    if (!$array_pixel_count[$i]) {
      break;
    }

    $key[] = $values_count[$array_pixel_count[$i]];
  }

  for ($i = 0; $i < count($array_pixel_count); $i++) {
    if ($key[$i] == 1) {
      $valid[] = $key[$i];
    }
  }

  if ($valid) {
    if (count($valid) >= 2) {
      return "";
    }
  }

  for ($i = 0; $i < count($array_pixel_count); $i++) {
    if ($key[$i] == 1) {
      $key_array = "$i";
      break;
    }
  }

  if (!$key_array) {
    for ($l = 0; $l < count($array_pixel_count); $l++) {
      if ($key[$l] == 2) {
        $key_array = "$l";
        break;
      }
    }
  }

  if (!$key_array) {
    for ($l = 0; $l < count($array_pixel_count); $l++) {
      if ($key[$l] == 3) {
        $key_array = "$l";
        break;
      }
    }
  }

  return $key_array;
}

function coordinate($img, $negate = 0) {
  //$img = file_get_contents("coba1.png");
  if (300 >= strlen($img)) {
    print "image not found!";
    r();
    return "";
  }
  
  $isx = [
    [10, 74, 138, 202, 265],
    [5, 58, 110, 163, 217, 270],
    [3, 48, 93, 138, 183, 228, 273],
    [3, 44, 84, 124, 164, 204, 244, 284]
  ];

  $array_container = [
    [31, 96, 159, 226, 286],
    [25, 80, 135, 185, 240, 290],
    [22, 68, 112, 158, 202, 248, 295],
    [20, 60, 100, 140, 180, 220, 260, 300]
  ];

  for ($o = 0; $o < count($isx); $o++) {
    for ($z = 0; $z < count($isx[$o]); $z++) {
      ob_start();
      $image = imagecreatefromstring($img);
      $width = imagesx($image);
      $height = imagesy($image);
      
      if (count($isx[$o]) == 5) {
        $cut_width = 45;
      } elseif (count($isx[$o]) == 6) {
        $cut_width = 45;
      } elseif (count($isx[$o]) == 7) {
        $cut_width = 39;
      } elseif (count($isx[$o]) == 8) {
        $cut_width = 33;
      }
      
      if ($negate == 1) {
        imagefilter($image, IMG_FILTER_GRAYSCALE);
        imagefilter($image,IMG_FILTER_NEGATE);
      } elseif ($negate == 2) {
        imagefilter($image, IMG_FILTER_NEGATE);
      } elseif ($negate == 3) {
        imagefilter($image,IMG_FILTER_GRAYSCALE);
      } else {
        for ($x = 0; $x < 2; $x++) {
          imagealphablending($image, false);
          imagesavealpha($image, true);
          $transparan = imagecolorallocatealpha($image, 0, 0, 0, 127);
          imagefill($image, 0, 0, $transparan);
        }
      }
    
      $image = imagecrop($image, ['x' => $isx[$o][$z], 'y' => 0, 'width' => $cut_width, 'height' => $height]);
      imagepng($image);
      imagedestroy($image);
      
      $data = ob_get_contents();
      ob_end_clean();
      $file[] = $data;
      $foo[] = strlen($data);
    }

    $string_array = count_key($file, count($isx[$o]));
    if (!$string_array) {
      unset($file);
      continue;
    }
    
    if ($string_array){
      return [
        "x" => $array_container[$o][$string_array],
        "y" => rand($height/2, 30),
        "ans" => base64_encode("1,701,24,915,8,915,Mozilla,0,19,".$array_container[$o][$string_array].",412,24,1,412,Linux armv8l,0,".time())
      ];
    }
  }
}



function demo($methode,$sitekey,$site){
  while(true){
    $host = "recaptcha-v3-solver-0-1-score.p.rapidapi.com";
    $h = array(
      'X-RapidAPI-Key: bb5ef0f9f7msh7c6f6bbd32b20e5p138ee2jsn7f7e780ec6f8',
      'X-RapidAPI-Host: '.$host);
      $response = curl("https://".$host."/?siteKey=".$sitekey."&action=examples/v3scores&site=".$site,$h);
      if($response[0][0]["x-ratelimit-requests-remaining"] == 0){die($response[1]);
      }
      if(!$response[2]->token){
        continue;
      }
      return $response[2]->token;
  }
}
function demok($methode,$sitekey,$site){
  while(true){
    $host = "recaptcha-v2-solver.p.rapidapi.com";
    $h = array(
      'X-RapidAPI-Key: 5a6415fc95msh3d2bd7a05de9698p123541jsnc5536eda498e',
      'X-RapidAPI-Host: '.$host);
      $response = curl("https://".$host."/?siteKey=".$sitekey."&site=".$site,$h);
      if($response[0][0]["x-ratelimit-requests-remaining"] == 0){die($response[1]);
      }
      if(!$response[2]->token){
        continue;
      }
      return $response[2]->token;
  }
}

function c() {
    $clear = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') ? 'cls' : 'clear';
    pclose(popen($clear, 'w'));
}

function az_num($amount = false) {
    $array = array_merge(range("A", "Z"), range(0, 9));
    $az = $az_num = '';

    for ($s = 0; $s < count($array); $s++) {
        if (range(0, 25)[$s] >= $s) {
            $az .= $array[$s];
        }
        $az_num .= $array[$s];
    }

    if ($amount >= 1) {
        return substr(str_shuffle(strtolower($az) . $az_num), 0, $amount);
    } else {
        die("masukan jumlah angka\n\ncontoh -> az_num(123);\n");
    }
}

function new_cookie($cookie_old, $cookie_new) {
    $array = array('&' => '%26', '+' => '%2B', ';' => '&');
    parse_str(strtr($cookie_old, $array), $old);
    parse_str(strtr($cookie_new, $array), $new);
    $array_merge = array_merge($old, $new);
    return http_build_query($array_merge, '', ';', PHP_QUERY_RFC3986);
}

function multiexplode($delimiters, $string) {
    $ready = str_replace($delimiters, $delimiters[0], $string);
    return explode($delimiters[0], $ready);
}

function multi_strpos($haystack, $needles) {
    if (!is_array($needles)) {
        $needles = array($needles);
    }

    foreach ($needles as $needle) {
        if (strpos(strtolower($haystack), strtolower($needle)) !== false) {
            return true;
        }
    }

    return false;
}

function arr_rand($my_array = array()) {
    $copy = array();
    while (count($my_array)) {
        $element = array_rand($my_array);
        $copy[$element] = $my_array[$element];
        unset($my_array[$element]);
    }
    return array_merge($copy);
}

function set_cookie($result, $array = 0) {
    preg_match_all('/^Set-Cookie:\s*([^;\r\n]*)/mi', $result, $matches);
    $cookies = array();
    
    foreach ($matches[1] as $item) {
        parse_str($item, $cookie);
        $cookies = array_merge($cookies, $cookie);
    }
    
    if ($array) {
        return $cookies;
    }

    return urldecode(http_build_query($cookies, '', ';', PHP_QUERY_RFC3986)) . ";";
}



function scrape_chek(){
    $raw = curl("https://raw.githubusercontent.com/TheSpeedX/PROXY-List/master/http.txt")[1];
    $list = arr_rand(explode("\n",$raw));
    for($i=0;$i<count($list);$i++){
      $proxy=  explode(':', $list[$i]);
      $host = $proxy[0]; 
      $port = $proxy[1]; 
      $TimeoutInSeconds = 1;
      $valid = false;
      if($fp = @fsockopen($host,$port,$errCode,$errStr,$TimeoutInSeconds)){
      $valid = true;
      } 
      if($valid == true){
        return $list[$i];
      }
    }
}

function scrape_list() {
    $file = "proxyscrape_premium_http_proxies.txt";

    if (!file_exists($file)) {
        die("file $file tidak ada\n");
    }

    return trimed(array_values(arr_rand(file($file)))[0]);
}

function scrape_valid() {
    re:
    $key_scrape = save("key_scrape");
    $h = ["user-agent: Mozilla/5.0"];
    $url = "https://api.proxyscrape.com/v2/account/datacenter_shared/whitelist?sessionid=$key_scrape&userid=$key_scrape&type=";
    $my_ip = curl("https://api.proxyscrape.com/ip.php", $h)[1];

    while (true) {
        $ip = curl($url . "get", $h)[2];

        if (!$ip || $ip->status == "invalid") {
            print "key tidak berguna lagi silakan ganti";
            sleep(2);
            r();
            #unlink("key_scrape");
            goto re;
        }

        $list = explode(n, curl("https://api.proxyscrape.com/v2/account/datacenter_shared/proxy-list?sessionid=$key_scrape&userid=$key_scrape&type=displayproxies&protocol=http", $h)[1]);

        if (!$list[2]) {
            continue;
        }

        $proxy = str_replace(n,"",trimed(arr_rand(array_filter($list))[0]));

        if (!$ip->whitelisted[0]) {
            $req = curl($url . "add&ip[]=$my_ip", $h)[2];

            if ($req->status == "ok") {
                return $proxy;
            }
        }

        if ($my_ip == $ip->whitelisted[0]) {
            return $proxy;
        }

        if ($my_ip !== $ip->whitelisted[0]) {
            $req = curl($url . "remove&ip[]=" . $ip->whitelisted[0], $h)[2];

            if ($req->status == "ok") {
                continue;
            }
        }
    }
}





function movePage(){
    return [
         0 => "ERROR CONNECTION",
         100 => "Response 100 Continue",
         101 => "Response 101 Switching Protocols",
         200 => "Response 200 OK",
         201 => "Response 201 Created",
         202 => "Response 202 Accepted",
         203 => "Response 203 Non-Authoritative Information",
         204 => "Response 204 No Content",
         205 => "Response 205 Reset Content",
         206 => "Response 206 Partial Content",
         300 => "Response 300 Multiple Choices",
         301 => "Response 301 Moved Permanently",
         302 => "Response 302 Found",
         303 => "Response 303 See Other",
         304 => "Response 304 Not Modified",
         305 => "Response 305 Use Proxy",
         307 => "Response 307 Temporary Redirect",
         400 => "Response 400 Bad Request",
         401 => "Response 401 Unauthorized",
         402 => "Response 402 Payment Required",
         403 => "Response 403 Forbidden",
         404 => "Response 404 Not Found",
         405 => "Response 405 Method Not Allowed",
         406 => "Response 406 Not Acceptable",
         407 => "Response 407 Proxy Authentication Required",
         408 => "Response 408 Request Time-out",
         409 => "Response 409 Conflict",
         410 => "Response 410 Gone",
         411 => "Response 411 Length Required",
         412 => "Response 412 Precondition Failed",
         413 => "Response 413 Request Entity Too Large",
         414 => "Response 414 Request-URI Too Large",
         415 => "Response 415 Unsupported Media Type",
         416 => "Response 416 Requested range not satisfiable",
         417 => "Response 417 Expectation Failed",
         500 => "Response 500 Internal Server Error",
         501 => "Response 501 Not Implemented",
         502 => "Response 502 Bad Gateway",
         503 => "Response 503 Service Unavailable",
         504 => "Response 504 Gateway Time-out"
     ];
}

function remove_emoji($string) {
    return preg_replace('/[[:^print:]]/', '', $string);
}

function trimed($txt) {
    return preg_replace('/\s+/', '', $txt);
}

function lah($x = 0, $inp = 0) {
    if ($x == 1) {
        ket(k.explode("/", host)[2], m."no ".$inp." can be bypassed").line();
    } elseif ($x == 2) {
        ket(k.explode("/", host)[2], m."sorry there is no method for ".$inp).line();
    } else {
        ket(k.explode("/", host)[2], m."sorry no energy").line();
    }
}

function rt() {
    c();
    $t = $_SERVER["TMPDIR"];
    
    if (file_exists($t)) {
        system("rm -rf $t/* 2>&1");
        return true;
    }
}

function tx($a, $b = 0) {
    while (true) {
        print(h . "Input " . $a . c . " > " . p);
        $tx = trim(fgets(STDIN));

        if ($b) {
            $num = trimed(preg_replace("/[^0-9]/", "", $tx));
            
            if ($num >= 0) {
                return $num;
            } else {
                continue;
            }
        }

        return $tx;
    }
}


function ex($a, $b, $c, $d) {
    return explode($b, explode($a, $d)[$c])[0];
}

function new_save($name, $delete = false){
    $file = "data.json";
    $host = explode("/", $name)[2] ? explode("/", $name)[2] : ($name ? $name : "");
    
    if (!file_get_contents($file)) {
        file_put_contents($file,"[]");
    }    
    $decode = json_decode(file_get_contents($file), true);
    
    if ($delete) {
      
        if (strpos(http_build_query($decode), $host) === false) {
            return $decode;
        }
    }
    
    if ($decode[$host] == null) {
        $data[$host] = tx($host);
        $create = 1;
    } else {
        $data[$host] = $decode[$host];
    }
    $array = array_merge($decode, $data);
    
    if (strpos(http_build_query($array), $host) !== false) {
      
        if ($delete) {
            unset($array[$host]);
            $del = 1;
        }
    }
    
    if (preg_match_all('/"([^"]+)"\s*:\s*/', file_get_contents($file), $matches, PREG_SET_ORDER)) {
        $count = count($matches);
        
        for ($i = 2; $i < $count; $i++) {
          
            if ($matches[$i][1] == "email") {
              
                if (preg_match("#(email)#is", http_build_query($array))) {
                    $array_up["email"] = $data["email"];
                    $array = array_merge($array_up, $array);
                    $up = 1;
                    break;
                }
            }
        }
        
        for ($i = 2; $i < $count; $i++) {
          
            if ($matches[$i][1] == "user-agent") {
              
                if (preg_match("#(Mozilla)#is", http_build_query($array))) {
                    $array_up["user-agent"] = $data["user-agent"];
                    $array = array_merge($array_up, $array);
                    $up = 1;
                    break;
                }
            }
        }
    }
    
    if ($create || $up || $del) {
        file_put_contents($file, json_encode($array, JSON_PRETTY_PRINT));
        return json_decode(file_get_contents($file), true);
    } else {
        return $decode;
    }
}


function Save($a) {
    if (file_exists($a)) {
        $b = file_get_contents($a);
    } else {
        $b = tx($a);
        n;
        file_put_contents($a, $b);
    }
    return $b;
}

function an($input) {
    $a = str_split($input); 
    foreach ($a as $b) {
        print $b;
        usleep(1500);
    }
}

function text_line($input) {
    $n = "\n";
    $a = str_split(" ".$input.n); 
    foreach ($a as $b => $c) {
        if (strlen($input) >= 55) {
            if ($b >= strlen($input) / 2) {
                if ($c == " ") {
                    print $n;
                    unset($n);
                }
            }
        }
        print $c;
        usleep(1500);
    }
    line();
}

function tmr($a, $tmr) {
    date_default_timezone_set('UTC').r();
    $timr = time() + $tmr;
    $col = [b, c, h, k, m, p, u];
    
    while (true):
        $res = $timr - time();
        
        if ($res < 1) {
            break;
        }
        
        if ($a == 1) {
            print $col[array_rand($col)].'CLAIM NEXT TIME:'.date(' H', $res).'H'.date(' i', $res).'M'.date(' s', $res).'S'.d;r();
        } elseif ($a == 2) {
            print $col[array_rand($col)].'please wait'.date(' H:i:s ', $res).d;r();
        }
    endwhile;
}

function countdown($countdown) {
    for ($i = 0; $i < count($countdown); $i++) {
        $timer = bcdiv($countdown[$i], 1000) - time();
        
        if ($timer >= -2) {
            if ($timer >= 5500) {
                continue;
            } else {
                tmr(1, $timer);
                break;
            }
        }
    }
}

function diff_time($fr, $time) {
    date_default_timezone_set('asia/jakarta');
    $start = strtotime($time);
    $stop = strtotime(date("H:i"));
    $diff = $stop - $start;
    
    if (explode("-", $diff)[1]) {
        $dif = explode("-", $diff)[1];
    } else {
        $dif = $diff;
    }
    
    if ($fr * 60 >= $dif) {
        return 1;
    }
}

function L($t) {
    r();
    $col = [b, c, h, k, m, p, u];
    
    for ($i = 1; $i <= $t; $i++) {
        print $col[array_rand($col)]."\rLoading... [".intval($i/$t*100)."%]";
        flush();
        sleep(1);
    }
    
    r();
}

function r() {
    sleep(1);
    print "\r".str_repeat(' ', 62)."\r";
}

function line() {
    print str_repeat(p.'─', 50).n;
}

function ket($a, $aa, $b = 0, $bb = 0, $c = 0, $cc = 0, $d = 0, $dd = 0) {
    if ($a or $aa) {
        print h.$a.c." > ".p.$aa.n;
    } 
    if ($b or $bb) {
        print h.$b.c." > ".p.$bb.n;
    } 
    if ($c or $cc) {
        print h.$c.c." > ".p.$cc.n;
    } 
    if ($d or $dd) {
        print h.$d.c." > ".p.$dd.n;
    }
}

function ket_line($a, $aa, $b = 0, $bb = 0, $c = 0, $cc = 0) {
    if ($a or $aa) {
        print h.$a.c." > ".p.$aa;
    } 
    if ($b or $bb) {
        print " | ".h.$b.c." > ".p.$bb;
    } 
    if ($c or $cc) {
        print " | ".h.$c.c." > ".p.$cc;
    }
    print n;
}

function curl($url, $header = false, $post = false, $followlocation = false, $cookiejar = false, $alternativ_cookie = false, $proxy = false) {
    $i = 0;
    while (true) {
        $i++;
        if (!parse_url($url)["scheme"]) {
            print m."url tidak valid";
            sleep(2);
            r();
        }
        $default[CURLOPT_URL] = trimed($url);
        if ($followlocation) {
            $default[CURLOPT_FOLLOWLOCATION] = $followlocation;
        }
        $default[CURLOPT_RETURNTRANSFER] = 1;
        $default[CURLOPT_ENCODING] = 'gzip,deflate';
        $default[CURLOPT_HEADER] = 1;
        $default[CURLOPT_SSL_VERIFYPEER] = 0;
        $default[CURLOPT_SSL_VERIFYHOST] = 0;
        $default[CURLOPT_CONNECTTIMEOUT] = 15;
        $default[CURLOPT_TIMEOUT] = 30;
        if ($header) {
            $default[CURLOPT_HTTPHEADER] = $header;
        }
        if ($post) {
            if ($post == 1) {
                $default[CURLOPT_POST] = 1;
            } else {
                $default[CURLOPT_POST] = 1;
                $default[CURLOPT_POSTFIELDS] = $post;
            }
        }
        if ($cookiejar) {
            $default[CURLOPT_COOKIEFILE] = $cookiejar;
            $default[CURLOPT_COOKIEJAR] = $cookiejar;
        }
        if ($alternativ_cookie) {
            $default[CURLOPT_COOKIE] = $alternativ_cookie;
        }
        if ($proxy) {
            $default[CURLOPT_PROXY] = $proxy;
        }
        $options = $default;
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);
        $response = substr($output, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
        $info = curl_getinfo($ch);
        curl_close($ch);
        if (!$info["primary_ip"]) {
            print m.movePage()[$info["http_code"]];
            r();
            print explode("port", curl_error($ch))[0];
            r();
            continue;
        } else {
            foreach (explode("\r\n", substr($output, 0, strpos($output, "\r\n\r\n"))) as $i => $line) {
                if ($i == 0) {
                    $headers['http_code'] = $line;
                } else {
                    list($key, $value) = explode(': ', $line);
                    $header_array[$key] = $value;
                }
            }
        }
        if ($info["http_code"] == 0) {
            if (10 >= $i) {
                print k.movePage()[$info["http_code"]];
                r();
                continue;
            }
        }
        print p.movePage()[$info["http_code"]];
        r();
        return [[$header_array, $info, $output], $response, json_decode(str_replace([n, "﻿"], "", strip_tags($response)))];
    }
}


function asci($string){
    $res = ip();
    date_default_timezone_set($res["t"]);
    $acssi = [
        "a" => ["┌─┐","├─┤","┴ ┴"],
        "b" => ["┌┐ ","├┴┐","└─┘"],
        "c" => ["┌─┐","│  ","└─┘"],
        "d" => ["┌┬┐"," ││","─┴┘"],
        "e" => ["┌─┐","├┤ ","└─┘"],
        "f" => ["┌─┐","├┤ ","└  "],
        "g" => ["┌─┐","│ ┬","└─┘"],
        "h" => ["┬ ┬","├─┤","┴ ┴"],
        "i" => ["┬","│","┴"],
        "j" => [" ┬"," │","└┘"],
        "k" => ["┬┌─","├┴┐","┴ ┴"],
        "l" => ["┬  ","│  ","┴─┘"],
        "m" => ["┌┬┐","│││","┴ ┴"],
        "n" => ["┌┐┌","│││","┘└┘"],
        "o" => ["┌─┐","│ │","└─┘"],
        "p" => ["┌─┐","├─┘","┴  "],
        "q" => ["┌─┐ ","│─┼┐","└─┘└"],
        "r" => ["┬─┐","├┬┘","┴└─"],
        "s" => ["┌─┐","└─┐","└─┘"],
        "t" => ["┌┬┐"," │ "," ┴ "],
        "u" => ["┬ ┬","│ │","└─┘"],
        "v" => ["┬  ┬","└┐┌┘"," └┘ "],
        "w" => ["┬ ┬","│││","└┴┘"],
        "x" => ["─┐ ┬","┌┴┬┘","┴ └─"],
        "y" => ["┬ ┬","└┬┘"," ┴ "],
        "z" => ["┌─┐","┌─┘","└─┘"],
        " "=>[" "," "," "],
        "1" => ["┬","│","┴"],  
        "2" => ["┌─┐","┌─┘","└─┘"],  
        "3" => ["┌─┐"," ├┤","└─┘"],
        "4" => ["┬ ┬","└─┤","  ┘"],
        "5" => ["┌─┐","└─┐","└─┘"],
        "6" => ["┌─┐","├─┐","└─┘"],
        "7" => ["┌─┐","  │","  ┘"],
        "8" => ["┌─┐","├─┤","└─┘"],
        "9" => ["┌─┐","└─┤","└─┘"],
        "0" => ["┌─┐","│ │","└─┘"]
    ];
    $x = str_split($string);
    print p."time:".date("H:i").str_repeat(p.' ',7).mp." ▶ ".d.p." Xianjing7".str_repeat(p.' ',7)."date:".date("m/d/Y").n;
    line();
    print " ";
    foreach($x as $data){
    print h.$acssi[$data][0];
    }
    print h." country ".c." > ".p.$res["c"].n." ";
    foreach($x as $data){
    print c.$acssi[$data][1];
    }
    print h." region".c." > ".p.$res["r"].n." ";
    foreach($x as $data){
    print p.$acssi[$data][2];
    }
    print h." ip".c." > ".p.$res["i"].n;
    foreach($x as $data){
    print c.$acssi[$data][3];
    }
    line();
}

function ip() {
    $if = json_decode(file_get_contents("https://ipinfo.io/?utm_source=ipecho.net&utm_medium=referral&utm_campaign=upsell_sister_sites"));
    return [
        "i" => $if->ip,
        "r" => $if->region,
        "c" => $if->country,
        "t" => $if->timezone
    ];
}

function user_agent() {
    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
        $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Safari/537.36';
    } else {
        $user_agent = 'Mozilla/5.0 (Linux; Android 11; M2012K11AG) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/W.X.Y.Z Mobile Safari/537.36';
    }
    return $user_agent;
}

function head($xml = 0, $boundary = 0) {
    global $u_a, $u_c;
    $header = [];
    $header[] = "Host: " . explode("/", host)[2];
    if ($boundary) {
        $header[] = "content-type: multipart/form-data; boundary=----WebKitFormBoundary" . $boundary;
    }
    if ($xml) {
        $header[] = "x-requested-with: XMLHttpRequest";
    }
    if (!$u_a) {
        $u_a = user_agent();
    }
    $header[] = "user-agent: " . $u_a;
    if ($u_c) {
        $header[] = "cookie: " . $u_c;
    }
    return $header;
}



function multi_atb($r) {
    $apikey = save("apikey_multibot");
    preg_match_all('# <img src="(.*?)"#is', $r, $main_img);
    preg_match_all('#rel=\\\"(.*?)\\\"><img src=\\\"(.*?)\\\"#is', $r, $rell_img);
    if ($rell_img[1]) {
        for ($k = 0; $k < count($main_img[1]); $k++) {
            if (preg_match("#data:image#is", $main_img[1][$k])) {
                $main = $main_img[1][$k];
                break;
            }
        }
        if (!$main) {
            return "";
        }
        $code = az_num(16);
        $boundary = "------WebKitFormBoundary" . $code;
        $content = "Content-Disposition: form-data; name=";
        $data = '';
        for ($i = 0; $i < count($rell_img[1]); $i++) {
            $data .= $boundary . n;
            $data .= $content . '"' . $rell_img[1][$i] . '"' . n . n;
            $data .= $rell_img[2][$i] . n;
        }
        $data .= $boundary . n;
        $data .= $content . '"main"' . n . n;
        $data .= $main . n;
        $data .= $boundary . n;
        $data .= $content . '"method"' . n . n;
        $data .= "antibot" . n;
        $data .= $boundary . n;
        $data .= $content . '"key"' . n . n;
        $data .= $apikey . n;
        $data .= $boundary . n;
        $data .= $content . '"json"' . n . n;
        $data .= "1" . n;
        $data .= $boundary . "--";
    }
    $h = [
        "Content-Type: multipart/form-data; boundary=----WebKitFormBoundary" . $code
    ];
    $o = 0;
    while ($o <= 20) {
        $o++;
        if ($o == 15) {
            return "";
        }
        $js = curl("https://multibot.in/in.php", $h, $data)[2];
        if ($js->status == 1) {
            $id = $js->request;
            break;
        }
    }
    $x = 0;
    while ($x <= 20) {
        $x++;
        if ($x == 15) {
            return "";
        }
        sleep(5);
        $js = curl("https://multibot.in/res.php?action=get&id=" . $id . "&key=" . $apikey . "&json=1", ["Accept: */*"])[2];
        if ($js->request == "WRONG_RESULT") {
            return "";
        }
        if ($js->status == 1) {
            return " " . str_replace(",", " ", $js->request);
        }
        print $js->request;
        r();
    }
}

function multibot($method, $sitekey, $pageurl, $rr = 0) {
    if ($method == 'invisible_recaptchav2') {
        $method = 'recaptchav2';
    }
    if (!$sitekey) {
        print m . "sitekey not found";
        sleep(2);
        r();
        return "";
    }
    refresh:
    print p;
    $host = "api.multibot.in";
    $name_api = "apikey_multibot";
    $apikey = save($name_api);
    $recaptchav2 = http_build_query([
        "key" => $apikey,
        "method" => "userrecaptcha",
        "googlekey" => $sitekey,
        "pageurl" => $pageurl
    ]);
    $hcaptcha = http_build_query([
        "key" => $apikey,
        "method" => "hcaptcha",
        "sitekey" => $sitekey,
        "pageurl" => $pageurl
    ]);
    $type = [
        "recaptchav2" => $recaptchav2,
        "hcaptcha" => $hcaptcha
    ];
    $ua = [
        "host: " . $host,
        "content-type: application/json/x-www-form-urlencoded"
    ];
    $s = 0;
    while (true) {
        $s++;
        $r = curl("http://" . $host . "/in.php?" . $type[$method], $ua)[1];
        if ($r == "ERROR_USER_BALANCE_ZERO") {
            unlink($name_api);
            goto refresh;
        } elseif ($r == "ERROR_WRONG_USER_KEY") {
            if ($s == 3) {
                unlink($name_api);
                goto refresh;
            }
        }
        $id = explode('|', $r)[1];
        if (!$id) {
            if ($s == 3) {
                return "";
            }
            print "Get ID Captcha";
            r();
            continue;
        }
        sleep(5);
        $x = 0;
        while (true) {
            $x++;
            if ($x == 40) {
                return "";
            }
            $r1 = curl("http://" . $host . "/res.php?" . http_build_query([
                    "key" => $apikey,
                    "action" => "get",
                    "id" => $id
                ]), $ua)[1];
            if ($r1 == "CAPCHA_NOT_READY") {
                print str_replace("_", " ", $r1);
                sleep(5);
                r();
                continue;
            } elseif (strlen($r1) >= 50) {
                return explode('|', $r1)[1];
            } else {
                print str_replace("_", " ", $r1);
                r();
                goto refresh;
            }
        }
    }
}
function xevil($method, $sitekey, $pageurl, $rr = 0) {
    if ($method == 'invisible_recaptchav2') {
        $method = 'recaptchav2';
    }
    if (!$sitekey) {
        print m . "sitekey not found";
        sleep(2);
        r();
        return "";
    }
    refresh:
    print p;
    $host = "sctg.xyz";
    $name_api = "apikey_xevil";
    $apikey = save($name_api);
    $recaptchav2 = http_build_query([
        "key" => $apikey,
        "method" => "userrecaptcha",
        "googlekey" => $sitekey,
        "pageurl" => $pageurl
    ]);
    $hcaptcha = http_build_query([
        "key" => $apikey,
        "method" => "hcaptcha",
        "sitekey" => $sitekey,
        "pageurl" => $pageurl
    ]);
    $type = [
        "recaptchav2" => $recaptchav2,
        "hcaptcha" => $hcaptcha
    ];
    $ua = [
        "host: " . $host,
        "content-type: application/json/x-www-form-urlencoded"
    ];
    $s = 0;
    while (true) {
        $s++;
        $r = curl("http://" . $host . "/in.php?" . $type[$method], $ua)[1];
        if ($r == "ERROR_USER_BALANCE_ZERO") {
            unlink($name_api);
            goto refresh;
        } elseif ($r == "ERROR_WRONG_USER_KEY") {
            if ($s == 3) {
                unlink($name_api);
                goto refresh;
            }
        }
        $id = explode('|', $r)[1];
        if (!$id) {
            if ($s == 3) {
                return "";
            }
            print "Get ID Captcha";
            r();
            continue;
        }
        sleep(5);
        $x = 0;
        while (true) {
            $x++;
            if ($x == 40) {
                return "";
            }
            $r1 = curl("http://" . $host . "/res.php?" . http_build_query([
                    "key" => $apikey,
                    "action" => "get",
                    "id" => $id
                ]), $ua)[1];
            if ($r1 == "CAPCHA_NOT_READY") {
                print str_replace("_", " ", $r1);
                sleep(5);
                r();
                continue;
            } elseif (strlen($r1) >= 50) {
                return explode('|', $r1)[1];
            } else {
                print str_replace("_", " ", $r1);
                r();
                goto refresh;
            }
        }
    }
}

function solvemedia($sitekey, $pageurl) {
    $r = get_e("https://api-secure.solvemedia.com/papi/challenge.ajax");
    preg_match_all("#(magic|chalapi|chalstamp|lang|size|theme|type)(:'|:)(.*?)(,|',)#is", trimed($r), $array);
    $c = array_combine($array[1], $array[3]);
    $url = str_replace("&", ";", urldecode(http_build_query(["https://api-secure.solvemedia.com/papi/_challenge.js?k" => $sitekey, ";f" => "_ACPuzzleUtil.callbacks[0]", "l" => $c["lang"], "t" => $c["type"], "s" => $c["size"], "c" => "js,h5c,h5ct,svg,h5v,v/h264,v/webm,h5a,a/mp3,a/ogg,ua/chrome,ua/chromeW,os/android,os/android11,fwv/" . az_num(6) . "." . az_num(6) . ",jslib/jquery,htmlplus", "am" => $c["magic"], "ca" => $c["chalapi"], "ts" => $c["chalstamp"], "ct" => time() + rand(80, 100), "th" => $c["theme"], "r" => "0." . rand(1111111111111111, rand(100, 200) . "9999999999999")])));
    $header[] = 'Host: api-secure.solvemedia.com';
    $header[] = 'sec-ch-ua: "Chromium";v="W", " Not;A Brand";v="99"';
    $header[] = 'sec-ch-ua-mobile: ?1';
    $header[] = 'user-agent: ' . user_agent();
    $header[] = 'sec-ch-ua-platform: "Android"';
    $header[] = 'referer: ' . $pageurl;
    $header[] = 'accept-encoding: gzip, deflate';
    $header[] = 'accept-language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
    $header[] = 'sec-fetch-site: cross-site';
    $header[] = 'sec-fetch-mode: no-cors';
    $header1[] = 'accept: */*';
    $header1[] = 'sec-fetch-dest: script';
    $header2[] = 'accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8';
    $header2[] = 'sec-fetch-dest: image';
    $r = curl($url, array_merge($header, $header1));
    $challenge = explode('"', $r[1])[5];
    $url = "https://api-secure.solvemedia.com/papi/media?c=" . $challenge . ";w=300;h=150;fg=000000;bg=f8f8f8";
    $r = curl($url, array_merge($header, $header2));
    $img[] = base64_encode($r[1]);
    $text = explode(":", googleapis($img, "normal"))[1];
    if ($text) {
        return [$text, $challenge];
    }
}

function recaptchav3($sitekey, $pageurl) {
    $h = [
        "Host: www.recaptcha.net",
        "User-Agent: Googlebot/2.1 (+https://www.google.com/bot.html)",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8",
        "Referer: " . $pageurl,
        "Accept-Encoding: gzip, deflate, br",
        "Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7"
    ];
    $anchor_url = "https://www.recaptcha.net/recaptcha/api2/anchor?ar=1&k=" . $sitekey . "&co=" . str_replace("=", ".", base64_encode("https://" . parse_url($pageurl)["host"] . ":443")) . "&hl=id&v=" . az_num(24) . "&size=invisible&cb=" . strtolower(az_num(12));
    $query = parse_url($anchor_url);
    foreach (explode("&", $query["query"]) as $i => $line) {
        list($key, $value) = explode('=', $line);
        $results[$key] = $value;
    }
    $r = curl($anchor_url, $h);
    preg_match('/"recaptcha-token" value="(.*?)"/', $r[1], $token);
    sleep(3);
    $data = http_build_query([
        "v" => $results["v"],
        "reason" => "q",
        "c" => $token[1],
        "k" => $results["k"],
        "co" => $results["co"]
    ]);
    $h1 = [
        "Host: www.recaptcha.net",
        "Content-Length: " . strlen($data),
        "User-Agent: Googlebot/2.1 (+https://www.google.com/bot.html)",
        "Accept: */*",
        "Origin: https://www.recaptcha.net",
        "Referer: " . $anchor_url,
        "Accept-Encoding: gzip, deflate, br",
        "Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7"
    ];
    $r1 = curl("https://www.recaptcha.net/recaptcha/api2/reload?k=" . $results["k"], $h1, $data);
    preg_match("/\d+/", explode('"', $r1[1])[4], $s);
    if ($s[0] >= 110) {
        preg_match('/"rresp","(.*?)"/', $r1[1], $rresp);
        return $rresp[1];
    }
}

function icon_bits() {
    $data = http_build_query([
        "cID" => false,
        "rT" => true,
        "tM" => "light"
    ]);

    $r = base_run(host . "system/libs/captcha/request.php", $data, 1);

    if ($r["status"] >= 201) {
        return "";
    }

    $hash = $r["json"];

    if (!$hash[1]) {
        return "";
    }

    $file_size = [];

    for ($x = 0; $x < count($hash); $x++) {
        $r1 = base_run(host . "system/libs/captcha/request.php?cid=0&hash=" . $hash[$x]);

        if ($r1["status"] >= 201) {
            return "";
        }

        $file_size[] = strlen(str_replace([n, " "], "", trimed($r1["res"])));
    }

    $array = array_count_values($file_size);

    for ($i = 0; $i < count($file_size); $i++) {
        if (!$file_size[$i]) {
            break;
        }

        $code[] = $array[$file_size[$i]];
    }

    for ($i = 0; $i < count($file_size); $i++) {
        if ($code[$i] == 1) {
            $proses = "$i";
            break;
        }
    }

    $answer = $hash[$proses];

    $data1 = http_build_query([
        "cID" => false,
        "pC" => $answer,
        "rT" => 2
    ]);

    $r = base_run(host . "system/libs/captcha/request.php", $data1, 1);

    if ($r["status"] == 200) {
        return $answer;
    }
}


function antibot($html){
  preg_match_all('#rel=\\\"(.*?)\\\">#is',$html,$rell);
  preg_match_all('#png;base64,(.*?)(\\\"|")#is',$html,$img);
  $text_rel = $rell[1];
  $captcha = googleapis($img[1]);
  $text_main = $captcha;
  $text_res = $captcha[1];
  $txt[] = array('1'=>'one', '2'=>'two', '3'=>'three', '4'=>'four', '5'=>'five', '6'=>'six', '7'=>'seven', '8'=>'eight', '9'=>'nine', '10'=>'ten', 'notextreturn' => '');
  $txt[] = array('one'=>'1', 'two'=>'2', 'three'=>'3', 'four'=>'4', 'five'=>'5', 'six'=>'6', 'seven'=>'7', 'eight'=>'8', 'nine'=>'9', 'ten'=>'10');
  $txt[] = array('i'=>'1', 'ii'=>'2', 'iii'=>'3', 'iv'=>'4', 'v'=>'5', 'vi'=>'6', 'vii'=>'7', 'viii'=>'8', 'ix'=>'9', 'x'=>'10');
  $txt[] = array('c@t'=>'cat', 'd0g'=>'dog', '1!0n'=>'lion', 't!g3r'=>'tiger', 'm0nk3y'=>'monkey', '31eph@nt'=>'elephant', 'c0w'=>'cow', 'f0x'=>'fox', 'm0us3'=>'mouse', '@nt'=>'ant');
  $txt[] = array('1'=>'2-1', '2'=>'1+1', '3'=>'1+2', '4'=>'2+2', '5'=>'3+2', '6'=>'2+4', '7'=>'3+4', '8'=>'4+4', '9'=>'1+8', '11'=>'5+6');
  $txt[] = array('3-2'=>'1', '8-6'=>'2', '1+2'=>'3', '3+1'=>'4', '9-4'=>'5', '3+3'=>'6', '6+1'=>'7', '2*4'=>'8', '3+6'=>'9', '2+8'=>'10');
  $txt[] = array('200'=>'zoo', '020'=>'ozo', '002'=>'ooz', '500'=>'soo', '050'=>'oso', '005'=>'oos', '101'=>'lol', '505'=>'sos', '202'=>'zoz', '111'=>'lll');
  $txt[] = array('2*a'=>'aa', '3*a'=>'aaa', '2*b'=>'bb', '3*b'=>'bbb', '1*a+1*b'=>'ab', '1*a+2*b'=>'abb', '2*a+2*b'=>'aabb', '2*c'=>'cc', '3*c'=>'ccc', '1*c+1*a'=>'ca', '1*c+1*b'=>'cb', '1*c+2*a'=>'caa', '1*c+2*b'=>'cbb', '2*c+1*a'=>'cca');
  $txt[] = array('aa'=>'2*a', 'aaa'=>'3*a', 'bb'=>'2*b', 'bbb'=>'3*b', 'ab'=>'1*a+1*b', 'abb'=>'1*a+2*b', 'aabb'=>'2*a+2*b', 'cc'=>'2*c', 'ccc'=>'3*c', 'ca'=>'1*c+1*a', 'cb'=>'1*c+1*b', 'caa'=>'1*c+2*a', 'cbb'=>'1*c+2*b', 'cca'=>'2*c+1*a');
  $txt[] = array('--+'=>'oox', '-+-'=>'oxo', '+--'=>'xoo', '++-'=>'xxo', '-++'=>'oxx', '+-+'=>'xox', '---'=>'ooo', '+++'=>'xxx', '+-+-'=>'xoxo', '+-+'=>'-oxox');
  $txt[] = array('oox'=>'--x', 'oxo'=>'-x-', 'xoo'=>'x--', 'xxo'=>'xx-', 'xxo'=>'-xx', 'xox'=>'x-x', 'ooo'=>'---', 'xxx'=>'xxx', 'xoxo'=>'x-x-', 'oxox'=>'-x-x');
  $txt[] = array('--+'=>'--x', '-+-'=>'-x-', '+--'=>'x--', '++-'=>'xx-', '-++'=>'-xx', '+-+'=>'x-x', '---'=>'xxx', '+++'=>'---', '+-+-'=>'x-x-', '-+-+'=>'-x-x');
  $txt[] = array('oo+'=>'--x', 'o+o'=>'-x-', '+oo'=>'x--', '++o'=>'xx-', 'o++'=>'-xx', '+o+'=>'x-x', 'ooo'=>'---', '+++'=>'xxx', '+o+o'=>'x-x-', 'o+o+'=>'-x-x');
  #tambahan
  $txt[] = array('1'=>'-one', '2'=>'-two', '3'=>'-three', '4'=>'-four', '5'=>'-five', '6'=>'-six', '7'=>'-seven', '8'=>'-eight', '9'=>'-nine', '10'=>'-ten');
  $txt[] = array('one'=>'-1', 'two'=>'-2', 'three'=>'-3', 'four'=>'-4', 'five'=>'-5', 'six'=>'-6', 'seven'=>'-7', 'eight'=>'-8', 'nine'=>'-9', 'ten'=>'-10');
  $txt[] = array('cat'=>'cat', 'dog'=>'dog', 'lion'=>'lion', 'tiger'=>'tiger', 'monkey'=>'monkey', 'elephant'=>'elephant', 'cow'=>'cow', 'fox'=>'fox', 'mouse'=>'mouse', 'ant'=>'ant');
  $txt[] = array('c@t'=>'-cat', 'd0g'=>'-dog', '1!0n'=>'-lion', 't!g3r'=>'-tiger', 'm0nk3y'=>'-monkey', '31eph@nt'=>'-elephant', 'c0w'=>'-cow', 'f0x'=>'-fox', 'm0us3'=>'-mouse', '@nt'=>'-ant');
  $txt[] = array('zoo'=>'zoo', 'ozo'=>'ozo', 'ooz'=>'ooz', 'soo'=>'soo', 'oso'=>'oso', 'oos'=>'oos', 'lol'=>'lol', 'sos'=>'sos', 'zoz'=>'zoz', 'lll'=>'lll');
  $txt[] = array('200'=>'200', '020'=>'020', '002'=>'002', '500'=>'500', '050'=>'050', '005'=>'005', '101'=>'101', '505'=>'505', '202'=>'202', '111'=>'111');
  $txt[] = array('zoo'=>'200', 'ozo'=>'020', 'ooz'=>'002', 'soo'=>'500', 'oso'=>'050', 'oos'=>'005', 'lol'=>'101', 'sos'=>'505', 'zoz'=>'202', 'lll'=>'111');
  $txt[] = array('one'=>'one', 'two'=>'two', 'three'=>'three', 'four'=>'four', 'five'=>'five', 'six'=>'six', 'seven'=>'seven', 'eight'=>'eight', 'nine'=>'nine', 'ten'=>'ten');
  $txt[] = array('z00'=>'200', '0z0'=>'020', '00z'=>'002', 's00'=>'500', '0s0'=>'050', '00s'=>'005', 'i0i'=>'i0i', 's0s'=>'505', 'z0z'=>'202', 'iii'=>'111');
  $txt[] = array('200'=>'z00', '020'=>'0z0', '002'=>'00z', '500'=>'s00', '050'=>'0s0', '005'=>'00s', '101'=>'i0i', '505'=>'s0s', '202'=>'z0z', '111'=>'iii');
  #noise
  $txt[] = array('lol'=>'lot','mouss'=>'mouse','com'=>'cow','tig3r'=>'tiger','snow'=>'mouse','cet'=>'cat','mous3'=>'mouse','cot'=>'cat','bor'=>'dog','bor'=>'dog');
  $txt[] = array('monk'=>'monkey','mous3e'=>'mouse','bleph@nt'=>'elephant','seved'=>'seven','ent'=>'ant','10'=>'fen','ten'=>'fen','tion'=>'lion','monk3y'=>'monkey','m0nkey'=>'monkey');
  $txt[] = array('3lephenta'=>'elephant','esnow'=>'mouse','nt'=>'ant','c@t'=>'caf','c0t'=>'cat','111'=>'|||','|||'=>'111','tlg3r'=>'tiger','jet'=>'cat','tigar'=>'tiger');
  $txt[] = array('tig@r'=>'tiger','tlg@r'=>'tiger','mqus3'=>'mouse','don'=>'lion','moo'=>'cow','tan'=>'ten','t@n'=>'ten','ton'=>'ten','tg3r'=>'tiger','tgar'=>'tiger');
  $txt[] = array('fig³r'=>'tiger','tig³r'=>'tiger','tg³r'=>'tiger','tlg³r'=>'tiger','t!g³r'=>'tiger','ssnom'=>'mouse','l¹on'=>'lion','t¹on'=>'lion','mous³'=>'mouse','m0us³','m⁰use'=>'mouse');
  $txt[] = array('3fephent'=>'elephant','3feph@nt'=>'elephant','3faphent'=>'elephant','3fephant'=>'elephant','3f@ph@nt'=>'elephant','3!ephent'=>'elephant','e!eph@nt'=>'elephant','3lephenf'=>'elephant','eleph@nt'=>'elephant','sieph@nt'=>'elephant');
  $txt[] = array('101'=>'lot','3lephant'=>'elephant','110n'=>'lion','3lephent'=>'elephant','cou'=>'cow','cov'=>'cow','tg³r4'=>'tiger','ent14'=>'ant','jxc'=>'fox','monksy'=>'monkey');
  $txt[] = array('110n'=>'tion','cet4'=>'cat','3teph@nt'=>'elephant','eteph@nt'=>'elephant','etephant'=>'elephant','3tephant'=>'elephant','1g3r'=>'tiger','monk3'=>'monkey','monk3'=>'-monkey','hon'=>'lion');
  $txt[] = array('700'=>'200','007'=>'002','070'=>'020','900'=>'500','009'=>'005','090'=>'050','005'=>'០០s','505'=>'sus','blephant'=>'elephant');
  $txt[] = array('one'=>'-one', 'two'=>'-two', 'three'=>'-three', 'four'=>'-four', 'five'=>'-five', 'six'=>'-six', 'seven'=>'-seven', 'eight'=>'-eight', 'nine'=>'-nine', 'ten'=>'-ten');
  $txt[] = array('3leph@nt'=>'elephant','31ephant'=>'elephant','coww'=>'cow','cⓐt'=>'cat','bat'=>'cat','cor'=>'cow','voil'=>'lion','008'=>'005','800'=>'500','080'=>'050');
  for($u = 0;$u<count($txt);$u++){
    for($b = 0;$b<count($text_res);$b++){
      if(explode(",",$text_main[0])[$b] == $txt[$u][$text_res[0]]){
        $text_re[0] = $txt[$u][$text_res[0]];
            break;
      }
    }
  }
  if(!$text_re[0]){
    $text_re[0] = $text_res[0];
  }
  for($u = 0;$u<count($txt);$u++){
    for($b = 0;$b<count($text_res);$b++){
      if(explode(",",$text_main[0])[$b] == $txt[$u][$text_res[1]]){
        $text_re[1] = $txt[$u][$text_res[1]];
        break;
      }
    }
  }
  if(!$text_re[1]){
    $text_re[1] = $text_res[1];
  }
  for($u = 0;$u<count($txt);$u++){
    for($b = 0;$b<count($text_res);$b++){
      if(explode(",",$text_main[0])[$b] == $txt[$u][$text_res[2]]){
        $text_re[2] = $txt[$u][$text_res[2]];
        break;
      }
    }
  }
  if(!$text_re[2]){
    $text_re[2] = $text_res[2];
  }
  $alt = explode(",",$text_main[0]);
  $main = str_replace(",","",$text_main[0]);
  $res = [$text_re[0],$text_re[1],$text_re[2]];
  $rel = [$text_rel[0],$text_rel[1],$text_rel[2]];
  $input = [
  $res[0].$res[1].$res[2],
  $res[0].$res[2].$res[1],
  $res[1].$res[2].$res[0],
  $res[1].$res[0].$res[2],
  $res[2].$res[0].$res[1],
  $res[2].$res[1].$res[0]
  ];
  $input1 = [
    $res[0].$res[1],
    $res[0].$res[2],
    $res[1].$res[2],
    $res[1].$res[0],
    $res[2].$res[0],
    $res[2].$res[1]
    ];
    $input2 = [
      $res[1].$res[2],
      $res[2].$res[1],
      $res[2].$res[0],
      $res[0].$res[2],
      $res[0].$res[1],
      $res[1].$res[0]
      ];
      $input3 = [
        $res[0].$res[2],
        $res[0].$res[1],
        $res[1].$res[0],
        $res[1].$res[2],
        $res[2].$res[1],
        $res[2].$res[0]
        ];
        $output = [
          " ".$rel[0]." ".$rel[1]." ".$rel[2],
          " ".$rel[0]." ".$rel[2]." ".$rel[1],
          " ".$rel[1]." ".$rel[2]." ".$rel[0],
          " ".$rel[1]." ".$rel[0]." ".$rel[2],
          " ".$rel[2]." ".$rel[0]." ".$rel[1],
          " ".$rel[2]." ".$rel[1]." ".$rel[0]
          ];
          for($i = 0;$i<count($input);$i++){
            if(!$input1[$i] || !$input2[$i] || !$input3[$i]){
              print k."refresh antibot captcha!";
              r();
              break;
            }
            if($input[$i] == $main){
              return $output[$i];
            }
            if($input1[$i] == $alt[0].$alt[1]){
              return $output[$i];
            }
            if($input2[$i] == $alt[1].$alt[2]){
              return $output[$i];
            }
            if($input3[$i] == $alt[0].$alt[2]){
              return $output[$i];
            }
          }
}

function arr_api(){
$package = [
//"",
"",
"",
"kr.infozone.documentrecognition_en",
"com.inverseai.image_to_text_OCR_scanner",
"aculix.smart.text.recognizer",
"image.to.text.ocr"
];
$cert = [
//"",
"",
"",
"00a56ee22492473e1b57670fa9c44185817e5586",
"FDC669CB376A69B6D6065B8CCE8C188ADDDC4F3E",
"70E6AB2300C9406792452EA39A40690B91519C85",
"FDC669CB376A69B6D6065B8CCE8C188ADDDC4F3E"
];

$api = [
//"AIzaSyAfci4iiOtZc_ORMF2gXlQcG-0Uu2k2mgE",
"AIzaSyDSfHPltpIGd0etqy9CnVdIQGReCIrE35k",
"AIzaSyAwmW3dg4fP99_hGS6QzXb7jKwwnOcBtsE",
"AIzaSyDm5IoUGFaQLpFXqoMvB9i20xc62J0taVA",
"AIzaSyDqfshA40_b5IpjtZEuGJ8oUlRMnY4Ynk4",
"AIzaSyCt2nW_3i-RBp4kLM-9T0CzcbYQlHbJGek",
"AIzaSyA5MInkpSbdSbmozCQSuBY3pylSTgmLlaM"
];

for($i=0;$i<count($cert);$i++) {
$h[] = ["Accept-Encoding: gzip",
      "User-Agent: Dalvik/2.1.0 (Linux; U; Android 13; M2012K11AG Build/TQ3A.230901.001)",
      "x-android-package: ".$package[$i],
      "x-android-cert: ".$cert[$i],
      "Content-Type: application/json; charset=UTF-8",
      "Host: vision.googleapis.com",
      "Connection: Keep-Alive"];
}

$array = [
["api" => $api[0],"header" => $h[0]],
["api" => $api[1],"header" => $h[1]],
["api" => $api[2],"header" => $h[2]],
["api" => $api[3],"header" => $h[3]],
["api" => $api[4],"header" => $h[4]],
//["api" => $api[5],"header" => $h[5]]
];
return arr_rand($array);
}

function googleapis($img, $type=0){
  $arr_api = arr_api();
  for($i = 0;$i<count($img);$i++){
    ob_start();
    $base64_string = base64_decode($img[$i]);
    $image = imagecreatefromstring($base64_string);
    imagefilter($image, IMG_FILTER_SMOOTH, 1);
    imagefilter($image,IMG_FILTER_NEGATE);
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagecropauto($image , IMG_CROP_DEFAULT);
    imagepng($image);
    imagedestroy($image);
    $data = ob_get_contents();
    ob_end_clean();
    $imgg[] = $data;
    $data = json_encode(["requests"=>[["features"=>[["maxResults"=> 1,"type" => "DOCUMENT_TEXT_DETECTION"]],"image" => ["content" => base64_encode($imgg[$i])]]]]);
    ulang:
      $r = curl("https://vision.googleapis.com/v1/images:annotate?key=".$arr_api[$i]["api"],$arr_api[$i]["header"],$data)[1];
      if(preg_match("#(error|quota_limit_value|RESOURCE_EXHAUSTED)#is",$r)){print_r($r);die($arr_api[$i]["api"]);
        print p."Please wait, there is a limit!";
        r();
        goto ulang;
      }
      if($type == "normal"){
        return strip_tags(transliterator_transliterate('Any-Latin;Latin-ASCII;',str_replace([n,"﻿"],"",json_decode($r)->responses[0]->textAnnotations[0]->description)));
      }
      $convert = strtolower(str_replace([",,,,,,",",,,,,",",,,,",",,,",",,"],",",str_replace([" ",":","&",":","$","★","{","}","(",")","·",";","°","¯","`",".","ʾ","✔","#","_","܀","ʿ","܆","'"],",",rtrim(ltrim(strip_tags(transliterator_transliterate('Any-Latin;Latin-ASCII;',str_replace([n,"﻿"],"",json_decode($r)->responses[0]->textAnnotations[0]->description))))))));
      if($i == 0){
        $text1 = $convert;
      } else {
        $text[] = str_replace(",","",$convert);
      }
  }
  return [$text1,$text];
}




function mtk($a,$b,$c){
  if($b=="+"){
    return $a+$c;
  } elseif($b=="-"){
    return $a-$c;
  } elseif($b=="*"){
    return $a*$c;
  } elseif($b=="/"){
    return $a/$c;
  } else {
    return "error";
  }
}


        
function antb($ab){
    $a = $ab[1][0];
    $b = $ab[1][1];
    $c = $ab[1][2];
    return [
        [" ".$b,$c,$a],
        [" ".$a,$b,$c],
        [" ".$a,$c,$b],
        [" ".$b,$a,$c],
        [" ".$c,$b,$a],
        [" ".$c,$a,$b]
    ];
}

rt();
c();
const b = "\033[1;34m",
      c = "\033[1;36m",
      h = "\033[1;32m",
      k = "\033[1;33m",
      m = "\033[1;31m",
      mp = "\033[101m\033[1;37m",
      p = "\033[1;37m",
      u = "\033[1;35m",
      d = "\033[0m",
      n = "\n";


#https://urlcorner.com/CBqzuo1tu69
#https://cutp.in/TnqH
#eval(str_replace('<?php',"",file_get_contents(("build_index.php"))));
#print_r(bypass_shortlinks("https://earnow.online/L5u0D6HU"));




function build($url = 0) {
    if (preg_match("#(clk.st|clks.pro)#is", $url)) {
        $inc = "/clkclk.";
    } else {
        $inc = "/flyinc.";
    }
    $r = parse_url($url);
    
    return [
        "client_id" => az_num(8) . "-" . az_num(4) . "-" . az_num(4) . "-" . az_num(4) . "-" . az_num(16),
        "links" => "https://" . $r["host"] . $r["path"],
        "inc" => "https://" . $r["host"] . $inc . $r["path"],
        "go" => [
            "https://" . $r["host"] . "/links/go",
            "https://" . $r["host"] . "/go" . $r["path"],
            "https://" . $r["host"] . "/" . explode("/", $r["path"])[1] . "/links/go",
            "https://go/" . $r["host"] . $r["path"],
            "https://" . $r["host"] . "/xreallcygo" . $r["path"]
        ]
    ];
}



function visit_short($r, $site_url = 0, $data_token = 0) {
    $file_name = "control";
    $control = file($file_name);

    if (!$control[0]) {
        $control = ["tolol"];
    }

    $config = arr_rand(config());
    $name = $r["name"];
    $lefts = $r["left"];
    $visit = $r["visit"];
    
    if (!$name[0] || !$lefts[0]) {
        print p . "terjadi kesalahan tidak terdeteksi nama shortlinks";
        sleep(2);
        r();
        return "refresh";
    }

    $count = count($config) + count($name);

    for ($i = 0; $i < $count; $i++) {
        for ($s = 0; $s < $count; $s++) {
            $open = multiexplode(["_", "{", "[", "(", "-desktop", "-easy", "-mid", "-hard", "vip"], str_replace("-[", "[", trimed(strtolower($name[$s]))))[0];
            #die(print_r($open));
            #print $open.n;
            if (strtolower($config[$i]) == $open) {
                for ($p = 0; $p < $count; $p++) {
                    
                    if (strtolower(str_replace(n, "", $control[$p])) == host.$open or strtolower(str_replace(n, "", $control[$p])) == $open or explode("/", $lefts[$s])[0] == "0" or $lefts[$s] == "0") {
                        goto up;
                    }

                    if (preg_match("#(•)#is", $lefts[$s])) {
                        if (explode("•", $r["left"][$s])[0] == explode("•", $r["left"][$s])[1]) {
                            goto up;
                        }
                    }
                }
                
                if (preg_replace("/[^0-9]/", "", $r["visit"][$s])) {
                    if (mode == "af") {
                        $r1 = base_run(host.$r["visit"][$s], http_build_query([$r["token"][1][$s] => $r["token"][2][$s]]));
                    } elseif (mode == "icon") {
                        $cap = icon_bits();

                        if (!$cap) {
                            return "refresh";
                        }

                        $data2 = http_build_query([
                            "a" => "getShortlink",
                            "data" => preg_replace("/[^0-9]/", "", $r["visit"][$s]),
                            "token" => $r["token"],
                            "captcha-idhf" => 0,
                            "captcha-hf" => $cap
                        ]);

                        $r1 = base_run(host."system/ajax.php", $data2);

                        if ($r1["json"]->shortlink) {
                            $r1["url"] = $r1["json"]->shortlink;
                        }
                    } elseif (mode == "earnbitmoon") {
                        $cap = captcha_bitmoon();

                        if (!$cap) {
                            return "refresh";
                        }

                        $data2 = http_build_query([
                            "a" => "getShortlink",
                            "data" => preg_replace("/[^0-9]/", "", $r["visit"][$s]),
                            "token" => $r["token"],
                            "ic-hf-id" => 1,
                            "ic-hf-se" => $cap,
                            "ic-hf-hp" => ""
                        ]);

                        $r1 = base_run(host."system/ajax.php", $data2);

                        if ($r1["json"]->shortlink) {
                            $r1["url"] = $r1["json"]->shortlink;
                        }
                    } elseif (mode == "no_icon") {
                        $data = http_build_query([
                            "a" => "getShortlink",
                            "data" => preg_replace("/[^0-9]/", "", $r["visit"][$s]),
                            "token" => $r["token"]
                        ]);

                        $res = base_run(host."system/ajax.php", $data)["json"];

                        if ($res->shortlink) {
                            $r1["url"] = $res->shortlink;
                            goto run;
                        }
                    } elseif (mode == "vie_free") {
                        if (preg_match("#pre_verify#is", $r["visit"][$s])) {
                            $left = $r["left"][$s];
                            $vv = str_replace("pre_verify", "go", $r["visit"][$s]);
                            $r = base_run($r["visit"][$s]);
                            #die(print_r($r));
                            $cap = multi_atb($r["res"]);

                            if (!$cap) {
                                return "refresh";
                            }

                            $rsp = ["antibotlinks" => $cap];
                            $r["visit"][$s] = $vv;
                            $r["left"][$s] = $left;
                        }

                        if ($r["token_csrf"][1][0]) {
                            $data = data_post($r["token_csrf"], "one", $rsp);
                        }

                        if ($site_url == 1 || preg_match("#(free-ltc-info|feyorra.site)#is", $r["visit"][$s])) {
                            $r1 = base_run(str_replace("go", "cancel", $r["visit"][$s]), $data);
#die(print_r($r1));
#preg_match_all('#location: (.*)#i', $r1["r"], $res);die(print_r($res));
                            if (preg_match("#".host."#is", $r1["url1"])) {
                                preg_match_all('#location: (.*)#i', $r1["r"], $res);
#die(print_r($res));
                                if ($res[1][1]) {
                                    $r1["url1"] = trimed($res[1][1]);
                                }
                            }
                        } else {
                            $r1 = base_run($r["visit"][$s], $data);
                        }

                        if ($r1["url1"]) {
                            $r1["url"] = $r1["url1"];
                        }
                        if (preg_match("#".$r1["url1"]."#is", host)) {
                          $r1["url1"] = "";
                        }
                    } elseif (mode == "only_site") {
                        $r1 = base_run($site_url.$r["visit"][$s]);

                        if ($r1["url1"]) {
                            $r1["url"] = $r1["url1"];
                        }
                    } elseif (mode == "site_url") {
                        if ($data_token) {
                            $data_token = $data_token.$r["visit"][$s];
                        }

                        $r1 = base_run($site_url, $data_token);
                    } elseif (mode == "path") {
                        $r1 = base_run(host.$r["visit"][$s]);
                    } elseif (mode == "firefaucet") {
                        $data = $r[$name[$s]];
                        for ($rq = 0; $rq < count($data[1]); $rq++) {
                            if ($data[0][$rq]) {
                                $rrq = "$rq";
                            }
                        }
                        $raw = explode("&&", "&".$data[2][$rrq])[1];
                        parse_str($raw, $out);

                        for ($tq = 0; $tq < count($r["code"]); $tq++) {
                            if ($out[$r["code"][$tq]]) {
                                $data_post =  str_replace($out[$r["code"][$tq]], $r[$r["code"][$tq]], $raw);
                            }
                        }

                        $r1 = base_run(host.$data[1][$rrq]."/", $data_post);
                    } elseif (mode == "ofer") {
                        $data = http_build_query(array_merge([
                            "action" => "getShortlink",
                            "data" => $r["visit"][$s],
                        ], $data_token));

                        $r1 = base_offer($site_url, $data, 1);
                        $data = http_build_query(["action" => "redirect"]);

                        if (!$r1["json"]->link) {
                            return "refresh";
                        }

                     #  print_r($r1);
                        $r1 = base_offer($r1["json"]->link, $data, 1);
                        if ($r1["json"]->link) {
                            $r1["url"] = $r1["json"]->link;
                        }
                    } else {
                        die(m."mode bypass not found".n);
                    }

                    if ($r1["failed"]) {
                        if (!file_get_contents($file_name)) {
                            file_put_contents($file_name, host.$open);
                        } else {
                            file_put_contents($file_name, get_e($file_name).n.host.$open);
                        }

                        print m.$r1["failed"]." ".p.$name[$s];
                        r();
                        return "refresh";
                    }

                    run:
                    if (!parse_url($r1["url"])["scheme"]) {
                        print m."Failed to generate this link ".p.$name[$s];
                        r();
                        return "refresh";
                    }

                    ket_line("", ltrim(rtrim($name[$s])), "left", trimed($r["left"][$s]));
                    ket("", k.$r1["url"]).line();
                    
                    if (preg_match("#rsshort.com#is", $r1["url"])) {
                        $xxnx = 7;
                    } else {
                        $xxnx = 5;
                    }
                    $seconds = 90;

                    for ($h = 0; $h < $xxnx; $h++) {
                        $r2 = bypass_shortlinks($r1["url"]);

                        if (preg_match("#(http)#is", $r2)) {
                            return $r2;
                        }
                    
                        print m . "shortlinks gagal di bypass sedang mengulangi!";
                        sleep(3);
                        r();
                    }
                    
                    if (preg_match("#(refresh|skip)#is", $r2)) {
                        print p.$r2;
                        r();
                        return $r2;
                    }
                    return "refresh";
                }
            }
        }

        up:
    }
}






function h_short($xml = 0, $referer = 0, $agent =0, $boundary = 0) {
    if ($xml) {
        $headers[] = 'Accept: */*';
    } else {
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v = b3;q=0.9';
    }
    
    if ($boundary) {
        $headers[] = "content-type: multipart/form-data; boundary=----WebKitFormBoundary".$boundary;
    }
    
    if ($xml) {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
    }
    $headers[] = 'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7';
    ///$headers[] = 'CF-Connecting-IP: 127.0.0.1, 68.180.194.242';
  
    if ($agent) {
      #$agent = ' (compatible; Google-Youtube-Links)';
      $agent = ' (compatible; Googlebot/2.1; +https://www.google.com/bot.html)';
    } else {
        $user_agent = user_agent();
    }
    $headers[] = 'User-agent: '.$user_agent.$agent;
  
    if ($xml) {
        $headers[] = 'X-Requested-With: XMLHttpRequest';
    }
    if ($referer) {
        $headers[] = 'referer: '.$referer;
    }
    return $headers;
}




function base_short($url, $xml=0, $data=0, $referer=0, $agent=0, $alternativ_cookie=0, $boundary=0, $proxy=0) {
    start:
    $r = curl($url,h_short($xml, $referer, $agent, $boundary), $data,false,false, $alternativ_cookie, $proxy);
    if ($r[0][1]["http_code"] == "0") {
      goto start;
    }
    preg_match('#(reCAPTCHA_site_key":"|data-sitekey=")(.*?)(")#is', $r[1], $recaptchav2);
    preg_match('#(invisible_reCAPTCHA_site_key":")(.*?)(")#is', $r[1], $invisible_recaptchav2);
    preg_match('#(hcaptcha_checkbox_site_key":"|h-captcha" data-sitekey="|get_cap_data" data-site_key=")(.*?)(")#is', $r[1], $hcaptcha);
    preg_match('#(render=|g-recaptcha btn btn-warning" data-sitekey=")(.*?)(")#is', $r[1], $recaptchav3);
    preg_match_all('#(submit_data" action="|<a href="|action="|href = ")(.*?)(")#is', $r[1], $url1);
    preg_match_all("#(url='|location.href ='|<a href='|var api =".n."  ')(.*?)(')#is", $r[1], $url2);
    preg_match_all("#window.open(.*?)'(.*?)'#is", $r[1], $url3);
    preg_match('#share(.*?)url=(.*?)"#is', $r[1], $url4);
    preg_match('#pingback" href="(.*?)"#is', $r[1], $url9);
    preg_match('#</noscript><title>(.*?)<#is', $r[1], $url5);
    preg_match('#url=(.*?)"#is', $r[1], $url6);
    preg_match('#var url="(.*?)"#is', $r[1], $url7);
    preg_match('#noreferrer"href="(.*?)"#is', $r[1], $url8);
    preg_match_all('#hidden" name="(.*?)" value="(.*?)"#is', $r[1], $token_csrf);
    preg_match_all('#(t|") name="(.*?)" type="hidden" value="(.*?)"#is', $r[1], $token_csrf2);
    preg_match_all('#hidden" id="(.*?)" value="(.*?)"#is', $r[1], $token_csrf3);
    preg_match_all('#<input type="text" name="(.*?)" placeholder="(.*?)"#is', $r[1], $token_csrf4);
    preg_match('#(id="second">|varcountdownValue=|PleaseWait|class="timer"value="|class="timer">)([0-9]{1}|[0-9]{2})(;|"|<|s)#is', str_replace([n," "],"", $r[1]), $timer);
    preg_match_all('#(dirrectSiteCode = |ai_data_id=|ai_ajax_url=)"(.*?)(")#is', $r[1], $code_data_ajax);
    preg_match('#(sessionId: ")(.*?)(")#is', $r[1], $sessionId);
    preg_match('#(var Wtpsw = )(.*?)(;)#is', $r[1], $json_ajax);//die(print_r($r[0]));
    return [
        "status" => $r[0][1]["http_code"],
        "cookie" => set_cookie($r[0][2]),
        "data" => set_cookie($r[0][2], 1),
        "res" => $r[1],
        "hcaptcha" => $hcaptcha[2],
        "recaptchav2" => $recaptchav2[2],
        "recaptchav3" => $recaptchav3[2],
        "invisible_recaptchav2" => $invisible_recaptchav2[2],
        "token_csrf" => $token_csrf,
        "token_csrf2" => $token_csrf2,
        "token_csrf3" => $token_csrf3,
        "token_csrf4" => $token_csrf4,
        "timer" => $timer[2],
        "json" => json_decode($r[1]),
        "url" => $r[0][1]["redirect_url"],
        "url1" => $url1[2],
        "url2" => $url2[2],
        "url3" => $url3[2],
        "url4" => $url4[2],
        "url5" => $url5[1],
        "url6" => $url6[1],
        "url7" => $url7[1],
        "url8" => $url8[1],
        "url9" => $url9[1],
        "code_data_ajax" => $code_data_ajax[2],
        "sessionId" => $sessionId[2],
        "json_ajax" => json_decode($json_ajax[2])
    ];
}

function executeNode($r, $stripslashes = 0) {
    if (preg_match_all('#<script>(.*?)</script>#is', $r, $out)) {
        #die(print_r($out[1]));
        for ($i = 0; $i < count($out[1]); $i++) {
            if (preg_match('#0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ#is', $out[1][$i], $script)) {
                $input = str_replace("eval", "console.log", rtrim(ltrim($out[1][$i])));
                $output = exec("node -e '$input' 2>&1");
                if ($output) {
                    $res[] = $output;
                    continue;
                    #break;
                    #exit;
                }
            }
        }
        if ($res[0]) {
            $html = html_entity_decode(str_replace($out[0], $res, $r));
            if ($stripslashes) {
                $x = stripslashes($html);
            } else {
                $x = $html;
            }
            preg_match_all('#hidden" name="(.*?)" value="(.*?)"#is', $x, $token);
            #preg_match_all('#<input type="hidden" id="(.*?)" name="(.*?)"#is', $x, $id);
            preg_match_all('#document.getElementById[(]"in(.*?)"[)].value = "(.*?)";#is', $x, $id);
            preg_match_all('#<div wire:snapshot="(.*?)" wire:#is', $x, $snapshot);
            preg_match('#userToken":"(.*?)"#is', $x, $user_token);
            preg_match('#data-csrf="(.*?)"#is', $x, $csrf);
            preg_match("#location.replace[(]'(.*?)'#is", $x, $url);
            preg_match('#p (\d+/\d+)#is', $x, $step);
            preg_match("#countDown = (\d+)#is", $x, $tmr);
            if ($step[1]) {
                if (explode("/", $step[1])[0] == explode("/", $step[1])[1]) {
                    $final = 1;
                }
            }
            if ($token[1][1]) {
                $fix = [
                    array_values(array_unique($token[0])),
                    array_values(array_unique($token[1])), 
                    array_values(array_unique($token[2]))
                    
                ];
                if ($id) {
                  $fix = [
                      array_merge(array_values(array_unique($token[0])), $id[0]),
                      array_merge(array_values(array_unique($token[1])), $id[1]),
                      array_merge(array_values(array_unique($token[2])), $id[2])
                  ];
                }
                
            }
            return [
                "res" => $x,
                "token_csrf" => $fix,
                "url" => $url[1],
                "step" => $step[1],
                "final_step" => $final,
                "snapshot" => $snapshot[1],
                "user_token" => $user_token[1],
                "csrf" => $csrf[1],
                "timer" => $tmr[1]
            ];
        }
    }
}

function bypass_shortlinks($url, $separator = 0) {
    ulang:
    $url = str_replace("http:", "https:", $url);
    $coundown = 15;
    $seconds = 90;
    $host = parse_url(
    $url)["host"];
    $query = parse_url($url);
    
    if (explode("=", $query["query"])[0] == "api") {
        $url = "https://".explode("=", $query["query"])[2];
        $host = parse_url($url)["host"];
    }
    
    if (explode("p=", $url)[1]) {
        $url = "https://ser7.crazyblog.in".explode("p=", $url)[1];
        $host = parse_url($url)["host"];
    }
    
    if (preg_match("#(luckydice.net|kalimbanote.com|cryptoflare.cc|myhealths.icu|clk.st|urlsfly.me|wefly.me|shortsfly.me|linksfly.me)#is", $host)) {
        $run = build($url);
        $time = time() + $seconds;
        $r = base_short($url); #print_r($r);
        $link = $r["url"];
        
        if (preg_match("#(clk.st)#is", $host)) {
            $referer = $link;
        } elseif (preg_match("#(luckydice.net|kalimbanote.com|cryptoflare.cc|myhealths.icu)#is", $host)) {
            $url = str_replace("luckydice.net", "urlhives.com", str_replace("kalimbanote.com", "linkhives.com", str_replace("cryptoflare.cc", "linkhives.com", str_replace("myhealths.icu", "urlhives.com", $url))));
            $run = str_replace(["clkclk./", "flyinc./"],"",build($url));
            $referer = "https://mcrypto.club/";
          
        } else {
            $referer = "wss://advertisingexcel.com";
        }
        $r1 = base_short($run["inc"], 0, 0, $referer, 1)["url"];
        
        if (preg_match("#(".$host.")#is", $r1)) {
            return "refresh";
        }
        
        if ($r1) {
            print h."success";
            r();
            $timer = $time - time();
            if ($timer >= 1) {
                L($timer);
            }
            return $r1;
        }
    } elseif (preg_match("#(link1s.com|link1s.net|insfly.pw|earnify.pro|links.earnify.pro|shrinke.us|adrev.link|nx.chainfo.xyz|linksly.co|owllink.net|go.birdurls.com|link.birdurls.com|go.owllink.net|link.owllink.net|mitly.us|go.illink.ne|link.illink.net|coinpayz.link|oko.sh|go.mtraffics.com|go.megaurl.in|go.megafly.in|clik.pw|usalink.io|link.usalink.io|go.hatelink.me|ez4short.com|link.shrinkme.link|go.shorti.io|shorti.io|sheralinks.com|linksfly.link|link.adlink.click|url.beycoin.xyz|cryptosh.pro|aii.sh|link.vielink.top|bestlink.pro|ccurl.net|1shorten.com|adbull.me|ser7.crazyblog.in|ex-foary.com|short.dash-free.com|shrinkme.info|shortplus.xyz|atglinks.com|link.short2url.in|link.revly.click|go.tinygo.co|m.tinygo.co|hbz.us|s3.addurl.biz|go.wez.info|m.wez.info|s2.addurl.biz|go.viewfr.com|m.viewfr.com|s1.addurl.biz|cashlinko.com|linkjust.com|dz4link.com|panylink.com|panyflay.me|panyshort.link|droplink.co|oscut.space|oscut.fun|kyshort.xyz|go.revcut.net|l2.revcut.net|121989.xyz|go.urlcut.pro|131989.xyz|go.faho.us|141989.xyz|go.eazyurl.xyz|link.eazyurl.xyz|clockads.in|go.shtfly.com|go.bitss.sbs|546512.xyz|dailytime.store|go.foxylinks.site|m.pkr.pw|linkjust.com|adbitfly.com|adshort.co|lollty.com|10short.com|short2money.com|shrinkme.org|shrinkme.us|teralinks.in|urlpay.in|linksly.pw|short.paylinks.cloud|ez4short.xyz|go.shortsme.in|exashorts.fun|go.paylinks.cloud|go.cutlink.xyz|151989.xyz|go.urlcash.site|link.24payu.top|icutlink.com|120898.xyz|freeltc.top|linksfly.top|bitcosite.com)#is", $host)) {
        if (preg_match("#(link1s.com)#is", $host)) {
              $referer = "https://google.com/";
        } elseif (preg_match("#(insfly.pw|oscut.space|oscut.fun|kyshort.xyz|clockads.in|linksly.pw|exashorts.fun)#is", $host)) {
            $referer = "https://clk.wiki/";
        } elseif (preg_match("#(shrinke.us|shrinkme.info|shrinkme.org|shrinkme.us)#is", $host)) {
            $referer = "https://themezon.net/";
        } elseif (preg_match("#(linksfly.top)#is", $host)) {
            $referer = "https://go.bitcosite.com/";
        } elseif (preg_match("#(linksly.co)#is", $host)) {
            $referer = "https://en.themezon.net/";
        } elseif (preg_match("#(link.usalink.io|usalink.io)#is", $host)) {
            $referer = "https://link.theconomy.me";
        } elseif (preg_match("#(ez4short.com)#is", $host)) {
            $referer = "https://ez4mods.com/";
        } elseif (preg_match("#(link.shrinkme.link)#is", $host)) {
            $referer = "https://blog.anywho-com.com/";
        } elseif (preg_match("#(sheralinks.com)#is", $host)) {
            $referer = "https://blogyindia.com/";
        } elseif (preg_match("#(linksfly.link)#is", $host)) {
            $referer = "https://insurance.uprwssp.org/";
        } elseif (preg_match("#(link.adlink.click)#is", $host)) {
            $referer = "https://www.diudemy.com/";
        } elseif (preg_match("#(url.beycoin.xyz)#is", $host)) {
            $referer = "https://adsluffa.online/";
        } elseif (preg_match("#(link.vielink.top)#is", $host)) {
            $referer = "https://phongcachsao.vn/";
        } elseif (preg_match("#(bestlink.pro)#is", $host)) {
            $referer = "https://ez4short.com/";
        } elseif (preg_match("#(adbull.me)#is", $host)) {
            $referer = "https://deportealdia.live/";
        } elseif (preg_match("#(shortplus.xyz)#is", $host)) {
            $referer = "https://1.newworldnew.com/";
        } elseif (preg_match("#(link.short2url.in)#is", $host)) {
            $referer = "https://blog.mphealth.online/";
        } elseif (preg_match("#(earnify.pro|links.earnify.pro)#is", $host)) {
            $referer = "https://go.linksfly.link/";
        } elseif (preg_match("#(go.shorti.io)#is", $host)) {
            $referer = "https://healthmedic.xyz/";
        } elseif (preg_match("#(link.revly.click)#is", $host)) {
            $referer = "https://coinsrev.com/";
        } elseif (preg_match("#(go.tinygo.co|m.tinygo.co|s3.addurl.biz|hbz.us)#is", $host)) {
            $referer = "https://wpcheap.net/";
        } elseif (preg_match("#(go.wez.info|m.wez.info|s2.addurl.biz)#is", $host)) {
            $referer = "https://aduzz.com/";
        } elseif (preg_match("#(go.viewfr.com|m.viewfr.com|s1.addurl.biz)#is", $host)) {
            $referer = "https://cryptfaucet.com/";
        } elseif (preg_match("#(cashlinko.com)#is", $host)) {
            $referer = "https://techyuth.xyz/";
        } elseif (preg_match("#(linkjust.com)#is", $host)) {
            $referer = "https://forexrw7.com/";
        } elseif (preg_match("#(dz4link.com)#is", $host)) {
            $referer = "https://dz4link.com/";
        } elseif (preg_match("#(panylink.com)#is", $host)) {
            $referer = "https://statepany.online/";
        } elseif (preg_match("#(panyflay.me)#is", $host)) {
            $referer = "https://btcpany.com/";
        } elseif (preg_match("#(panyshort.link)#is", $host)) {
            $referer = "https://panytourism.online/";
        } elseif (preg_match("#(droplink.co)#is", $host)) {
            $referer = "https://yoshare.net/";
        } elseif (preg_match("#(go.bitss.sbs|546512.xyz|go.shtfly.com|go.revcut.net.co|l2.revcut.net|121989.xyz|go.urlcut.pro|131989.xyz|go.faho.us|141989.xyz|go.eazyurl.xyz|link.eazyurl.xyz|go.cutlink.xyz|151989.xyz|120898.xyz|bitcosite.com)#is", $host)) {
            $referer = ["https", "wss"][rand(0, 1)]."://away.vk.com/";
        } elseif (preg_match("#(linkjust.com)#is", $host)) {
            $referer = "https://forexrw7.com/";
        } elseif (preg_match("#(adbitfly.com)#is", $host)) {
            $referer = "https://coinsward.com/";
        } elseif (preg_match("#(adshort.co)#is", $host)) {
            $referer = "https://techgeek.digital/";
        } elseif (preg_match("#(lollty.com)#is", $host)) {
            $referer = "https://mamahawa.com/";
        } elseif (preg_match("#(short2money.com)#is", $host)) {
            $referer = "https://lollty.pro/";
        } elseif (preg_match("#(10short.com)#is", $host)) {
            $referer = "https://skip.10short.vip/";
        } elseif (preg_match("#(sox.link)#is", $host)) {
            $referer = "https://coincroco.com/";
        } elseif (preg_match("#(teralinks.in)#is", $host)) {
            $referer = "https://daddy.webseriesreel.in/";
        } elseif (preg_match("#(urlpay.in)#is", $host)) {
            $referer = "https://daddy.webseriesreel.in/";
        } elseif (preg_match("#(ez4short.xyz)#is", $host)) {
            $referer = "https://healthynepal.in/";
        } elseif (preg_match("#(link.24payu.top)#is", $host)) {
            $referer = "https://go2.24payu.top/";
        } elseif (preg_match("#(icutlink.com)#is", $host)) {
            $referer = "https://zegtrends.com/";
        } elseif (preg_match("#(freeltc.top)#is", $host)) {
            $referer = "https://linx.cc/";
        } elseif (preg_match("#(nx.chainfo.xyz)#is", $host)) {
            $referer = "https://bitzite.com/";
        } else {
            $referer = 0;
        }
        if (preg_match("#(dz4link.com|Nx.chainfo.xyz|go.illink.net|go.birdurls.com|link.birdurls.com|go.owllink.net|link.owllink.net|go.illink.net|link.illink.net)#is", $host)) {
            $cloud = 1;
        } else {
            $cloud = 0;
        }
        if (preg_match("#(sox.link)#is", $host)) {
            $proxy = 1;
        } else {
            $proxy = 0;
        }
        $url = str_replace("120898.xyz", "c2g.at", str_replace("link.24payu.top", "go2.24payu.top", str_replace("go.urlcash.site", "urlcash.site", str_replace(["go.cutlink.xyz", "151989.xyz"], "cutlink.xyz", str_replace("go.paylinks.cloud", "paylinks.cloud", str_replace("go.shortsme.in", "shortsme.in", str_replace("short.paylinks.cloud", "paylinks.cloud", str_replace("clik.pw", "pwrpa.cc/go", str_replace("teralinks.in", "go.teralinks.in", str_replace("short2money.com", "forextrader.site/NewLink", str_replace("lollty.com", "forextrader.site/SkipLink", str_replace("adbitfly.com/short", "adbitfly.com", str_replace("m.pkr.pw", "jameeltips.us/blog", str_replace("go.foxylinks.site", "link.foxylinks.site", str_replace(["go.bitss.sbs", "546512.xyz"], "bitss.sbs", str_replace("go.shtfly.com", "shtfly.com", str_replace(["go.eazyurl.xyz", "link.eazyurl.xyz"],"eazyurl.xyz", str_replace(["go.faho.us", "141989.xyz"], "faho.us", str_replace(["go.urlcut.pro", "131989.xyz"], "urlcut.pro", str_replace(["go.revcut.net", "l2.revcut.net", "go.revcut.net", "121989.xyz"], "revcut.net", str_replace("kyshort.xyz/go", "kyshort.xyz", str_replace(["go.viewfr.com", "m.viewfr.com", "s1.addurl.biz"], "thanks.viewfr.com", str_replace(["go.wez.info", "m.wez.info", "s2.addurl.biz"] ,"thanks.wez.info", str_replace(["go.tinygo.co", "m.tinygo.co", "s3.addurl.biz", "hbz.us"],"thanks.tinygo.co", str_replace("links.earnify.pro", "earnify.pro", str_replace("link.revly.click", "en.revly.click", str_replace("link.short2url.in", "techyuth.xyz/blog", str_replace("short.dash-free.com", "dash-free.com", str_replace("link.vielink.top", "short.vielink.top", str_replace("usalink.io", "link.theconomy.me", str_replace("url.beycoin.xyz/short", "url.beycoin.xyz", str_replace("link.adlink.click", "blog.adlink.click", str_replace("linksfly.link", "go.linksfly.link", str_replace(["go.shorti.io", "shorti.io"],"blog.financeandinsurance.xyz", str_replace("link.shrinkme.link", "blog.shrinkme.link", str_replace("go.hatelink.me", "q.hatelink.me", str_replace("linksly.co", "go.linksly.co", str_replace("link.usalink.io", "link.theconomy.me", str_replace("go.megafly.in", "get.megafly.in", str_replace("go.megaurl.in", "get.megaurl.in", str_replace("go.mtraffics.com", "get.mtraffics.com", str_replace(["go.illink.net", "link.illink.net"], "illink.net", str_replace(["go.owllink.net", "link.owllink.net"] ,"owllink.net", str_replace(["go.birdurls.com", "link.birdurls.com"], "birdurls.com", str_replace(["bitcosite.com/1", "nx.chainfo.xyz"], "go.bitcosite.com", str_replace(["shrinkme.org", "shrinkme.info", "shrinkme.us"],"en.shrinke.me", str_replace("shrinke.us", "en.shrinke.me", $url)))))))))))))))))))))))))))))))))))))))))))))));
        $run = build($url);#die(print_r($referer));
        $r = base_short($run["links"], 0, 0, $referer, $cloud);
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];;
        #die(print_r($r));
        if (preg_match("#(verify/[?]/)#is", $r["url"])) {
            $verify = str_replace("http:", "https:", $r["url"]);
            $r = base_short($verify, 0, 0, $verify);
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "2") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "3") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "4") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
           $cookie[] = $r["cookie"];
           $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "5") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "6") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "7") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "8") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[2][3])[0] == "9") {
            $data = data_post($t, "five");
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if ($t[1][2] == "f_n") {
            $method = "recaptchav2";
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "four", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if ($t[1][2] == "ref") {
            $method = "recaptchav2";
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "five", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[1][2])[4] == "f_n") {
            $method = "recaptchav2";
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "four2", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }#die(print_r($r));
        
        if (explode('"', $t[2][2])[0] == "captcha") {
            $method = "recaptchav2";
            
            if (!$r[$method]) {
                $method = "invisible_recaptchav2";
            }
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "five", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if (explode('"', $t[1][3])[0] == "ad_form_data") {
            $t = array(
                array_merge(array_diff($t[0],[$t[1][0], $t[2][0]])),
                array_merge(array_diff($t[1], [$t[1][0], $t[2][0]])),
                array_merge(array_diff($t[2], [$t[1][0], $t[2][0]]))
            );
        }
        
        if (explode('"', $t[1][2])[0] == "ad_form_data") {
            $data = data_post($t, "four");
            L($coundown);
           
            if (preg_match("#(lollty.com)#is", $host)) {
                $run["go"][0] = str_replace("forextrader.site", "forextrader.site/SkipLink", $run["go"][0]);
            } elseif (preg_match("#(short2money.com)#is", $host)) {
                $run["go"][0] = str_replace("forextrader.site", "forextrader.site/NewLink", $run["go"][0]);
            }
            $r1 = base_short(str_replace("pwrpa.cc", "pwrpa.cc/go", str_replace("jameeltips.us", "jameeltips.us/blog", str_replace("techyuth.xyz", "techyuth.xyz/blog", $run["go"][0]))), 1, $data, 0, $cloud, join('', $cookie))["json"];
           
            if (preg_match("#(http)#is", $r1->url)) {
                print h."success";
                r();
                return $r1->url;
            } else {
                return "refresh";
            }
        }
        if (explode('"', $t[1][7])[0] == "country") {
            $data = data_post($t, "nine");
            L($coundown);
           
            
            $r1 = base_short( $run["go"][0], 1, $data, 0, $cloud, join('', $cookie))["json"];
           
            if (preg_match("#(http)#is", $r1->url)) {
                print h."success";
                r();
                return $r1->url;
            } else {
                return "refresh";
            }
        }
    } elseif (preg_match("#(tii.la|tei.ai)#is", $host)) {
        $url = str_replace("tei.ai", "tii.la", $url);
        $run = build($url);
        $r = base_short($run["links"]);
        $t = $r["token_csrf"];
        $cookie[] = $r["cookie"];
        $data = data_post($t, "three");
        $r1 = base_short($run["links"],"", $data, 0, join('', $cookie));
      
        if ($r1["timer"] or $r1["timer"] == 0) {
            L($coundown);
            $t = $r1["token_csrf"];
            $cookie[] = $r1["cookie"];
            $data = data_post($t, "two");
            $r2 = base_short($run["go"][0], 1, $data, 0, 0, join('', $cookie))["json"];
          
            if (preg_match("#(http)#is", $r2->url)) {
                print h.$r2->status;
                r();
                unset($cookie);
                return $r2->url;
            }
        }
    } elseif (preg_match("#(try2link.com)#is", $host)) {
        $run = build($url);
        $r = base_short($url);
        $cookie[] = $r["cookie"];
        $referer[] = "https://trip.businessnews-nigeria.com/";
        $referer[] = "https://forexit.online/";
        $referer[] = "https://mobi2c.com/";
        $referer[] = "https://te-it.com/";
        $referer[] = "https://world2our.com/";
        $referer[] = "https://hightrip.net/";
        $referer[] = "https://healthy4pepole.com/";
        $referer[] = "https://to-travel.net/";
        
        for ($x = 0; $x < count($referer); $x++) {
            $r = base_short($url, 0, 0, $referer[$x], 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            
            if (explode('"', $t[1][2])[0] == "ad_form_data") {
                L($coundown);
                $data = data_post($t, "four");
                $r1 = base_short($run["go"][0], 1, $data, $url, 0, join('', $cookie))["json"];
                
                if (preg_match("#(http)#is", $r1->url)) {
                    h.$r1->status;
                    r();
                    return $r1->url;
                }
            }
            sleep(2);
        }
    } elseif (preg_match("#(linkdam.me|terafly.me|forexly.cc|insurancly.cc|goldly.cc|playstore.pw|botfly.me)#is", $host)) {
      $r = base_short($url);
      if (preg_match("#(playstore.pw)#is", $host)) {
        $r["url"] = $url;
      } elseif (preg_match("#(botfly.me)#is", $host)) {
        $url = "https://adsy.pw/how-to-locate-good-risk-reward-opportunities-in-forex-trading-botfly".parse_url($url)["path"];
      } elseif (explode("?", $r["url"])[1]) {
        $url = explode("?", $r["url"])[1];
      } else {
        goto ulang;
      }
      $r1 = base_short($url, 0, 0, $r["url"]);
      $cookie[] = $r1["cookie"];
      $t = $r1["token_csrf"];
      if ($t[2][2] == "continue") {
        $data = data_post($t, "five");
        $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
        $cookie[] = $r1["cookie"];
        $t = $r1["token_csrf"];
        if ($t[2][2] == "continue") {
          $data = data_post($t, "five");
          $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
          $cookie[] = $r1["cookie"];
          $t = $r1["token_csrf"];
          if ($t[2][2] == "continue") {
            $data = data_post($t, "five");
            $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
            $cookie[] = $r1["cookie"];
            $t = $r1["token_csrf"];
            if ($t[2][2] == "continue") {
              $data = data_post($t, "five");
              $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
              $cookie[] = $r1["cookie"];
              $t = $r1["token_csrf"];
              if ($t[2][2] == "continue") {
                $data = data_post($t, "five");
                $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
                $cookie[] = $r1["cookie"];
                $t = $r1["token_csrf"];
                if ($t[2][2] == "continue") {
                  $data = data_post($t, "five");
                  $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
                  $cookie[] = $r1["cookie"];
                  $t = $r1["token_csrf"];
                  if ($t[2][2] == "continue") {
                    $data = data_post($t, "five");
                    $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
                    $cookie[] = $r1["cookie"];
                    $t = $r1["token_csrf"];
                  }
                }
              }
            }
          }
        }
        if (!$t[0][0]) {
          return "refresh";
          }
          if ($t[2][2] == "captcha") {
            $method = "recaptchav2";
            $cap = request_captcha($method, $r1[$method], $url);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "five", $rsp);
            $r1 = base_short($url,0, $data, $url, 0, join('', $cookie));
            $cookie[] = $r1["cookie"];
            $t = $r1["token_csrf"];
          }
          if ($t[1][2] == "ad_form_data") {
            L($coundown);
            $data = data_post($t, "four");
            $r2 = base_short(build($url)["go"][2], 1, $data, $url, 0, join('', $cookie))["json"];
          }
          if (preg_match("#(http)#is", $r2->url)) {
           print h.$r2->status;
           r();
           unset($cookie);
           return $r2->url;
          }
      }
    } elseif (preg_match("#(destyy.com|festyy.com|gestyy.com|hestyy.com|ceesty.com|corneey.com)#is", $host)) {
        while(true) {
            $r = base_short($url,0, $url);
            $cookie[] = $r["cookie"];
            $link = $r["url"];
            
            if (preg_match("#(freeze)#is", $link)) {
                $r = base_short($link, 0, 0, 0, 0, join('', $cookie));
                $cookie[] = $r["cookie"];
                L($coundown);
                $r = base_short($url, 0, 0, $link, 0, join('', $cookie));
                $cookie[] = $r["cookie"]; 
            }
          
            if (preg_match("#(sessio)#is", $link)) {
                $r = base_short($link, 0, 0, 0, 0, join('', $cookie));
                $cookie[] = $r["cookie"];
            }
            $sessionId = $r["sessionId"];
            
            if (!$sessionId) {
                unset($cookie);
                continue;
            }
            L($coundown);
            $r1 = base_short("https://clkmein.com/shortest-url/end-adsession?adSessionId=".$sessionId."&adbd=0&callback=reqwest_".time(), 0, 0, $url, 0, join('', $cookie))["res"];
            
            if (ex('":"', '"', 2, $r1) == "ok") {
                print h."succses";
                r();
                return str_replace("\/", "/", ex('":"', '"', 1, $r1));
            }
        }
    } elseif (preg_match("#(exe.io)#is", $host)) {
        $r = base_short($url);
        $cookie[] = $r["cookie"];
        $url = $r["url"];
       
        if (!parse_url($url)["scheme"]) {
            return "refresh";
        }
        $r = base_short($url, 0, 0, $url, 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        $data = data_post($t, "five2");
        $r = base_short($url,0, $data, $url, 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        $method = "invisible_recaptchav2";
        $cap = request_captcha($method, $r[$method], $url);
        $rsp = array("g-recaptcha-response" => $cap);
        $data = data_post($t, "five", $rsp);
        $r = base_short($url,0, $data, $url, 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];#die(print_r($r));
        
        if (explode('"', $t[1][2])[0] == "ad_form_data") {
            L($coundown);
            $data = data_post($t, "four");
            $r1 = base_short(build($url)["go"][0], 1, $data, 0, 0, join('', $cookie))["json"];
            
            if (preg_match("#(http)#is", $r1->url)) {
                h.$r1->status;
                r();
                return $r1->url;
            }
        }
    } elseif (preg_match("#(cuty.io)#is", $host)) {
        $r = base_short($url); #die(print_r($r));
        $cookie[] = $r["cookie"];
        $url = $r["url"];
        
        if ($r["url"]) {
            $r = base_short($url, 0, 0, 0, 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            $data = data_post($t, "null");
            $r = base_short($url,0, $data, 0, 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            $method = "recaptchav2";
            $cap = request_captcha($method, $r[$method], $url);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "null", $rsp);
            $r = base_short($url,0, $data, 0, 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            
            if (explode('"', $t[1][1])[0] == "data") {
                L($coundown);
                $data = data_post($t, "two");
                $r1 = base_short(build($url)["go"][1], 1, $data, 0, 0, join('', $cookie));
                
                if ($r1["url"]) {
                    print h."success";
                    r();
                    return $r1["url"];
                }
            }
        }
    } elseif (preg_match("#(oii.io|fc-lc.xyz)#is", $host)) {
        $run = build($url);
        $r = base_short($run["links"]);
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        
        if ($t[1][2] == "f_n") {
            $method = "invisible_recaptchav2";
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "four", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], $cloud, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
        }
        
        if ($t[1][2] == "ref") {
            $method = "invisible_recaptchav2";
            $cap = request_captcha($method, $r[$method], $run["links"]);
            $rsp = array("g-recaptcha-response" => $cap);
            $data = data_post($t, "five", $rsp);
            $r = base_short($run["links"], 0, $data, $run["links"], 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
      }
      $link = $r["url1"][0];
      
      if (preg_match("#(http)#is", $link)) {
        
          if (explode('"', $t[1][4])[0] == "user_faucet") {
              $data = data_post($t, "four");
              $r = base_short($link, 1, $data, 0, 0, join('', $cookie));
              $cookie[] = $r["cookie"];
              $t = $r["token_csrf"];
            
          } elseif ($t[1][1] == "random_token") {
              $data = data_post($t, "four");
              $r = base_short($link, 1, $data, 0, 0, join('', $cookie));
              $cookie[] = $r["cookie"];
              $t = $r["token_csrf"];
              
              if ($t[1][8]) {
                  $data = data_post($t, "eight");
                  $r = base_short($link, 1, $data, 0, 0, join('', $cookie));
                  $cookie[] = $r["cookie"];
                  $t = $r["token_csrf"];
              }
          } else {
              $data = data_post($t, "three");
              $r = base_short($link, 1, $data, 0, 0, join('', $cookie));
              $cookie[] = $r["cookie"];
              $t = $r["token_csrf"];
          }
          
          if ($t[1][1] == "ad_form_data") {
              L($coundown+10);
              $data = data_post($t, "six");
              $r1 = base_short(str_replace("fc-lc.xyz", "fc.lc", str_replace("oii.io/links/go", "oii.io/links/go1",build($url)["go"][0])), 1, $data, $link, 0, join('', $cookie))["json"];
              
              if (preg_match("#(http)#is", $r1->url)) {
                  h."success";
                  r();
                  return $r1->url;
              }
          }
      }
    } elseif (preg_match("#(doshrink.com)#is", $host)) {
        $run = build($url);
        $r = base_short($run["links"]);
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        $url = $r["url1"][0];
        
        if (!parse_url($url)["scheme"]) {
            return "refresh";
        }
        $data = data_post($t, "null");
        $r = base_short($url, 1, $data, $run["links"], 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        $sitekey = $r["data"]["rcap"];
        $cookie[] = $r["cookie"];
        $r = base_short("https://kiktu.com/wp-admin/admin-ajax.php", 1, "action=generate2", "https://kiktu.com/", 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        
        if (!parse_url($r["url7"])["scheme"]) {
            return "refresh";
        }
        $r = base_short($r["url7"]);
        $cap = request_captcha("recaptchav2", $sitekey,"https://kiktu.com/");
        $data = http_build_query([
            "wgurl" => $t[2][0],
            "wgr" => $sitekey,
            "wgp" => 1,
            "wgcsrf" => $r["res"],
            "wgcaptcharesponse" => $cap
        ]);
        $r = base_short("https://kiktu.com/recaptcha", 0, $data, "https://kiktu.com/", 1, join('', $cookie));
        $cookie[] = $r["cookie"];
        $newcsrf = $r["data"]["newcsrf"];
        $slug = $r["data"]["slug"];
        $r = base_short("https://api.bigdatacloud.net/data/client-ip");
        $ip_n = "websgrid=".$r["json"]->ipNumeric.";";
        $data = json_encode(["ver" => $r["json"]->ipNumeric]);
        $verify = base_short("https://kiktu.com/verify", 1, $data, 0, 0, join('', $cookie));
        $cookie[] = $verify["cookie"];
        $r = base_short($run["links"]."?".http_build_query(["data" => $slug,"secret" => $verify["json"]->response,"wgcsrf" => $newcsrf]), 0 ,0 ,"https://kiktu.com/", 0, $ip_n.join('', $cookie)); 
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        
        if (explode('"', $t[1][2])[0] == "ad_form_data") {
            L($coundown);
            $data = data_post($t, "four");
            $r1 = base_short($run["go"][0], 1, $data, 0, 0, join('', $cookie))["json"];
            if (preg_match("#(http)#is", $r1->url)) {
                h.$r1->status;
                r();
                return $r1->url;
            }
        }
    } elseif (preg_match("#(clk.asia)#is", $host)) {
        $url = str_replace("clk.asia", "clk.wiki", $url);
        $r = base_short($url);
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        $url1 = $url."?".http_build_query([$t[1][0] => $t[2][0]]);
        $r = base_short($url1, 0, 0, 0, 0, join('', $cookie));
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
       
        if ($t[1][0] == "token") {
            $method = "hcaptcha";
            $cap = request_captcha($method, $r[$method], $url1);
            $rsp = array("h-recaptcha-response" => $cap);
            $data = data_post($t, "six", $rsp);
            $r = base_short($url1,0, $data, 0, 0, join('', $cookie));
        }
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
      
        if ($t[1][1] == "ad_form_data") {
            L($coundown);
            $data = data_post($t, "two");
            $r1 = base_short(build($url)["go"][0], 1, $data, 0, 0, join('', $cookie))["json"];
            
            if (preg_match("#(http)#is", $r1->url)) {
                h.$r1->status;
                r();
                return $r1->url;
            }
        }
    } elseif (preg_match("#(ctr.sh|_easycut.io)#is", $host)) {
        while(true) {
          
            if (preg_match("#(ctr.sh)#is", $host)) {
                $re = "https://sinonimos.de/?url8j=";
            } elseif (preg_match("#(easycut.io)#is", $host)) {
                $re = "https://quesignifi.ca/?url8j=";
            }
            $r = base_short($re.$url,0,0,1);//die(print_r($r));
            $cookie[] = $r["cookie"];
            $url1 = $r["url1"][0];
            
            if (!parse_url($url1)["scheme"]) {
                unset($cookie);
                continue;
            }
            $r = base_short($url1, 0, 0, $url1, 1, join('', $cookie));
            $cookie[] = $r["cookie"];
            
            if (preg_match("#(ctr.sh)#is", $host)) {
                $method = "recaptchav3";
                $cap = recaptchav3($r[$method], $url1);
            } elseif (preg_match("#(easycut.io)#is", $host)) {
                $method = "recaptchav2";
                $cap = request_captcha($method, $r[$method], $url1);
            }
            $data = http_build_query([
                "g-recaptcha-response" => $cap,
                "validator" => "true"
            ]);
            $r = base_short($url1, 1, $data, $url1, 1, join('', $cookie));#die(print_r($r));
            $cookie[] = $r["cookie"];
            $url2 = $r["url"];
            
            if (!parse_url($url2)["scheme"]) {
                $url2 = $r["url9"];
            }
            if (!parse_url($url2)["scheme"]) {
                unset($cookie);
                continue;
            }
            $data = http_build_query([
                "no-recaptcha-noresponse" => "true",
                "validator" => "true"
            ]);
            $r = base_short($url2, 1, $data, $url2, 1, join('', $cookie));
            $cookie[] = $r["cookie"];
            $url3 = $r["url"];
            
            if (!parse_url($url3)["scheme"]) {
                unset($cookie);
                continue;
            }
            $r = base_short($url3,0, $data, $url3, 1, join('', $cookie));
            $cookie[] = $r["cookie"];
            $final = $r["url6"];
            
            if (!$final) {
                $url4 = $r["url"];
                $r = base_short($url4,0, $data, $url4, 1, join('', $cookie));
                $cookie[] = $r["cookie"];
                $final = $r["url6"];
            }
            $r = base_short(str_replace("&tk", "?token", $final), 0, 0, 0, 1, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            
            if (explode('"', $t[1][2])[0] == "ad_form_data") {
                $data = data_post($t, "four");
                L($coundown);
                $r1 = base_short(build($final)["go"][0], 1, $data, 0, 1, join('', $cookie))["json"];
                
                if (preg_match("#(http)#is", $r1->url)) {
                    print h.$r1->status;
                    r();
                    return $r1->url;
                }
            }
        }
    } elseif (preg_match("#(ouo.io)#is", $host)) {
        $url = str_replace("ouo.io", "ouo.press", $url);
        $run = build($url);
        $r = base_short($run["links"]);
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf2"];
        $method = "recaptchav3";
        if ($r[$method]) {
            $cap = recaptchav3($r[$method], $run["links"]);
            $data = http_build_query([
                explode('"', $t[2][0])[0] => $t[3][0],
                explode('"', $t[2][1])[0] => $cap,
                "v-token" => "bx"
            ]);
            L($coundown);
            $r1 = base_short($run["go"][4],0, $data, $run["links"], 0, join('', $cookie));
            if ($r1["url"]) {
                print h."success";
                r();
                return $r1["url"];
            }
        }
    } elseif (preg_match("#(_earnow.online)#is", $host)) {
      while(true) {
      $run = build($url);
      $r = base_short($url);
      unset($cookie);
      $link = $r["url"];
      if (!parse_url($link)["scheme"]) {
        unset($cookie);
        continue;
      }
      $cookie[] = $r["cookie"];
      $r = base_short($link, 0, 0, $url, 0, join('', $cookie));

      $link = $r["url"];
      if (!parse_url($link)["scheme"]) {
        unset($cookie);
        continue;
      }
      $cookie[] = $r["cookie"];
      
      $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
      $link = str_replace("http:", "https:", $r["url5"]);
      if (!parse_url($link)["scheme"]) {
        unset($cookie);
        continue;
      }
      $cookie[] = $r["cookie"];
     
      $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
      $link = $r["url"];
     if (!parse_url($link)["scheme"]) {
        unset($cookie);
        continue;
      }
     
      $cookie[] = $r["cookie"];

      $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
      
      $cookie[] = $r["cookie"];
      $t = $r["token_csrf"];
      $method = "recaptchav2";
      $cap = request_captcha($method, $r[$method], $link);
      if (!$cap) {
        unset($cookie);
        continue;
      }
      $rsp = array("g-recaptcha-response" => $cap);
      $data = data_post($t, "null", $rsp);
      $r = base_short($link, 0, $data, $link, 0, join('', $cookie));
      
      $cookie[] = $r["cookie"];
      $t = $r["token_csrf"];
      L(25);
      $data = data_post($t, "null");
      $r = base_short($link, 0, $data, $link, 0, join('', $cookie));
      
      $cookie[] = $r["cookie"];
      $t = $r["token_csrf"];
      L(25);
      $url = $link;
      $data = data_post($t, "null");
      $r = base_short($link, 1, $data, 0, 0, join('', $cookie));
      $link = $r["url"];
     #die(print_r($r));
      if (preg_match("#key#is", $link)) {
        $cookie[] = $r["cookie"];
        $t = $r["token_csrf"];
        break;
      }
      
      
      
      }
      
      
      
      
      while(true) {
      $r = base_short($link, 0, 0, $url, 0, join('', $cookie));
      $link = $r["url"];
      if (!parse_url($link)["scheme"]) {
        continue;
      }
      $cookie1[] = $r["cookie"];
      $r = base_short($link, 0, 0, $link, 0, join('', $cookie1));
      
      $link = str_replace("http:", "https:", $r["url5"]);
      if (!parse_url($link)["scheme"]) {
        unset($cookie1);
        continue;
      }
      $cookie1[] = $r["cookie"];
     
      $r = base_short($link, 0, 0, $link, 0, join('', $cookie1));
      
      $link = $r["url"];
     if (!parse_url($link)["scheme"]) {
        unset($cookie1);
        continue;
      }
     
      $cookie1[] = $r["cookie"];
      $data = data_post($t, "null");
      $r = base_short($link, 0, $link, $link, 0, join('', $cookie1));
      $cookie1[] = $r["cookie"];


$method = "recaptchav2";
      $cap = request_captcha($method, $r[$method], $link);
      if (!$cap) {
        unset($cookie);
        continue;
      }
      $rsp = array("g-recaptcha-response" => $cap);
      $data = data_post($t, "null", $rsp);
      $r = base_short($link, 0, $data, $link, 0, join('', $cookie));

      
      die(print_r($r));
      }
      
      
      $cookie[] = $r["cookie"];
      $t = $r["token_csrf2"];
      $method = "recaptchav3";
      if ($r[$method]) {
        $cap = recaptchav3($r[$method], $run["links"]);
        $data = http_build_query([
          explode('"', $t[2][0])[0] => $t[3][0],
          explode('"', $t[2][1])[0] => $cap,
          "v-token" => "bx"
          ]);
          L($coundown);
          $r1 = base_short($run["go"][4],0, $data, $run["links"], 0, join('', $cookie));
          if ($r1["url"]) {
            print h."success";
            r();
            unset($cookie);
            return $r1["url"];
          }
      }
    } elseif (preg_match("#(rsshort.com)#is", $host)) {
        $api = new_save("scraperapi")["scraperapi"];
        /*if (file_get_contents("key_scrape")) {
            $scrape = scrape_valid();
        }
        for ($c = 0; $c < 3; $c++) {
            $r = $r = base_short($url, 0, 0, 0, 0, 0, 0, $scrape);
            $time = time() + $seconds;
            
            $link = $r["url"];
            
            if (!$link) {
                continue;
            }
            $cookie[] = $r["cookie"];
            $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
            
            $link = $r["url"];
            
            if (!$link) {
                continue;
            }
            $cookie[] = $r["cookie"];
            $r = base_short($link, 0, 0, $link, 1, join('', $cookie));
            
            die(print_r($r));
            
            $link = $r["url2"][0];
            
            if (!$link) {
                continue;
            }
            
            if ($link) {
                break;
            }
        }*/
        $time = time() + $seconds;
        for ($c = 0; $c < 3; $c++) {
            $r = base_short("https://api.scrapingant.com/v2/general?url=".$url."&x-api-key=".$api);
            
            //print_r($r);
            if ($r["status"] == 401 || md5($r["res"]) ==  "2334dc46017fbf6c6e1822a69efae72a") {
                new_save("scraperapi", true);
                print m."scraperapi telah mencapai batas limit".n;
                goto ulang;
            }
            $link = $r["url2"][0];
            
            if (!$link) {
                continue;
            }
            
            if ($link) {
                break;
            }
        }
        $link = $r["url2"][0];
        if (!$link) {
            return "refresh";
        }
        $cookie[] = $r["cookie"];
        while(true) {
            unset($coordinate);
            $r = base_short($link, 0, 0, $link, 1, join('', $cookie));
            //print_r($r);
            
            if ($r["url"]) {
              
                if ($r["status"] == 307) {
                    return "refresh";
                }
                print h."success";
                r();
                $timer = $time - time();
                
                if ($timer >= 1) {
                    L($timer);
                }
                return $r["url"];
            }
            $link1 = $r["url1"][0];
            if (!$link1) {
                return "refresh";
            }
            $cookie[] = $r["cookie"];
            $cookie[] = array_reverse($cookie);
            $node = executeNode($r["res"], 1);
            $node = executeNode($node["res"], 1);
            #print_r($node);
            $rs = "https://".parse_url($link1)["host"]."/";
            if ($node["token_csrf"][1][1] == "_iconcaptcha-token") {
                $data_post = data_post($node["token_csrf"], "two");
                parse_str($data_post, $ic);
                $eol = "\n";
                $boundary = "------WebKitFormBoundary";
                $content = 'Content-Disposition: form-data; name="payload"';
                $code = az_num(16);
                $data = '';
                $data .= $boundary.$code.$eol;
                $data .= $content.$eol.$eol;
                $data .= base64_encode(json_encode(["i" => 1, "a" => 1, "t" => "dark", "tk" => $ic["_iconcaptcha-token"], "ts" => round(time() * 1000)])).$eol;
                $data .= $boundary.$code.'--';re:
                $r = base_short($rs."iconcaptchar/captcharequest", 1, $data, $rs, 0, join('', $cookie), $code);
                
                if ($r["status"] >= 400) {
                    continue;
                }
                $r = base_short($rs."iconcaptchar/captcharequest?payload=".base64_encode(json_encode(["i" => 1, "tk" => $ic["_iconcaptcha-token"], "ts" => round(time() * 1000)])), 0, 0, $rs, 0, join('', $cookie));
                
                if ($r["status"] >= 400 or !$r["res"]) {
                    continue;
                }
                
                if (strlen($r["res"]) >= 100) {
                    $coordinate = coordinate($r["res"]);
                    
                    for ($i = 0; $i < 5; $i++) {
                        $coordinate = coordinate($r["res"], $i);
                        
                        if ($coordinate["x"]) {
                            break;
                        }
                    }
                    
                    if (!$coordinate["x"]) {
                        continue;
                    }
                }
                $eol = "\n";
                $boundary = "------WebKitFormBoundary";
                $content = 'Content-Disposition: form-data; name="payload"';
                $code = az_num(16);
                $data = '';
                $data .= $boundary.$code.$eol;
                $data .= $content.$eol.$eol;
                $data .= base64_encode(json_encode(["i" => 1, "x" => $coordinate["x"], "y" => $coordinate["y"], "w" => 320, "a" => 2, "tk" => $ic["_iconcaptcha-token"], "ts" => round(time() * 1000)])).$eol;
                $data .= $boundary.$code.'--';
                $r = base_short($rs."iconcaptchar/captcharequest", 1, $data, $rs, 0, join('', $cookie), $code);
                
                if ($r["status"] >= 400) {
                    continue;
                }
                $rsp = array("ic-hf-se" => $coordinate["x"].", ".$coordinate["y"].",320", "ic-hf-id" => 1,"ic-hf-hp" => "");
                $data_post = data_post($node["token_csrf"], "two", $rsp);
            } else {
                $data_post = data_post($node["token_csrf"], "one");
            }
            $r = base_short($link,0, $data_post, $rs, 0, join('', $cookie));
            $url = $r["url"];
            if (!$url) {
                continue;
            }
            $cookie[] = $r["cookie"];
            continue;
        }
    } elseif (preg_match("#(clks.pro)#is", $host)) {
        $run = build($url);
        if (file_get_contents("key_scrape")) {
            $scrape = scrape_valid();
        }
        $time = time() + $seconds;
        $r = base_short($run["inc"], 0, 0, "https://mdn.lol/", 0, 0, 0, $scrape);
        
        if (preg_match("#(-cut|final)#is", $r["url"])) {
            print "limit";
            return "refresh";
        }
        if ($r["url"]) {
            print h."success";
            r();
            $timer = $time - time();
            if ($timer >= 1) {
                L($timer);
            }
            parse_str(explode("?", $r["url"])[1], $get);
            
            if ($get["get"]) {
                return base64_decode($get["get"]);
              
            } else {
                return $r["url"];
            }
        }
    } elseif (preg_match("#(urlcorner.com)#is", $host)) {
      $r = base_short($url);
      $referer = "https://leaveadvice.com/";
      $node = executeNode($r["res"]);
      $link = $node["url"];
      if (!parse_url($link)["scheme"]) {
        return "refresh";
      }
      
      
      $cookie[] = $r["cookie"];
      parse_str(str_replace("?", "&", $link), $tk);
      $data = http_build_query(["safe_link" => $tk["tk"], "wpcls" => parse_url($link)["host"]]);
      $r = base_short($referer."conf1-srt", 1, $data, $referer, 0, join('', $cookie));
      
      
      $link = $r["res"];
      if (!parse_url($link)["scheme"]) {
        return "refresh";
      }
      $cookie[] = $r["cookie"];
      parse_str(str_replace("?", "&", $link), $tk);
      $data = http_build_query(["safe_link" => $tk["tk"], "wpcls" => parse_url($link)["host"]]);
      $r = base_short($referer."conf2-srt", 1, $data, $referer, 0, join('', $cookie));
      
      
      $link = $r["res"];
      if (!parse_url($link)["scheme"]) {
        return "refresh";
      }
      $cookie[] = $r["cookie"];
      $r = base_short($link, 0, 0, $referer, 0, join('', $cookie));
      $cookie[] = $r["cookie"];
      parse_str(str_replace("?", "&", $link), $tk);
      
      $method = "hcaptcha";
          $cap = request_captcha($method,"c328fbe1-085c-4246-a274-6a11b4ae4cd7", $referer);
      $data = http_build_query(["safe_link" => $tk["tk"], 
      $method => $cap, "abv" => false, "adfl" => false
      
      ]);
      $r = base_short($referer."conf3-srt", 1, $data, $referer, 0, join('', $cookie));
      L(60);
      #die(print_r($r));
      $link = $r["res"];
      if (!parse_url($link)["scheme"]) {
        return "refresh";
      }
      $cookie[] = $r["cookie"];
      //$cookie[] = array_reverse($cookie);
      $r = base_short($link, 0, 0, $referer, 0, join('', $cookie));
      
      #$node = executeNode($r["res"]);
      print_r($r);
      

 
      #die(print_r($node));
    } elseif (preg_match("#(mgnet.xyz|1bit.space)#is", $host)) {
        $time = time() + $seconds;
        $r = base_short($url);
        $cookie[] = $r["cookie"];
        $link = explode("https:", parse_url($r["url"])["path"])[1];
        
        if ($link) {
            $r1 = base_short($r["url"], 0, 0, $url, 0, join('', $cookie));
           
            if ($r1["url"]) {
                base_short($r1["url"], 0, 0, $url, 0, join('', $cookie));
                print h."success";
                r();
                $timer = $time - time();
                
                if ($timer >= 1) {
                    L($timer);
                }
                return "https:".$link;
            }
        } else {
            return "refresh";
      }
    } elseif (preg_match("#(adrinolinks.com)#is", $host)) {
      $path = str_replace("/", "",parse_url($url)["path"]);
      $r = base_short($url,0, $data, $url);
      $url = $r["url"];
      $parse = parse_url($url);
      if ($parse["query"] !== "link=".$path) {
        return "refresh";
      }
      
      $cookie[] = $r["cookie"];
      $host = "https://".$parse["host"];
      $r = base_short($url, 0, 0, $host, 0, join('', $cookie));
      $cookie[] = $r["cookie"];
      $data = "newwpsafelink4=".$path;
      $r = base_short($host,0, $data, $host, 0, join('', $cookie));
      $hash = $r["url1"];
      for ($i = 0; $i < count($hash); $i++) {
        if (preg_match("#(open.php)#is", $hash[$i])) {
          $code = $hash[$i];
          break;
        }
      }
      if (!$code) {
        return "refresh";
      }
      $cookie[] = $r["cookie"];
      $r = base_short($host.$code, 0, 0, $host, 0, join('', $cookie));
      if ($r["url"]) {
        L(20);
        print h."success";
        r();
        return $r["url"];
      }
    } elseif (preg_match("#(link4m.com)#is", $host)) {
        $parse = parse_url($url);
        $r = base_short(str_replace("link4m.com", "link4m.com/go", $url));
        $cookie[] = $r["cookie"];
        $method = "recaptchav2";
        $cap = request_captcha($method, $r[$method], $url);
        $data = http_build_query(
            array(
                "g-recaptcha-response" => $cap,
                "alias" => str_replace("/", "", $parse["path"])
            )
        );
        $r2 = base_short(str_replace($parse["path"], "/links/check-captcha", $url), 0, $data, 0, 0, join('', $cookie))["json"];
        
        if ($r2->success == true) {
            L(15);
            return $r2->url;
        }
    } elseif (preg_match("#(adbx.pro|adbits.xyz|adbits.pro|v2p.icu)#is", $host)) {
        $r = base_short($url);
        $link = $r["url"];
        
        if (!preg_match("#(http)#is", $link)) {
            return "refresh";
        }
        $cookie[] = $r["cookie"];
        $r = base_short($link, 0, 0, 0, 0, join('', $cookie));
        $link = $r["url"];
        
        if (!preg_match("#(http)#is", $link)) {
            return "refresh";
        }
        $cookie[] = $r["cookie"];
        $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
        $link = $r["url"];
        
        if (!preg_match("#(http)#is", $link)) {
            return "refresh";
        }
        $cookie[] = $r["cookie"];
        while(true) {
          
            $cookie[] = array_reverse($cookie);
            $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
            $cookie[] = $r["cookie"];
            $t = $r["token_csrf"];
            $t4 = $r["token_csrf4"];
            #print_r($r);
            if (!$t[1][0]) {
                return "refresh";
                
            } elseif ($t[2][2] == 1) {
                
                if ($t4[1][0] == "antibot_number_0") {
                    $rsp = array(
                        $t4[1][0] => substr(preg_replace("/[^0-9]/", "", $t4[2][0]), 0, 6)
                    );
                } else {
                    $method = "hcaptcha";
                    $cap = request_captcha($method, $r[$method], $link);
                    $rsp = array(
                        "g-recaptcha-response" => $cap,
                        "h-captcha-response" => $cap
                    );
                }
                $data = data_post($t, "two", $rsp);
                $r = base_short($link, 0, $data, $link, 0, join('', $cookie));
                $cookie[] = $r["cookie"];
                
            } else {
                L(29);
                $data = data_post($t, "one");
                $r = base_short($link, 0, $data, $link, 0, join('', $cookie));
                $cookie[] = $r["cookie"];
                $t = $r["token_csrf"];
               // print_r($r);
                if (!$t[1][1]) {
                  
                    if (preg_match("#(http)#is", $r["url"])) {
                        print h."success";
                        r();
                        return $r["url"];
                    }
                }
            }
            if (!preg_match("#(http)#is", $r["url"])) {
                return "refresh";
            }
        }
    } elseif (preg_match("#(tmearn.net)#is", $host)){
        $url = str_replace("tmearn.net/link", "blogmado.com", $url);
        $referer = "https://tmposts.com";
        $r = base_short($url, 0, 0, $referer);
        $link = $r["url"];
        
        if (!preg_match("#(http)#is", $link)) {
            return "refresh";
        }
        $cookie[] = $r["cookie"];
        $r = base_short($link, 0, 0, $link, 0, join('', $cookie));
        $t = $r["token_csrf"];
        $t3 = $r["token_csrf3"];
        
        if (!$t[1][0]) {
          return "refresh";
        }
        $cookie[] = $r["cookie"];
        $method = "recaptchav2";
        $cap = request_captcha($method, $r[$method], $link);
        $rsp = array(
          "g-recaptcha-response" => $cap,
          $t[1][0] => $t[2][0]
        );
        $data = data_post($t3, "null", $rsp);
        $r = base_short($link, 0, $data, $link, 0, join('', $cookie));
        $url8 = $r["url8"];
        
        if (!preg_match("#(http)#is", $url8)) {
            return "refresh";
        }
        return $url8;
    } elseif (preg_match("#(cxxxxcc)#is", $host)){
    
    } else {
      
        if ($separator) {
            return "skip";
            
        } else {
            return "refresh";
        }
    }
}



function data_post($t, $type, $array = 0) {
    if ($type ==  "null") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0]
        );
    } elseif ($type ==  "one") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1]
        );
    } elseif ($type ==  "two") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2]
        );
    } elseif ($type ==  "three") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3]
        );
    } elseif ($type ==  "four") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4]
        );
    } elseif ($type ==  "four2") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => "",
            explode('"', $t[1][2])[4] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4]
        );
    } elseif ($type ==  "five") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5]
        );
    } elseif ($type ==  "five2") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => "",
            explode('"', $t[1][2])[4] => $t[2][2],   
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5]
        );
    } elseif ($type ==  "six") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5],
            explode('"', $t[1][6])[0] => $t[2][6],
        );
    } elseif ($type ==  "seven") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5],
            explode('"', $t[1][6])[0] => $t[2][6],
            explode('"', $t[1][7])[0] => $t[2][7],
        );
    } elseif ($type ==  "eight") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5],
            explode('"', $t[1][6])[0] => $t[2][6],
            explode('"', $t[1][7])[0] => $t[2][7],
            explode('"', $t[1][8])[0] => $t[2][8],
        );
    } elseif ($type ==  "nine") {
        $data = array(
            explode('"', $t[1][0])[0] => $t[2][0],
            explode('"', $t[1][1])[0] => $t[2][1],
            explode('"', $t[1][2])[0] => $t[2][2],
            explode('"', $t[1][3])[0] => $t[2][3],
            explode('"', $t[1][4])[0] => $t[2][4],
            explode('"', $t[1][5])[0] => $t[2][5],
            explode('"', $t[1][6])[0] => $t[2][6],
            explode('"', $t[1][7])[0] => $t[2][7],
            explode('"', $t[1][8])[0] => $t[2][8],
            explode('"', $t[1][9])[0] => $t[2][9],
        );
    }
    if ($array) {
        $build = http_build_query(array_merge($data, $array));
    } else {
        $build = http_build_query($data);
    }
    return str_replace(["deleted", ""], "", $build);
}





function config() {
    $config[] = "fly";
    $config[] = "fly1";
    $config[] = "fly2";
    $config[] = "fly3";
    $config[] = "fly4";
    $config[] = "Linksfly";
    $config[] = "Linksfly1";
    $config[] = "linksfly2";
    $config[] = "URLHives";
    $config[] = "Linkfly";
    $config[] = "Linksfly.me";
    $config[] = "Urlsfly";
    $config[] = "Urlsfly.me";
    $config[] = "Shortfly";
    $config[] = "Shortsfly";
    $config[] = "Shortsfly.me";
    $config[] = "Wefly";
    $config[] = "Wefly.me";
    $config[] = "TryLink";
    $config[] = "Try2link";
    $config[] = "try2link.com";
    $config[] = "shorti";
    $config[] = "Shortiio";
    $config[] = "Shorti.io";
    $config[] = "Owlink";
    $config[] = "Owllink";
    $config[] = "owllink.net";
    $config[] = "owllink-net";
    $config[] = "illink";
    $config[] = "illink.net";
    $config[] = "Bird";
    $config[] = "BirdUrl";
    $config[] = "BirdURLs";
    $config[] = "Birdsurl";
    $config[] = "birdurls.com";
    $config[] = "birdsurls.com";
    $config[] = "Link1s";
    $config[] = "link1s.com";
    $config[] = "ShrinkEarn";
    $config[] = "shrinkearn-com";
    $config[] = "shrinkearn.com";
    $config[] = "SheraLinks";
    $config[] = "AdLink";
    $config[] = "adlink.click";
    $config[] = "LinksFly.link";
    $config[] = "LinksFlylink";
    $config[] = "Lksfly";
    $config[] = "LFly";
    $config[] = "Chaininfo";
    $config[] = "chainfo";
    $config[] = "chainfo.xyz";
    $config[] = "Clkst";
    $config[] = "Clk.st";
    $config[] = "Insfly";
    $config[] = "insfly.pw";
    $config[] = "Adrevlinks";
    $config[] = "Ezshort";
    $config[] = "Ez4Short";
    $config[] = "ez4shortx";
    $config[] = "Shrinkme";
    $config[] = "Shrink.me";
    $config[] = "linksly-co";
    $config[] = "linksly.co";
    $config[] = "Linksly";
    $config[] = "Linkslypw";
    $config[] = "Lsly";
    $config[] = "Shortest";
    $config[] = "Hatelink";
    $config[] = "Mitly";
    $config[] = "mitlyus";
    $config[] = "mitly.us";
    $config[] = "clkSH";
    $config[] = /*"clk";
    $config[] = "Clk-sh";
    $config[] = "clk.sh";*/
    $config[] = "Cut-Urls";
    $config[] = /*"Exe";
    $config[] = "exe-io";
    $config[] = "exeio";
    $config[] = "Exe.io";*/
    $config[] = "CPLink";
    $config[] = "Mtraffics";
    $config[] = /*"Megaurl";
    $config[] = "Megaurl.in";
    $config[] = "megaurl.io";
    $config[] = "Megafly";
    $config[] = "Megafly.in";*/
    $config[] = "Powclick";
    $config[] = "Earnify";
    $config[] = "Earnifypro";
    $config[] = /*"cuty-io";
    $config[] = "Cuty";
    $config[] = "Cuti.io";
    $config[] = "cuty.io";
    $config[] = "Cutyio";*/
    $config[] = "Usalink";
    $config[] = "usalink-io";
    $config[] = "usalink.io";
    $config[] = "Shrinkme";
    $config[] = "Shrinkme.io";
    $config[] = "shrinkme-io";
    $config[] = "shrinkmel";
    $config[] = "ShrinkmeLink";
    $config[] = "shrinkme.link";
    $config[] = "Beycoin";
    $config[] = "Goldly";
    $config[] = "goldly.cc";
    $config[] = /*"okoo";*/
    $config[] = "Forexly";
    $config[] = "forexlt.cc";
    $config[] = "Insurancely";
    $config[] = "insurancly";
    $config[] = "insurancly.cc";
    $config[] = "botfly";
    $config[] = "botfly.me";
    $config[] = "shrink.pe";
    $config[] = "limkdam";
    $config[] = "linkdam";
    $config[] = "linkdam.me";
    $config[] = "vielink";
    $config[] = /*"oii";
    $config[] = "oii.io";*/
    $config[] =/* "fc";
    $config[] = "fclc";
    $config[] = "fc-lc";
    $config[] = "fc.lc";*/
    $config[] = "Bestlink";
    $config[] = "1short";
    $config[] = "1shorten.com";
    $config[] = "CCurl";
    $config[] = "ccurl.net";
    $config[] = "adbull";
    $config[] = "adbull.net";
    $config[] = "dashfree";
    $config[] = "dash-free";
    $config[] = "dash-free.com";
    $config[] = "tmearn";
    $config[] = "tmearn.net";
    $config[] = "hrshort";
    $config[] = "hrshort.com";
    $config[] = "exfoary";
    $config[] = "ex-foary";
    $config[] = "ex-foary.com";
    $config[] = "Clicksfly";
    $config[] = "clicksflycom";
    $config[] = "Clickflcom";
    $config[] = "Clicksfly.com";
    $config[] = "Genlink";
   /* $config[] = "ctr";
    $config[] = "ctrsh";
    $config[] = "ctr.sh";*/
    $config[] = /*"ouo";*/
    $config[] = "revly";
    /*$config[] = "easycut";
    $config[] = "easycut.io";
    $config[] = "easycut-io";*/
    $config[] = "TeraFlyOwoo";
    $config[] = "TeraFlyOgoo";
    $config[] = "TeraFlyOmoo";
    $config[] = "TeraFlyOtoo";
    $config[] = "TeraFlyOroo";
    $config[] = "TeraFly/Owoo";
    $config[] = "TeraFly/Ogoo";
    $config[] = "TeraFly/Omoo";
    $config[] = "TeraFly/Otoo";
    $config[] = "TeraFly/Oroo";
    $config[] = "Panylink";
    $config[] = "Panyflay";
    $config[] = "PanyShort";
    $config[] = "panyshort.link";
    $config[] = "Cashlinko";
    $config[] = "viewfr-com";
    $config[] = "viewfr.com";
    $config[] = "viewfr";
    $config[] = "TinyGo-co";
    $config[] = "TinyGo";
    $config[] = "Tiny.go";
    $config[] = "Tiny.co";
    $config[] = "tinygo.co";
    $config[] = "wez-info";
    $config[] = "wez.info";
    $config[] = "wez";
   // $config[] = "DropLink";
    $config[] = "Oscut";
    $config[] = "KyShort";
    $config[] = "RevCut";
    $config[] = "RevCut.net";
    $config[] = "revly.click";
    $config[] = "URLCut";
    $config[] = "EazyUrl";
    $config[] = "FAHO";
    $config[] = "ClockAd";
    $config[] = "ClockAds";
    $config[] = "Clockads.in";
    $config[] = "Bitss";
    $config[] = "LinkHives";
    $config[] = "Adbitfly";
    $config[] = "ShtFly";
    $config[] = "Adshort";
    $config[] = "Adshort.co";
    $config[] = /*"clik.pw";*/
    #$config[] = "shortyearn";
    #$config[] = "shortyearn.com";
    //$config[] = "doshrink";
    //$config[] = "doshrink.com";
    $config[] = "linkjust.com";
    $config[] = "Linkjust";
    $config[] = "clks";
    $config[] = /*"clk";*/
    $config[] = "clkspro";
    $config[] = "clkspro";
    $config[] = "clks.pro";
    $config[] = "Loll";
    $config[] = "Lollty";
    $config[] = "Lollty.com";
    $config[] = "Cryptosh";
    $config[] = "Cryptosh.pro";
    $config[] = "FoxyLinks";
    $config[] = "10Short";
    $config[] = "10Short.com";
    $config[] = "cashurl.win";
    $config[] = "shortplus.xyz";
    $config[] = "urlpay";
    $config[] = "urlpay.in";
    $config[] = "1bitSpace";
    $config[] = "1bit.Space";
    $config[] = "Mgnet";
    $config[] = "Mgnet.xyz";
    $config[] = "reshort";
    $config[] = "rss";
    $config[] = "rs";
    $config[] = "rsshort";
    $config[] = "RSSshort";
    $config[] = "RSShorTcom";
    $config[] = "rsshort.com";
    $config[] = "Rsshorteasy";
    $config[] = "Paylinks";
    $config[] = "Paylinks.cloud";
    $config[] = "Shortsme";
    $config[] = "Shortsme.in";
    //$config[] = "adrinolinks";
    //$config[] = "adrinolinks.com";
    $config[] = "v2p";
    $config[] = "v2p.icu";
    $config[] = "adbits";
    $config[] = "adbits.pro";
    $config[] = "adbits.xyz";
    $config[] = "Urlcash";
    $config[] = "Urlcash.site";
    $config[] = "Short2money";
    $config[] = "Short2money.com";
    $config[] = "24pays";
    $config[] = "24pays.top";
    $config[] = "icutlink";
    $config[] = "icutlink.com";
    $config[] = "cutlink";
    $config[] = "adbx";
    $config[] = "adbx.pro";
    $config[] = "c2g";
    $config[] = "c2g.at";
    $config[] = "Freeltc.top";
    $config[] = "Freeltc";
    $config[] = "link4m";
    $config[] = "link4m.com";
    $config[] = "teralinks";
    $config[] = "teralinks.in";
    return $config;
}




   

go:
c();
$web = [
    1 => "autofaucet.org",
    2 => "autoclaim.in",
    3 => "autobitco.in"
];
for($i=1;$i<10;$i++){
    if($web[$i]){
        ket($i,$web[$i]);
    }
}
$p = tx("number", 1);
$host=$web[2];
if(!$host){
    goto go;
}
eval(str_replace('name_host',explode(".",$host)[0],str_replace('example',$host,'const host="https://example/",sc="name_host",cookie_only="cookie_example",mode="af";c();')));
$asu = cookie_only;

DATA:
$u_a = new_save("user-agent")["user-agent"];
$u_c = new_save(host)[explode("/", host)[2]];
c();
$r = base_run(host."dashboard/claim/auto");
if($r["status"] == 403){
    print m.sc." cloudflare!".n;
    new_save(host, true);
    goto DATA;
} elseif($r["register"]){
    print m.sc." cookie expired!".n;
    new_save(host, true);
    goto DATA;
}

    $r = base_run(host."dashboard/shortlinks");
    if($r["status"] == 403){
        print m.sc." cloudflare!".n;
        new_save(host, true);
        goto DATA;
    } elseif($r["register"]){
        print m.sc." cookie expired!".n;
        new_save(host, true);
        goto DATA;
    }
    c().asci(sc).ket("username",$r["username"],"balance",$r["balance"]).line();
    shortlinks:
    while(true){
        $r = base_run(host."dashboard/shortlinks");
        if($r["status"] == 403){
            print m.sc." cloudflare!".n;
            new_save(host, true);
            goto DATA;
        } elseif($r["register"]){
            print m.sc." cookie expired!".n;
            new_save(host, true);
            goto DATA;
        }
        if($r["status"] == 1){
            base_run(host."verify/hshort",http_build_query(["status" => 1]));
            goto shortlinks;
        }
        $bypas = visit_short($r);
        if($bypas == "refresh"){
            goto shortlinks;
        } elseif(!$bypas){
            lah(1,"shortlinks");
            goto auto;
        }
        $r3 = base_run($bypas);
        if($r3["notif"]){
            an(h.$r3["notif"].n);
            line().ket("balance",$r3["balance"]).line();
        }
    }
    auto:
    $r1 = base_run(host."verify/cl-au",http_build_query(["currency" => "USDT","payout" => 1,"frequency" => $frequency[0],"boost" => $boost[0]]));
    $r2 = base_run(host."verify/cli-au",http_build_query(["currencies[]" => "USDT","payout" => 1,"frequency" => $frequency[0],"boost" => $boost[0]]));
    if($r2["res"]){
        print k.$r2["res"];
        r();
    }
    if($frequency[0] == 1){
      $fr = 2;
    } elseif($frequency[0] == 2){
      $fr = 5;
    } elseif($frequency[0] == 3){
      $fr = 10;
    }
    if(sc == "autofaucet"){
      $tg="8";
    } elseif(sc == "autoclaim"){
      $tg="15";
    } elseif(sc == "autobitco"){
      $tg="1";
    }
    
    while(true){
        if(diff_time($fr, $tg.":30") == 1){
          goto shortlinks;
        }
        $r3 = base_run(host."dashboard/claim/auto/start");
        if($r3["status"] == 403){
            print m.sc." cloudflare!".n;
            new_save(host, true);
            goto DATA;
        } elseif($r3["register"]){
            print m.sc." cookie expired!".n;
            new_save(host, true);
            goto DATA; 
        } elseif($r3["notif"]){
            an(h.$r3["notif"].n);
            line();
            ket("balance",$r3["balance"]).line();
        } elseif($r3["time"]){
            tmr(1,$r3["time"]);
        } else {
            print m."FCT TOKEN not found".n;
            goto shortlinks;
    }
}
    
function base_run($url,$data=0){
    $header = head();
    $r = curl($url,$header,$data,true,false);
    unset($header);
    preg_match("#(signup|register|signin)#is",$r[1],$register);
    preg_match('#<p class="username">(.*?)</p>#is',$r[1],$username);
    preg_match('#<p class="amount">(.*?)</span>#is',$r[1],$balance);
    preg_match('#shortlinks" value="(.*?)"#is',$r[1],$status);
    preg_match_all('#<p class="name">(.*?)<#is',$r[1],$name);
    preg_match_all('#" action="/(.*?)"#is',$r[1],$visit);
    preg_match_all('#id="views">(.*?)<#is',$r[1],$left);
    preg_match_all('#hidden" name="(.*?)" value="(.*?)"#is',$r[1],$t_cs);
    preg_match('#content="(.*?)"#is',$r[1],$tmr);
    preg_match('#class="(alert alert-success" style="margin: 30px;" role="alert"|fas fa-check green"></i)>(.*?)(.</p>|</div>|. <script)#is',$r[1],$n_r);
    //print_r($amn);
    //die(print_r());
    return [
        "res" => $r[1],
        "status" => $r[0][1]["http_code"],
        "register" => $register[0],
        "username" => $username[1],
        "balance" => strip_tags($balance[1]),
        "status" => $status[1],
        "visit" => $visit[1],
        "left" => $left[1],
        "name" => $name[1],
        "token" => $t_cs,
        "time" => $tmr[1],
        "notif" => str_replace(". ",n,strip_tags($n_r[2])),
        "url" => $r[0][0]["location"]
    ];
}
