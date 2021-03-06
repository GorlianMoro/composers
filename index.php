<?php
require __DIR__ . '/vendor/autoload.php';


if (isset($_GET['lat']))
{
  $lat = $_GET['lat'];
}

if (isset($_GET['lon']))
{
  $lon = $_GET['lon'];
}

$api = new Yandex\Geo\Api();

if (isset($_POST['found']))
{
  $api->setPoint('', '');
  
  if (isset($_POST['adress']))
  {
    $adres = htmlentities($_POST['adress']);
    $api->setQuery("$adres");
  }
}
// Настройка фильтров
$api
    ->setLimit('') // кол-во результатов
    ->setLang(\Yandex\Geo\Api::LANG_RU) // локаль ответа
    ->load();

$response = $api->getResponse();
$response->getFoundCount(); // кол-во найденных адресов
$response->getQuery(); // исходный запрос
$response->getLatitude(); // широта для исходного запроса
$response->getLongitude(); // долгота для исходного запроса

// Список найденных точек
$collection = $response->getList();
foreach ($collection as $item)
{
    $item->getAddress(); // вернет адрес
    $item->getLatitude(); // широта
    $item->getLongitude(); // долгота
    $item->getData(); // необработанные данные
}
 ?>

 <!DOCTYPE html>
 <html lang="ru">
   <head>
     <meta charset="utf-8">
     <title>Яндекс карта</title>
     <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript">
   </script>
     <script type="text/javascript">
     ymaps.ready(init);
          function init(){
              var myMap = new ymaps.Map("map", {
                  center: ['<?php echo $lat; ?>', '<?php echo $lon; ?>'],
                  zoom: 7
              });
              var myPlacemark = new ymaps.Placemark(['<?php echo $lat; ?>', '<?php echo $lon; ?>'], {
                  hintContent: 'Содержимое всплывающей подсказки',
                  balloonContent: 'Содержимое балуна'
              });
              myMap.geoObjects.add(myPlacemark);
          }
    </script>
   </head>
   <body>
     <form class="" action="index.php" method="post">
       <input type="text" name="adress" value="" placeholder="Адрес">
       <input type="submit" name="found" value="Найти">
     </form> <br>
     <table>
       <tbody>
         <tr>
           <th>адрес</th>
           <th>широта</th>
           <th>долгота</th>
         </tr>
         <tr>
           <?php foreach ($collection as $item)
           { ?>
           <td> <a href="index.php?adr=<?php echo $item->getAddress()?>&lat=<?php echo $item->getLatitude() ?>&lon=<?php echo $item->getLongitude() ?>"><?php echo $item->getAddress() . '<br>'; ?></a> </td>
           <td><?php echo $item->getLatitude() . '<br>'; ?></td>
           <td><?php echo $item->getLongitude() . '<br>';?></td>
         </tr>
          <?php } ?>
       </tbody>
     </table>
     <div id="map" style="width: 500px; height: 500px;"></div>
   </body>
 </html>
