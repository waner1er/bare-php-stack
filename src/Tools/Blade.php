<?php

namespace App\Tools;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;

class Blade
{
    protected Factory $factory;
    protected BladeCompiler $compiler;
    protected Container $container;

    public function __construct(string $viewsPath, string $cachePath)
    {
        $this->container = Container::getInstance();
        if (!$this->container) {
            $this->container = new Container();
            Container::setInstance($this->container);
        }

        $filesystem = new Filesystem();
        $eventDispatcher = new Dispatcher($this->container);

        $this->compiler = new BladeCompiler($filesystem, $cachePath);

        if (isset($_ENV['APP_DEBUG']) && $_ENV['APP_DEBUG'] === 'true') {
            foreach (glob($cachePath . '/*.php') as $file) {
                @unlink($file);
            }
        }

        $resolver = new EngineResolver();
        $resolver->register('blade', function () use ($filesystem) {
            return new CompilerEngine($this->compiler, $filesystem);
        });
        $resolver->register('php', function () use ($filesystem) {
            return new PhpEngine($filesystem);
        });

        $finder = new FileViewFinder($filesystem, [$viewsPath]);

        $this->factory = new Factory($resolver, $finder, $eventDispatcher);
        $this->factory->setContainer($this->container);

        $this->container->instance('view', $this->factory);
        $this->container->alias('view', Factory::class);
        $this->container->alias('view', 'Illuminate\Contracts\View\Factory');
    }

    public function render(string $view, array $data = []): string
    {
        return $this->factory->make($view, $data)->render();
    }

    public function compiler(): BladeCompiler
    {
        return $this->compiler;
    }
}
