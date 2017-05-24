<?php

namespace Hyn\Tenancy\Listeners\Filesystem;

use Hyn\Tenancy\Abstracts\AbstractTenantDirectoryListener;
use Hyn\Tenancy\Events\Hostnames\Identified;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;

class LoadsTranslations extends AbstractTenantDirectoryListener
{
    protected $configBaseKey = 'tenancy.folders.trans';

    /**
     * @var string
     */
    protected $path = 'lang';

    /**
     * @param Identified $event
     */
    public function load(Identified $event)
    {
        $this->readLanguageFiles($this->directory->path($this->path, true));
    }

    protected function readLanguageFiles(string $path)
    {
        if ($this->config->get('tenancy.folders.trans.override-global')) {
            app()->singleton('translation.loader', function ($app) use ($path) {
                return new FileLoader($app['files'], $path);
            });
            app()->singleton('translator', function ($app) {
                $translator = new Translator($app['translation.loader'], $app['config']['app.locale']);
                $translator->setFallback($app['config']['app.fallback_locale']);
                return $translator;
            });
        } else if ($namespace = $this->config->get('tenancy.folders.trans.namespace')) {
            app('translator')->addNamespace($namespace, $path);
        }
    }
}
