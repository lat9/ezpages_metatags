EZ-Pages Meta Tag Fields for Zen Cart 1.3.9 and 1.5.0 
Version 2.1 - Updated by That Software Guy 
-Added Zen Cart 1.3.9 version 

Version: 2.0 - Updated by ScriptJunkie
Author: Robert Mullaney
http://www.ebspromo.com/
Zen Forum PM ebspromo
Donations welcome at paypal@ebspromo.com


Released under the GNU General Public License - See License.txt
This script is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.


====================
  PURPOSE & AIM
====================
This mod adds 3 additional fields in the EZ-Pages editor
to accomodate meta title, keywords and description. It will
allow you to update those values without having to manually
edit meta_tags.php. If you leave the fields blank, it will
default back to whatever is defined in meta_tags.php.



====================
  FILES LIST
====================

New Files Added
------------------
SQL/install_sql.txt


Core Files Modified
--------------------
admin/ezpages.php
admin/includes/languages/english/ezpages.php
includes/modules/YOUR_CUSTOM_TEMPLATE/meta_tags.php

Database Changes: YES



====================
  INSTALLATION
====================

**ALWAYS - BACK UP YOUR FILES BEFORE INSTALLING AN ADDON**

1. Extract the contents of the zip file to a temp directory on your PC, leaving the folder and file structure intact.  

2. Use zc_139 or zc_150 depending on 
whether your Zen Cart version is 1.3.9 or 1.5.0.

3. Rename YOUR_CUSTOM_TEMPLATE (in includes/templates/) to match the name of your template override folder

4. If you or another addon have made any changes to the following files, you will need to use a file merging software like WinMerge or BeyondCompare to merge the file changes together.

	- admin/ezpages.php
	- admin/includes/languages/english/ezpages.php
	- includes/modules/YOUR_CUSTOM_TEMPLATE/meta_tags.php

All edits to these files are easy to locate because they are clearly commented as follows:


		/***** BEGIN EZPAGES SEO MOD *****/

		/**** END EZPAGES SEO MOD *****/


5. Using your favorite FTP program, upload the CONTENTS of the /admin folder to your store's admin directory. 

6. Using your favorite FTP program, upload the CONTENTS of the /includes folder to your store's /includes directory.

7. Open SQL/install_sql.txt and copy the contents of the file.

8. Log in to your store's admin. Navigate to admin > Tools > Install SQL Patches and paste the contents of the install_sql.txt file into the box and click "Send".



=====================
CHANGE LOG
=====================
Version 1.0 - 07/10/2008 - Initial Release for Zen Cart v1.3.8

Version 2.0 - 03/30/2012 - Updated for Zen Cart v1.5.0 by ScriptJunkie
Updated admin/ezpages.php & includes/modules/YOUR_CUSTOM_TEMPLATE/meta_tags.php to be compatible with Zen Cart v1.5.0. Cleaned up the folder/file structure in the download package to bring it up to date with current standards. Clarified the installation instructions. Cleaned up the README file and brought it up to date.

Version 2.1 - 08/01/12 - Added version 1.3.9. 

