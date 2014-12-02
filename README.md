Puli Extension for Twig
=======================

[![Build Status](https://travis-ci.org/puli/twig-puli-extension.png?branch=master)](https://travis-ci.org/puli/twig-puli-extension)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/puli/twig-puli-extension/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/puli/twig-puli-extension/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/b9a2807d-a63a-45a1-a681-0f706f81805c/mini.png)](https://insight.sensiolabs.com/projects/b9a2807d-a63a-45a1-a681-0f706f81805c)
[![Latest Stable Version](https://poser.pugx.org/puli/twig-puli-extension/v/stable.png)](https://packagist.org/packages/puli/twig-puli-extension)
[![Total Downloads](https://poser.pugx.org/puli/twig-puli-extension/downloads.png)](https://packagist.org/packages/puli/twig-puli-extension)
[![Dependency Status](https://www.versioneye.com/php/puli:twig-puli-extension/1.0.0/badge.png)](https://www.versioneye.com/php/puli:twig-puli-extension/1.0.0)

Latest release: none

PHP >= 5.3.9

With this extension for the [Twig templating engine], you can refer to template
files through [Puli] paths:

```php
echo $twig->render('/acme/blog/views/show.html.twig');
```

Read [Puli at a Glance] if you want to learn more about Puli.

Authors
-------

* [Bernhard Schussek] a.k.a. [@webmozart]
* [The Community Contributors]

Installation/Documentation
--------------------------

Follow the guide [Loading Twig Templates with Puli] to install the extension in
your project. This guide will tell you all you need to know to use the extension.

Contribute
----------

Contributions to are very welcome!

* Report any bugs or issues you find on the [issue tracker].
* You can grab the source code at Puli’s [Git repository].

Support
-------

If you are having problems, send a mail to bschussek@gmail.com or shout out to
[@webmozart] on Twitter.

License
-------

All contents of this package are licensed under the [MIT license].

[Bernhard Schussek]: http://webmozarts.com
[The Community Contributors]: https://github.com/puli/twig-puli-extension/graphs/contributors
[Twig templating engine]: http://twig.sensiolabs.org
[Puli]: https://github.com/puli/puli
[Loading Twig Templates with Puli]: http://puli.readthedocs.org/en/latest/extensions/twig.html
[Puli at a Glance]: http://puli.readthedocs.org/en/latest/at-a-glance.html
[issue tracker]: https://github.com/puli/puli/issues
[Git repository]: https://github.com/puli/twig-puli-extension
[@webmozart]: https://twitter.com/webmozart
[MIT license]: LICENSE
