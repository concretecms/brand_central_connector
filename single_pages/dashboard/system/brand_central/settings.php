<?php

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

/** @var string $endpoint */
/** @var string $clientId */
/** @var string $clientSecret */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<form action="#" method="post">
    <?php echo $token->output("save_brand_central_connector_settings"); ?>

    <div class="form-group">
        <?php echo $form->label("endpoint", t("Endpoint")); ?>
        <?php echo $form->text('endpoint', $endpoint); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientId", t("Client Id")); ?>
        <?php echo $form->text('clientId', $clientId); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("clientSecret", t("Client Secret")); ?>
        <?php echo $form->password('clientSecret', $clientSecret); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-right">
                <button type="submit" class="btn btn-primary">
                    <?php echo t("Save"); ?>
                </button>
            </div>
        </div>
    </div>
</form>