<?php

class cic {
    
    CONST VERSION = '3.0';
    
    CONST TPE_IDENTIFIER = '6193146';
    
    CONST COMPAGNY = 'abifrance';
    
    CONST DEFAULT_LANGAGUE = 'FR';
    
    CONST DEFAULT_CURRENTY = 'EUR';
    
    CONST KEY ='149C2042CEBB3E61C406233627220780AAC0039E';
    
    CONST ENCRYPT_PROTOCOLE = 'sha1';
    
    CONST URL_PAYPMENT = 'https://p.monetico-services.com/test/paiement.cgi';
    
    CONST CALLBACK_URL = 'http://dev-local.cma-connect.com/callback-payment/cic/';
    
    CONST CALLBACK_URL_OK = 'http://dev-local.cma-connect.com/callback-payment/cic/';
    
    CONST CALLBACK_ERR = 'http://dev-local.cma-connect.com/callback-payment/cic/';
    
    protected $_mac = "%s*%s*%s*%s*%s*%s*%s*%s*%s**********";
    
    protected $_encrypted_mac = null;
    
    public $tpe;
    
    public $date;
    
    public $amount;
    
    public $reference;
       
    public $version;
    
    public $language;
    
    public $compagny;
    
    public $mail;
    
    public $currenty;
    
    public $free_field='';
    
    
    
    
    
    function __construct() {
        
    }
    
    function debug($data) {
         $backtrace= debug_backtrace();
                echo '<br/><p><a href="#" onclick="$(this).parent().next(\'ol\').slideToggle(); return false;"><strong>'.$backtrace[0]['file'].'</strong><br/> ligne : '.$backtrace[0]['line'].'</a></p>';
                echo '<ol style="display:none">';
                foreach($backtrace as $k=>$v)
                {
                    if($k>0)
                    {
                        echo '<li><strong>'.$v['file'].'</strong><br/> ligne : '.$v['line'].'</li>';
                    }
                }
                echo '</ol>';
                echo '<pre>';
                print_r($data);
                echo '</pre>';
    }
    
    function getPaymentBtn($datas) {
        
        $this->__buildbasicValue();
        
        $this->__buildDynamicValue($datas);
        
        $this->__createMAC();
        
        echo $this->__buildHtmlOutput();
        
     
        
    }
    
    private function __buildbasicValue() {
        $this->date = $this->__getCurrentDate();
        $this->tpe = self::TPE_IDENTIFIER;
        $this->compagny = self::COMPAGNY;
        $this->version = self::VERSION;
        $this->language = self::DEFAULT_LANGAGUE;
        $this->currenty = self::DEFAULT_CURRENTY;
    }
    
    private function __buildDynamicValue($datas) {
        
        foreach($datas as $k=>$v):
            
            switch($k):
                case 'amount' : $this->amount = $v; break;
                case 'ref'    : $this->reference = $v; break;
                case 'mail'   : $this->mail = $v; break;
            
            endswitch;
            
        endforeach;
        
    }
    
    
    private function __createMAC() {
        
                $this->_mac =  \sprintf($this->_mac,
                                                    $this->tpe,
                                                    $this->date,
                                                    $this->amount.$this->currenty,
                                                    $this->reference,
                                                    $this->free_field,
                                                    $this->version,
                                                    $this->language,
                                                    $this->compagny,
                                                    $this->mail);
                
               $this->_encrypted_mac =  hash_hmac(self::ENCRYPT_PROTOCOLE, $this->_mac, $this->__getUsableKey(self::KEY));
    }
    
    private function __buildHtmlOutput(){
        \ob_start(); ?>


<form method="post" name="MoneticoFormulaire" target="_top" action="<?php echo self::URL_PAYPMENT ?>">
    <input type="hidden" name="version" value="<?php echo $this->version ?>">
<input type="hidden" name="TPE" value="<?php echo $this->tpe ?>">
<input type="hidden" name="date" value="<?php echo $this->date ?>">
<input type="hidden" name="montant" value="<?php echo $this->amount.$this->currenty ?>">
<input type="hidden" name="reference" value="<?php echo $this->reference ?>">
<input type="hidden" name="MAC" value="<?php echo $this->_encrypted_mac ?>">
<input type="hidden" name="url_retour" value="<?php echo self::CALLBACK_URL ?>">
<input type="hidden" name="url_retour_ok" value="<?php echo self::CALLBACK_URL_OK ?>">
<input type="hidden" name="url_retour_err" value="<?php echo self::CALLBACK_ERR ?>">
<input type="hidden" name="lgue" value="<?php echo $this->language ?>">
<input type="hidden" name="societe" value="<?php echo $this->compagny ?>">
<input type="hidden" name="mail" value="<?php echo $this->mail ?>">
<input type="submit" name="bouton" value="Paiement CB">
</form>


        <?php
        $content = \ob_get_clean();
        return $content;
    }
    
    
    private function __getCurrentDate() {
        
        return  date('d/m/Y:G:i:s');
    }
    
    private function __getUsableKey($key)
    {
        $hexStrKey  = substr($key, 0, 38);
        $hexFinal   = "" . substr($key, 38, 2) . "00";
        $cca0=ord($hexFinal);
        if ($cca0>70 && $cca0<97)
            $hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
        else {
            if (substr($hexFinal, 1, 1)=="M")
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            else
                $hexStrKey .= substr($hexFinal, 0, 2);
        }
        return pack("H*", $hexStrKey);
    }
    
}


$datas = array(
    'amount' => '5',
    'ref' => 'refpayment',
    'mail' => 'customermail@mail.com',
    
);


$test = new cic();
$test->getPaymentBtn($datas);
die();
?>



<?php
function getUsableKey($key)
    {
        $hexStrKey  = substr($key, 0, 38);
        $hexFinal   = "" . substr($key, 38, 2) . "00";
        $cca0=ord($hexFinal);
        if ($cca0>70 && $cca0<97)
            $hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
        else {
            if (substr($hexFinal, 1, 1)=="M")
                $hexStrKey .= substr($hexFinal, 0, 1) . "0";
            else
                $hexStrKey .= substr($hexFinal, 0, 2);
        }
        return pack("H*", $hexStrKey);
    }

$version = "3.0";
$tpe = "6193146";
$date = date('d/m/Y:G:i:s');
$ref_payment = 'abo_test'.rand();
$montant = "10";
$devise = "EUR";
$key = "149C2042CEBB3E61C406233627220780AAC0039E";
$mac = $tpe.'*'.$date.'*'.$montant.$devise.'*'.$ref_payment.'**'.$version.'*FR*abifrance*internaute@sonemail.fr**********';

$callback = hash_hmac("sha1", $mac, \getUsableKey($key));


?>



