<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Model {

    use Magento\Backend\Setup\ConfigOptionsList;
    use Magento\Framework\App\Area;
    use Magento\Framework\App\Cache\Manager;
    use Magento\Framework\App\DeploymentConfig;
    use Magento\Framework\App\DeploymentConfig\Reader;
    use Magento\Framework\App\DeploymentConfig\Writer;
    use Magento\Framework\App\Filesystem\DirectoryList;
    use Magento\Framework\App\MaintenanceMode;
    use Magento\Framework\App\ResourceConnection;
    use Magento\Framework\App\State\CleanupFiles;
    use Magento\Framework\Component\ComponentRegistrar;
    use Magento\Framework\Config\ConfigOptionsListConstants;
    use Magento\Framework\Config\File\ConfigFilePool;
    use Magento\Framework\DB\Adapter\AdapterInterface;
    use Magento\Framework\DB\Ddl\Table;
    use Magento\Framework\Filesystem;
    use Magento\Framework\Filesystem\Directory\WriteInterface;
    use Magento\Framework\Filesystem\DriverPool;
    use Magento\Framework\Math\Random;
    use Magento\Framework\Model\ResourceModel\Db\Context;
    use Magento\Framework\Module\ModuleList\Loader;
    use Magento\Framework\Module\ModuleListInterface;
    use Magento\Framework\ObjectManagerInterface;
    use Magento\Framework\Registry;
    use Magento\Framework\Setup\FilePermissions;
    use Magento\Framework\Setup\LoggerInterface;
    use Magento\Framework\Setup\Patch\PatchApplier;
    use Magento\Framework\Setup\Patch\PatchApplierFactory;
    use Magento\Framework\Setup\SampleData\State;
    use Magento\Framework\Setup\SchemaListener;
    use Magento\Setup\Controller\ResponseTypeInterface;
    use Magento\Setup\Model\AdminAccount;
    use Magento\Setup\Model\AdminAccountFactory;
    use Magento\Setup\Model\ConfigModel;
    use Magento\Setup\Model\DeclarationInstaller;
    use Magento\Setup\Model\Installer;
    use Magento\Setup\Model\ObjectManagerProvider;
    use Magento\Setup\Model\PhpReadinessCheck;
    use Magento\Setup\Module\ConnectionFactory;
    use Magento\Setup\Module\DataSetup;
    use Magento\Setup\Module\DataSetupFactory;
    use Magento\Setup\Module\Setup;
    use Magento\Setup\Module\SetupFactory;
    use Magento\Setup\Validator\DbValidator;
    use PHPUnit\Framework\MockObject\MockObject;
    use PHPUnit\Framework\TestCase;
    use Magento\Setup\Model\SearchConfig;

    /**
     * @SuppressWarnings(PHPMD.TooManyFields)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    class InstallerTest extends TestCase
    {
        /**
         * @var \Magento\Setup\Model\Installer
         */
        private $object;

        /**
         * @var FilePermissions|MockObject
         */
        private $filePermissions;

        /**
         * @var Writer|MockObject
         */
        private $configWriter;

        /**
         * @var Reader|MockObject
         */
        private $configReader;

        /**
         * @var DeploymentConfig|MockObject
         */
        private $config;

        /**
         * @var ModuleListInterface|MockObject
         */
        private $moduleList;

        /**
         * @var Loader|MockObject
         */
        private $moduleLoader;

        /**
         * @var DirectoryList|MockObject
         */
        private $directoryList;

        /**
         * @var AdminAccountFactory|MockObject
         */
        private $adminFactory;

        /**
         * @var LoggerInterface|MockObject
         */
        private $logger;

        /**
         * @var Random|MockObject
         */
        private $random;

        /**
         * @var MockObject
         */
        private $connection;

        /**
         * @var MaintenanceMode|MockObject
         */
        private $maintenanceMode;

        /**
         * @var Filesystem|MockObject
         */
        private $filesystem;

        /**
         * @var MockObject
         */
        private $objectManager;

        /**
         * @var ConfigModel|MockObject
         */
        private $configModel;

        /**
         * @var CleanupFiles|MockObject
         */
        private $cleanupFiles;

        /**
         * @var DbValidator|MockObject
         */
        private $dbValidator;

        /**
         * @var SetupFactory|MockObject
         */
        private $setupFactory;

        /**
         * @var DataSetupFactory|MockObject
         */
        private $dataSetupFactory;

        /**
         * @var State|MockObject
         */
        private $sampleDataState;

        /**
         * @var ComponentRegistrar|MockObject
         */
        private $componentRegistrar;

        /**
         * @var MockObject|PhpReadinessCheck
         */
        private $phpReadinessCheck;

        /**
         * @var \Magento\Framework\Setup\DeclarationInstaller|MockObject
         */
        private $declarationInstallerMock;

        /**
         * @var SchemaListener|MockObject
         */
        private $schemaListenerMock;

        /**
         * Sample DB configuration segment
         * @var array
         */
        private static $dbConfig = [
            'default' => [
                ConfigOptionsListConstants::KEY_HOST => '127.0.0.1',
                ConfigOptionsListConstants::KEY_NAME => 'magento',
                ConfigOptionsListConstants::KEY_USER => 'magento',
                ConfigOptionsListConstants::KEY_PASSWORD => '',
            ]
        ];

        /**
         * @var Context|MockObject
         */
        private $contextMock;

        /**
         * @var PatchApplier|MockObject
         */
        private $patchApplierMock;

        /**
         * @var PatchApplierFactory|MockObject
         */
        private $patchApplierFactoryMock;

        protected function setUp(): void
        {
            $this->filePermissions = $this->createMock(FilePermissions::class);
            $this->configWriter = $this->createMock(Writer::class);
            $this->configReader = $this->createMock(Reader::class);
            $this->config = $this->createMock(DeploymentConfig::class);

            $this->moduleList = $this->getMockForAbstractClass(ModuleListInterface::class);
            $this->moduleList->expects($this->any())->method('getOne')->willReturn(
                ['setup_version' => '2.0.0']
            );
            $this->moduleList->expects($this->any())->method('getNames')->willReturn(
                ['Foo_One', 'Bar_Two']
            );
            $this->moduleLoader = $this->createMock(Loader::class);
            $this->directoryList =
                $this->createMock(DirectoryList::class);
            $this->adminFactory = $this->createMock(AdminAccountFactory::class);
            $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
            $this->random = $this->createMock(Random::class);
            $this->connection = $this->getMockForAbstractClass(AdapterInterface::class);
            $this->maintenanceMode = $this->createMock(MaintenanceMode::class);
            $this->filesystem = $this->createMock(Filesystem::class);
            $this->objectManager = $this->getMockForAbstractClass(ObjectManagerInterface::class);
            $this->contextMock =
                $this->createMock(Context::class);
            $this->configModel = $this->createMock(ConfigModel::class);
            $this->cleanupFiles = $this->createMock(CleanupFiles::class);
            $this->dbValidator = $this->createMock(DbValidator::class);
            $this->setupFactory = $this->createMock(SetupFactory::class);
            $this->dataSetupFactory = $this->createMock(DataSetupFactory::class);
            $this->sampleDataState = $this->createMock(State::class);
            $this->componentRegistrar =
                $this->createMock(ComponentRegistrar::class);
            $this->phpReadinessCheck = $this->createMock(PhpReadinessCheck::class);
            $this->declarationInstallerMock = $this->createMock(DeclarationInstaller::class);
            $this->schemaListenerMock = $this->createMock(SchemaListener::class);
            $this->patchApplierFactoryMock = $this->createMock(PatchApplierFactory::class);
            $this->patchApplierMock = $this->createMock(PatchApplier::class);
            $this->patchApplierFactoryMock->expects($this->any())->method('create')->willReturn(
                $this->patchApplierMock
            );
            $this->object = $this->createObject();
        }

        /**
         * Instantiates the object with mocks
         * @param MockObject|bool $connectionFactory
         * @param MockObject|bool $objectManagerProvider
         * @return Installer
         */
        private function createObject($connectionFactory = false, $objectManagerProvider = false)
        {
            if (!$connectionFactory) {
                $connectionFactory = $this->createMock(ConnectionFactory::class);
                $connectionFactory->expects($this->any())->method('create')->willReturn($this->connection);
            }
            if (!$objectManagerProvider) {
                $objectManagerProvider =
                    $this->createMock(ObjectManagerProvider::class);
                $objectManagerProvider->expects($this->any())->method('get')->willReturn($this->objectManager);
            }

            return new Installer(
                $this->filePermissions,
                $this->configWriter,
                $this->configReader,
                $this->config,
                $this->moduleList,
                $this->moduleLoader,
                $this->adminFactory,
                $this->logger,
                $connectionFactory,
                $this->maintenanceMode,
                $this->filesystem,
                $objectManagerProvider,
                $this->contextMock,
                $this->configModel,
                $this->cleanupFiles,
                $this->dbValidator,
                $this->setupFactory,
                $this->dataSetupFactory,
                $this->sampleDataState,
                $this->componentRegistrar,
                $this->phpReadinessCheck
            );
        }

        /**
         * @param array $request
         * @param array $logMessages
         * @dataProvider installDataProvider
         * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
         */
        public function testInstall(array $request, array $logMessages)
        {
            $this->config->expects($this->atLeastOnce())
                ->method('get')
                ->willReturnMap(
                    [
                        [ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT, null, true],
                        [ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY, null, true],
                        ['modules/Magento_User', null, '1']
                    ]
                );
            $allModules = ['Foo_One' => [], 'Bar_Two' => []];

            $this->declarationInstallerMock->expects($this->once())->method('installSchema');
            $this->moduleLoader->expects($this->any())->method('load')->willReturn($allModules);
            $setup = $this->createMock(Setup::class);
            $table = $this->createMock(Table::class);
            $connection = $this->getMockBuilder(AdapterInterface::class)
                ->setMethods(['getSchemaListener', 'newTable'])
                ->getMockForAbstractClass();
            $connection->expects($this->any())->method('getSchemaListener')->willReturn($this->schemaListenerMock);
            $setup->expects($this->any())->method('getConnection')->willReturn($connection);
            $table->expects($this->any())->method('addColumn')->willReturn($table);
            $table->expects($this->any())->method('setComment')->willReturn($table);
            $table->expects($this->any())->method('addIndex')->willReturn($table);
            $connection->expects($this->any())->method('newTable')->willReturn($table);
            $resource = $this->createMock(ResourceConnection::class);
            $this->contextMock->expects($this->any())->method('getResources')->willReturn($resource);
            $resource->expects($this->any())->method('getConnection')->willReturn($connection);
            $dataSetup = $this->createMock(DataSetup::class);
            $dataSetup->expects($this->any())->method('getConnection')->willReturn($connection);
            $cacheManager = $this->createMock(Manager::class);
            $cacheManager->expects($this->any())->method('getAvailableTypes')->willReturn(['foo', 'bar']);
            $cacheManager->expects($this->exactly(3))->method('setEnabled')->willReturn(['foo', 'bar']);
            $cacheManager->expects($this->exactly(3))->method('clean');
            $cacheManager->expects($this->exactly(3))->method('getStatus')->willReturn(['foo' => 1, 'bar' => 1]);
            $appState = $this->getMockBuilder(\Magento\Framework\App\State::class)
                ->disableOriginalConstructor()
                ->disableArgumentCloning()
                ->getMock();
            $appState->expects($this->once())
                ->method('setAreaCode')
                ->with(Area::AREA_GLOBAL);
            $registry = $this->createMock(Registry::class);
            $searchConfigMock = $this->getMockBuilder(SearchConfig::class)->disableOriginalConstructor()->getMock();
            $this->setupFactory->expects($this->atLeastOnce())->method('create')->with($resource)->willReturn($setup);
            $this->dataSetupFactory->expects($this->atLeastOnce())->method('create')->willReturn($dataSetup);
            $this->objectManager->expects($this->any())
                ->method('create')
                ->willReturnMap([
                    [Manager::class, [], $cacheManager],
                    [\Magento\Framework\App\State::class, [], $appState],
                    [
                        PatchApplierFactory::class,
                        ['objectManager' => $this->objectManager],
                        $this->patchApplierFactoryMock
                    ],
                ]);
            $this->patchApplierMock->expects($this->exactly(2))->method('applySchemaPatch')->willReturnMap(
                [
                    ['Bar_Two'],
                    ['Foo_One'],
                ]
            );
            $this->patchApplierMock->expects($this->exactly(2))->method('applyDataPatch')->willReturnMap(
                [
                    ['Bar_Two'],
                    ['Foo_One'],
                ]
            );
            $this->objectManager->expects($this->any())
                ->method('get')
                ->willReturnMap([
                    [\Magento\Framework\App\State::class, $appState],
                    [Manager::class, $cacheManager],
                    [DeclarationInstaller::class, $this->declarationInstallerMock],
                    [Registry::class, $registry],
                    [SearchConfig::class, $searchConfigMock]
                ]);
            $this->adminFactory->expects($this->any())->method('create')->willReturn(
                $this->createMock(AdminAccount::class)
            );
            $this->sampleDataState->expects($this->once())->method('hasError')->willReturn(true);
            $this->phpReadinessCheck->expects($this->once())->method('checkPhpExtensions')->willReturn(
                ['responseType' => ResponseTypeInterface::RESPONSE_TYPE_SUCCESS]
            );
            $this->filePermissions->expects($this->any())
                ->method('getMissingWritablePathsForInstallation')
                ->willReturn([]);
            $this->filePermissions->expects($this->once())
                ->method('getMissingWritableDirectoriesForDbUpgrade')
                ->willReturn([]);
            call_user_func_array(
                [
                    $this->logger->expects($this->exactly(count($logMessages)))->method('log'),
                    'withConsecutive'
                ],
                $logMessages
            );
            $this->logger->expects($this->exactly(2))
                ->method('logSuccess')
                ->withConsecutive(
                    ['Magento installation complete.'],
                    ['Magento Admin URI: /']
                );

            $this->object->install($request);
        }

        /**
         * @return array
         * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
         */
        public function installDataProvider()
        {
            return [
                [
                    'request' => [
                        ConfigOptionsListConstants::INPUT_KEY_DB_HOST => '127.0.0.1',
                        ConfigOptionsListConstants::INPUT_KEY_DB_NAME => 'magento',
                        ConfigOptionsListConstants::INPUT_KEY_DB_USER => 'magento',
                        ConfigOptionsListConstants::INPUT_KEY_ENCRYPTION_KEY => 'encryption_key',
                        ConfigOptionsList::INPUT_KEY_BACKEND_FRONTNAME => 'backend',
                    ],
                    'logMessages' => [
                        ['Starting Magento installation:'],
                        ['File permissions check...'],
                        ['Required extensions check...'],
                        ['Enabling Maintenance Mode...'],
                        ['Installing deployment configuration...'],
                        ['Installing database schema:'],
                        ['Schema creation/updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Schema post-updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Installing search configuration...'],
                        ['Installing user configuration...'],
                        ['Enabling caches:'],
                        ['Current status:'],
                        [print_r(['foo' => 1, 'bar' => 1], true)],
                        ['Installing data...'],
                        ['Data install/update:'],
                        ['Disabling caches:'],
                        ['Current status:'],
                        [print_r([], true)],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Data post-updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Enabling caches:'],
                        ['Current status:'],
                        [print_r([], true)],
                        ['Caches clearing:'],
                        ['Cache cleared successfully'],
                        ['Disabling Maintenance Mode:'],
                        ['Post installation file permissions check...'],
                        ['Write installation date...'],
                        ['Sample Data is installed with errors. See log file for details']
                    ],
                ],
                [
                    'request' => [
                        ConfigOptionsListConstants::INPUT_KEY_DB_HOST => '127.0.0.1',
                        ConfigOptionsListConstants::INPUT_KEY_DB_NAME => 'magento',
                        ConfigOptionsListConstants::INPUT_KEY_DB_USER => 'magento',
                        ConfigOptionsListConstants::INPUT_KEY_ENCRYPTION_KEY => 'encryption_key',
                        ConfigOptionsList::INPUT_KEY_BACKEND_FRONTNAME => 'backend',
                        AdminAccount::KEY_USER => 'admin',
                        AdminAccount::KEY_PASSWORD => '123',
                        AdminAccount::KEY_EMAIL => 'admin@example.com',
                        AdminAccount::KEY_FIRST_NAME => 'John',
                        AdminAccount::KEY_LAST_NAME => 'Doe',
                    ],
                    'logMessages' => [
                        ['Starting Magento installation:'],
                        ['File permissions check...'],
                        ['Required extensions check...'],
                        ['Enabling Maintenance Mode...'],
                        ['Installing deployment configuration...'],
                        ['Installing database schema:'],
                        ['Schema creation/updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Schema post-updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Installing search configuration...'],
                        ['Installing user configuration...'],
                        ['Enabling caches:'],
                        ['Current status:'],
                        [print_r(['foo' => 1, 'bar' => 1], true)],
                        ['Installing data...'],
                        ['Data install/update:'],
                        ['Disabling caches:'],
                        ['Current status:'],
                        [print_r([], true)],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Data post-updates:'],
                        ['Module \'Foo_One\':'],
                        ['Module \'Bar_Two\':'],
                        ['Enabling caches:'],
                        ['Current status:'],
                        [print_r([], true)],
                        ['Installing admin user...'],
                        ['Caches clearing:'],
                        ['Cache cleared successfully'],
                        ['Disabling Maintenance Mode:'],
                        ['Post installation file permissions check...'],
                        ['Write installation date...'],
                        ['Sample Data is installed with errors. See log file for details']
                    ],
                ],
            ];
        }

        public function testCheckInstallationFilePermissions()
        {
            $this->filePermissions
                ->expects($this->once())
                ->method('getMissingWritablePathsForInstallation')
                ->willReturn([]);
            $this->object->checkInstallationFilePermissions();
        }

        public function testCheckInstallationFilePermissionsError()
        {
            $this->expectException('Exception');
            $this->expectExceptionMessage('Missing write permissions to the following paths:');
            $this->filePermissions
                ->expects($this->once())
                ->method('getMissingWritablePathsForInstallation')
                ->willReturn(['foo', 'bar']);
            $this->object->checkInstallationFilePermissions();
        }

        public function testCheckExtensions()
        {
            $this->phpReadinessCheck->expects($this->once())->method('checkPhpExtensions')->willReturn(
                ['responseType' => ResponseTypeInterface::RESPONSE_TYPE_SUCCESS]
            );
            $this->object->checkExtensions();
        }

        public function testCheckExtensionsError()
        {
            $this->expectException('Exception');
            $this->expectExceptionMessage('Missing following extensions: \'foo\'');
            $this->phpReadinessCheck->expects($this->once())->method('checkPhpExtensions')->willReturn(
                [
                    'responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR,
                    'data' => ['required' => ['foo', 'bar'], 'missing' => ['foo']]
                ]
            );
            $this->object->checkExtensions();
        }

        public function testCheckApplicationFilePermissions()
        {
            $this->filePermissions
                ->expects($this->once())
                ->method('getUnnecessaryWritableDirectoriesForApplication')
                ->willReturn(['foo', 'bar']);
            $expectedMessage = "For security, remove write permissions from these directories: 'foo' 'bar'";
            $this->logger->expects($this->once())->method('log')->with($expectedMessage);
            $this->object->checkApplicationFilePermissions();
            $this->assertSame(['message' => [$expectedMessage]], $this->object->getInstallInfo());
        }

        public function testUpdateModulesSequence()
        {
            $this->cleanupFiles->expects($this->once())->method('clearCodeGeneratedFiles')->willReturn(
                [
                    "The directory '/generation' doesn't exist - skipping cleanup",
                ]
            );
            $installer = $this->prepareForUpdateModulesTests();

            $this->logger->expects($this->at(0))->method('log')->with('Cache cleared successfully');
            $this->logger->expects($this->at(1))->method('log')->with('File system cleanup:');
            $this->logger->expects($this->at(2))->method('log')
                ->with('The directory \'/generation\' doesn\'t exist - skipping cleanup');
            $this->logger->expects($this->at(3))->method('log')->with('Updating modules:');
            $installer->updateModulesSequence(false);
        }

        public function testUpdateModulesSequenceKeepGenerated()
        {
            $this->cleanupFiles->expects($this->never())->method('clearCodeGeneratedClasses');

            $installer = $this->prepareForUpdateModulesTests();

            $this->logger->expects($this->at(0))->method('log')->with('Cache cleared successfully');
            $this->logger->expects($this->at(1))->method('log')->with('Updating modules:');
            $installer->updateModulesSequence(true);
        }

        public function testUninstall()
        {
            $this->config->expects($this->once())
                ->method('get')
                ->with(ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS)
                ->willReturn([]);
            $this->configReader->expects($this->once())->method('getFiles')->willReturn([
                'ConfigOne.php',
                'ConfigTwo.php'
            ]);
            $configDir = $this->getMockForAbstractClass(
                WriteInterface::class
            );
            $configDir
                ->expects($this->exactly(2))
                ->method('getAbsolutePath')
                ->willReturnMap(
                    [
                        ['ConfigOne.php', '/config/ConfigOne.php'],
                        ['ConfigTwo.php', '/config/ConfigTwo.php']
                    ]
                );
            $this->filesystem
                ->expects($this->any())
                ->method('getDirectoryWrite')
                ->willReturnMap([
                    [DirectoryList::CONFIG, DriverPool::FILE, $configDir],
                ]);
            $this->logger->expects($this->at(0))->method('log')->with('Starting Magento uninstallation:');
            $this->logger
                ->expects($this->at(2))
                ->method('log')
                ->with('No database connection defined - skipping database cleanup');
            $cacheManager = $this->createMock(Manager::class);
            $cacheManager->expects($this->once())->method('getAvailableTypes')->willReturn(['foo', 'bar']);
            $cacheManager->expects($this->once())->method('clean');
            $this->objectManager->expects($this->any())
                ->method('get')
                ->with(Manager::class)
                ->willReturn($cacheManager);
            $this->logger->expects($this->at(1))->method('log')->with('Cache cleared successfully');
            $this->logger->expects($this->at(3))->method('log')->with('File system cleanup:');
            $this->logger
                ->expects($this->at(4))
                ->method('log')
                ->with("The directory '/var' doesn't exist - skipping cleanup");
            $this->logger
                ->expects($this->at(5))
                ->method('log')
                ->with("The directory '/static' doesn't exist - skipping cleanup");
            $this->logger
                ->expects($this->at(6))
                ->method('log')
                ->with("The file '/config/ConfigOne.php' doesn't exist - skipping cleanup");
            $this->logger
                ->expects($this->at(7))
                ->method('log')
                ->with("The file '/config/ConfigTwo.php' doesn't exist - skipping cleanup");
            $this->logger->expects($this->once())->method('logSuccess')->with('Magento uninstallation complete.');
            $this->cleanupFiles->expects($this->once())->method('clearAllFiles')->willReturn(
                [
                    "The directory '/var' doesn't exist - skipping cleanup",
                    "The directory '/static' doesn't exist - skipping cleanup"
                ]
            );

            $this->object->uninstall();
        }

        public function testCleanupDb()
        {
            $this->config->expects($this->once())
                ->method('get')
                ->with(ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS)
                ->willReturn(self::$dbConfig);
            $this->connection->expects($this->at(0))->method('quoteIdentifier')->with('magento')->willReturn(
                '`magento`'
            );
            $this->connection->expects($this->at(1))->method('query')->with('DROP DATABASE IF EXISTS `magento`');
            $this->connection->expects($this->at(2))->method('query')->with('CREATE DATABASE IF NOT EXISTS `magento`');
            $this->logger->expects($this->once())->method('log')->with('Cleaning up database `magento`');
            $this->object->cleanupDb();
        }

        /**
         * Prepare mocks for update modules tests and returns the installer to use
         * @return Installer
         */
        private function prepareForUpdateModulesTests()
        {
            $allModules = [
                'Foo_One' => [],
                'Bar_Two' => [],
                'New_Module' => [],
            ];

            $cacheManager = $this->createMock(Manager::class);
            $cacheManager->expects($this->once())->method('getAvailableTypes')->willReturn(['foo', 'bar']);
            $cacheManager->expects($this->once())->method('clean');
            $this->objectManager->expects($this->any())
                ->method('get')
                ->willReturnMap([
                    [Manager::class, $cacheManager]
                ]);
            $this->moduleLoader->expects($this->once())->method('load')->willReturn($allModules);

            $expectedModules = [
                ConfigFilePool::APP_CONFIG => [
                    'modules' => [
                        'Bar_Two' => 0,
                        'Foo_One' => 1,
                        'New_Module' => 1
                    ]
                ]
            ];

            $this->config->expects($this->atLeastOnce())
                ->method('get')
                ->with(ConfigOptionsListConstants::KEY_MODULES)
                ->willReturn(true);

            $newObject = $this->createObject(false, false);
            $this->configReader->expects($this->once())->method('load')
                ->willReturn(['modules' => ['Bar_Two' => 0, 'Foo_One' => 1, 'Old_Module' => 0]]);
            $this->configWriter->expects($this->once())->method('saveConfig')->with($expectedModules);

            return $newObject;
        }
    }
}

namespace Magento\Setup\Model {

    /**
     * Mocking autoload function
     *
     * @returns array
     */
    function spl_autoload_functions()
    {
        return ['mock_function_one', 'mock_function_two'];
    }
}
