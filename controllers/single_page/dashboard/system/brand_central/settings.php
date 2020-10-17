<?php

namespace Concrete\Package\BrandCentralConnector\Controller\SinglePage\Dashboard\System\BrandCentral;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{
    /** @var Repository */
    protected $config;
    /** @var Validation */
    protected $formValidator;

    public function on_start()
    {
        parent::on_start();

        $this->config = $this->app->make(Repository::class);
        $this->formValidator = $this->app->make(Validation::class);
    }

    public function view()
    {
        if ($this->request->getMethod() === "POST") {

            $this->formValidator->setData($this->request->request->all());

            $this->formValidator->addRequiredToken("save_brand_central_connector_settings");
            $this->formValidator->addRequired("endpoint");
            $this->formValidator->addRequired("clientId");
            $this->formValidator->addRequired("clientSecret");

            if ($this->formValidator->test()) {

                // @todo: check if the endpoint is reachable

                // @todo: check if the api credentials are valid

                // @todo: check if secret has changed only if yes save it

                $this->config->save("brand_central_connector.endpoint", $this->request->request->get("endpoint"));
                $this->config->save("brand_central_connector.client_id", $this->request->request->get("clientId"));
                $this->config->save("brand_central_connector.client_secret", $this->request->request->get("clientSecret"));

                $this->set("success", t("The settings has been updated successfully."));
            } else {
                $this->error = $this->formValidator->getError();
            }
        }

        // @todo: don't pass secret in plain text

        $this->set('endpoint', $this->config->get("brand_central_connector.endpoint"));
        $this->set('clientId', $this->config->get("brand_central_connector.client_id"));
        $this->set('clientSecret', $this->config->get("brand_central_connector.client_secret"));
    }
}

