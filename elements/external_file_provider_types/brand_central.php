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
        <?php echo $form->label("endpoint", t("BrandCentral Site URL")); ?>
        <?php echo $form->text('endpoint', $configuration->endpoint); ?>

        <div class="help-block">
            <?php echo t("Enter the URL of your BrandCentral site, with a trailing slash. For example: https://www.mybrandcentralsite.com/"); ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientId", t("Client Id")); ?>
        <?php echo $form->text('clientId', $configuration->clientId); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientSecret", t("Client Secret")); ?>
        <?php echo $form->password('clientSecret', $configuration->clientSecret); ?>

        <div class="help-block">
            <?php echo t("Generate a Client ID and a Client Secret from the API Settings page in your BrandCentral page. Copy the generated values from that external site and paste them into here."); ?>
        </div>
    </div>
<?php } ?>