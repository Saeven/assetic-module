<?php
namespace AsseticBundleTest;

use Assetic\Asset\FileAsset;
use AsseticBundle;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-11-17 at 11:53:23.
 */
class Service extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AsseticBundle\Service
     */
    protected $object;

    /**
     * @var array
     */
    protected $defaultOptions = array();

    /**
     * @var AsseticBundle\Configuration
     */
    protected $configuration;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->defaultOptions = array(
            'webPath' => TEST_PUBLIC_DIR,
            'routes' => array(
                'home' => array(
                    '@base_css',
                    '@base_js',
                ),
            ),
            'modules' => array(
                'test_application' => array(
                    'root_path' => TEST_ASSETS_DIR,
                    'collections' => array(
                        'base_css' => array(
                            'assets' => array(
                                'css/global.css',
                            ),
                            'filters' => array(
                                'CssRewriteFilter' => array(
                                    'name' => 'Assetic\Filter\CssRewriteFilter'
                                )
                            ),
                            'options' => array(),
                        ),
                        'base_js' => array(
                            'assets' => array(
                                'js/test.js',
                            )
                        ),
                        'base_images' => array(
                            'assets' => array(
                                'images/*.png',
                            ),
                            'options' => array(
                                'move_raw' => true,
                            )
                        ),
                        'base_fonts' => array(
                            'assets'  => array(
                                'fonts/*',
                            ),
                            'options' => array(
                                'disable_source_path' => true,
                                'move_raw'            => true,
                                'targetPath'          => 'public2/fonts/test'
                            )
                        ),
                    ),
                ),
            )
        );

        $this->configuration = new AsseticBundle\Configuration($this->defaultOptions);
        $this->object = new AsseticBundle\Service($this->configuration);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {}

    /**
     * @param string $routeName
     * @dataProvider getRoutesNamesProvider
     */
    public function testSetRouterName($routeName) {
        $result = $this->object->setRouteName($routeName);
        $this->assertNull($result);
        $this->assertEquals($routeName, $this->object->getRouteName());
    }

    public function getRoutesNamesProvider() {
        return array(
            'simple' => array('some'),
            'chained' => array('some/route/more'),
        );
    }

    public function testGetRouterName() {
        $value = $this->object->getRouteName();
        $this->assertEquals($value, AsseticBundle\Service::DEFAULT_ROUTE_NAME);

        $expected = 'name-router';
        $this->object->setRouteName($expected);
        $value = $this->object->getRouteName();
        $this->assertEquals($value, $expected);
    }

    public function testSetAssetManager() {
        $value = new \Assetic\AssetManager();
        $result = $this->object->setAssetManager($value);
        $this->assertNull($result);
        $this->assertSame($value, $this->object->getAssetManager());
    }

    public function testGetAssetManager() {
        $result = $this->object->getAssetManager();
        $this->assertInstanceOf('Assetic\AssetManager', $result);
    }

    public function testSetFilterManager() {
        $value = new \Assetic\FilterManager();
        $result = $this->object->setFilterManager($value);
        $this->assertNull($result);
        $this->assertSame($value, $this->object->getFilterManager());
    }

    public function testGetFilterManager() {
        $result = $this->object->getFilterManager();
        $this->assertInstanceOf('Assetic\FilterManager', $result);
    }

    public function testSetControllerName() {
        $expected = 'some-name';
        $value = $this->object->setControllerName($expected);
        $this->assertNull($value);
        $this->assertEquals($expected, $this->object->getControllerName());
    }

    public function testGetControllerName() {
        $value = $this->object->getControllerName();
        $this->assertNull($value);
    }

    public function testSetActionName() {
        $expected = 'some-name';
        $value = $this->object->setActionName($expected);
        $this->assertNull($value);
        $this->assertEquals($expected, $this->object->getActionName());
    }

    public function testGetActionName() {
        $value = $this->object->getActionName();
        $this->assertNull($value);
    }

    public function testInitLoadedModules() {
        $loadModules = array('test_application' => 'test_application');
        $this->object->build($loadModules);
        $assetManager = $this->object->getAssetManager();

        $this->assertTrue($assetManager->has('base_css'));
        $this->assertTrue($assetManager->has('base_js'));

        $this->assertFalse($assetManager->has('base_images'));
        $this->assertFalse($assetManager->has('base_fonts'));

        $assetFile = $assetManager->get('base_css')->getTargetPath();
        $this->assertStringStartsWith('base_css.', $assetFile);
        $this->assertStringEndsWith('.css', $assetFile);

        $assetFile = $assetManager->get('base_js')->getTargetPath();
        $this->assertStringStartsWith('base_js.', $assetFile);
        $this->assertStringEndsWith('.js', $assetFile);
    }

    public function testGetRendererName() {
        $renderer = $this->getMockBuilder('Zend\View\Renderer\RendererInterface')->disableOriginalConstructor()->getMock();
        $name = $this->object->getRendererName($renderer);
        $this->assertEquals(get_class($renderer), $name);
    }

    public function testHasStrategyForRenderer() {
        $renderer = $this->getMockBuilder('Zend\View\Renderer\RendererInterface')->disableOriginalConstructor()->getMock();
        $value = $this->object->hasStrategyForRenderer($renderer);
        $this->assertFalse($value);

        $this->object->getConfiguration()->addRendererToStrategy(
            get_class($renderer),
            'AsseticBundle\View\NoneStrategy'
        );

        $value = $this->object->hasStrategyForRenderer($renderer);
        $this->assertTrue($value);
    }

    public function testGetStrategyForRendererNull() {
        $renderer = $this->getMockBuilder('Zend\View\Renderer\RendererInterface')->disableOriginalConstructor()->getMock();
        $value = $this->object->getStrategyForRenderer($renderer);
        $this->assertNull($value);
    }

    /**
     * @expectedException AsseticBundle\Exception\InvalidArgumentException
     */
    public function testGetStrategyForRendererFailure() {
        $renderer = $this->getMockBuilder('Zend\View\Renderer\RendererInterface')->disableOriginalConstructor()->getMock();

        $this->object->getConfiguration()->addRendererToStrategy(
            get_class($renderer),
            'AsseticBundle\View\NonExisting'
        );

        $this->object->getStrategyForRenderer($renderer);
    }

    public function testGetStrategyForRendererSuccess() {
        $renderer = $this->getMockBuilder('Zend\View\Renderer\RendererInterface')->disableOriginalConstructor()->getMock();

        $this->object->getConfiguration()->addRendererToStrategy(
            get_class($renderer),
            'AsseticBundle\View\NoneStrategy'
        );

        $value = $this->object->getStrategyForRenderer($renderer);
        $this->assertInstanceOf('AsseticBundle\View\StrategyInterface', $value);
    }

    public function testWriteAssetIfNotExists() {
        $this->configuration->setBuildOnRequest(true);
        $this->configuration->setWriteIfChanged(true);

        $this->object->build();

        $manager = $this->object->getAssetManager();
        $factory = $this->object->createAssetFactory($this->configuration->getModule('test_application'));
        $asset   = $manager->get('base_css');
        $targetFile = $this->configuration->getWebPath($asset->getTargetPath());
        if (is_file($targetFile)) {
            unlink($targetFile);
        }

        $this->assertFileNotExists($targetFile);
        $this->object->writeAsset($asset, $factory);
        $this->assertFileExists($targetFile);
    }

    public function testWriteAssetIfIsUpdated() {
        $this->configuration->setBuildOnRequest(true);
        $this->configuration->setWriteIfChanged(true);

        $this->object->build();

        $manager = $this->object->getAssetManager();
        $assets  = $manager->get('base_css')->all();
        $factory = $this->object->createAssetFactory($this->configuration->getModule('test_application'));

        /** @var \Assetic\Asset\AssetInterface $asset */
        $asset = $assets[0];
        $asset->setTargetPath($manager->get('base_css')->getTargetPath());
        $targetFile = $this->configuration->getWebPath($asset->getTargetPath());
        if (is_file($targetFile)) {
            unlink($targetFile);
        }

        $this->assertFileNotExists($targetFile);
        $this->object->writeAsset($asset, $factory);
        $this->assertFileExists($targetFile);

        $sourceFile = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
        $targetMTime = filemtime($targetFile);

        // ensure that file modification timestamp is changed
        touch($targetFile, $targetMTime + 2);

        clearstatcache(true, $targetFile);
        $modifiedTargetMTime = filemtime($targetFile);

        $this->assertGreaterThan($targetMTime, $modifiedTargetMTime);

        $modifiedAsset = new FileAsset($sourceFile);
        $modifiedAsset->setTargetPath($targetFile);

        $this->object->writeAsset($modifiedAsset, $factory);

        clearstatcache(true, $targetFile);
        $modifiedTargetMTime = filemtime($targetFile);

        $this->assertGreaterThan($targetMTime, $modifiedTargetMTime);
    }

    public function testWriteAssetIfNotUpdated() {
        $this->configuration->setBuildOnRequest(true);
        $this->configuration->setWriteIfChanged(true);

        $this->object->build();

        $manager = $this->object->getAssetManager();
        $factory = $this->object->createAssetFactory($this->configuration->getModule('test_application'));
        $assets  = $manager->get('base_css')->all();

        /** @var \Assetic\Asset\AssetInterface $asset */
        $asset = $assets[0];
        $asset->setTargetPath($manager->get('base_css')->getTargetPath());
        $targetFile = $this->configuration->getWebPath($asset->getTargetPath());
        if (is_file($targetFile)) {
            unlink($targetFile);
        }

        $this->assertFileNotExists($targetFile);
        $this->object->writeAsset($asset, $factory);
        $this->assertFileExists($targetFile);

        $sourceFile = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
        $targetMTime = filemtime($targetFile);

        sleep(2);

        $modifiedAsset = new FileAsset($sourceFile);
        $modifiedAsset->setTargetPath($targetFile);

        $this->object->writeAsset($modifiedAsset, $factory);

        clearstatcache(true, $targetFile);
        $targetMTimeNotModified = filemtime($targetFile);

        $this->assertLessThanOrEqual($targetMTime, $targetMTimeNotModified);
    }

    public function testCacheBusterStrategyWorker()
    {
        $factory = $this->object->createAssetFactory($this->configuration->getModule('test_application'));
        // no workers by default:
        $this->assertAttributeEquals(array(), 'workers', $factory);

        $cacheBusterStrategy = $this->getMockBuilder('AsseticBundle\CacheBuster\LastModifiedStrategy')->getMock();
        $this->object->setCacheBusterStrategy($cacheBusterStrategy);
        $factory = $this->object->createAssetFactory($this->configuration->getModule('test_application'));
        // cache buster strategy is added to workers list:
        $this->assertAttributeEquals(array($cacheBusterStrategy), 'workers', $factory);
    }
}
