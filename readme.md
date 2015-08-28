# NewUp Playground: Workbench

[![Latest Stable Version](https://poser.pugx.org/newup-playground/workbench/v/stable)](https://packagist.org/packages/newup-playground/workbench) [![Total Downloads](https://poser.pugx.org/newup-playground/workbench/downloads)](https://packagist.org/packages/newup-playground/workbench) [![Latest Unstable Version](https://poser.pugx.org/newup-playground/workbench/v/unstable)](https://packagist.org/packages/newup-playground/workbench) [![License](https://poser.pugx.org/newup-playground/workbench/license)](https://packagist.org/packages/newup-playground/workbench)

This playground recreates the [workbench](https://github.com/laravel/framework/tree/4.2/src/Illuminate/Workbench) functionality from Laravel 4.2 using the NewUp package generator.

## Using The Playground

First make sure that NewUp is installed and configured. After that, issue the following command to create a new Laravel 4.2 package, where `<output>` is the directory to create the package and `vendor/package` is the vendor and package name of the package to create:

~~~
newup a newup-playground/workbench <output> vendor/package
~~~

The playground also provides support for Workbench's `--resources` option to create Laravel specific directories:

~~~
newup a newup-playground/workbench <output> vendor/package --resources
~~~

For more information on the output of this playground, consult the official [workbench documentation](http://laravel.com/docs/4.2/packages).

When this playground creates the final "composer.json" file it will use the configuration settings set in `config/configuration.php` configuration file. Specifically, it will use the `authors` and the `license` when creating the file. It is similar to the `workbench.php` configuration file from relevant versions of Laravel.