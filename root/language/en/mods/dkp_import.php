<?php
/**
 * bbdkp common language file
 * 
 * @package bbDkp
 * @copyright 2009 bbdkp <http://code.google.com/p/bbdkp/>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id$
 * 
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}


// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(

'IMPORT_EQDKP132' => 'Import EQDKP 1.3.2 data Into bbdkp 1.1.0-RC2', 
'IMPORT_EQDKP132_CONFIRM' => 'Are you ready to import ? Your EQDKP tables need to begin with \'eqdkp\', and only dynamic data will be imported, not static data like class definitions etc. ', 
'IMPORT_EQDKP140' => 'Import EQDKP 1.4.0 data Into bbdkp 1.1.0-RC2', 
'IMPORT_EQDKP140_CONFIRM' => 'Are you ready to import ? Your EQDKP tables need to begin with \'EQDKP\', and only dynamic data will be imported, not static data like class definitions etc. ', 
'IMPORT_EQDKPPLUS' => 'Import EQDKP-PLUS data Into bbdkp 1.1.0-RC2', 
'IMPORT_EQDKPPLUS_CONFIRM' => 'Are you ready to import ? Your EQDKP-PLUS tables need to begin with \'EQDKP\', and only dynamic data will be imported, not static data like class definitions etc. ', 

'GUILD_ID'	=> 'guild id',
'GUILD_ID_EXPLAIN'	=> 'Choose your guild',

'DKP_ID'	=> 'dkp id',
'DKP_ID_EXPLAIN'	=> 'Choose your DKP pool',

'UMIL_CHOOSE' => 'Choose Game',

'UMIL_BBTP' => 'bbdkp table prefix',
'UMIL_BBTP_EXPLAIN' => 'change bbdkp table prefix from default (bbeqdkp_) if necessary',

'UMIL_EQTP' => 'eqdkp table prefix', 
'UMIL_EQTP_EXPLAIN' => 'change eqdkp table prefix from default (eqdkp_) if necessary', 

'UMIL_INSERT_EQDATA' => 'Inserted EverQuest Data', 
'UMIL_INSERT_EQ2DATA' => 'Inserted EverQuest II Data', 
'UMIL_INSERT_VANGUARDDATA' => 'Inserted Vanguard Data', 
'UMIL_INSERT_WOWDATA' => 'Inserted Warcraft Data',

'UMIL_INSERT_EQDKP' => 'Inserted EQDKP Data successfully',

'UMIL_INSERT_RANKS' => 'Copy EQDKP Rank : %s.',
'UMIL_REMOVE_RANKS' => 'Deleted EQDKP Ranks',  
'UMIL_RANKS_FAIL' => 'EQDKP Ranks Table not found',

'UMIL_INSERT_MEMBER' => 'Copy EQDKP Member : %s.',
'UMIL_REMOVE_MEMBER' => 'Deleted EQDKP Members',  
'UMIL_MEMBER_FAIL' => 'EQDKP Member Table not found',

'UMIL_INSERT_DKP' => 'Copy EQDKP Dkp : %s.',
'UMIL_REMOVE_DKP' => 'Deleted EQDKP Dkp',  
'UMIL_DKP_FAIL' => 'EQDKP Membertable not found',

'UMIL_INSERT_ADJ' => 'Copy EQDKP Dkp Adjustment: %s.',
'UMIL_REMOVE_ADJ' => 'Deleted EQDKP Dkp Adjustment',  
'UMIL_ADJ_FAIL' => 'EQDKP Adjustment table not found',

'UMIL_INSERT_EVENT' => 'Copy EQDKP event : %s.',
'UMIL_REMOVE_EVENT' => 'Deleted EQDKP event ',  
'UMIL_EVENT_FAIL' => 'EQDKP Event table not found',

'UMIL_INSERT_RAID' => 'Copy EQDKP Raid : %s.',
'UMIL_REMOVE_RAID' => 'Deleted EQDKP Raid ',  
'UMIL_RAID_FAIL' => 'EQDKP Raid table not found',

'UMIL_INSERT_ITEMS' => 'Copy EQDKP items : %s.',
'UMIL_REMOVE_ITEMS' => 'Deleted EQDKP item ',  
'UMIL_ITEMS_FAIL' => 'EQDKP Item table not found',

'UMIL_INSERT_RAIDATTENDEES' => 'Copy EQDKP Raidattendees. ',
'UMIL_REMOVE_RAIDATTENDEES' => 'Deleted EQDKP Raidattendees ',  
'UMIL_RAIDATTENDEES_FAIL' => 'EQDKP Raidattendees table not found',


));

?>
