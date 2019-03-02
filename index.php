<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Апдейт цен по адсетам из ФБ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="favicon.ico" /> 
</head>
<body>
<?php
$apikey='cb8ab98658dc1d7d2bf7418d52992d61';
$domain='http://webyellow.tk';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $domain.'http://webyellow.tk/admin_api/v1/campaigns');
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Api-Key: '.$apikey));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$campaigns=json_decode(curl_exec($ch));
?>
<br/>
<br/>
<center>
Выберите кампанию:
<form action="loadcosts.php" method="post" enctype="multipart/form-data">
    <select name="campaign">
    <?php
    foreach ($campaigns as $campaign) {
        echo "<option value=\"".$campaign->id."\">".$campaign->name."</option>\n";
    }
    ?>
    </select>
    <br/>
    <br/>
    <br/>
    Выберите csv файл с расходами:
    <br/>
    <input name="filename" type="file" accept=".csv"/>
    <br/>
    <br/>
    <br/>
    !!!При выгрузке отчёта в фб надо на вкладке
    <br/>
    "Группы объявлений" добавить столбец "Название кампании"!!!
    <br/>
    <button type="submit">Загрузить расходы</button>
</form>
</center>
</body>
</html>
