<?php

//Pour appeler cette fonction:

// include 'ebay.php'; on inclu ce script dans la page qu'on veut
// ebay('le code eam que vous voulez'); //On appel la fonction en lui donnant comme paramètre un code EAM

function ebay ($query){

// error_reporting(E_ALL);  // Activation des erreurs pour facilité le débug

// Mes variables pour la requete a L'api
$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL à appeler
$version = '1.0.0';  // API version
$appid = 'Labrosse-hardwher-PRD-5b7edec1b-d36855df';  // Mon apiID
$globalid = 'EBAY-FR';  // La langue
//$query = '3536403351380';  // Ce que je veut rechercher
$safequery = urlencode($query);  // Création de la requete en URL

global $filterarray ;
global $urlfilter;
global $i;

$i = '0';  // Initialisation du filtre d'objet a 0

// Création d'un tableau de paramètre
$filterarray =
  array(
    array(
    'name' => 'MaxPrice',
    'value' => '2500',
    'paramName' => 'Currency',
    'paramValue' => 'USD'),
    array(
    'name' => 'FreeShippingOnly',
    'value' => 'false',
    'paramName' => '',
    'paramValue' => ''),
    array(
    'name' => 'ListingType',
    'value' => array('AuctionWithBIN','FixedPrice','StoreInventory'),
    'paramName' => '',
    'paramValue' => ''),
    array(
    'name' => 'Condition',
    'value' => ["New"],
    'paramName' => '',
    'paramValue' => ''),
  );

// Transformation du tableau de paramètre en Url
function buildURLArray ($filterarray) {
  // global $urlfilter;
  // global $i;

  global $filterarray ;
  global $urlfilter;
  global $i;

  foreach($filterarray as $itemfilter) {

    foreach ($itemfilter as $key =>$value) {
      if(is_array($value)) {
        foreach($value as $j => $content) {
          $urlfilter .= "&itemFilter($i).$key($j)=$content";
        }
      }
      else {
        if($value != "") {
          $urlfilter .= "&itemFilter($i).$key=$value";
        }
      }
    }
    $i++;
  }
  return "$urlfilter";
} // Fin de la Transformation du tableau de paramètre en Url

// Exectution de la requete de Transformation du tableau de paramètre en Url
buildURLArray($filterarray);

// construction de la requete  HTTP GET CALL
$apicall =  "$endpoint?";
$apicall .= "OPERATION-NAME=findItemsByKeywords";
$apicall .= "&sortOrder=PricePlusShippingLowest";
$apicall .= "&SERVICE-VERSION=$version";
$apicall .= "&SECURITY-APPNAME=$appid";
$apicall .= "&GLOBAL-ID=$globalid";
$apicall .= "&keywords=$safequery";
$apicall .= "&paginationInput.entriesPerPage=1";
$apicall .= "$urlfilter";


// Charger la requete et la transforme en XML
$resp = simplexml_load_file($apicall);

// Check si la requete à fonctionner
if ($resp->ack == "Success") {
  $results = '';

  // Si la requete ma bien renovyer mon xml recupération des éléments qui m'intéresse

  //foreach($resp->searchResult->item as $item) { //Si on veut plusieurs item
    $item  = $resp->searchResult->item ;
    $pic   = $item->galleryURL;
    $link  = $item->viewItemURL;
    $title = $item->title;
    $price =  floatval($item->sellingStatus->currentPrice) ;
    $fdp   =  floatval($item->shippingInfo->shippingServiceCost) ;
    $fullPrice = $price + $fdp ;
    // Affichage
    $results = "<p> <img src=\"$pic\"><a href=\"$link\"> $title  <em>$fullPrice Euros</em> fdp inclu </a></p>";

  //} //Fin du foreach
}
// Si la requete n'est pas un succes
else {
  $results  = "<h3>Marche pas</h3>";
}
?>

<!--Affichage <3-->
<h1>Recherche du code ean "<?php echo $query; ?>" sur Ebay  </h1>
<h2>Le resultat est :   <?php  echo $results;?></h2>



<?php

}; //fin de ma fonction EBAY()

?>
