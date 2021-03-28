<?php
include_once 'version.php';
if ($_GET['check'] == 'update') {
    $check_url = 'https://blog-1252410096.cos.ap-nanjing.myqcloud.com/freewind/update_info.json';
    ob_clean();
    try {
        $s = json_decode(get_file($check_url));
        $result = [];
        foreach ($s as $i) {
            if (strcmp($i->version, $version) > 0) {
                $result[] = [
                    "version" => $i->version,
                    "date" => $i->date,
                    "info" => $i->info
                ];
            }
        }
        if (empty($result)) {
            $res = [
                'updated' => false,
                'success' => true,
                'msg' => "当前版本已为最新版本，无需更新",
            ];
        } else {
            $res = [
                'updated' => true,
                'msg' => "当前版本为{$version}，最新版本为{$result[0]['version']}，更新时间为{$result[0]['date']}，是否更新至最新版本"
            ];
        }
    } catch (Exception $e) {
        $res = [
            'updated' => false,
            'success' => false,
            'msg' => "发生未知错误",
        ];
    }
    ob_clean();
    echo json_encode($res);
    exit();
}
if ($_GET['updated'] == 'true') {
    $file_list_url = "https://blog-1252410096.cos.ap-nanjing.myqcloud.com/freewind/file.json";
    $s = json_decode(get_file($file_list_url));
    ob_clean();
    $files = [];
    foreach ($s as $i) {
        if (strcmp($i->version, $version) > 0) {
            foreach ($i->files as $file) {
                $files[] = $file;
            }
        }
    }
    $files = array_unique($files);
    $base_url = "https://blog-1252410096.cos.ap-nanjing.myqcloud.com/freewind/freewind/";
    foreach ($files as $filename) {
        $url = $base_url . $filename;
        $content = get_file($url);
        $path = '../' . $filename;
        $dir = dirname($path);
        if (!is_dir($dir)) {
            var_dump(mkdir($dir, 0777, true));
        }
        $file = fopen($path, "w");
        fwrite($file, $content);
        fclose($file);
    }
    ob_clean();
    $res = [
        'code' => 2000,
        'msg' => "更新成功，请重新检查更新",
    ];
    echo json_encode($res);
    exit();
}
function get_file($url)
{
    $header['Content-Type'] = 'application/json;charset=UTF-8';
    $opts = array(
        CURLOPT_TIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => $header
    );
    $opts[CURLOPT_URL] = $url . '?' . http_build_query([]);
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    return curl_exec($ch);
}