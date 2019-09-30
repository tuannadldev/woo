<?php
/**
 * Plugin Name:       Product OMS
 * Description:       Get the products from OMS system by graphql
 * Version:           1.0.0
 * Author:            Ho Quang Khanh
 * Author URI:        mailto:hoqkhanh@gmail.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*
 * Plugin constants
 */
if(!defined('PRODUCTOMS_URL'))
  define('PRODUCTOMS_URL', plugin_dir_url( __FILE__ ));
if(!defined('PRODUCTOMS_PATH'))
  define('PRODUCTOMS_PATH', plugin_dir_path( __FILE__ ));

/*
 * Main class
 */
/**
 * Class Feedier
 *
 * This class creates the option page and add the web app script
 */
class product_oms
{

  /**
   * Feedier constructor.
   *
   * The main plugin actions registered for WordPress
   */
  public function __construct()
  {

  }

  public function cron_task() {
    $path ="/";
    $this->getProducts($path);
  }

  /**
   *
   */
  function getProductTotal() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query":"query{\n  countProduct(search:\"\"){\n    count\n  }\n}","variables":null}');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);


    $data = json_decode($result, 'array');

    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

    if (!empty($data)){
      return $data['data']['countProduct']['count'];
    }

    return 0;
  }

  /**
   * @param string $path
   */
  function getProducts($path) {

    $product_total = $this->getProductTotal();
    $page_total = ceil($product_total/256);
    $data_final = [];


    for ($i = 1; $i <= $page_total; $i++) {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, 'https://eapi.pharmacity.vn/graphql');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, '{"query":"{\n  product(page:'.$i.') {\n    id, images, content_html, description, name, short_description, vendor {\n      name\n    }, category {\n      name,  parentCategory {\n      name\n    }\n    }, tags {\n      name\n    }, brand {\n      name\n    },  productVariants {\n      price, sku, price_after_discount, unit, stock_quantity, status\n    }\n  }\n }","variables":null,"operationName":null}');
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

      $headers = array();
      $headers[] = 'Content-Type: application/json';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      $result = curl_exec($ch);


      $data = json_decode($result, 'array');

      if (isset($data['data']['product'])) {
        $data_final = array_merge($data_final,$data['data']['product']);
      }

      if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
      }
      curl_close ($ch);
    }

    $this->writeDataToCSV($data_final);
    $this->writeDataToCSVBySKU($data_final);
    $this->writeDataToCSVForNew($data_final);
  }

  /**
   *
   */
  function writeDataToCSV($jsonDecoded) {
    $upload_dir   = wp_upload_dir();

    $URL = $upload_dir['basedir'].'/'.'wpallimport'.'/'.'files';
    $csvFileName = '/product_oms.csv';
    $URL .= $csvFileName;

    $array_csv = [];
    $header_csv = [
      'id',
      'name',
      'content_html',
      'description',
      'short_description',
      'sku',
      'unit',
      'price',
      'price_after_discount',
      'stock_quantity',
      'status',
      'images',
      'vendor',
      'category',
      'parent_category',
      'brand',
//      'tags'
    ];



    //Loop through the associative array.
    $data = [];
    $index = 0;

    //$sku_array  = ['P01551','P01952','P02067','P02116','P02159','P02160','P02251','P02808','P03081','P03095','P03223','P03255','P03263','P03265','P03267','P03330','P03472','P03474','P03624','P03625','P03628','P03630','P03631','P03634','P04108','P04114','P04163','P04193','P04229','P04408','P04450','P04477','P04478','P04482','P04731','P04934','P05113','P05114','P05842','P06307','P06309','P06310','P06341','P06377','P06407','P06409','P06410','P06630','P07040','P07450','P07470','P07471','P07474','P07503','P07507','P07510','P07526','P07537','P07543','P07558','P07559','P07565','P07567','P07600','P07603','P07718','P07732','P07863','P07875','P08031','P08032','P08034','P08036','P08037','P08040','P08045','P08049','P08053','P08054','P08055','P08144','P08146','P08158','P08226','P08343','P08752','P08755','P08824','P08826','P08828','P08883','P08884','P08890','P08985','P08986','P08987','P09216','P09217','P09218','P09219','P09220','P09221','P09227','P09239','P09258','P09259','P09260','P09261','P09271','P09272','P09322','P09334','P09539','P09541','P09542','P09562','P09588','P09629','P09630','P09632','P09634','P09635','P09638','P09727','P10144','P10385','P10387','P10392','P10393','P10394','P10395','P10546','P10548','P10549','P10557','P10560','P10561','P10567','P10568','P10569','P10571','P10573','P10619','P10622','P10624','P10696','P10701','P10743','P10845','P10846','P10919','P11380','P11461','P11462','P11505','P11506','P11508','P11509','P11584','P11814','P11835','P12019','P12057','P12065','P12749','P12750','P12751','P12752','P12759','P12760','P12971','P12972','P13067','P13068','P13181','P13182','P13184','P13215','P13225','P13226','P13227','P13228','P13229','P13231','P13297','P13301','P13302','P13303','P13304','P13305','P13306','P13307','P13308','P13371','P13419','P13461','P13462','P13476','P13477','P13479','P13480','P13481','P03268','P03344','P03379','P03486','P08075','P08079','P08447','P08653','P08781','P09257','P09702','P10424','P10434','P10515','P11425','P11467','P11469','P11510','P11545','P11820','P12664','P13136','P13138','P13139','P08207','P01559','P03840','P03547','P10679','P03910','P12977','P11400','P01566','P03245','P03694','P10970','P03732','P08178','P03655','P10415','P12976','P02542','P03377','P03823','P07984','P04372','P09524','P09121','P03728','P11314','P07076','P04590','P00570','P03237','P12932','P01567','P07595','P03648','P08642','P11316','P07391','P03548','P10436','P03722','P09161','P11552','P12978','P10835','P03606','P03558','P10118','P03244','P03352','P03235','P08209','P03233','P07177','P11313','P11553','P01434','P03333','P05258','P04381','P03670','P07075','P08788','P03645','P03353','P07074','P09420','P12993','P07606','P11405','P03295','P10879','P03869','P07192','P10972','P08648','P11317','P12979','P09533','P10491','P03729','P09173','P03839','P09560','P09159','P09422','P05201','P09421','P07528','P07392','P03297','P03646','P09640','P11401','P11499','P11399','P11402','P11587','P03759','P10329','P03713','P10692','P09525','P12997','P07073','P11398','P08210','P03734','P10917','P05260','P08790','P03641','P01436','P01452','P03549','P09423','P12980','P10890','P12995','P07854','P09168','P10887','P11531','P11586','P07302','P08789','P10145','P13097','P12973','P08302','P10888','P10889','P13101','P03842','P09485','P03876','P10907','P10915','P05169','P05291','P12934','P07175','P03837','P09499','P11500','P03638','P10564','P10891','P13100','P08303','P12974','P12998','P10916','P12022','P03378','P09497','P11498','P12020','P10691','P12940','P10918','P03871','P04864','P04871','P08211','P07597','P08012','P10848','P08015','P04369','P03349','P12021','P01448','P11315','P03380','P03699','P03773','P07176','P10147','P10849','P11497','P11543','P11874','P13099','P07607','P10328','P11875','P12936','P10427','P12941','P03700','P10892','P12981','P03836','P08208','P09500','P10428','P11585','P07194','P09222','P03304','P05121','P08014','P10426','P10401','P10695','P03355','P03719','P06435','P07180','P10912','P03848','P07579','P10139','P11533','P12939','P12942','P07596','P08022','P10492','P10494','P11876','P07195','P10400','P12878','P12975','P12999','P13008','P03299','P03654','P07593','P07860','P11318','P11320','P07530','P07981','P09172','P10399','P10493','P10880','P03650','P03657','P03760','P03847','P08020','P09226','P09626','P10694','P03301','P03309','P07885','P10398','P10563','P10565','P10621','P10909','P11877','P03714','P03865','P04368','P05203','P07578','P10146','P10364','P10698','P10878','P10910','P10973','P11484','P03838','P05289','P07580','P09162','P09224','P09431','P09726','P10140','P10429','P12018','P03303','P05576','P09215','P09223','P09625','P10362','P10490','P10697','P10699','P10924','P11834','P01462','P03302','P03821','P03867','P03880','P04200','P04364','P05117','P07193','P07393','P07441','P07445','P08019','P08345','P08893','P09753','P10547','P10925','P01464','P01466','P01467','P01468','P01564','P01565','P03345','P03581','P03585','P04199','P05569','P05574','P08017','P08243','P08892','P09140','P09141','P09142','P09164','P09553','P10388','P10551','P10574','P10759','P10760','P11319','P11869','P11871','P01465','P02420','P03077','P03298','P03300','P03311','P03312','P03314','P03334','P03381','P03575','P03577','P03583','P03586','P03587','P03588','P03589','P03590','P03591','P03592','P03594','P03602','P03603','P03604','P03605','P03661','P03693','P03716','P03736','P03863','P03873','P03878','P04378','P04676','P05123','P05202','P05328','P05567','P05568','P05801','P06298','P06299','P06413','P07045','P07050','P07051','P07052','P07190','P07191','P07390','P07442','P07443','P07444','P07464','P07465','P07466','P07529','P07577','P07608','P07684','P07685','P07686','P07690','P07781','P07855','P07856','P07884','P08013','P08016','P08021','P08023','P08025','P08026','P08027','P08028','P08029','P08643','P08891','P09139','P09160','P09165','P09225','P09273','P09332','P09333','P09430','P09627','P09628','P09750','P09752','P10363','P10488','P10489','P10531','P10550','P10700','P10908','P11836','P11870','P11872','P00385','P01438','P01546','P01556','P01562','P01563','P01918','P02271','P02548','P02614','P02645','P03225','P03275','P03276','P03280','P03337','P03341','P03343','P03361','P03373','P03387','P03388','P03393','P03394','P03468','P03469','P03553','P03554','P03674','P03676','P03678','P03679','P03681','P03682','P03691','P03704','P03709','P03710','P03730','P03731','P03803','P03810','P03825','P03826','P03830','P03831','P03832','P03834','P03835','P03881','P03885','P03886','P04077','P04078','P04080','P04145','P04209','P04347','P04519','P04548','P05138','P05139','P05246','P05304','P05507','P06012','P06014','P06115','P06116','P06275','P06288','P06302','P06303','P06379','P06437','P06439','P06452','P06453','P06632','P07031','P07032','P07034','P07035','P07038','P07056','P07057','P07059','P07061','P07063','P07064','P07065','P07066','P07067','P07069','P07070','P07071','P07072','P07078','P07083','P07087','P07088','P07090','P07168','P07172','P07174','P07307','P07308','P07311','P07312','P07341','P07343','P07344','P07345','P07346','P07356','P07359','P07401','P07402','P07421','P07424','P07425','P07427','P07428','P07429','P07436','P07438','P07439','P07440','P07455','P07456','P07461','P07482','P07483','P07484','P07488','P07532','P07533','P07534','P07535','P07536','P07541','P07542','P07552','P07553','P07574','P07587','P07604','P07610','P07612','P07614','P07615','P07616','P07620','P07634','P07658','P07659','P07660','P07661','P07662','P07663','P07664','P07665','P07667','P07728','P07729','P07730','P07760','P07761','P07762','P07763','P07858','P07859','P07872','P07874','P07878','P07879','P07880','P07881','P07882','P07886','P07887','P07888','P07899','P07900','P07901','P07903','P07909','P07910','P07912','P08018','P08097','P08128','P08129','P08130','P08131','P08179','P08200','P08212','P08213','P08217','P08218','P08219','P08220','P08221','P08222','P08233','P08234','P08356','P08465','P08635','P08636','P08637','P08645','P08646','P08649','P08650','P08652','P08694','P08872','P08873','P09120','P09170','P09262','P09263','P09264','P09265','P09323','P09324','P09325','P09326','P09327','P09328','P09329','P09330','P09331','P09400','P09402','P09403','P09405','P09408','P09409','P09410','P09411','P09503','P09504','P09507','P09511','P09512','P09514','P09555','P09556','P09582','P09583','P09584','P10158','P10159','P10177','P10330','P10331','P10332','P10333','P10334','P10335','P10416','P10417','P10418','P10420','P10421','P10422','P10423','P10425','P10432','P10433','P10516','P10517','P10518','P10519','P10520','P10527','P10528','P10529','P10552','P10554','P10570','P10578','P10579','P10580','P10583','P10616','P10617','P10618','P10676','P10677','P10684','P10734','P10737','P10859','P10911','P10913','P10914','P11008','P11332','P11333','P11345','P11386','P11387','P11388','P11391','P11392','P11396','P11451','P11452','P11453','P11454','P11457','P11477','P11488','P11489','P11490','P11501','P11519','P11525','P11538','P11539','P11540','P11541','P11542','P11547','P11548','P11728','P11823','P11824','P11847','P11887','P11888','P11889','P11890','P12055','P12056','P12059','P12060','P12066','P12067','P12073','P12754','P12755','P12756','P12757','P12758','P12966','P12967','P13002','P13003','P13111','P13142','P13143','P13146','P13147','P13177'];

    $sku_array = explode(',',get_option('product_sku_option_name'));

    foreach($jsonDecoded as $row) {

      if (!in_array($row['productVariants'][0]['sku'],$sku_array)){
        // Product Basic Information
        $data[$index]['id'] = $row['id'];
        $data[$index]['name'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['name'])));
        $data[$index]['content_html'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['content_html'])));
        $data[$index]['description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['description'])));
        $data[$index]['short_description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '',$row['short_description'])));
        // Product Variants
        $data[$index]['sku'] = (isset($row['productVariants'][0]['sku']))?$row['productVariants'][0]['sku']:NULL;
        $data[$index]['unit'] = (isset($row['productVariants'][0]['unit']))?$row['productVariants'][0]['unit']:NULL;
        $data[$index]['price'] = (isset($row['productVariants'][0]['price']))?$row['productVariants'][0]['price']:NULL;
        $data[$index]['price_after_discount'] = (isset($row['productVariants'][0]['price_after_discount']))?$row['productVariants'][0]['price_after_discount']:NULL;
        $data[$index]['stock_quantity'] = (isset($row['productVariants'][0]['stock_quantity']))?$row['productVariants'][0]['stock_quantity']:NULL;
        $data[$index]['status'] = (isset($row['productVariants'][0]['status']))?$row['productVariants'][0]['status']:NULL;

        // Product Images
        $images = [];
        if (!empty($row['images']) && $row['images']) {

          foreach ($row['images'] as $image) {
            $image_attr = pathinfo($image);
            $images[] = $image_attr['dirname'].'/'.$image_attr['filename'].'_l.'.$image_attr['extension'];
          }
        }
        $data[$index]['images'] = implode(';',$images);

        $data[$index]['vendor'] = (isset($row['vendor']['name']))?$row['vendor']['name']:NULL;
        $data[$index]['category'] = (isset($row['category']['name']))?$row['category']['name']:NULL;
        $data[$index]['parent_category'] = (isset($row['category']['parentCategory']['name']))?$row['category']['parentCategory']['name']:NULL;
        $data[$index]['brand'] = (isset($row['brand']['name']))?$row['brand']['name']:NULL;
        //      $data[$index]['tags'] = (isset($row['tags']['name']))?implode(',',$row['tags']['name']):NULL;
        $index++;
      }

    }

    $array_csv = $data;
    array_unshift($array_csv, $header_csv);

    //Open file pointer.
    $fp = fopen($URL, 'w');

    foreach ($array_csv as $line) {
      fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
      fputcsv($fp, array_values($line),'|', "'");
    }
    //Finally, close the file pointer.
    fclose($fp);

  }

  /**
   *
   */
  function writeDataToCSVBySKU($jsonDecoded) {
    $upload_dir   = wp_upload_dir();

    $URL = $upload_dir['basedir'].'/'.'wpallimport'.'/'.'files';
    $csvFileName = '/product_oms_by_sku.csv';
    $URL .= $csvFileName;

    $array_csv = [];
    $header_csv = [
      'id',
      'name',
      'content_html',
      'description',
      'short_description',
      'sku',
      'unit',
      'price',
      'price_after_discount',
      'stock_quantity',
      'status',
      'images',
      'vendor',
      'category',
      'parent_category',
      'brand',
      //      'tags'
    ];



    //Loop through the associative array.
    $data = [];
    $index = 0;

    //$sku_array  = ['P01551','P01952','P02067','P02116','P02159','P02160','P02251','P02808','P03081','P03095','P03223','P03255','P03263','P03265','P03267','P03330','P03472','P03474','P03624','P03625','P03628','P03630','P03631','P03634','P04108','P04114','P04163','P04193','P04229','P04408','P04450','P04477','P04478','P04482','P04731','P04934','P05113','P05114','P05842','P06307','P06309','P06310','P06341','P06377','P06407','P06409','P06410','P06630','P07040','P07450','P07470','P07471','P07474','P07503','P07507','P07510','P07526','P07537','P07543','P07558','P07559','P07565','P07567','P07600','P07603','P07718','P07732','P07863','P07875','P08031','P08032','P08034','P08036','P08037','P08040','P08045','P08049','P08053','P08054','P08055','P08144','P08146','P08158','P08226','P08343','P08752','P08755','P08824','P08826','P08828','P08883','P08884','P08890','P08985','P08986','P08987','P09216','P09217','P09218','P09219','P09220','P09221','P09227','P09239','P09258','P09259','P09260','P09261','P09271','P09272','P09322','P09334','P09539','P09541','P09542','P09562','P09588','P09629','P09630','P09632','P09634','P09635','P09638','P09727','P10144','P10385','P10387','P10392','P10393','P10394','P10395','P10546','P10548','P10549','P10557','P10560','P10561','P10567','P10568','P10569','P10571','P10573','P10619','P10622','P10624','P10696','P10701','P10743','P10845','P10846','P10919','P11380','P11461','P11462','P11505','P11506','P11508','P11509','P11584','P11814','P11835','P12019','P12057','P12065','P12749','P12750','P12751','P12752','P12759','P12760','P12971','P12972','P13067','P13068','P13181','P13182','P13184','P13215','P13225','P13226','P13227','P13228','P13229','P13231','P13297','P13301','P13302','P13303','P13304','P13305','P13306','P13307','P13308','P13371','P13419','P13461','P13462','P13476','P13477','P13479','P13480','P13481','P03268','P03344','P03379','P03486','P08075','P08079','P08447','P08653','P08781','P09257','P09702','P10424','P10434','P10515','P11425','P11467','P11469','P11510','P11545','P11820','P12664','P13136','P13138','P13139','P08207','P01559','P03840','P03547','P10679','P03910','P12977','P11400','P01566','P03245','P03694','P10970','P03732','P08178','P03655','P10415','P12976','P02542','P03377','P03823','P07984','P04372','P09524','P09121','P03728','P11314','P07076','P04590','P00570','P03237','P12932','P01567','P07595','P03648','P08642','P11316','P07391','P03548','P10436','P03722','P09161','P11552','P12978','P10835','P03606','P03558','P10118','P03244','P03352','P03235','P08209','P03233','P07177','P11313','P11553','P01434','P03333','P05258','P04381','P03670','P07075','P08788','P03645','P03353','P07074','P09420','P12993','P07606','P11405','P03295','P10879','P03869','P07192','P10972','P08648','P11317','P12979','P09533','P10491','P03729','P09173','P03839','P09560','P09159','P09422','P05201','P09421','P07528','P07392','P03297','P03646','P09640','P11401','P11499','P11399','P11402','P11587','P03759','P10329','P03713','P10692','P09525','P12997','P07073','P11398','P08210','P03734','P10917','P05260','P08790','P03641','P01436','P01452','P03549','P09423','P12980','P10890','P12995','P07854','P09168','P10887','P11531','P11586','P07302','P08789','P10145','P13097','P12973','P08302','P10888','P10889','P13101','P03842','P09485','P03876','P10907','P10915','P05169','P05291','P12934','P07175','P03837','P09499','P11500','P03638','P10564','P10891','P13100','P08303','P12974','P12998','P10916','P12022','P03378','P09497','P11498','P12020','P10691','P12940','P10918','P03871','P04864','P04871','P08211','P07597','P08012','P10848','P08015','P04369','P03349','P12021','P01448','P11315','P03380','P03699','P03773','P07176','P10147','P10849','P11497','P11543','P11874','P13099','P07607','P10328','P11875','P12936','P10427','P12941','P03700','P10892','P12981','P03836','P08208','P09500','P10428','P11585','P07194','P09222','P03304','P05121','P08014','P10426','P10401','P10695','P03355','P03719','P06435','P07180','P10912','P03848','P07579','P10139','P11533','P12939','P12942','P07596','P08022','P10492','P10494','P11876','P07195','P10400','P12878','P12975','P12999','P13008','P03299','P03654','P07593','P07860','P11318','P11320','P07530','P07981','P09172','P10399','P10493','P10880','P03650','P03657','P03760','P03847','P08020','P09226','P09626','P10694','P03301','P03309','P07885','P10398','P10563','P10565','P10621','P10909','P11877','P03714','P03865','P04368','P05203','P07578','P10146','P10364','P10698','P10878','P10910','P10973','P11484','P03838','P05289','P07580','P09162','P09224','P09431','P09726','P10140','P10429','P12018','P03303','P05576','P09215','P09223','P09625','P10362','P10490','P10697','P10699','P10924','P11834','P01462','P03302','P03821','P03867','P03880','P04200','P04364','P05117','P07193','P07393','P07441','P07445','P08019','P08345','P08893','P09753','P10547','P10925','P01464','P01466','P01467','P01468','P01564','P01565','P03345','P03581','P03585','P04199','P05569','P05574','P08017','P08243','P08892','P09140','P09141','P09142','P09164','P09553','P10388','P10551','P10574','P10759','P10760','P11319','P11869','P11871','P01465','P02420','P03077','P03298','P03300','P03311','P03312','P03314','P03334','P03381','P03575','P03577','P03583','P03586','P03587','P03588','P03589','P03590','P03591','P03592','P03594','P03602','P03603','P03604','P03605','P03661','P03693','P03716','P03736','P03863','P03873','P03878','P04378','P04676','P05123','P05202','P05328','P05567','P05568','P05801','P06298','P06299','P06413','P07045','P07050','P07051','P07052','P07190','P07191','P07390','P07442','P07443','P07444','P07464','P07465','P07466','P07529','P07577','P07608','P07684','P07685','P07686','P07690','P07781','P07855','P07856','P07884','P08013','P08016','P08021','P08023','P08025','P08026','P08027','P08028','P08029','P08643','P08891','P09139','P09160','P09165','P09225','P09273','P09332','P09333','P09430','P09627','P09628','P09750','P09752','P10363','P10488','P10489','P10531','P10550','P10700','P10908','P11836','P11870','P11872','P00385','P01438','P01546','P01556','P01562','P01563','P01918','P02271','P02548','P02614','P02645','P03225','P03275','P03276','P03280','P03337','P03341','P03343','P03361','P03373','P03387','P03388','P03393','P03394','P03468','P03469','P03553','P03554','P03674','P03676','P03678','P03679','P03681','P03682','P03691','P03704','P03709','P03710','P03730','P03731','P03803','P03810','P03825','P03826','P03830','P03831','P03832','P03834','P03835','P03881','P03885','P03886','P04077','P04078','P04080','P04145','P04209','P04347','P04519','P04548','P05138','P05139','P05246','P05304','P05507','P06012','P06014','P06115','P06116','P06275','P06288','P06302','P06303','P06379','P06437','P06439','P06452','P06453','P06632','P07031','P07032','P07034','P07035','P07038','P07056','P07057','P07059','P07061','P07063','P07064','P07065','P07066','P07067','P07069','P07070','P07071','P07072','P07078','P07083','P07087','P07088','P07090','P07168','P07172','P07174','P07307','P07308','P07311','P07312','P07341','P07343','P07344','P07345','P07346','P07356','P07359','P07401','P07402','P07421','P07424','P07425','P07427','P07428','P07429','P07436','P07438','P07439','P07440','P07455','P07456','P07461','P07482','P07483','P07484','P07488','P07532','P07533','P07534','P07535','P07536','P07541','P07542','P07552','P07553','P07574','P07587','P07604','P07610','P07612','P07614','P07615','P07616','P07620','P07634','P07658','P07659','P07660','P07661','P07662','P07663','P07664','P07665','P07667','P07728','P07729','P07730','P07760','P07761','P07762','P07763','P07858','P07859','P07872','P07874','P07878','P07879','P07880','P07881','P07882','P07886','P07887','P07888','P07899','P07900','P07901','P07903','P07909','P07910','P07912','P08018','P08097','P08128','P08129','P08130','P08131','P08179','P08200','P08212','P08213','P08217','P08218','P08219','P08220','P08221','P08222','P08233','P08234','P08356','P08465','P08635','P08636','P08637','P08645','P08646','P08649','P08650','P08652','P08694','P08872','P08873','P09120','P09170','P09262','P09263','P09264','P09265','P09323','P09324','P09325','P09326','P09327','P09328','P09329','P09330','P09331','P09400','P09402','P09403','P09405','P09408','P09409','P09410','P09411','P09503','P09504','P09507','P09511','P09512','P09514','P09555','P09556','P09582','P09583','P09584','P10158','P10159','P10177','P10330','P10331','P10332','P10333','P10334','P10335','P10416','P10417','P10418','P10420','P10421','P10422','P10423','P10425','P10432','P10433','P10516','P10517','P10518','P10519','P10520','P10527','P10528','P10529','P10552','P10554','P10570','P10578','P10579','P10580','P10583','P10616','P10617','P10618','P10676','P10677','P10684','P10734','P10737','P10859','P10911','P10913','P10914','P11008','P11332','P11333','P11345','P11386','P11387','P11388','P11391','P11392','P11396','P11451','P11452','P11453','P11454','P11457','P11477','P11488','P11489','P11490','P11501','P11519','P11525','P11538','P11539','P11540','P11541','P11542','P11547','P11548','P11728','P11823','P11824','P11847','P11887','P11888','P11889','P11890','P12055','P12056','P12059','P12060','P12066','P12067','P12073','P12754','P12755','P12756','P12757','P12758','P12966','P12967','P13002','P13003','P13111','P13142','P13143','P13146','P13147','P13177'];

    $sku_array = explode(',',get_option('product_sku_option_name'));

    foreach($jsonDecoded as $row) {

      if (in_array($row['productVariants'][0]['sku'],$sku_array)){
        // Product Basic Information
        $data[$index]['id'] = $row['id'];
        $data[$index]['name'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['name'])));
        $data[$index]['content_html'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['content_html'])));
        $data[$index]['description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['description'])));
        $data[$index]['short_description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '',$row['short_description'])));
        // Product Variants
        $data[$index]['sku'] = (isset($row['productVariants'][0]['sku']))?$row['productVariants'][0]['sku']:NULL;
        $data[$index]['unit'] = (isset($row['productVariants'][0]['unit']))?$row['productVariants'][0]['unit']:NULL;
        $data[$index]['price'] = (isset($row['productVariants'][0]['price']))?$row['productVariants'][0]['price']:NULL;
        $data[$index]['price_after_discount'] = (isset($row['productVariants'][0]['price_after_discount']))?$row['productVariants'][0]['price_after_discount']:NULL;
        $data[$index]['stock_quantity'] = (isset($row['productVariants'][0]['stock_quantity']))?$row['productVariants'][0]['stock_quantity']:NULL;
        $data[$index]['status'] = (isset($row['productVariants'][0]['status']))?$row['productVariants'][0]['status']:NULL;

        // Product Images
        $images = [];
        if (!empty($row['images']) && $row['images']) {

          foreach ($row['images'] as $image) {
            $image_attr = pathinfo($image);
            $images[] = $image_attr['dirname'].'/'.$image_attr['filename'].'_l.'.$image_attr['extension'];
          }
        }
        $data[$index]['images'] = implode(';',$images);

        $data[$index]['vendor'] = (isset($row['vendor']['name']))?$row['vendor']['name']:NULL;
        $data[$index]['category'] = (isset($row['category']['name']))?$row['category']['name']:NULL;
        $data[$index]['parent_category'] = (isset($row['category']['parentCategory']['name']))?$row['category']['parentCategory']['name']:NULL;
        $data[$index]['brand'] = (isset($row['brand']['name']))?$row['brand']['name']:NULL;
        //      $data[$index]['tags'] = (isset($row['tags']['name']))?implode(',',$row['tags']['name']):NULL;
        $index++;
      }

    }

    $array_csv = $data;
    array_unshift($array_csv, $header_csv);

    //Open file pointer.
    $fp = fopen($URL, 'w');

    foreach ($array_csv as $line) {
      fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
      fputcsv($fp, array_values($line),'|', "'");
    }
    //Finally, close the file pointer.
    fclose($fp);

  }

  /**
   *
   */
  function writeDataToCSVForNew($jsonDecoded) {
    $upload_dir   = wp_upload_dir();

    $URL = $upload_dir['basedir'].'/'.'wpallimport'.'/'.'files';
    $csvFileName = '/product_oms_for_new.csv';
    $URL .= $csvFileName;

    $array_csv = [];
    $header_csv = [
      'id',
      'name',
      'content_html',
      'description',
      'short_description',
      'sku',
      'unit',
      'price',
      'price_after_discount',
      'stock_quantity',
      'status',
      'images',
      'vendor',
      'category',
      'parent_category',
      'brand',
      //      'tags'
    ];



    //Loop through the associative array.
    $data = [];
    $index = 0;


    foreach($jsonDecoded as $row) {

        // Product Basic Information
        $data[$index]['id'] = $row['id'];
        $data[$index]['name'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['name'])));
        $data[$index]['content_html'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['content_html'])));
        $data[$index]['description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '', $row['description'])));
        $data[$index]['short_description'] = html_entity_decode(htmlspecialchars_decode(str_replace(array("\n", "\r","|"), '',$row['short_description'])));
        // Product Variants
        $data[$index]['sku'] = (isset($row['productVariants'][0]['sku']))?$row['productVariants'][0]['sku']:NULL;
        $data[$index]['unit'] = (isset($row['productVariants'][0]['unit']))?$row['productVariants'][0]['unit']:NULL;
        $data[$index]['price'] = (isset($row['productVariants'][0]['price']))?$row['productVariants'][0]['price']:NULL;
        $data[$index]['price_after_discount'] = (isset($row['productVariants'][0]['price_after_discount']))?$row['productVariants'][0]['price_after_discount']:NULL;
        $data[$index]['stock_quantity'] = (isset($row['productVariants'][0]['stock_quantity']))?$row['productVariants'][0]['stock_quantity']:NULL;
        $data[$index]['status'] = (isset($row['productVariants'][0]['status']))?$row['productVariants'][0]['status']:NULL;

        // Product Images
        $images = [];
        if (!empty($row['images']) && $row['images']) {

          foreach ($row['images'] as $image) {
            $image_attr = pathinfo($image);
            $images[] = $image_attr['dirname'].'/'.$image_attr['filename'].'_l.'.$image_attr['extension'];
          }
        }
        $data[$index]['images'] = implode(';',$images);

        $data[$index]['vendor'] = (isset($row['vendor']['name']))?$row['vendor']['name']:NULL;
        $data[$index]['category'] = (isset($row['category']['name']))?$row['category']['name']:NULL;
        $data[$index]['parent_category'] = (isset($row['category']['parentCategory']['name']))?$row['category']['parentCategory']['name']:NULL;
        $data[$index]['brand'] = (isset($row['brand']['name']))?$row['brand']['name']:NULL;
        //      $data[$index]['tags'] = (isset($row['tags']['name']))?implode(',',$row['tags']['name']):NULL;
        $index++;
      }


    $array_csv = $data;
    array_unshift($array_csv, $header_csv);

    //Open file pointer.
    $fp = fopen($URL, 'w');

    foreach ($array_csv as $line) {
      fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
      fputcsv($fp, array_values($line),'|', "'");
    }
    //Finally, close the file pointer.
    fclose($fp);

  }

}




