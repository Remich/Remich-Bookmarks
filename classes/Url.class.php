<?php

	/**
     * Class provides easy way to manipulate url parameters
     * @author Alexander Podgorny
     * modified by René Michalke
     */
    class Url {
    	 
		public static function getCurrentUrl() {
		
			return ((empty($_SERVER['HTTPS'])) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		}
		
		public static function getCurrentPath() {
		
			$ar = explode("/", $_SERVER['PHP_SELF']);
			unset($ar[sizeof($ar)-1]);
			$path = implode("/", $ar)."/";
			return ((empty($_SERVER['HTTPS'])) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST']. $path;
		
		}
		
		public static function getCurrentServerPath() {
		
			$ar = explode("/", $_SERVER['SCRIPT_FILENAME']);
			unset($ar[sizeof($ar)-1]);
			$str = implode("/", $ar)."/";
			return $str;
		
		}
    
        /**
         * Splits url into array of it's pieces as follows:
         * [scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]
         * In addition it adds 'query_params' key which contains array of 
         * url-decoded key-value pairs
         *
         * @param String $sUrl Url
         * @return Array Parsed url pieces
         */
        public static function explode($sUrl) {
            $aUrl = parse_url($sUrl);
            $aUrl['query_params'] = array();
            $aPairs = explode('&', @$aUrl['query']);
            //DU::show($aPairs);
            foreach($aPairs as $sPair) {
                if (trim($sPair) == '') { continue; }
                list($sKey, $sValue) = explode('=', $sPair);
                $aUrl['query_params'][$sKey] = urldecode($sValue);
            }
            return $aUrl;
        }
        /**
         * Compiles url out of array of it's pieces (returned by explodeUrl)
         * 'query' is ignored if 'query_params' is present
         * 
         * @param Array $aUrl Array of url pieces
         */
        public static function implode($aUrl) {
            //[scheme]://[user]:[pass]@[host]/[path]?[query]#[fragment]
            
            $sQuery = '';
            
            // Compile query
            if (isset($aUrl['query_params']) && is_array($aUrl['query_params'])) {
                $aPairs = array();
                foreach ($aUrl['query_params'] as $sKey=>$sValue) {
                    $aPairs[] = $sKey.'='.urlencode($sValue);               
                }
                $sQuery = implode('&', $aPairs);    
            } else {
                $sQuery = $aUrl['query'];
            }
            
            // Compile url
            $sUrl = 
                $aUrl['scheme'] . '://' . (
                    isset($aUrl['user']) && $aUrl['user'] != '' && isset($aUrl['pass']) 
                       ? $aUrl['user'] . ':' . $aUrl['pass'] . '@' 
                       : ''
                ) .
                $aUrl['host'] . (
                    isset($aUrl['path']) && $aUrl['path'] != ''
                       ? $aUrl['path']
                       : ''
                ) . (
                   $sQuery != ''
                       ? '?' . $sQuery
                       : ''
                ) . (
                   isset($aUrl['fragment']) && $aUrl['fragment'] != ''
                       ? '#' . $aUrl['fragment']
                       : ''
                );
            return $sUrl;
        }
        /**
         * Parses url and returns array of key-value pairs of url params
         *
         * @param String $sUrl
         * @return Array
         */
        public static function getParams($sUrl) {
            $aUrl = self::explode($sUrl);
            return $aUrl['query_params'];
        }
        /**
         * Removes existing url params and sets them to those specified in $aParams
         *
         * @param String $sUrl Url
         * @param Array $aParams Array of Key-Value pairs to set url params to
         * @return  String Newly compiled url 
         */
        public static function setParams($sUrl, $aParams, $whiteList = NULL) {
            $aUrl = self::explode($sUrl);
            $aUrl['query'] = '';
            $aUrl['fragment'] = '';
            
            if(sizeof($aParams) == 0 && $whiteList != NULL) {
            	foreach($aUrl['query_params'] as $key => $item)
            		if(!in_array($key, $whiteList))
            			unset($aUrl['query_params'][$key]);
            } else $aUrl['query_params'] = $aParams;
            
            return self::implode($aUrl);
        }
        /**
         * Updates values of existing url params and/or adds (if not set) those specified in $aParams
         *
         * @param String $sUrl Url
         * @param Array $aParams Array of Key-Value pairs to set url params to
         * @return  String Newly compiled url 
         */
        public static function updateParams($sUrl, $aParams) {
            $aUrl = self::explode($sUrl);
            $aUrl['query'] = '';
            $aUrl['query_params'] = array_merge($aUrl['query_params'], $aParams);
            return self::implode($aUrl);
        }
        /**
         * Removes url params specified in $aParams
         *
         * @param String $sUrl Url
         * @param Array $aParams Array of Value which specifies the Keys to remove 
         * @return  String Newly compiled url 
         */
        public static function removeParams($sUrl, $aParams) {
            $aUrl = self::explode($sUrl);
            $aUrl['query'] = '';
            foreach($aParams as $item) if(isset($aUrl['query_params'][$item])) unset($aUrl['query_params'][$item]);
            return self::implode($aUrl);
        }
        
        public static function initAndFixPath() {
        
        	$modules = array('bookmarks');
        	
        	foreach($modules as $item) {
        	
        		if(!isset($_SESSION['url_'.$item]))
        			$_SESSION['url_'.$item] = Url::getCurrentUrl();
        		else {
		    	
		    		$aSe = Url::explode($_SESSION['url_'.$item]);
					$aCu = Url::explode(Url::getCurrentUrl());
				
					if($aSe['path'] != $aCu['path'])
						$aSe['path'] = $aCu['path'];
					
					$_SESSION['url_'.$item] = Url::implode($aSe);
				
				}
        		
        	}
        	
        }
        
        public static function is_local_host($url) {

			$host = parse_url($url, PHP_URL_HOST);
			$ips = gethostbynamel($host);
		
			if (is_array($ips)) {
				foreach ($ips as $ip) {
				    $long = ip2long($ip);
				    if ($long > 0x7F000000 && $long < 0x7FFFFFFF) {
				        return true;
				    }
				}
			}

			return false;
		}
        
        
    } // <!-- end class ’Url’ -->
	
?>
