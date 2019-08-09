.. include:: Includes.txt

=============
Documentation
=============

The following document holds information about the installation of the *TYPO3
dfgviewer* extension.

Installation
============

The current release 4.1 will only work with TYPO3 7.6. We will release a
TYPO3 8.7 compatible version soon.

The extension is based on *Kitodo.Presentation (dlf)*. Before you can start to
use the *DFG Viewer (dfgviewer)* in your TYPO3 installation, you have to install
both extensions. This is done preferable by composer.

Install a fresh TYPO3 7.6
-------------------------

If you have no TYPO3 7.6 system running, you may install a fresh system by composer::

    # assume the installation directory will be /var/www/dfgviewer
    cd /var/www/
    # enable HTTP because some dependancies of TYPO3 7.6 refer to a non-HTTPS resource.
    composer config -g secure-http false
    composer create-project typo3/cms-base-distribution dfgviewer 7.6

.. error::

    Unfortunately, this will end in an error message::

        [RuntimeException]
        Could not scan for classes inside "web/typo3/sysext/extbase/Tests/Unit/Object/Container/Fixtures/" which does not appear to be a file nor a folder

    You have to edit the /var/www/dfgviewer/composer.json by hand and remove the line::

        "classmap": ["web/typo3/sysext/extbase/Tests/Unit/Object/Container/Fixtures/"]

Save and close the composer.json and run composer update in /var/www/dfgviewer::

    composer update

Create the FIRST_INSTALL file in the documentroot of your new installation::

    touch web/FIRST_INSTALL

Continue now in your webbrowser to install the TYPO3 7.6 CMS.

At the end of the Install Tool, choose the option "Do nothing, just get me to
the Backend.".

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

Install DFG-Viewer and Kitodo.Presentation via Composer
-------------------------------------------------------

Composer commands::

    composer require slub/dfgviewer:dev-master

This will install the DFG-Viewer extension and Kitodo.Presentation 2.2 from TER.

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
