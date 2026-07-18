<?php

if (!defined('ABSPATH')) {
    header("HTTP/1.1 403 Forbidden");
    exit("Direct access not allowed.");
}

$url = "https://sheets.googleapis.com/v4/spreadsheets/1H_nvVejVAC9HuzvFQOiruK-Z39tx3Xzi94kFAC69kbM/values/Keturunan!B3:E?key=AIzaSyAkjLLGuoaJ0IkFQTSlxsLH2mhI1Rl6kVc";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);

if (isset($data['values'])) {
    $removeFirst = array_slice($data['values'], 1);
    $fixedData = [];
    $totalColumns = 4;

    $rawData = [];
    foreach ($removeFirst as $row) {
        $paddedRow = array_pad($row, $totalColumns, "");
        // if (trim($paddedRow[0]) === "") {
        //     continue;
        // }
        $rawData[$paddedRow[0]] = [
            "id"        => $paddedRow[0] ?: '-',
            "pid"       => $paddedRow[3] ?: '-',
            "Nama"      => $paddedRow[1] ?: '-',
            "Deskripsi" => $paddedRow[2] ?: '-',
        ];
    }

    $getGenerasi = function ($id, $items, &$cache) use (&$getGenerasi) {
        if (isset($cache[$id])) return $cache[$id];
        $pid = $items[$id]['pid'];
        if ($pid === '-' || !isset($items[$pid])) {
            return $cache[$id] = 1;
        }
        return $cache[$id] = $getGenerasi($pid, $items, $cache) + 1;
    };

    $cache = [];
    foreach ($rawData as $id => $item) {
        $item['Generasi'] = $getGenerasi($id, $rawData, $cache);
        $fixedData[] = $item;
    }

    $data['values'] = $fixedData;
}
