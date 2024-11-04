<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__) . '/sfDoctrineBaseTask.class.php');

/**
 * Create classes for the current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineBuildModelTask.class.php 30901 2010-09-13 17:41:16Z Kris.Wallsmith $
 */
class sfDoctrineBuildModelTask extends sfDoctrineBaseTask
{
    /**
     * @see sfTask
     */
    protected function configure()
    {
        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
        ]);

        $this->namespace = 'doctrine';
        $this->name = 'build-model';
        $this->briefDescription = 'Creates classes for the current model';

        $this->detailedDescription = <<<EOF
The [doctrine:build-model|INFO] task creates model classes from the schema:

  [./symfony doctrine:build-model|INFO]

The task read the schema information in [config/doctrine/*.yml|COMMENT]
from the project and all enabled plugins.

The model classes files are created in [lib/model/doctrine|COMMENT].

This task never overrides custom classes in [lib/model/doctrine|COMMENT].
It only replaces files in [lib/model/doctrine/base|COMMENT].
EOF;
    }

    /**
     * @see sfTask
     */
    protected function execute($arguments = [], $options = [])
    {
        $this->logSection('doctrine', 'generating model classes');

        $config = $this->getCliConfig();
        $builderOptions = $this->configuration->getPluginConfiguration('sfDoctrinePlugin')->getModelBuilderOptions();

        $stubFinder = sfFinder::type('file')->prune('base')->name('*' . $builderOptions['suffix']);
        $before = $stubFinder->in($config['models_path']);

        $schema = $this->prepareSchemaFile($config['yaml_schema_path']);

        $import = new Doctrine_Import_Schema();
        $import->setOptions($builderOptions);
        $import->importSchema($schema, 'yml', $config['models_path']);

        $modelPackagePaths = [];

        // markup base classes with magic methods
        foreach (sfYaml::load($schema) as $model => $definition) {
            $packagePath = isset($definition['package']) ? '/' . substr($definition['package'], 0, strpos($definition['package'], '.')) : '';
            $modelPackagePaths[$model] = $packagePath;

            if (isset($definition['concrete_accessors']) && $definition['concrete_accessors']) {
                continue;
            }

            $file = sprintf('%s%s/%s/Base%s%s', $config['models_path'], $packagePath, $builderOptions['baseClassesDirectory'], $model, $builderOptions['suffix']);
            $code = file_get_contents($file);

            // introspect the model without loading the class
            if (preg_match_all('/@property (\w+) \$(\w+)/', $code, $matches, PREG_SET_ORDER)) {
                $properties = [];
                foreach ($matches as $match) {
                    $properties[$match[2]] = $match[1];
                }

                $typePad = max(array_map('strlen', array_merge(array_values($properties), [$model])));
                $namePad = max(array_map('strlen', array_keys(array_map(['sfInflector', 'camelize'], $properties))));
                $setters = [];
                $getters = [];

                foreach ($properties as $name => $type) {
                    $camelized = sfInflector::camelize($name);
                    $collection = 'Doctrine_Collection' == $type;

                    $getters[] = sprintf('@method %-' . $typePad . 's %s%-' . ($namePad + 2) . 's Returns the current record\'s "%s" %s', $type, 'get', $camelized . '($load = true)', $name, $collection ? 'collection' : 'value');
                    $setters[] = sprintf('@method %-' . $typePad . 's %s%-' . ($namePad + 2) . 's Sets the current record\'s "%s" %s', $model, 'set', $camelized . '($value, $load = true)', $name, $collection ? 'collection' : 'value');
                }

                // use the last match as a search string
                $code = str_replace($match[0], $match[0] . PHP_EOL . ' *' . PHP_EOL . ' * ' . implode(PHP_EOL . ' * ', array_merge($getters, $setters)), $code);
                file_put_contents($file, $code);
            }
        }

        $properties = parse_ini_file(sfConfig::get('sf_config_dir') . '/properties.ini', true);
        $tokens = [
            '##PACKAGE##'    => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
            '##SUBPACKAGE##' => 'model',
            '##NAME##'       => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
            ' <##EMAIL##>'   => '',
            "{\n\n}"         => "{\n}\n",
        ];

        // Force Record Generator for Versionable
        $this->runVersionableGenerator($modelPackagePaths, $config['models_path'], $builderOptions);

        // cleanup new stub classes
        $after = $stubFinder->in($config['models_path']);
        $this->getFilesystem()->replaceTokens(array_diff($after, $before), '', '', $tokens);

        // cleanup base classes
        $baseFinder = sfFinder::type('file')->name('Base*' . $builderOptions['suffix']);
        $baseDirFinder = sfFinder::type('dir')->name('base');
        $this->getFilesystem()->replaceTokens($baseFinder->in($baseDirFinder->in($config['models_path'])), '', '', $tokens);
    }

    /**
     * Always generate classes for Versionable classes, instead of on-demand with eval. Configure the generator with paths and options.
     *
     * @param string[] $modelPackagePaths
     * @param string $modelsPath
     * @param array  $builderOptions
     */
    protected function runVersionableGenerator(array $modelPackagePaths, string $modelsPath, array $builderOptions): void
    {
        // setup connection
        $databaseManager = new sfDatabaseManager($this->configuration);

        // clean out the tables, so we start as fresh as possible.
        \Doctrine_Manager::connection()->evictTables();
        // load all models again
        \Doctrine_Core::loadModels($modelsPath);

        $models = \Doctrine_Core::getLoadedModels();

        foreach ($models as $model) {
            $table = \Doctrine_Core::getTable($model);

            // Versionable uses the AuditLog generator
            if (!$table->hasGenerator(Doctrine_AuditLog::class) || $table->isGenerator()) {
                continue;
            }

            $this->logSection('Versionable', sprintf('Configuring Generator for "%s"', $model));

            if (!isset($modelPackagePaths[$model])) {
                // for some reason a model exists that is not in the schema
                throw new \RuntimeException(sprintf('Missing Package Path for "%s"', $model));
            }

            $generatePath = $modelsPath . $modelPackagePaths[$model];

            $generator = $table->getGenerator(Doctrine_AuditLog::class);
            $generator->setOption('generateFiles', true);
            $generator->setOption('generatePath', $generatePath);
            $generator->setOption('builderOptions', $builderOptions);

            $this->logSection('Versionable', sprintf('Running Generator for "%s"', $model));
            $this->logSection('Versionable', sprintf('  to create "%s"', $generator->getTable()->getComponentName()));
            $this->logSection('Versionable', sprintf('  with path "%s"', $generatePath));

            // copied from \Doctrine_Record_Generator::initialize
            $generator->buildTable();

            $fk = $generator->buildForeignKeys($table);

            $generator->getTable()->setColumns($fk);

            $generator->buildRelation();

            $generator->setTableDefinition();
            $generator->setUp();

            $generator->generateClassFromTable($generator->getTable());
        }
    }
}
