<?php

require __DIR__ . '/vendor/autoload.php';

use Shirtigo\ApiClient\ApiClient;

$BASE_URL = 'https://cockpit.local/api/';
$API_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQxZjQyNmM3NTJiOTZiNWNhOTYzYTc5N2I5YWM2MzkyNjE3Y2QyMzRhNmQ2MjZhOTAyYWY5OGY2MzRkNTZhZTJkOGQ2Y2FjNjNkOWNjNmNkIn0.eyJhdWQiOiIxIiwianRpIjoiZDFmNDI2Yzc1MmI5NmI1Y2E5NjNhNzk3YjlhYzYzOTI2MTdjZDIzNGE2ZDYyNmE5MDJhZjk4ZjYzNGQ1NmFlMmQ4ZDZjYWM2M2Q5Y2M2Y2QiLCJpYXQiOjE1MDkwMzE0NzAsIm5iZiI6MTUwOTAzMTQ3MCwiZXhwIjoxODI0NTY0MjcwLCJzdWIiOiIxIiwic2NvcGVzIjpbInJlYWQtdXNlciIsIndyaXRlLXVzZXIiLCJyZWFkLWRlc2lnbiIsIndyaXRlLWRlc2lnbiIsInJlYWQtcHJvamVjdCIsIndyaXRlLXByb2plY3QiLCJyZWFkLW9yZGVyIiwid3JpdGUtb3JkZXIiLCJyZWFkLXN0YXRzIl19.JM_4cZqq6qFX3NuRj1TA4ZyTPeLAqsozCO9ZUbaujeJuY5eDFeSPEtzQfhknlzXramzeWOqu4FuICowawwRKPRa0G3i_Uc_5lX3jvX7r-tsjee6g60lebFiI4NP-5471NEE53awLWNPP38iRZYpnU0g3DMYHXc0SMUUmNLpL3Ew4JkRcihCHFQbX9HLrAQkR0dXnowB0xE6rjr7j-toX5v7ryWnArJvLrwBuNu8DSu6nw2QUSGeVS3aQCVZGmRtZntJbb73Ivn9nHjLv7dd08zGgd5pWrxq3CHxP30iEyu04mLfHSJ-V6xQfAcH182tlhoXemQa7IEQCWnqh05GdoqA5yrQsZr4TKAembguxh4EES0Pscq-ISezFDg80G8j22xCMi0XjUT4tagvFyLX2yw5UQSiz7ExPpP8VFFYm_Drv_UFDldFolgKqyRwIFoirerADCZDrVZeFcJQ4Y1E0pvkfUmlgYTuGmAl-8d4Mh5w6l2u74NNeenPKDeyUIo7Yl0E9ORU6YkaWCMiZ19q02B34VbzMonRHaPtLgISKEJike7PKSLHhtYdlrIGJ14F_h8BNpV1atri-m6CfDKZZArthG58BO6Bsd0qL1IgJiePNP7L5Z2GVZq1dytJlXcYFGCYXN9Zz2m5zavyz3AfJM6KBAs3X2gW1xsW-Tn0Nagw';

$client = new ApiClient($API_TOKEN, $BASE_URL);

$base_products = $client->get('base-products')->data;
$base_product = end($base_products);

$colors = array_map(function($c) { return $c->id; }, $base_product->colors->data);
$test_sizes = array_map(function($c) { return $c->sizes[0]->id; }, $base_product->colors->data);
print_r($test_sizes);

/*$data = $client->get('user');
echo $data->firstname, $data->lastname;
echo "This token belongs to: {$data->firstname} {$data->lastname}.\n";*/

