<?php
$apikey='cb8ab98658dc1d7d2bf7418d52992d61'; //ваш api ключ для доступа к API кейтаро
$domain='http://webyellow.tk/'; //домен на котором висит кейтаро
$timezone='Europe/Samara'; //ваша временная зона
$adset_subname='sub_id_2'; //название субметки в кейтаро, где будет хранится имя адсета
$campaign_subname='ad_campaign_id';//субметка названия кампании в кейтаро

//Поля из заголовка (1 строка) csv-отчёта фейсбук
$adsetname = '"Название группы объявлений"';
$spendname = '"Потраченная сумма (RUB)"';
$datename = '"Дата начала отчетности"';
//!!!При выгрузке отчёта в фб надо на вкладке "Группы объявлений" 
//добавить столбец с именем кампании!!!
$campaignname = '"Название кампании"'; 

//Загружаем полученный файл в переменную
//Разбиваем его по строкам и ищем в первой строке (заголовок) позиции нужных полей
$csv = file_get_contents($_FILES['filename']['tmp_name']);
$csvsplit = explode("\n", $csv);
$csvheader = explode(",", $csvsplit[0]);

$dateind = array_search($datename, $csvheader);
$adsetind = array_search($adsetname, $csvheader);
$spendind = array_search($spendname, $csvheader);
$campind = array_search($spendname, $csvheader);

if ($adsetind === false)
{
    echo "Не нашли в файле нужного поля".$adsetname.". Неправильный формат файла!";
    return;
}
else if($spendind === false) 
{
    echo "Не нашли в файле нужных полей ".$spendname.". Неправильный формат файла!";
    return;
}
else if($dateind === false) 
{
    echo "Не нашли в файле нужного поля ".$datename.". Неправильный формат файла!";
    return;
}
else if($campaignind === false) 
{
    echo "Не нашли в файле нужного поля ".$campaignname.". Неправильный формат файла!";
    return;
}

$keitaro_campaign = $_POST['campaign'];

for ($i = 1; $i < count($csvsplit); $i++) {
    if ($csvsplit[$i] == "") { continue; }
    $line = explode(",", $csvsplit[$i]);

    $adset = $line[$adsetind]; //Это ИМЯ нашей группы объявлений
    $curspend = $line[$spendind]; //Потраченное бабло
    $date=$line[$dateind]; //Дата затрат
    $campaign=$line[$campind]; //Название кампании

    if ($adset == "") { continue; }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Api-Key: '.$apikey));
    curl_setopt($ch, CURLOPT_URL, $domain.'admin_api/v1/campaigns/' . $keitaro_campaign . '/update_costs');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $params = [
        'start_date' => $date . ' 00:00',
        'end_date' => $date . ' 23:59',
        'cost' => $curspend,
        'timezone' => $timezone, 
        'filters' => [
            $adset_subname => str_replace('"', "", $adset),
            $campaign_subname => str_replace('"', "", $campaign)
        ]
    ];
    echo json_encode($params); //раскомментить для дебага передаваемых в API параметров
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    $rawres=curl_exec($ch);
    echo $rawres; //раскомментить для просмотра полученного ответа от Кейтаро
    $updateres= json_decode($rawres);
    if($updateres->success)
    {
        echo "Загрузили расходы в " . $curspend . "р для адсета " . $adset . " за " . $date . "<br/>";
    }
    else{
        echo "Не смогли загрузить расходы для адсета " . $adset;
    }
}
?>