<?php
class TissVersionEnum
{
    const tissV3_05_00 = 'tissV3_05_00';
    const tissV3_04_01 = 'tissV3_04_01';
    const tissV3_04_00 = 'tissV3_04_00';
    const tissV3_03_03 = 'tissV3_03_03';
    const tissV3_03_02 = 'tissV3_03_02';
    const tissV3_03_01 = 'tissV3_03_01';
    const tissV3_02_02 = 'tissV3_02_02';
    const tissV3_02_01 = 'tissV3_02_01';
    const tissV3_02_00 = 'tissV3_02_00';
    const tissV2_02_03 = 'tissV2_02_03';
    const tissV2_02_02 = 'tissV2_02_02';
    const tissV2_02_01 = 'tissV2_02_01';
    const tissV2_01_03 = 'tissV2_01_03';
} 

class xmlValidador{
    private $errors;
    private $version;
    private $xsdPath;
    public $hash;

    function __construct($version) {
        $this->version = $version;
        $this->errors = [];
        $this->xsdPath = "tissSchemaValid/";
        
    }

    public function ValidateXml($xmlfile){
        $filename_xml = $xmlfile;
        $filename_xsd = $this->xsdPath.$this->version.'.xsd';
        libxml_use_internal_errors(true);
        $product_schema_errors=array();
        $xml_doc = new DOMDocument();
        $xml_doc->load($filename_xml);
        $valid_xml = $xml_doc->schemaValidate($filename_xsd) ;

        $msg ="";
        foreach (libxml_get_errors() as $error) {
            $this->errors[] = $error; 
        }
        libxml_clear_errors();

        if( $valid_xml ){
            $hashnode = $xml_doc->getElementsByTagName('hash');
            $hash = $hashnode->item(0)->nodeValue;
            $hashnode->item(0)->nodeValue = "";

            $content =$xml_doc->saveXML(); 
            $text = preg_replace('/[\t\n]/',"",$content);        
            $text = preg_replace('/\>\s+\</',"",$text);
            $text = preg_replace('/<[^>]+>/',"",$text);
            $newHash = md5($text);
            $this->hash['status'] = $newHash == $hash;
            $this->hash['value'] = $hash;

        }
        return $valid_xml;
    }


    public  function getErrors(){
        return $this->errors;
    }

    public function DisplayErrors(){
        $newerror=[];
        $newerror['status'] = '4';
        foreach ($this->errors as $key => $value) {
            $newerror['Errors'][] = [ 
                'linha'=>$value->line,
                'msg'=>  $value->message
                //'msg'=> preg_replace('/({[^\\\\]+?})/', '', $value->message)
                
            ];
        }
        $newerror['hash'] = $this->hash;
        return json_encode($newerror);
    }
}

$result="";
if(isset($_FILES['userfile']) && isset($_POST['versao'])){
    $versao = $_POST['versao'];
    $filexsd = 'tissSchemaValid/'.$versao.'.xsd';

    if($_FILES['userfile']['error']==0){
        $arquivo = $_FILES['userfile'];
        //$result = load_and_validate_xml($arquivo['tmp_name'], $filexsd);
        $validator = new xmlValidador($versao);
        if($validator->ValidateXml($arquivo['tmp_name'])){
            $result=[];
            $result['status'] = '0';
            $result['msg'] = 'arquivo ok';
            $result['hash']= $validator->hash;

            print_r(json_encode($result));
        }else{
            print_r($validator->DisplayErrors());
        }
    }
}
?>
