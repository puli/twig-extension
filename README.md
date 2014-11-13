Loading Twig Templates with Puli
================================

[![Build Status](https://travis-ci.org/puli/twig-puli-extension.png?branch=master)](https://travis-ci.org/puli/twig-puli-extension)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/puli/twig-puli-extension/badges/quality-score.png?s=f1fbf1884aed7f896c18fc237d3eed5823ac85eb)](https://scrutinizer-ci.com/g/puli/twig-puli-extension/)
[![Code Coverage](https://scrutinizer-ci.com/g/puli/twig-puli-extension/badges/coverage.png?s=5d83649f6fc3a9754297da9dc0d997be212c9145)](https://scrutinizer-ci.com/g/puli/twig-puli-extension/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/728198dc-dc0f-4bab-b5c0-c0b4e2a55bce/mini.png)](https://insight.sensiolabs.com/projects/728198dc-dc0f-4bab-b5c0-c0b4e2a55bce)
[![Latest Stable Version](https://poser.pugx.org/puli/twig-puli-extension/v/stable.png)](https://packagist.org/packages/puli/twig-puli-extension)
[![Total Downloads](https://poser.pugx.org/puli/twig-puli-extension/downloads.png)](https://packagist.org/packages/puli/twig-puli-extension)
[![Dependency Status](https://www.versioneye.com/php/puli:twig-puli-extension/1.0.0/badge.png)](https://www.versioneye.com/php/puli:twig-puli-extension/1.0.0)

Latest release: none

PHP >= 5.3.9

With this extension for the [Twig templating engine], you can refer to template
files through Puli paths:

```php
echo $twig->render('/acme/blog/views/show.html.twig');
```

Installation
------------

In order to activate the extension, create a new [`PuliTemplateLoader`] and
register it with Twig. The loader turns a Puli path into an absolute path when
loading a template. Then, create a new [`PuliExtension`] and add it to Twig.
The extension takes care that templates loaded by the [`PuliTemplateLoader`]
are processed correctly.

```php
use Puli\Twig\PuliExtension\PuliTemplateLoader;
use Puli\Twig\PuliExtension\PuliExtension;

$twig = new \Twig_Environment(new PuliTemplateLoader($repo));
$twig->addExtension(new PuliExtension($repo));
```

As you see in this code snippet, you need to pass the Puli repository to
both the loader and the extension. If you don't know how to create that, you can 
find more information in Puli's [main documentation].

Usage
-----

Using Puli in Twig is straight-forward: Use Puli paths wherever you would
usually use a file path. For example:

```twig
{% extends '/acme/blog/views/layout.html.twig' %}

{% block content %}
    {# ... #}
{% endblock %}
```

Contrary to Twig's default behavior, you can also refer to templates using
relative paths:

```twig
{% extends 'layout.html.twig' %}

{% block content %}
    {# ... #}
{% endblock %}
```

[Twig templating engine]: http://twig.sensiolabs.org
[main documentation]: ../README.md
[`PuliTemplateLoader`]: ../src/Extension/Twig/PuliTemplateLoader.php
[`PuliExtension`]: ../src/Extension/Twig/PuliExtension.php
