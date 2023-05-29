<?

/****************************************************
Author: Alexey G. Piyanin (drdrzlo at mail dot ru)
Version: 1.2 - Jun 7 2006
******************************************************/
class HTML_SAXParser{
  var $prcObj=null;
  #---
  var $funcBegin='';
  var $funcEnd  ='';
  var $funcChr  ='';
  var $funcCmnt ='';

  var $__explodeList = array(
    '/<((!--)|[!:a-z0-9_]+)/i',
    '/[:-a-z0-9_]+\s*=/i',
    '/"[^"<>]*"/',
    "/'[^'<>]*'/",
    '#</[:a-z0-9_]+\s*>#i',
    '#>|/>|-->#',
  );

  function initFunc($begin='',$end='',$char='',$cmnt=''){
    $this->init($n=null,$begin,$end,$char,$cmnt);
  }

  function init(&$object,$begin='',$end='',$char='',$cmnt=''){
    $this->prcObj = &$object;
    #---
    $this->funcBegin=$begin;
    $this->funcEnd  =$end;
    $this->funcChr  =$char;
    $this->funcCmnt =$cmnt;
  }

  function parseString($str){
    if (empty($this->funcBegin) && empty($this->funcEnd) && empty($this->funcChr)) return;
    #---
    $lines = explode("\n",$str);
    #---
    $this->__initParseVar();
    foreach($lines as $buffer) if ($this->__parseBuffer($buffer."\n")==-1) break;
    $this->__character();
  }

  function parse($fileName){
    if (empty($this->funcBegin) && empty($this->funcEnd) && empty($this->funcChr)) return;
    #---
    $fh = @fopen ($fileName, 'r');
    if ($fh){
      $this->__initParseVar();
      while (!feof($fh)) if ($this->__parseBuffer(fgets($fh))==-1) break;
      $this->__character();
      #---
      fclose($fh);
    }
  }

  function __initParseVar(){
    $this->out = ''; $this->tag = '';
    #---
    $this->attribute = '';    $this->attributes   = array();
    $this->isComment = false; $this->isNonProcess = false;
    #---
    $this->beginTagPos = 0; $this->readSize = 0;
  }

  function __parseBuffer($buffer){
    $ret = 0;
    #---
    if ($buffer!=''){
      $res = $this->__parseStr($buffer);
      #---
      foreach($res as $pos=>$value){
        if (strpos($value,'</')===0 && !$this->isComment){
          $value = strtolower($value);
          #---
          if ($this->isNonProcess && ($value=='</script>' || $value=='</style>')){ $this->isNonProcess = false; }
          #---
          if (!$this->isNonProcess && 
             ($this->__character()===-1 || $this->__end(substr($value,2,-1),$this->readSize+$pos,strlen($value))===-1)){ $ret = -1; break; }
          #---
        }elseif($this->isComment && $value=='-->'){
          if($this->__comment(false,$value,$this->readSize+$pos)===-1){ $ret = -1; break; }
          #---
        }elseif($this->tag!='' && !$this->isNonProcess && !$this->isComment){
          if (isset($value[0]) && ($value[0]=='>' || (isset($value[1]) && $value[0]=='/' && $value[1]=='>'))){
            if ($this->__character()===-1 || $this->__begin($this->tag,$this->beginTagPos,$this->readSize+$pos+1-$this->beginTagPos)===-1){ $ret = -1; break; }
            #---
            if ($this->tag=='script' || $this->tag=='style') $this->isNonProcess = true;
            else if($this->tag[0]=='!' && $this->__end($this->tag,$this->readSize+$pos,strlen($value))===-1){ $ret = -1; break; }
            #---
            $this->tag = ''; $this->attribute = ''; $this->attributes = array();
          }elseif($this->attribute=='' && strpos($value,'=')!==false){
            $this->attribute = trim(substr($value,0,strpos($value,'=')));
            #---
          }elseif(trim($value)!=''){
            if (isset($value[0]) && ($value[0]=='"' || $value[0]=="'")) $value = substr($value,1,-1);
            #---
            $this->attribute = strtolower($this->attribute);
            if (!isset($this->attributes[$this->attribute])) $this->attributes[$this->attribute] = $value;
            else $this->attributes[$this->attribute] .= $value;
            #---
            $this->attribute = '';
          }
        }elseif($this->tag=='' && isset($value[0]) && $value[0]=='<' && !$this->isNonProcess && !$this->isComment){
          if($value!='<!--'){
            $this->tag = strtolower(substr($value,1));
            $this->beginTagPos = $this->readSize + $pos;
          }else{
            if($this->__comment(true,$value,$this->readSize+$pos)===-1){ $ret = -1; break; }
          }
        }else{
          $this->out .= $value;
        }
      }
    }
    #---
    $this->readSize += strlen($buffer);
    return $ret;
  }

  function __begin($tag,$pos,$length){
    if (($func=$this->funcBegin)!='') return(!is_object($this->prcObj) ? $func($tag,$this->attributes,$pos,$length) : $this->prcObj->$func($tag,$this->attributes,$pos,$length));
  }

  function __end($tag,$pos,$length){
    if (($func=$this->funcEnd)!='') return(!is_object($this->prcObj) ? $func($tag,$pos,$length) : $this->prcObj->$func($tag,$pos,$length));
  }

  function __character(){
    if ($this->out!='' && ($func=$this->funcChr)!=''){
      $res = (!is_object($this->prcObj) ? $func($this->out) : $this->prcObj->$func($this->out)); $this->out = '';
      return $res;
    }
  }

  function __comment($cmnt,$value,$pos){
    if (($func=$this->funcCmnt)!=''){
      if($this->__character()===-1) return(-1);
      #---
      $res = (!is_object($this->prcObj) ? $func($cmnt,$pos) : $this->prcObj->$func($cmnt,$pos));
    }else{
      $this->out .= $value;
      $res = 0;
    }
    $this->isComment = $cmnt;
    #---
    return($res);
  }

  function __parseStr($str){
    $tmp = array();
    #---
    foreach($this->__explodeList as $match){
      preg_match_all($match, $str, $chars, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
      #---
      foreach($chars as $list){
        foreach($list as $value){
          if(empty($value)) continue;
          #---
          $tmp[$value[1]] = $value[0];
        }
      }
    }
    if (reset($tmp)===null) return(array($str));
    #---
    ksort($tmp);
    $prePos = -1;
    #---
    $res = array();
    foreach($tmp as $pos=>$value){
      if ($pos>$prePos){
        if ($pos>0 && ($pos-$prePos)>1) $res[$prePos+1] = substr($str,++$prePos,$pos-$prePos);
        $prePos = $pos + strlen($value)-1;
        #---
        $res[$pos] = $value;
      }
    }
    #---
    $res[$prePos+1] = substr($str,++$prePos);
    return $res;
  }
}
?>