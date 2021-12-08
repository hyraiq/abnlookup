hyraiq/abnlookup
================

A PHP SDK to validate Australian Business Numbers (ABNs) and verify them with the 
[Australian Business Register Web Services API](https://abr.business.gov.au/Tools/WebServices).
The difference between validation and verification can be outlined as follows:

- Validation uses the official checksum calculation to check that a given number is a valid ABN. This _does not_ contact
    the ABR to ensure that the given ABN is assigned to a business
- Verification contact the ABR through their API to retrieve information registered against the ABN. It will tell you
    if the ABN actually belongs to a business.

In order to use the API (only necessary for verification), you'll need to 
[register an account](https://abr.business.gov.au/Tools/WebServices) to receive a GUID which is used as an API key. 
Once you register you can play with the API using the [official demo](https://abr.business.gov.au/json/) 
(note that this SDK uses the JSON services instead of XML).


## Type safety

The SDK utilises the [Symfony Serializer](https://symfony.com/doc/current/components/serializer.html) and the
[Symfony Validator](https://symfony.com/doc/current/components/validator.html) to deserialize and validate data returned
from the ABR API in order to provide valid [AbnResponse](./src/Model/AbnResponse.php) and 
[NamesResponse](./src/Model/NamesResponse.php) models. This means that if you receive a response from the SDK, it is 
guaranteed to be valid. 

Invalid responses from the ABR fall into three categories, which are handled with exceptions:

- `AbrConnectionException`: Unable to connect to the ABR, or the ABR returned an unexpected response
- `InvalidAbnException`: The ABN is invalid (ie. validation failed)
- `AbnNotFoundException`: The ABN is valid, however it is not assigned to a business (ie. verification failed)


## Usage

### Installation

```shell
$ composer require hyraiq/abnlookup
```

### Configuration with Symfony
If you are using Symfony with `autowire: true`, you need to pass you ABR GUID to the `AbnClient` in `services.yaml`:

```yaml
Hyra\Integrations\AbnLookup\AbnClient:
    arguments:
        $abnLookupGuid: "%env(ABN_LOOKUP_GUID)%"
```

You can then inject the `AbnClientInterface` directly into your controllers/services.

```php
class VerifyAbnController extends AbtractController
{
    public function __construct(
        private AbnClientInterface $abnClient,
    ) {
    }
    
    // ...  
}
```

### Configuration outside of Symfony

If you're not using Symfony, you'll need to instantiate the ABN client yourself, which can be registered in your service
container or just used directly. We have provided some helpers in the `Dependencies` class in order to create the
Symfony Serializer and Validator with minimal options.

```php
use Hyra\AbnLookup\Dependencies;
use Hyra\AbnLookup\AbnClient;

$abrGuid = '<insert your ABR GUID here>'

// Whichever http client you choose
$httpClient = new HttpClient();

$denormalizer = Dependencies::serializer();
$validator = Dependencies::validator();

$abnClient = new AbnClient($denormalizer, $validator, $httpClient, $abrGuid);
```

### Looking up an ABN

Once you have configured your `AbnClient` you can lookup an individual ABN. Note, this will validate the ABN before
calling the API in order to prevent unnecessary API requests.

```php
$abn = '12620650553';

try {
    $abnResponse = $abnClient->lookupAbn($abn);
} catch (AbrConnectionException $e) {
    die($e->getMessage())
} catch (InvalidAbnException) {
    die('Invalid ABN');
} catch (AbnNotFoundException) {
    die('ABN not found');
}

echo $abnResponse->abn; // 12620650553
echo $abnResponse->entityName; // Blenktech PTY LTD
echo $abnResponse->status; // Active
```

### Searching by name

You can also search the ABR by name, to receive a list of registered businesses that match the search term:

```php
$namesResponse = $abnClient->lookupName('Hyra iQ');

echo \sprintf('Received %d results', \count($namesResponse->names));
foreach ($namesResponse->names as $name) {
    echo \sprintf('%s: %s', $name->abn, $name->name);
}
```

## Testing

In automated tests, you can replace the `AbnClient` with the `StubAbnClient` in order to mock responses from the ABR.
There is also the `AbnFaker` which you can use during tests to get both valid and invalid ABNs.

```php
use Hyra\AbnLookup\Stubs\AbnFaker;
use Hyra\AbnLookup\Stubs\StubAbnClient;

$stubClient = new StubAbnClient();

$stubClient->lookupAbn(AbnFaker::invalidAbn()); // InvalidAbnException - Note, the stub still uses the validator

$stubClient->lookupAbn(AbnFaker::validAbn()); // LogicException - You need to tell the stub how to respond to specific queries

$abn = AbnFaker::validAbn();
$stubClient->addNotFoundAbns($abn);
$stubClient->lookupAbn($abn); // AbnNotFoundException

$abn = AbnFaker::validAbn();
$mockResponse = new AbnResponse();
$response->abn = $abn;
$response->abnStatus = 'active';
$response->abnStatusEffectiveFrom = new \DateTimeImmutable('2 years ago');
$response->entityTypeCode = 'PRV';
$response->entityTypeName = 'Australian Private Company';

$stubClient->addMockResponse($mockResponse);
$abnResponse = $stubClient->lookupAbn($abn); // $abnResponse === $mockResponse
```


## Contributing

All contributions are welcome! You'll need [docker](https://docs.docker.com/engine/install/) installed in order to
run tests and CI processes locally. These will also be run against your pull request with any failures added as
GitHub annotations in the Files view.

```shell
# First build the required docker container
$ docker compose build

# Then you can install composer dependencies
$ docker compose run php ./composer.phar install

# Now you can run tests and other tools
$ docker compose run php make (fix|psalm|phpstan|phpunit)
```

In order for you PR to be accepted, it will need to be covered by tests and be accepted by:

- [php-cs-fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer)
- [psalm](https://github.com/vimeo/psalm/)
- [phpstan](https://github.com/phpstan/phpstan)
