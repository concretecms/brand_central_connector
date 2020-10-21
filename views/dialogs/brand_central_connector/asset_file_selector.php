<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete5\BrandCentralConnector\AssetDetails;

/** @var int $assetId */
/** @var int $externalFileProviderId */
/** @var AssetDetails $assetDetails */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
?>

<form method="post" action="<?php echo Url::to("/ccm/brand_central_connector/select_asset_file/submit"); ?>"
      data-dialog-form="select-asset-file">

    <?php echo $token->output('select_asset_file'); ?>

    <?php echo $form->hidden('externalFileProviderId', $externalFileProviderId); ?>

    <div class="row">
        <div class="col-sm-12">
            <h2>
                <?php echo $assetDetails->getTitle(); ?>
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo h($assetDetails->getThumbnailUrl()); ?>"
                 alt="<?php echo h($assetDetails->getTitle()); ?>"
                 class="ccm-asset-thumbnail">
        </div>

        <div class="col-md-6">
            <h3>
                <?php echo t("Description"); ?>
            </h3>

            <?php if ($assetDetails->getDescription()) { ?>
                <p>
                    <?php echo nl2br(h($assetDetails->getDescription())); ?>
                </p>
            <?php } else { ?>
                <p><?=t('None')?></p>
            <?php } ?>

            <h3 class="ccm-asset-files-title">
                <?php echo t("Choose File Type"); ?>
            </h3>

            <?php $i = 0; ?>

            <?php foreach ($assetDetails->getFiles() as $remoteFileId => $remoteFileName) { ?>
                <div class="form-check">
                    <?php echo $form->radio("remoteFileId", $remoteFileId, ($i === 0), ["class" => "form-check-input", "id" => "remoteFileId" . $i]); ?>
                    <?php echo $form->label("remoteFileId" . $i, $remoteFileName, ["class" => "form-check-label"]); ?>
                    <?php $i++; ?>
                </div>
            <?php } ?>

        </div>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-right">
            <?php echo t('Import') ?>
        </button>
    </div>
</form>

<style>
    .ccm-asset-thumbnail {
        width: 100%;
        height: auto;
        margin-bottom: 25px;
    }

    .ccm-asset-files-title {
        font-size: 1em !important;
        font-weight: bold !important;
    }
</style>