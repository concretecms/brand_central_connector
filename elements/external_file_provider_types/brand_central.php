<?php

defined('C5_EXECUTE') or die('Access Denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete5\BrandCentralConnector\File\ExternalFileProvider\Configuration\BrandCentralConfiguration;

/** @var string $endpoint */
/** @var string $clientId */
/** @var string $clientSecret */
/** @var BrandCentralConfiguration $configuration */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<?php if (is_object($configuration)) { ?>
    <div class="form-group">
        <?php echo $form->label("endpoint", t("Endpoint")); ?>
        <?php echo $form->text('endpoint', $configuration->endpoint); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientId", t("Client Id")); ?>
        <?php echo $form->text('clientId', $configuration->clientId); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientSecret", t("Client Secret")); ?>
        <?php echo $form->password('clientSecret', $configuration->clientSecret); ?>
    </div>
<?php } ?>