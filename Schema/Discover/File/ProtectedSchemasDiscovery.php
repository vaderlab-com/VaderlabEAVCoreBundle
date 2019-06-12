<?php


namespace Vaderlab\EAV\Core\Schema\Discover\File;


use App\Kernel;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Vaderlab\EAV\Core\Annotation\ProtectedEntity;
use Vaderlab\EAV\Core\Reflection\EntityClassMetaResolver;
use Vaderlab\EAV\Core\Reflection\Reflection;

class ProtectedSchemasDiscovery
{
    /**
     * @var EntityClassMetaResolver
     */
    private $metaResolver;

    /**
     * @var BundleInterface[]
     */
    private $enabledBundlesMetaData;

    /**
     * @var string
     */
    private $appDir;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var Reflection
     */
    private $reflection;

    /**
     * ProtectedSchemasDiscovery constructor.
     * @param EntityClassMetaResolver $metaResolver
     * @param array $enabledBundles
     */
    public function __construct(
        EntityClassMetaResolver $metaResolver,
        Reflection $reflection,
        Kernel $kernel
    ) {

        $this->metaResolver     = $metaResolver;
        $this->enabledBundlesMetaData = $kernel->getBundles();
        $this->kernel = $kernel;
        $this->reflection = $reflection;
        $this->appDir = $kernel->getProjectDir() . '/src';
    }

    /**
     * Find all protected schemas of EAV models
     *
     * @return array
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     */
    public function discover(): array
    {
        $schemas = $this->loadModelsFromBundles();

        return $schemas;
    }

    /**
     * @TODO: Temporary solution
     */
    protected function loadModelsFromBundles()
    {
        $result = [
            'vendor' => [],
            'app' => []
        ];
        foreach ($this->enabledBundlesMetaData as $bundleMeta) {
            $root = $bundleMeta->getPath();
            $bundleName = $bundleMeta->getName();
            $files = $this->getBundleFilesList($root);
            $models = $this->loadBundleModels($files, $bundleMeta->getNamespace());
            if(!count($models)) {
                continue;
            }

            $result['vendor'][$bundleName] = $models;
        }

        $kernelRef = $this->reflection->createReflectionObject($this->kernel);
        $namespace = $kernelRef->getNamespaceName();

        $files = $this->getBundleFilesList($this->appDir);
        $models = $this->loadBundleModels($files, $namespace);
        if(!$models) {
            return $result;
        }

        $result['app'] = $models;

        return $result;
    }

    /**
     * @param string $root
     * @param string $namespace
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     */
    protected function load(string $root, string $namespace)
    {
        $files = $this->getBundleFilesList($root);
        $this->loadBundleModels($files, $namespace);
    }

    /**
     * @param array $bundleMeta
     * @return Finder
     */
    protected function getBundleFilesList(string $bundleRootDir): Finder
    {
        $finder = new Finder();
        $finder
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->ignoreUnreadableDirs(true)
            ->files()
            ->contains('ProtectedEntity')
            ->name('*.php')
            ->in($bundleRootDir)
        ;

        return $finder;
    }

    /**
     * @param Finder $files
     * @param string $namespace
     * @return array
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassBindException
     * @throws \Vaderlab\EAV\Core\Exception\Service\Reflection\EntityClassNotExistsException
     */
    protected function loadBundleModels(Finder $files, string $namespace)
    {
        $classes = [];
        foreach ($files as $file) {
            $classname = $this->getClassnameByFileinfo($file, $namespace);
            if(!$classname) {
                continue;
            }

            try {
                if(!$this->metaResolver->isProtectedSchema($classname)) {
                    continue;
                }
            } catch (AnnotationException $e) {
                continue;
            }


            $classes[] = $classname;
        }

        return $classes;
    }

    /**
     * @param SplFileInfo $fileInfo
     * @param string $namespace
     * @return string
     */
    protected function getClassnameByFileinfo(SplFileInfo $fileInfo, string $namespace)
    {
        $relative = $fileInfo->getRelativePathname();
        $relative = str_replace('.php', '', $relative);
        $relative = str_replace('/', '\\', $relative);

        $className = $namespace . '\\' . $relative;

        return class_exists($className) ? $className : $className;
    }

}