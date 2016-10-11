<?php
class MobileLogin_Utils
{
    public static function getTemplate($tpl, $vars ,$tplVars=null)
	{
		$json = json_encode($vars);
		$js_script = '<script type="text/javascript"> v = eval(\'(' . $json . ")');</script>\n";
		$content = @file_get_contents($tpl);
		if(false === $content){
			return false;
		}
		if(is_string($content) && strtolower(CHARSET) != 'utf-8' && strtolower(CHARSET) != 'utf8'){
			if(function_exists('iconv')){
				$content = @iconv('UTF-8', 'GBK//ignore', $content);
			}else if(function_exists('mb_convert_encoding')){
				$content = @mb_convert_encoding($content, 'GBK', 'UTF-8');
			}
		}

		$tplVars['js_script'] = $js_script;
		$tplVars['app_charset'] = CHARSET;
		if(is_array($tplVars)){
			foreach($tplVars as $key => $value){
				$content = str_replace("<%".$key."%>",$value,$content);
				$content = str_replace("<% ".$key." %>",$value,$content);
			}
		}
		return $content;
	}


	public static function includeJs($jsPath)
	{
		static $inited = false;
		if(false === $inited){
			$str = '';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'jquery.js"> </script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'jquery.min.js"> </script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'ajaxfileupload.js"> </script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'uploadpic.js"> </script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'uploadpic.js"> </script>';
			$str .= '<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js" type="text/javascript"></script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'evol.colorpicker.js"> </script>';
			$str .= '<script type="text/javascript" src="' . $jsPath . 'updateschedule.js"> </script>';
			echo $str;
			$inited = true;
		}
	}

    public static function checkSign()
    {
        $req_sign  = $_REQUEST["sign"];
        $requestid = $_REQUEST["requestid"];
        $req_str = "login_mobile_$requestid";
        $diff = time() - intval($requestid);
        if ($diff>=60 || $diff<0) return false;
        return md5($req_str)==$req_sign;
    }

	public static function diconv($charset1, $charset2, $str)
    {
		if(function_exists('iconv')){
			$msg = iconv($charset1, $charset2 . '//ignore', $str);	
		}else{
			$msg = mb_convert_encoding($str, $charset2, $charset1);
		}
        return $msg;
    }

	public static function includeCss($cssPath)
	{
		static $inited = false;
		if(false === $inited){
			$str ='';
			$str .= '<link href="' . $cssPath . 'demo.css" rel="stylesheet" />'; 
			$str .= '<link href="' . $cssPath . 'evol.colorpicker.min.css" rel="stylesheet" />';
			echo $str;
			$inited = true;
		}
	}

	public static function readLocalAkSk($file)
	{
		$tmp = @file_get_contents($file);
		$tmp2 = BIGAPPJSON::decode($tmp, true);
		if(!is_array($tmp2) || !isset($tmp2['app_key']) || !isset($tmp2['app_secret'])){
			return false;
		}
		return $tmp2;
	}

	public static function getDefPkg()
	{
		global $_G;
		$rndStr = self::_randString();
		if(isset($_G['siteurl'])){
			$tmp = @parse_url($_G['siteurl']);
			if(is_array($tmp)){
				$host = $tmp['host'];
				$hostArr = array();
				$tmp = explode('.', $host);
				foreach ($tmp as $v){
					if(preg_match('/^[a-zA-Z]+$/', $v)){
						$hostArr[] = $v;
					}
				}
				$num = count($hostArr);
				if(2 <= $num){
					$pkg = $hostArr[$num - 1] . '.' . $hostArr[$num - 2] . '.clan' . $rndStr;
					if(is_numeric($hostArr[$num - 1])){
						$pkg = 'com.' . $hostArr[$num - 2] . '.clan' . $rndStr;
					}
					return $pkg;
				}
			}
		}
		$pkg = 'com.youzu.clan' . $rndStr;
		return $pkg;
	}

	protected static function _randString($len = 16)
	{
		$output = '';
		for($i = 0; $i < $len; $i++){
			$chr = mt_rand(0, 25);
			$oct = chr(ord('a') + $chr);
			$output .= $oct;
		}
		return $output;
	}

	public static function addUrlQueryString($inputUrl, $arrParam = array()){
		if(empty($arrParam)){
			return $inputUrl;
		}
		$arrUrl = parse_url($inputUrl);
		if(!$arrUrl || !isset($arrUrl['scheme']) || !isset($arrUrl['host'])){
			return false;
		}
		$url = $arrUrl['scheme'] . '://';
		if(isset($arrUrl['user'])){
			$url .= $arrUrl['user'];
			if(isset($arrUrl['pass'])){
				$url .= ':' . $arrUrl['pass'];
			}
			$url .= '@';
		}
		$url .= $arrUrl['host'];
		if(isset($arrUrl['port'])){
			$url .= ':' . $arrUrl['port'];
		}
		$split = '/?';
		if(isset($arrUrl['path'])){
			$url .= $arrUrl['path'];
			$split = '?';
		}
		$qs = http_build_query($arrParam);
		if(isset($arrUrl['query'])){
			parse_str($arrUrl['query'], $queryArr);
			if(!empty($arrParam)){
				$arrParam = array_merge($queryArr,$arrParam);
				$qs = http_build_query($arrParam);
			}else{
				$qs = $arrUrl['query'];
			}
		}
		$url .= $split . $qs;
		if(isset($arrUrl['fragment'])){
			$url .= '#' . $arrUrl['fragment'];
		}
		return $url;
	}

	public static function loadTemplate($tpl, $vars ,$tplVars=null)
	{
		$json = json_encode($vars);
		$js_script = '<script type="text/javascript"> v = eval(\'(' . $json . ")');</script>\n";
		$content = @file_get_contents($tpl);
		if(false === $content){
			return false;
		}
		if(is_string($content) && strtolower(CHARSET) != 'utf-8' && strtolower(CHARSET) != 'utf8'){
		    if(function_exists('iconv')){
			    $content = @iconv('UTF-8', 'GBK//ignore', $content);
		    }else if(function_exists('mb_convert_encoding')){
			    $content = @mb_convert_encoding($content, 'GBK', 'UTF-8');
			}
		}
		$tplVars['js_script'] = $js_script;
		$tplVars['app_charset'] = CHARSET;
		if(is_array($tplVars)){
			foreach($tplVars as $key => $value){
				$content = str_replace("<%".$key."%>",$value,$content);
				$content = str_replace("<% ".$key." %>",$value,$content);
			}
		}
		echo $content;
	}

	public static function getFile($url, $dest = null)
	{
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
		$ret = curl_exec($ch);
		if(0 != curl_errno($ch)){
			return false;
		}
		if(is_null($dest)){
			return $ret;
		}
		file_put_contents($dest, $ret);
		return true;
	}

	public static function filterPid($settings){
		if(!empty($settings)){
			foreach($settings as $key => &$value){
				$value['title'] = self::converGbkString(urldecode($value['title']));
				$value['pic'] = self::converGbkString(urldecode(urldecode($value['pic'])));
				$value['pic'] = str_replace("&amp;","&",$value['pic']);
				$value['pic'] = self::addUrlQueryString($value['pic'],array("_v"=>time()));
				$value['url'] = self::converGbkString(urldecode($value['url']));
				$value['desc'] = self::converGbkString(urldecode($value['desc']));

				$preg = array(
						'2'=>'/\w+-(\d+)-(\d+)-(\d+)\.htm/i',
						'3'=>'/\w+-(\d+)-(\d+)\.htm/i',
						);

				if($value['type'] == 2){
					if(isset($preg[$value['type']]) 
							&& preg_match($preg[$value['type']],$value['url'],$matches) ){
						if(isset($matches[1]))
							$value['pid'] = $matches[1];
					}else{
						if(preg_match('/tid=(\d+)/i', $value['url'], $matches)){
							if(isset($matches[1]))
								$value['pid'] = $matches[1];
						}
					}
				}

				if($value['type'] == 3){
					if(isset($preg[$value['type']]) 
							&& preg_match($preg[$value['type']],$value['url'],$matches) ){
						if(isset($matches[1]))
							$value['pid'] = $matches[1];
					}else{
						if(preg_match('/fid=(\d+)/i', $value['url'], $matches)){
							if(isset($matches[1]))
								$value['pid'] = $matches[1];
						}
					}
				}
				runlog('login_mobile', "debug >>>>>>>>> info:".json_encode($value));
				
				
				runlog('login_mobile', "debug end >>>>>>>>> info pid >>>>>>>>:".$value['pid']);
			}
		}
		return $settings;
	}
}
?>
