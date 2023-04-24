# shirtigo-api-php
A PHP implementation of a client for the shirtigo print API.
Check out our [REST-API documentation](https://cockpit.shirtigo.com/docs/rest-api/) for a full list of all features.

# Basic usage
## Client object initialization
```php
use Shirtigo\ApiClient\ApiClient;

$BASE_URL = "https://cockpit.shirtigo.com/api/";
$API_TOKEN = "YOUR_API_TOKEN";

$client = new ApiClient($API_TOKEN, $BASE_URL);
```
## Access your shirtigo user data
```php
$data = $client->get('user');
echo "{$data->firstname} {$data->lastname}";
```

## Create a project
```php
$data = [
    'name' => "My test project " + random_string(),
];

$project = $client->post('projects', $data);
```

## Uploading a design
 The product creation reqires to upload a design first.
### From file:
```php
$design_path = './test_design.png';

$design = $client->post('designs/file', [], [], [
    'file' => fopen($design_path, 'r'),
]);
```
### From URL:
```php
$design = $client->post('designs/url', [
    'url' => "https:/my-web-storage/my-design.png",
]);
```

## Get a list of your existing designs
```php
$designs = $client->get('designs')->data;
```

## Order a list of products
```php
$products = [];
for ($i = 0; $i < count($colors); $i++) {
    $products[] = [
        'productId' => $product->id,
        'colorId' => $colors[$i],
        'sizeId' => $test_sizes[$i],
        'amount' => 1,
    ];
}

$order_data = [
    'delivery' => [
      'title' => 'Dr.',
      'company' => 'Shirtigo GmbH',
      'firstname' => 'Max',
      'lastname' => 'Mustermann',
      'street' => 'Musterstraße 12',
      'postcode' => '12345',
      'city' => 'Köln',
      'country' => 'Deutschland'
    ],
    'sender' => [
      'title' => 'Dr.',
      'company' => 'Shirtigo GmbH',
      'firstname' => 'Max',
      'lastname' => 'Mustermann',
      'street' => 'Musterstraße 12',
      'postcode' => '12345',
      'city' => 'Köln',
      'country' => 'Deutschland'
    ],
    'products' => $products,
];
```
## Check the current price for a planned order
```php
$prices = $client->post('orders/predict-price', $order_data);
```
## Post order request
```php
$order = $client->post('orders', $order_data);
```
