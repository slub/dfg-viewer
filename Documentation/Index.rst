.. include:: Includes.txt

=============
Documentation
=============

The following document holds information about the installation of the *TYPO3
dfgviewer* extension.

Installation
============

The current release 5.x will only work with TYPO3 8.7 LTS.

The extension is based on *Kitodo.Presentation (dlf)*. Before you can start to
use the *DFG Viewer (dfgviewer)* in your TYPO3 installation, you have to install
both extensions. The installation in only possible by composer.
Kitodo.presentation will be installed and configured automatically.

Install a fresh TYPO3 8.7 LTS
-----------------------------

If you have no TYPO3 8.7 system running, you may install a fresh system by composer::

    # Assuming the following settings:
    #   * the installation directory is /var/www/dfgviewer
    #   * the Apache DocumentRoot is /var/www/dfgviewer/public
    #   * Apache is running as user www-data with group www-data
    #   * execution of all following commands as user www-data
    www-data@localhost:~/> cd /var/www/
    # remove /var/www/dfgviewer if it already exist
    www-data@localhost:/var/www> rm -r dfgviewer/
    # load full TYPO3 8.7 via composer
    www-data@localhost:/var/www> composer create-project typo3/cms-base-distribution:^8.7 dfgviewer
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

The DFG-Viewer extension assumes the default language is German (&L=0) and you
configure an additional "website-language" English (&L=1). This is only relevant
for localization of the metadata and structures labels.

Recommended Steps after Installation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

1. Go to the language module and install "German" as backend language.
2. [optional] Change the backend language in your user settings to German.
3. Create a new record "Website Language" on Page "0".
4. Go to the Install Tool --> All Configurations and change the default settings of pageNotFoundOnCHashError to '0'.

Your *typo3conf/LocalConfiguration.php* should contain this::

  'FE' => [
          'pageNotFoundOnCHashError' => '0',
  ],

Now you have a working TYPO3 8.7 LTS installation and you can continue with composer
to install DFG-Viewer extension.

.. error::

      If you are using e.g. Debian 10 with MariaDB 10.3 you have to force an
      update of Doctrine/Dbal. This is due to a missing feature / bug in
      doctring/dbal 2.5.13. TYPO3 8.7 did not changed the dependancies for doctrine/dbal.

      This way is working for the DFG-Viewer::

          www-data@localhost:/var/www/dfgviewer> composer require doctrine/dbal 2.5.13
          # in composer.json, change the following line:
          # "doctrine/dbal": "2.7.2 as 2.5.13"
          www-data@localhost:/var/www/dfgviewer> composer update doctrine/dbal --with-dependencies


Install DFG-Viewer and Kitodo.Presentation via Composer
-------------------------------------------------------

Composer commands::

    composer require kitodo/presentation:^3.0
    composer require slub/dfgviewer:^5.0

This will install the DFG-Viewer 5.x extension and Kitodo.Presentation 3.0 from
`Packagist <https://github.com/slub/dfg-viewer>`_.



Now you have to install the extension "dfgviewer" via the extension manager. It
will install "dlf" (Kitodo.Presentation) as dependancy automatically.

During the installation, three pages will be created: a root page, the "Kitodo
Configuration" folder and the viewer itself.

Configure DFG-Viewer and Kitodo.Presentation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You have to open and save once the configuration of Kitodo.Presentation in the
extension manager. This is necessary to write the default configuration to the
LocalConfiguration.php file.

The DFG-Viewer is almost configured. Only the Page-ID-constants have to be
adjustet. Go to the template module and use the constant editor to fit your
installation.

Success
-------

Now your installation should work. You can test this with the following url
(replace *host* and *id* with the parameters of your installation):

http://localhost/index.php?id=2&tx_dlf%5Bid%5D=http%3A%2F%2Fdigital.slub-dresden.de%2Foai%2F%3Fverb%3DGetRecord%26metadataPrefix%3Dmets%26identifier%3Doai%3Ade%3Aslub-dresden%3Adb%3Aid-263566811

Known Problems
--------------

You should use the following configuration in *typo3conf\LocalConfiguration.php*::

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
