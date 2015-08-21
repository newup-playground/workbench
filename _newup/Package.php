<?php

namespace NewupPlayground\Workbench;

use Illuminate\Support\Str;
use NewUp\Configuration\ConfigurationWriter;
use NewUp\Templates\Package as PackageClass;
use NewUp\Templates\BasePackageTemplate;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Package extends BasePackageTemplate
{

    /**
     * Returns the paths that NewUp should transform.
     *
     * Paths can be transformed to rapidly create nested paths, as well
     * as paths that contain variables. The original file will not be
     * created when using transform paths. For example, a file named
     * "ServiceProvider.php" will not be created in the root of the
     * output folder.
     *
     * @return array
     */
    public function getTransformPaths()
    {
        return [
            'ServiceProvider.php' => 'src/{{ vendor|studly }}/{{ package|studly }}/{{ package|studly~"ServiceProvider" }}.php',
        ];
    }

    /**
     * Override the builderLoaded method so we can know when NewUp
     * has loaded the workbench playground. We can access user
     * arguments and options at this point in template
     * generation, which will prove very useful.
     */
    public function builderLoaded()
    {
        // Get the parsed vendor and package name from the user input. NewUp's Package class
        // provides a helper method just for this.
        $vendorPackageParts = PackageClass::parseVendorAndPackage($this->argument('package'));
        $vendor = $vendorPackageParts[0];
        $package = $vendorPackageParts[1];

        // Share the vendor and package with the template system.
        $this->with([
            'vendor' => $vendor,
            'package' => $package
        ]);

        if (!$this->option('resources')) {
            // If the user has not specified the resources option, we can just tell
            // NewUp to ignore the Laravel specific directories. It is important
            // to note that ignored paths are actually patterns.
            $this->ignorePath([
                'src/controllers*',
                'src/migrations*',
                'src/config*',
                'src/views*',
                'src/lang*',
                'public/*',
            ]);
        }

        // Laravel 4's workbench tool created a "composer.json" file in the directory
        // of the new package. This is also very easy to accomplish using NewUp.
        // First, we will get a PackageClass instance that we can work with
        // and manipulate. Conveniently, the PackageClass's structure is
        // very close to that of a "composer.json" file. Since NewUp
        // allows users to configure the default authors and license
        // we can get a PackageClass instance with all of those
        // values already set.
        $composerJson = PackageClass::getConfiguredPackage();

        // Now we can set the vendor and package name, which we parsed earlier. We
        // will also need to add a few more things that the PackageClass cannot
        // handle on its own. We will take care of that later with the
        // ConfigurationWriter class.
        $composerJson->setVendor($vendor)->setPackage($package);

        // The PackageClass cannot save anything by itself, but a ConfigurationWriter
        // class is available that can. We can create a new instance of the writer
        // and pass all the values from the PackageClass as an array!
        $writer = new ConfigurationWriter($composerJson->toArray());

        // Before we save anything, we need to add a few more things to the final
        // "composer.json" file. We need to add the require, autoload and minimum
        // stability sections. Also take note that we are casting some arrays
        // into objects to get the desired output when everything is finally
        // saved in the JSON format.
        $writer['require'] = (object)['php' => '>=5.4.0', 'illuminate/support' => '4.2.*'];

        $autoloadSection = [
            'psr-0' => (object)[Str::studly($vendor).'\\\\'.Str::studly($package).'\\\\' => 'src/']
        ];

        // Add the migrations to the autoload section if the user specified the "resources" option.
        if ($this->option('resources')) {
            $autoloadSection['classmap'][] = 'src/migrations';
        }

        // Now we can add the autoload section.
        $writer['autoload'] = (object)$autoloadSection;

        $writer['minimum-stability'] = 'stable';

        // Now it is time to save the "composer.json" file.
        $writer->save($this->outputDirectory().'/composer.json');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public static function getOptions()
    {
        return [
            ['resources', null, InputOption::VALUE_NONE, 'Create Laravel specific directories.'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    public static function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The name (vendor/name) of the package.'],
        ];
    }

}