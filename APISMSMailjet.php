<?php 

use \Mailjet\Resources;
require 'vendor/autoload.php';
require 'config.php';

class APISMSMailjet{

  private $user;
  private $MJ_APIKEY_PUBLIC;
  private $MJ_APIKEY_PRIVATE;
  private $MailJetContactlistID;
  private $filename;
  private $mj;

   function __construct($user, $MJ_APIKEY_PUBLIC,$MJ_APIKEY_PRIVATE, $MailJetContactlistID, $filename) {
       
      $this->user = $user;
      $this->MJ_APIKEY_PUBLIC = $MJ_APIKEY_PUBLIC;
      $this->MJ_APIKEY_PRIVATE = $MJ_APIKEY_PRIVATE;
      $this->MailJetContactlistID = $MailJetContactlistID;
      $this->filename = $filename;      
    }


    public function getCountFile(){
      $filename = $this->filename;
      $file = file($filename);
      return count($file);    
    }

    public function getFile(){
      $file = file($this->filename);
      return $file;    
    }

    public function getContactListID(){
      return $this->MailJetContactlistID;
    }

  //Clean Numero
    public function testNumero($num){

      $numClean = "";
      if(strlen($num) == 12){
        if(substr($num,0,3) == "336" || substr($num,0,3) == "337"){
            $numClean = "+".$num;
        }
      }
      return substr($numClean,0,12);       
    }       
      
     //SEND SMS
    function sendSMS($body, $debug){

      $this->mj = new \Mailjet\Client('0a358f571057407987d3a8ab00c7c1ce', NULL, true, ['url' => "api.mailjet.com", 'version' => 'v4', 'call' => false]);
      $response = $this->mj->post(Resources::$SmsSend, ['body' => $body]);
      $data = $response->getData();
      if($response->success()){
        return "Message envoyé au ".$body["To"];
      }else{
        return "Erreur : Message non envoyé au ".$body["To"];      
      }       
    }

    // get contactlist infos
    function getContactListInfos($id){
      $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3']);
      $response = $mj->get(Resources::$Contactslist, ['id' => $id]);
      $response->success() && var_dump($response->getData());
    }

    function getUnsubscribedContactsFromList($listID)
    {
        $mj = new \Mailjet\Client(getenv('MJ_APIKEY_PUBLIC'), getenv('MJ_APIKEY_PRIVATE'),true,['version' => 'v3']);
        $params = array(
            "method" => "GET",
            "ContactsList" => $listID,
            "Unsub" => true
        );

        $result = $mj->listrecipient($params);

        if ($mj->_response_code == 200)
           echo "success - got unsubscribed contact(s) ";
        else
           echo "error - ".$mj->_response_code;
        return $result;
    }
  }
?>


