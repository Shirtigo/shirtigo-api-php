# shirtigo-api-php
A PHP implementation of a client for the shirtigo print API.
Check out our [REST-API documentation](https://cockpit.shirtigo.com/docs/rest-api/) for a full list of all features.
Almost all features of the Shirtigo Cockpit can be accessed through our API, providing developers with a comprehensive interface to interact with and manage the platform's functionality programmatically. This enables seamless integration with various applications and platforms, ensuring a versatile and adaptable user experience.

In the following sections, we will showcase some examples that demonstrate the core functionality of the API, highlighting its key features and capabilities. These examples will provide a better understanding of how to interact with the API and leverage its potential to meet your specific requirements.

If you have any open questions or require further assistance, please don't hesitate to contact our technical support team at tech-support@shirtigo.de. Our experts will be more than happy to help you and provide guidance to ensure a smooth and efficient experience using our API.

# Basic usage
## Client object initialization
```php
use Shirtigo\ApiClient\ApiClient;

$BASE_URL = "https://cockpit.shirtigo.com/api/";
$API_TOKEN = "YOUR_API_TOKEN";

$client = new ApiClient($API_TOKEN, $BASE_URL);
```
## Access your shirtigo account data
```php
$data = $client->get('user');
echo "{$data->firstname} {$data->lastname}";
```


# Design
A "design" object represents a print motif that can be subsequently utilized for the creation of various products.
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

# BaseProducts
BaseProducts are blank products that can be combined with designs.
A list of our assortment can also be downloaded as xlsx file in the Cockpit dashboard: [Download Catalog](https://cockpit.shirtigo.com/app/baseproducts/xlsxfile)

## Get a list of all baseProducts
```php
$baseProducts = $client->get('base-products')->data;
```

# Create a product collection
Products are organized into collections, with each baseProduct being assignable to a collection/ project only once. To create a new product, first create a project, and then, in a subsequent step, assign the desired products to that project.
## Create a project
```php
$data = [
    'name' => "My test project " + random_string(),
];

$project = $client->post('projects', $data);
```

## Add a product to the project
In this example we will add a black Organic Shirt with a one-sided print to our project. The processing specifications define the design, its placement, dimensions, and other relevant details needed for customizing the BaseProduct. Please not, that you can add custom processings for each color.
```php
$data = [
    "project_id" => $project->reference,
    "base_product_id" => 235,
    "processings" => [
        [
            "processingarea_type" => "front",
            "processingposition" => "chest-center",
            "processingmethod" => "dtg",
            "design_reference" => $design->reference,
            "offset_top" => 50,
            "offset_center" => 0,
            "width" => 250,
            "is_customizable" => false,
            "force_position" => false,
            "colors" => [
                [
                    "colorId" => 326,
                    "price" => 2195,
                    "sortPosition" => 1
                ]
            ]
        ]
    ]
];

$product = $client->post('customized-product', $data);
```

# Create an order
Depending on your usecase there a different ways to place an order
- Order a existing product
- Order a non-existing product (e.g. each of your products is customized)

## Order an existing product from your account
In this szenario the sku for your existing product has the following pattern: productId + baseProductColorId + baseProductSizeId
```php
$data = [
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
    'products' => [
        'sku' => 'c123456.122.8',
        'amount' => 1
    ]
];

$order = $client->post('orders', $data);
```

## Order a non-existing product
In this scenario, the base_product_sku is utilized to specify the variant of the BaseProduct (e.g., STTU755C0021S for an Organic Shirt in Black, size S). Along with the SKU, it is necessary to provide the processing specifications for customization, which follow the same format as adding a product to a project.

```php
$data = [
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
    'products' => [
        'base_product_sku' => 'STTU755C0021S',
        'sales_price_gross' => '2199',
        'amount' => 1,
        'processings' => [
            [
                'design_url' => 'https://mydomain.com/myimage.png',
                'width' => 250,
                'height' => 350,
                'processingarea_type' => 'front',
                'processingposition' => 'chest-center',
                'processingmethod' => 'dtg',
                'offset_top' => 20,
                'offset_center' => 0,
                'force_position' => false,
            ],
        ],
    ],
];

$order = $client->post('orders', $data);

```


## Check the current price for a planned order/ cart
```php
$prices = $client->post('orders/predict-price', $order_data);
```
