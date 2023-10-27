<?php

class ProjectConfiguration extends sfProjectConfiguration
{
    public function setup()
    {
        $this->enableAllPluginsExcept();
    }

    public function getAllPluginPaths(): array
    {
        $pluginPaths = parent::getAllPluginPaths();

        $pluginPaths['sfDoctrinePlugin'] = dirname(__DIR__, 5) . '/sfDoctrinePlugin';

        return $pluginPaths;
    }

    public function initializeDoctrine(): void
    {
        chdir(sfConfig::get('sf_root_dir'));

        $task = new sfDoctrineBuildTask($this->dispatcher, new sfFormatter());
        $task->setConfiguration($this);
        $task->run(
            [],
            [
                'no-confirmation' => true,
                'db' => true,
                'model' => true,
                'forms' => true,
                'filters' => true,
            ]
        );
    }

    public function loadFixtures($fixtures): void
    {
        $path = sfConfig::get('sf_data_dir') . '/' . $fixtures;
        if (!file_exists($path)) {
            throw new sfException('Invalid data fixtures file');
        }
        chdir(sfConfig::get('sf_root_dir'));
        $task = new sfDoctrineDataLoadTask($this->dispatcher, new sfFormatter());
        $task->setConfiguration($this);
        $task->run(array($path));
    }

    public function configureDoctrine(Doctrine_Manager $manager): void
    {
        $manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, true);

        $options = ['baseClassName' => 'sfDoctrineRecord'];
        sfConfig::set('doctrine_model_builder_options', $options);
    }

    public function configureDoctrineConnection(Doctrine_Connection $connection): void
    {
    }

    public function configureDoctrineConnectionDoctrine2(Doctrine_Connection $connection): void
    {
        $connection->setAttribute(Doctrine_Core::ATTR_VALIDATE, false);
    }
}
