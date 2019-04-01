# shirtigo-api-php
A PHP implementation of a client for the shirtigo print API.
Check out our [REST-API documentation](https://cockpit.shirtigo.de/docs/rest-api/) for a full list of all features.

# Basic usage
## Client object initialization
```php
use Shirtigo\ApiClient\ApiClient;

$BASE_URL = "https://cockpit.shirtigo.de/api/";
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

## Add a processing area to a project
```php
$data = [
      'area' => 'front',
      'position' => 'center',
      'method' => 'print',
      'design' => $design->reference,
      'offset_top' => 300,
      'offset_center' => 10,
      'width' => 200,
];
$client->post("projects/{$project->reference}/processings", $data);
```

## Get a list of available base products to process (print)
```php
$base_products = $client->get('base-products')->data;
```

we select the last one for our test
```php
$base_product = end($base_products);

$colors = array_map(function($c) { return $c->id; }, $base_product->colors->data);
$test_sizes = array_map(function($c) { return $c->sizes[0]->id; }, $base_product->colors->data);
```

## Create a product
Product creation combines a base product, and a project.
Let's create a product with our design on the shirt front
```php
$data = [
  'project_id' => $project->reference,
  'base_product_id' => $base_product->id,
  'colors' => $colors,
];

$product = $client->post('products', $data);
```

## Publish finished project
 Finished projects need to be published before products can be ordered from a project
```php
$client->post("projects/{$project->reference}/publish");
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
