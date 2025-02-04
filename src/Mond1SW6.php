<?php

declare(strict_types=1);

namespace Mondu\MonduPayment;

use Mondu\MonduPayment\Bootstrap\PaymentMethods;
use Mondu\MonduPayment\Bootstrap\Database;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;

class Mond1SW6 extends Plugin
{
    public function install(InstallContext $installContext): void
    {
        $bootstrapper = $this->getBootstrapClasses($installContext);

        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->preInstall();
        }
        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->install();
        }
        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->postInstall();
        }

        parent::install($installContext); // TODO: Change the autogenerated stub
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $bootstrapper = $this->getBootstrapClasses($activateContext);

        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->activate();
        }
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);

        $bootstrapper = $this->getBootstrapClasses($updateContext);

        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->setUpdateContext($updateContext);
            $bootstrap->update();
        }

    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        $bootstrapper = $this->getBootstrapClasses($uninstallContext);

        $keepUserData = $uninstallContext->keepUserData();

        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->uninstall($keepUserData);
        }

        if (!$keepUserData) {
            $this->removeMigrations();
        }
    }

    protected function getBootstrapClasses(InstallContext $installContext)
    {
        $bootstrapper = [
            new PaymentMethods(),
            new Database()
        ];

        $pluginRepository = $this->container->get('plugin.repository');
        $plugins = $pluginRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('baseClass', get_class($this))),
            $installContext->getContext()
        );

        $plugin = $plugins->first();

        foreach ($bootstrapper as $bootstrap) {
            $bootstrap->setContext($installContext->getContext());
            $bootstrap->setInstallContext($installContext);
            $bootstrap->setContainer($this->container);
            $bootstrap->injectServices();
            $bootstrap->setPlugin($plugin);
        }

        return $bootstrapper;
    }
}
