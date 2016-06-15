=============
Documentation
=============

The following document holds information about the installation of the *TYPO3 dfgviewer* extension. In case of further question please contact the release management.

Installation
============

The following documentation was tested with TYPO3 6.2.12. The extension is based on *Kitodo.Presentation (dlf)*. So before you can start to use the *DFG Viewer (dfgviewer)* in your TYPO3 installation, you have to install both extensions.

You can install the extension automatically via extension manager or semiautomatically via GitHub. The automatic way is straight forward so in the following the GitHub process is explained.
At first checkout the repository:

	git clone https://github.com/slub/dfg-viewer.git

After this create a symbolic link from your workspace to your TYPO3 extension folder:

	ln -s ~/dfg-viewer/dfgviewer/ /var/www/typo3conf/ext/

Repeat this process for the *Kitodo.Presentation (dlf)* (https://github.com/kitodo/kitodo-presentation.git) extension.

Now install both extensions via the extension manager within TYPO3. Also check if you have installed the necessary dependencies (e.g. *t3jquery*, *static_info_tables*). If the installation was successful, the category `GOOBI.PRESENTATION` with a subitem `DFG Viewer` will appear in the navigation tree on the left side of your TYPO3 backend.

Create a Page-Item in the category `WEB` as well as a configuration folder as a subitem of this page. The title of the page as well as the configuration file is arbitrary.

Go to the subitem `DFG Viewer` of the `GOOBI.PRESENTATION` category, choose the configuration folder and click both buttons (*Create structures*, *Create metadata*). This adds some basic configuration records in the configuration folder, which define METS structure type and MODS metadata fields.

Go to the `Template` tool, choose the page item, set the page to *Rootlevel* and add the *DFG Viewer (dfgviewer)* item to your *Selected Items* under *Includes*.
Then select the `Constants editor` and set the `storagePid` and `baseURL`according to your setup (`storagePid` should be the page ID of the configuration folder).

Before your installation will work you have to create an appropriate jQuery library. Go to the *T3 jQuery* extensions (could be found in the category *ADMIN TOOLS* and choose the first subitem *Process & Analyze t3jquery.txt in extensions*. Go to the dialog by choosing the following options: *select both extensions*, click *check*, click *Merge & Use* and finally click *Create jQuery Library* at the bottom of the page.

Now your installation should work. You can test this with the following url (replace *host* and *id* with the parameters of your installation):

http://localhost/index.php?id=21&tx_dlf%5Bid%5D=http%3A%2F%2Fdigital.slub-dresden.de%2Foai%2F%3Fverb%3DGetRecord%26metadataPrefix%3Dmets%26identifier%3Doai%3Ade%3Aslub-dresden%3Adb%3Aid-263566811

Known Problems
--------------

In TYPO3 6.2.12 there seems to be a *cHash* problem with the extension. For fixing it, add the following configuration parameters to *typo3conf\LocalConfiguration.php*:

'FE' => array(
	...
	'cHashRequiredParameters' => 'tx_dlf[id],',
        'pageNotFoundOnCHashError' => '0',
        'pageNotFound_handling' => '',
        ...
),
