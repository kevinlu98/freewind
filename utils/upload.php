<?php
// 允许上传的图片后缀
$allowedExts = array("gif", "jpeg", "jpg", "png");
$temp = explode(".", $_FILES["file"]["name"]);
echo $_FILES["file"]["size"];
$extension = end($temp);     // 获取文件后缀名
if ((($_FILES["file"]["type"] == "image/gif")
        || ($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/jpg")
        || ($_FILES["file"]["type"] == "image/pjpeg")
        || ($_FILES["file"]["type"] == "image/x-png")
        || ($_FILES["file"]["type"] == "image/png"))
    && ($_FILES["file"]["size"] < 2 * 1024 * 1024)
    && in_array($extension, $allowedExts)) {
    $res = [];
    if ($_FILES["file"]["error"] > 0) {
        $res = [
            'errno' => 1,
        ];
    } else {
        $arr = explode(".", $_FILES["file"]["name"]);
        $hz = $arr[count($arr) - 1];
        $name = gmmktime() . '.' . $hz;
        $filename = '../static/upload/' . date('Ymd') . '/' . $name;
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
        $res = [
            'errno' => 0,
            'data' => [
                [
                    'url' => 'static/upload/' . date('Ymd') . '/' . $name,
                    'alt' => "",
                    'href' => ''
                ]
            ]
        ];

    }
} else {
    $res = [
        'errno' => 1,
    ];
}

ob_clean();
echo json_encode($res);
exit();