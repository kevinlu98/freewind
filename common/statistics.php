<?php

ob_clean();
$dateArray = create_date_array();



$result = [
    'article' => article_count($dateArray),
    'category' => metas_count(),
    'tag' => metas_count('tag'),
];
echo json_encode($result);
// 当然我们也可以
exit();