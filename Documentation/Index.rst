.. include:: Includes.txt

=============
Documentation
=============

The following document holds information about the installation of the *TYPO3
dfgviewer* extension.

Installation
============

The current release 5.x will only work with 9.5 LTS.

The extension is based on `Kitodo.Presentation (dlf) <https://github.com/kitodo/kitodo-presentation>`_. Before you can start to
use the *DFG Viewer (dfgviewer)* in your TYPO3 installation, you have to install
both extensions. The installation in only possible by composer.
Kitodo.presentation will be installed and configured automatically.

System Requirements
-------------------

You need a webserver stack with Apache2 or Ngnix, PHP >= 7.3 and MySQL / MariaDB.
Debian 10 (buster) is known to work with Kitodo.Presentation 3.1 and DFG-Viewer 5.0.

We recommend at least:

* CPU: 1
* Memory: 2 GB
* Disk: 20 GB

Install a fresh TYPO3 9.5 LTS
-----------------------------

To install a fresh TYPO3 9.5 system, try the following installation procedure with composer::

    # Assuming the following settings:
    #   * the installation directory is /var/www/dfgviewer
    #   * the Apache DocumentRoot is /var/www/dfgviewer/public
    #   * Apache is running as user www-data with group www-data
    #   * execution of all following commands as user www-data
    www-data@localhost:~/> cd /var/www/
    # remove /var/www/dfgviewer if it already exist
    www-data@localhost:/var/www> rm -r dfgviewer/
    # load full TYPO3 via composer
    www-data@localhost:/var/www> composer create-project typo3/cms-base-distribution:^9.5 dfgviewer
    # create FIRST_INSTALL file
    www-data@localhost:/var/www> cd dfgviewer/
    www-data@localhost:/var/www/dfgviewer> touch public/FIRST_INSTALL

Now you can switch to the web-based installation of TYPO3 in your browser. Just
follow the 4 steps. You need your MySQL/MariaDB credentials of course.::

    # continue with installation via webbrowser
    http://example.com/
    1. Step: "System environement"
    2. Step: "Database connection"
    3. Step: "Select database" --> Best is to create the database and user before.
    4. Step: "Create admin user account"
    5. Step: "Installation done!" --> Select "Do nothing, just get me to the Backend."
    # Test your backend login:
    http://example.com/typo3/

The DFG-Viewer extension assumes the default language is German (&L=0). An
additionial "website-language" English (&L=1) will be created automatically on
installing the DFG-Viewer extension. This is only relevant for localization of
the metadata and structures labels.

Recommended Steps after Installation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

1. Go to the language module and install "German" as backend language.
2. [optional] Change the backend language in your user settings to German.
3. Go to the Install Tool --> All Configurations and change the default settings of pageNotFoundOnCHashError to '0'.

Your *typo3conf/LocalConfiguration.php* should contain this::

  'FE' => [
          'pageNotFoundOnCHashError' => '0',
  ],

Now you have a working TYPO3 9.5 LTS installation and you can continue with composer
to install DFG-Viewer extension.


Install DFG-Viewer and Kitodo.Presentation via Composer
-------------------------------------------------------

Composer commands::

    composer require slub/dfgviewer:^5.1

This will install the DFG-Viewer 5.1 extension and Kitodo.Presentation 3.1 from
`Packagist <https://github.com/slub/dfg-viewer>`_.

Install the Extension via extension manager or CLI::

    www-data@localhost:/var/www/dfgviewer> ./vendor/bin/typo3 extensionmanager:extension:install dlf
    www-data@localhost:/var/www/dfgviewer> ./vendor/bin/typo3 extensionmanager:extension:install dfgviewer

During the installation, three pages will be created: a root page, the "Kitodo
Configuration" folder and the viewer itself.

Configure DFG-Viewer and Kitodo.Presentation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The DFG-Viewer is almost configured. Only the Page-ID-constants have to be
checked and adjusted. Go to the template module and use the constant editor to
fit your installation.

Success
-------

Now your installation should work. You can test this with the following url
(replace *host* and *id* with the parameters of your installation):

http://example.com/index.php?id=2&tx_dlf%5Bid%5D=https%3A%2F%2Fdigital.slub-dresden.de%2Foai%2F%3Fverb%3DGetRecord%26metadataPrefix%3Dmets%26identifier%3Doai%3Ade%3Aslub-dresden%3Adb%3Aid-263566811

To pass a document to the viewer, use the tx_dlf[id] GET parameter. Don't forget to urlencode the value.::

    &tx_dlf[id]=https%3A%2F%2Fdigital.slub-dresden.de%2Foai%2F%3Fverb%3DGetRecord%26metadataPrefix%3Dmets%26identifier%3Doai%3Ade%3Aslub-dresden%3Adb%3Aid-263566811

Known Problems
--------------

You should use the following configuration in *typo3conf/LocalConfiguration.php*::

  'FE' => [
          'pageNotFoundOnCHashError' => '0',
          'pageNotFound_handling' => '',
  ],

If you want to reinstall the DFG-Viewer extension, the metadata and structure
records won't be created a second time. To force this, you have to delete the
entry in table 'sys_references'.

Contact and Issues
------------------

The source-code is hosted at GitHub: `slub/dfg-viewer <https://github.com/slub/dfg-viewer>`_.
Please use the issue tracker to report errors or feature requests.

You may contact us by email to typo3@slub-dresden.de.
