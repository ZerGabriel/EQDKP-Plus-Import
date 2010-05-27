<?php
/**
 * @package bbDkp-installer
 * @author sajaki9@gmail.com
 * @copyright (c) 2009 bbDkp <http://code.google.com/p/bbdkp/>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version $Id$
 * 
 */
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
define('IN_INSTALL', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// We only allow founder install 
if ($user->data['user_type'] != USER_FOUNDER)
{
    if ($user->data['user_id'] == ANONYMOUS)
    {
        login_box('', 'LOGIN');
    }

    trigger_error('NOT_AUTHORISED', E_USER_WARNING);
}

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
    trigger_error('Please download the latest UMIL (Unified MOD Install Library) 
    from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

if (!file_exists($phpbb_root_path . 'install/import_eqdkp132.' . $phpEx))
{
    trigger_error('Warning! Install directory has wrong name. 
    it must be \'install\'. Please rename it and launch again.', E_USER_WARNING);
}

$mod_name = 'EQDKP 1.3.2 Importer to bbDKP 1.1';

$version_config_name = 'eqdkp132importer_version';

$language_file = 'mods/dkp_import';

$options = array(

// the guild id to which you are importing your data, (must not be 0 !)
// if your guild isnt yet set up in bbdkp, do it.
// then look into the table bbeqdkp_memberguild for your id
// in a standard bbdkp install this value will be 1, otherwise it will be higher if multiple guilds.
		'guild_options' 	  => array('lang' => 'GUILD_ID', 'type' => 'select', 'function' => 'guild_options', 'explain' => true),  
// dkp id to be used
		'dkpidoptions' 		  => array('lang' => 'DKP_ID', 'type' => 'select', 'function' => 'dkpidoptions', 'explain' => true),  
//game
		'gameoptions' 		  => array('lang' => 'UMIL_CHOOSE', 'type' => 'select', 'function' => 'gameoptions', 'explain' => true),

// your bbdkp table prefix
		'bbdkp_table_prefix'  => array('lang' => 'UMIL_BBTP',  'type'  => 'text:40:255', 'explain' => true, 'default' => 'bbeqdkp_'),
// your eqdkp table prefix
	    'eqdkp_table_prefix'  => array('lang' => 'UMIL_EQTP',  'type'  => 'text:40:255', 'explain' => true, 'default' => 'eqdkp_'), 
 
);

$eqdkp_table_prefix= request_var('eqdkp_table_prefix', 'eqdkp_');
$bbdkp_table_prefix= request_var('bbdkp_table_prefix', 'bbeqdkp_');		 	
$guild_id= request_var('guild_id', 1);
$dkpid= request_var('dkpidoptions', 1);
if ( !class_exists('acp_dkp_mm')) 
   {
       // we need this class for accessing member functions
       include ($phpbb_root_path . 'includes/acp/acp_dkp_mm.' . $phpEx); 
   }
$acp_dkp_mm1 = new acp_dkp_mm;

// arrays to hold the unique keys
$memberiddata = array(); 
$raididdata = array(); 
   
$game = request_var('gameoptions', '');
   
switch ($game)
{
	case 'wow':
		// array for converting eqdkp classid to bbdkp classid (=blizz)
	    $bbdkpclass = array( 
	    	0 => 0, //unknown
	        1 => 1, //warrior 
	        2 => 4, //"Rogue
	        3 => 3, //"Hunter,
	        4 => 3, //"Hunter
	        5 => 5, //"Priest
	        6 => 2, // "Paladin
	        7 => 5, //"Priest
	        8 => 11, //Sruid
	        9 => 7, //Shaman
	        10 => 7, //Shaman
	        11 => 9, //Warlock
	        11 => 8, //Mage
	        13 => 2, //Paladin
	        12 => 1, //Warrior
	        14 => 6, //Death Knight
	      ); 
	      
	     // array for converting eqdkp raceid to bbdkp raceid (=blizz)
	    $bbdkprace = array( 
	        0 => 0, //Unknown
	        1 => 7, //Gnome
	        2 => 1, //Human
	        3 => 3, //Dwarf
	        4 => 4, //Night Elf
	        5 => 8, //Troll
	        6 => 5, //Undead
	        7 => 2, //Orc
	        8 => 6, //Tauren 
	        9 => 11, //Draenei
	        10 => 10, //Blood Elf
	      ); 
		break;
	
	case 'vanguard':
		
		// array for converting eqdkp classid to bbdkp classid
	    $bbdkpclass = array( 
	    	0 => 0, // unknown
	        1 => 1, //Bard 
	        2 => 2, //"Berserker
	        3 => 3, //"Blood Mage,
	        4 => 4, //"Cleric
	        5 => 5, //"Disciple
	        6 => 6, // "Dread Knight
	        7 => 7, //"Druid
	        8 => 0, //Inquisitor scrapped
	        9 => 8, //Monk
	        10 => 9, //Necromancer
	        11 => 10, //Paladin
	        12 => 11, //Psionicist
	        13 => 12, //Ranger 
	        14 => 13, //Rogue
	        15 => 0, //Shaman class scrapped
	        16 => 14, //Sorcerer
	        17 => 15, //Warrior
	      ); 
	      
	     // array for converting eqdkp raceid to bbdkp raceid
	    $bbdkprace = array( 
	    	 0 => 0, // unknown
	         1 => 0, //Barbarian - scrapped
	         2 => 16, //Dark Elf
	         3 => 2, //Dwarf
	         4 => 4, //Elf -> High Elf
	         5 => 7, //Giant -> lesser Giant
	         6 => 15, //Gnome
	         7 => 12, //Goblin
	         8 => 10, //Half-Elf
	         9 => 3, //Halfling
	        10 => 4, //High Elf
	        11 => 1, //Human
	        12 => 8, //Kojani
	        13 => 17, //Kurashasa
	        14 => 7, //Lesser Giant
	        15 => 18, //Mordebi
	        16 => 11, //Orc
	        17 => 14, //Qaliathari
	        18 => 13, //Raki
	        19 => 1, //Thestran
	        20 => 6, //Varanjar
	        21 => 19, //Varathari
	        22 => 5, //Vulmane
	        23 => 9, //Wood Elf
	      ); 
	      

		break; 

	case 'eq':
			
			// array for converting eqdkp classid to bbdkp classid
	    $bbdkpclass = array( 
	    	0 => 0, //Unknown
	        1 => 1, //Warrior
	        2 => 2, //Rogue
	        3 => 3, //Monk
	        4 => 4, //Ranger
	        5 => 5, //Paladin
	        6 => 6, //Shadowknight
	        7 => 7, //Bard
	        8 => 8, //Beastlord
	        9 => 9, //Cleric
	        10 => 10, //Druid
	        11 => 11, //Shaman
	        12 => 12, //Enchanter
	        13 => 13, //Wizard
	        14 => 14, //Necromancer
	        15 => 15, //Magician
	        16 => 16, //Berserker      				        				        				        				        				        				        				        				        				        				        				        				        
	      ); 
	      
	     // array for converting eqdkp raceid to bbdkp raceid
	    $bbdkprace = array( 
	    	 0 => 0, //unknown
	         1 => 1, //Gnome
	         2 => 2, //Human
	         3 => 3, //Barbarian
	         4 => 4, //Dwarf
	         5 => 5, //High Elf
	         6 => 6, //Dark Elf
	         7 => 8, //Wood Elf
	         8 => 6, //Half Elf
	         9 => 9, //Vah Shir
	        10 => 10, //Troll
	        11 => 11, //Ogre
	        12 => 12, //Frog
	        13 => 13, //Iksar
	        14 => 14, //Erudite
	        15 => 15, //Halfling
	        16 => 16, //

	      ); 

		break; 
		
	case 'eq2':
		
		// array for converting eqdkp classid to bbdkp classid
	    $bbdkpclass = array( 
	    	0 => 0, //unknown
	        1 => 0, //Fighter
	        2 => 0, //Scout
	        3 => 0, //Mage
	        4 => 0, //Priest
	        5 => 0, //Warrior
	        6 => 0, //Crusader
	        7 => 0, //Brawler
	        8 => 3, //Bruiser
	        9 => 13, //Monk
	        10 => 2, //Berserker
	        11 => 10, //Guardian
	        12 => 16, //Paladin
	        13 => 18, // Shadowknight
	        14 => 0, //Enchanter
	        15 => 0, //Sorcerer
	        16 => 0, //Summoner
	        17 => 11, //Illusionist
	        18 => 5, //Coercer
	        19 => 24, //Wizard
	        20 => 22, //Warlock
	        21 => 15, //Necromancer
	        22 => 6, //Conjuror
	        23 => 0, //Cleric
	        24 => 0, //Druid
	        25 => 0, //Shaman
	        26 => 20, //Templar
	        27 => 12, //Inquisitor
	        28 => 23, //Warden
	        29 => 9, //Fury
	        30 => 7, //Defiler
	        31 => 14, //Mystic
	        32 => 0, //Rogue
	        33 => 0, //Bard
	        34 => 0, //Predator
	        35 => 19, //Swashbuckler
	        36 => 4, //Brigand			        				        				        				        				        				        				        				        				        				        				        				        				        				        				        				        				        				        				        				        
	        37 => 8,//Dirge
	        38 => 21,//Troubador
	        39 => 1,//Assassin
	        40 => 17,//Ranger
	        41 => 0,//Craftsmen
	        42 => 0,//Scholar
	        43 => 0,//Outfitter
	        44 => 0,//Provisioner
	        45 => 0,//Woodworker
	        46 => 0,//Carpenter
	        47 => 0,//Armorer
	        48 => 0,//Weaponsmith
	        49 => 0,//Tailor
	        50 => 0,//Jeweler
	        51 => 0,//Sage
	        52 => 0,//Alchemist		        				        				        				        				        				        				        				        				        				        				        				        				        
	        				        				        
	      ); 
	      
	     // array for converting eqdkp raceid to bbdkp raceid
	    $bbdkprace = array( 
	    	 0 => 0, //unknown
	         1 => 1, //Gnome
	         2 => 2, //Human
	         3 => 3, //Barbarian
	         4 => 4, //Dwarf
	         5 => 5, //High Elf
	         6 => 6, //Dark Elf
	         7 => 8, //Wood Elf
	         8 => 6, //Half Elf
	         9 => 9, //Kerra
	        10 => 10, //Troll
	        11 => 11, //Ogre
	        12 => 12, //Froglok
	        13 => 14, //Erudite
	        14 => 13, //Iksar
	        15 => 16, //Ratonga
	        16 => 15, //Halfling
	      ); 
}
	
	
$versions = array(
    
    '1.0.0-PL1'    => array(
        
        'custom' => array( 
            'importfase1', 
            'importfase2',
            'importfase3',
            'importfase4',
            'importfase5',
            'importfase6',
            'importfase7',
            'importfase8',
            'importfase9',
            'bbdkp_caches'
       ),

       
    ),

);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);


// eqdkp_member_ranks	-> bbeqdkp_member_ranks
function importfase1($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx, $user; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $guild_id, $dkpid, $acp_dkp_mm1; 
    $text = ''; 
    
	if ($action == 'install')
	{
		if($umil->table_exists($eqdkp_table_prefix . 'member_ranks'))
		{
			//eqdkp has no rank with id "0"
			$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'member_ranks where rank_name is not NULL and rank_id > 0 ';
			$eqresult = $db->sql_query($sql);
			$number_ranks = 0; 
			while ($row = $db->sql_fetchrow($eqresult))
		    {
		        // check if rankid exists, insert if it doesnt
				$sql2 = 'SELECT count(*) as rcheck FROM ' . $bbdkp_table_prefix . 'member_ranks where rank_id = ' . $row ['rank_id'] . ' and guild_id = ' . $guild_id;
				$resultbb = $db->sql_query($sql2);
				if( (int) $db->sql_fetchfield('rcheck', false, $resultbb) == 0)
				{
					$acp_dkp_mm1->insertnewrank($row ['rank_id'], $row ['rank_name'] , $row ['rank_hide'], $row ['rank_prefix'],  $row ['rank_suffix'],  $guild_id) ; 
			        $text .= '<br />' . $row ['rank_name'] . ' inserted.';
			        $number_ranks++; 
				}
				else
				{
					$text .= $row ['rank_name'] . ' not inserted, rank ' . $row ['rank_id'] . ' already exists <br />';
				}
				$db->sql_freeresult($resultbb);
		    }
		    $db->sql_freeresult($eqresult);	
		   return array(
				'command'	=> array( 'UMIL_INSERT_RANKS', $text),
				'result'	=> 'SUCCESS'
			);

		}
		else
		{
			return array(
				'command'	=> array( 'UMIL_RANKS_FAIL'),
				'result'	=> 'FAIL'
			);
		}

	}
	
	if ($action == 'uninstall')
	{
		if($umil->table_exists($eqdkp_table_prefix . 'member_ranks'))
		{
			//eqdkp has no rank with id "0"
			$sql = 'SELECT rank_id FROM ' . $eqdkp_table_prefix . 'member_ranks where rank_name is not NULL and rank_id > 0 ';
			$eqresult = $db->sql_query($sql);
			while ($row = $db->sql_fetchrow($eqresult))
		    {
				$sql2 = 'delete FROM ' . $bbdkp_table_prefix . 'member_ranks where rank_id = ' . $row ['rank_id'] . ' and guild_id = ' . $guild_id;
				$db->sql_query($sql2);
		    }
		    $db->sql_freeresult($eqresult);
		    return 'UMIL_REMOVE_RANKS'; 
		}
		else
		{
			return array(
				'command'	=> array( 'UMIL_RANKS_FAIL'),
				'result'	=> 'FAIL'
			);
		
		}
	

	}

}


// eqdkp_members -> bbeqdkp_memberlist
function importfase2($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $guild_id, $dkpid, $acp_dkp_mm1, $bbdkprace, $bbdkpclass, $memberiddata;
    
     $text = ''; 
     
    // insert eqdkp members in bbdkp
	if ($action == 'install')
	{
	
		if($umil->table_exists($eqdkp_table_prefix . 'members'))
		{
			// treat members ordered by member_id
			$sql = 'SELECT member_id, member_name, member_status, member_level, member_race_id,
			 member_class_id, member_rank_id FROM ' . $eqdkp_table_prefix . 'members order by member_id ';
			
			$result = $db->sql_query($sql);
			
			$boardtime = array(); 
        	$boardtime = getdate(time() + $user->timezone + $user->dst - date('Z'));
        	$nowdate = $boardtime[0];
        				
		    $number_members = 0; 
		    while ($row = $db->sql_fetchrow($result))
		    {
				// check if already exists in bbDKP
		    	$sql2 = 'SELECT count(*) as rcheck 
			    		 FROM ' . $bbdkp_table_prefix . "memberlist 
			    		 WHERE member_name = '" . $db->sql_escape($row ['member_name']) . "'";
				$resultbb = $db->sql_query($sql2);
				if( (int) $db->sql_fetchfield('rcheck', false, $resultbb) == 0)
				{
		           //exists not
		           $acp_dkp_mm1->insertnewmember(
						$row ['member_name'], 
						$row ['member_status'], 
						$row['member_level'], 
						$bbdkprace[$row['member_race_id']],
						$bbdkpclass[$row['member_class_id']],  
						$row['member_rank_id'], 
					    "Member inserted " . date("F j, Y, g:i a") . ' by EQDKP 1.3.2 importer. ', 
						$nowdate, 
						mktime(0, 0, 0, 12, 31, 2030), 
						$guild_id, 
						0, 
						0, 
						' ');
			        $text .= '<br />' . $row ['member_name'] . ' inserted.';
					$number_members++; 			
				}
				else
				{
					$text .= '<br />' . $row ['member_name'] . ' not inserted, already exists in bbDKP. ' ;
				}
	
				// fill global array of eqdkp and bbdkp memberids
				$memberiddata[$row ['member_id']] = get_member_id( $row ['member_name']); 
				
				$db->sql_freeresult($resultbb);
		    }
		    $db->sql_freeresult($result);
		    return array(
				'command'	=> array( 'UMIL_INSERT_MEMBER', $text),
				'result'	=> 'SUCCESS'
			);
		
		}
		else
		{
			return array(
				'command'	=> array( 'UMIL_MEMBER_FAIL'),
				'result'	=> 'FAIL'
			);
		
		}
	}
	
	// remove members with same name as in eqdkp
	if ($action == 'uninstall')
	{
		if( $umil->table_exists($eqdkp_table_prefix . 'members') )
		{

			$sql = 'SELECT member_id, member_name FROM ' . $eqdkp_table_prefix . 'members';
			$eqresult = $db->sql_query($sql);
			
			while ($row = $db->sql_fetchrow($eqresult))
		    {
		    	$sql2 = 'delete FROM ' . $bbdkp_table_prefix . 'adjustments 
					where member_id = ' . get_member_id( $row ['member_name']) . ' and adjustment_dkpid =' . $dkpid ;
					$db->sql_query($sql2);
					
				$sql2 = 'delete FROM ' . $bbdkp_table_prefix . 'memberdkp where member_id = ' . get_member_id( $row ['member_name']) . ' and member_dkpid =' . $dkpid ; 
				$db->sql_query($sql2); 
				
				$sql2 = 'delete FROM ' . $bbdkp_table_prefix . "memberlist where member_name = '" . $db->sql_escape($row ['member_name']) . "'" ;
				$db->sql_query($sql2);
		    }
		    $db->sql_freeresult($eqresult);
		    return 'UMIL_REMOVE_MEMBER'; 
		}
		else
		{
			return array(
				'command'	=> array( 'UMIL_MEMBER_FAIL'),
				'result'	=> 'FAIL'
			);
		
		}
	

	}
	
	
}

// 3 eqdkp_members	-> bbeqdkp_memberdkp */
function importfase3($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $guild_id, $dkpid, $memberiddata;
     
    $text = ''; 
	if ($action == 'install')
	{
			if($umil->table_exists($eqdkp_table_prefix . 'members'))
			{
				// dkp points   
				$sql = 'SELECT member_id, member_earned, member_spent, member_adjustment,  member_status, 
				member_firstraid, member_lastraid, member_raidcount FROM ' . $eqdkp_table_prefix . 'members order by member_id  ';
				$result = $db->sql_query($sql);
				$number_dkpmembers = 0; 
			    while ($row = $db->sql_fetchrow($result))
			    {
			        $number_dkpmembers++; 
			        
					$query [] = array (
			   				'member_dkpid' 		    => $dkpid , 
			   				'member_id' 		    => $memberiddata[$row['member_id']] , 
			   				'member_earned'		    => $row['member_earned'] ,  
			   				'member_spent' 	        => $row['member_spent'] , 
			   				'member_adjustment' 	=> $row['member_adjustment'] , 
			   				'member_status' 	    => $row['member_status'],
			   				'member_firstraid' 	    => $row['member_firstraid'],
			  				'member_lastraid' 	    => $row['member_lastraid'] ,
			   				'member_raidcount' 		=> $row['member_raidcount'] , 
			  				);
				  				
				  	$text .= '<br />' . $row ['member_name'] . ' : ' . $row ['member_earned'] . ' - ' . $row ['member_spent'] . ' + ' . $row ['member_adjustment'] . 
				  	' = ' . $row ['member_earned'] - $row ['member_spent'] + $row ['member_adjustment'] . ' transferred.   ';
			    
			    }
				$db->sql_multi_insert($bbdkp_table_prefix . 'memberdkp ', $query);
				unset ($query);
				$db->sql_freeresult($result);

				return array(
					'command'	=> array( 'UMIL_INSERT_DKP', $text),
					'result'	=> 'SUCCESS'
				);
				
			}
			else
			{
				return array(
				'command'	=> array( 'UMIL_DKP_FAIL'),
				'result'	=> 'FAIL'
				);
			}
	}

}


// 4) eqdkp_adjustments    -> bbeqdkp_adjustments
function importfase4($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $guild_id, $dkpid;
    
    $text = ''; 
        
	switch ($action)
	{
		case 'install' :
			if($umil->table_exists($eqdkp_table_prefix . 'adjustments'))
			{
				$number_adjustments = 0; 
				$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'adjustments where member_name is not NULL';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
			    {
			        $number_adjustments++ ;
			        
			        $query [] = array (
						'adjustment_dkpid' 		    => $dkpid , 
						'adjustment_value'		    => $row ['adjustment_value'] ,  
						'adjustment_date' 	        => $row ['adjustment_date'] , 
						'member_id' 	            => get_member_id( $row ['member_name']),
						'adjustment_reason' 	    => $row['adjustment_reason'],
						'adjustment_added_by' 	    => $row['adjustment_added_by'],
						'adjustment_updated_by' 	=> (isset($row ['adjustment_updated_by']) ? $row ['adjustment_updated_by'] : ' '),  
						'adjustment_group_key' 		=> $row['adjustment_group_key'] , 
					);
			
			        $text .=  "<br/>" . $number_adjustments . ') '. $row ['member_name'] . ' : ' . $row ['adjustment_value'] . ' inserted' ;
			    }
			    $db->sql_multi_insert($bbdkp_table_prefix . 'adjustments', $query);
			    $db->sql_freeresult($result);
			    unset ($query);
				return array(
					'command'	=> array( 'UMIL_INSERT_ADJ', $text),
					'result'	=> 'SUCCESS'
				);
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_ADJ_FAIL'),
					'result'	=> 'FAIL'
				);
				
			}
				    
			break;
			
	}
	
}

// 5) eqdkp_events		  -> bbeqdkp_events
function importfase5($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $dkpid; 
    $text = ''; 
    
	switch ($action)
	{
		case 'install' :
			if($umil->table_exists($eqdkp_table_prefix . 'events'))
			{
			    $number_events = 0; 
				$sql = ' SELECT * FROM ' . $eqdkp_table_prefix . 'events ';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
			    {
			        $number_events++; 
			        $query [] = array (
						'event_dkpid' 		=> $dkpid , 
			        	'event_name'		=> $row ['event_name'],  
						'event_value' 	 	=> $row ['event_value'], 
						'event_added_by' 	=> $row ['event_added_by'], 
			        	'event_updated_by'	=> (isset($row ['event_updated_by']) ? $row ['event_updated_by'] : ' '), 
			        );
				
					$text .=  "<br/>" . $number_events . ') '. $row ['event_name'] . ' : ' . $row ['event_value'] . ' inserted' ;
					
			    }
			    $db->sql_multi_insert($bbdkp_table_prefix . 'events', $query);
			    unset ($query);
			    $db->sql_freeresult($result);
			    
			    return array(
					'command'	=> array( 'UMIL_INSERT_EVENT', $text),
					'result'	=> 'SUCCESS'
				);
			    
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_EVENT_FAIL'),
					'result'	=> 'FAIL'
				);
				
			}
		        
		break;
		
		case 'uninstall' :

			if($umil->table_exists($eqdkp_table_prefix . 'events'))
			{
				$sql = 'SELECT event_name FROM ' . $eqdkp_table_prefix . 'events ';
				$eqresult = $db->sql_query($sql);
				
				while ($row = $db->sql_fetchrow($eqresult))
			    {
					$sql2 = 'delete FROM ' . $bbdkp_table_prefix . "events where event_name = '" . $db->sql_escape($row ['event_name']) . "' and event_dkpid =" . $dkpid ;
					$db->sql_query($sql2);
			    }
			    $db->sql_freeresult($eqresult);
			    
			    return 'UMIL_REMOVE_EVENT'; 
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_EVENT_FAIL'),
					'result'	=> 'FAIL'
				);
			
			}		
		
		break;
			
	}
	return true;
}

// 6) eqdkp_raids -> bbeqdkp_raids
function importfase6($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $dkpid, $raididdata;
	switch ($action)
	{
		case 'install' :
			if($umil->table_exists($eqdkp_table_prefix . 'raids'))
			{
			
				if (!$umil->table_column_exists($bbdkp_table_prefix . 'raids', 'eqdkpraid_id'))
				{
					// add a new key in raids table to store the old eqdkp unique key. 
					$umil->table_column_add($bbdkp_table_prefix . 'raids', 'eqdkpraid_id', array('UINT', 0));
				}
				
			    $number_raids = 0;
				$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'raids ';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
			    {
			        $number_raids++;
		        	//bbdkp raid_id is set to autoincrease
			        $query [] = array (
						'raid_dkpid' 	    => $dkpid , 
			        	'eqdkpraid_id'		=> $row ['raid_id'],
						'raid_name' 		=> $row ['raid_name'], 
			        	'raid_note'		    => $row ['raid_note'],  
						'raid_date' 	 	=> $row ['raid_date'], 
						'raid_value' 	    => $row ['raid_value'],
			     		'raid_added_by' 	=> (isset($row ['raid_added_by']) ? $row ['raid_added_by'] : '')  ,
			     		'raid_updated_by' 	=> (isset($row ['raid_updated_by']) ? $row ['raid_updated_by'] : '')  
					);
			        $text .=  "<br/>" . $number_raids . ') Raid for '. $row ['raid_name'] . ' inserted for ' . $row ['raid_value'] . ' points ' ;
			        
			   }
			   $db->sql_multi_insert($bbdkp_table_prefix . 'raids', $query);
			   
			   unset ($query);	
			   $db->sql_freeresult($result);
			   
			   	$sql = 'SELECT raid_id, eqdkpraid_id FROM ' . $bbdkp_table_prefix . 'raids where eqdkpraid_id is not null and raid_dkpid = ' .  $dkpid;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$raididdata[$row ['eqdkpraid_id']] = $row ['raid_id']; 
				}
				$db->sql_freeresult($result);

				return array(
					'command'	=> array( 'UMIL_INSERT_RAID', $text),
					'result'	=> 'SUCCESS' 
			   );
								
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_RAID_FAIL'),
					'result'	=> 'FAIL'
				);
				
			}

		break;
		
		case 'uninstall' :
		
			if($umil->table_exists($eqdkp_table_prefix . 'raids')  and $umil->table_exists($eqdkp_table_prefix . 'raid_attendees'))
			{
				$sql = 'SELECT raid_name, raid_date FROM ' . $eqdkp_table_prefix . 'raids ';
				$eqresult = $db->sql_query($sql);
				
				while ($row = $db->sql_fetchrow($eqresult))
			    {
			    
			    	// remove the raidattendees
					$sql2 = 'delete FROM ' . $bbdkp_table_prefix . 'raid_attendees 
						where raid_id in ( 
							select raid_id from ' . $bbdkp_table_prefix . 'raids
							where raid_dkpid =' . $dkpid . " 
			 			    and raid_name = '" . $db->sql_escape($row ['raid_name']) . "' and raid_date = " . $row ['raid_date'] . ')' ; 
					$db->sql_query($sql2);

			    	// remove the raids
			    	$sql2 = 'delete FROM ' . $bbdkp_table_prefix . "raids where raid_name = '" . 
						$db->sql_escape($row ['raid_name']) . "' and raid_dkpid =" . $dkpid . ' and raid_date = ' . $row ['raid_date']; 
					$db->sql_query($sql2);
					
			    }
			    $db->sql_freeresult($eqresult);
			    
			    return 'UMIL_REMOVE_RAID'; 
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_RAID_FAIL'),
					'result'	=> 'FAIL'
				);
			
			}	
		
		break;
	}
	return true;	
}


//	8) eqdkp_raid_attendees -> bbeqdkp_raid_attendees
function importfase7($action, $version)
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $dkpid;
	switch ($action)
	{
		case 'install' :
			if($umil->table_exists($eqdkp_table_prefix . 'raid_attendees'))
			{
				$sql = 'SELECT raid_id, eqdkpraid_id FROM ' . $bbdkp_table_prefix . 'raids where eqdkpraid_id is not null and raid_dkpid = ' .  $dkpid;
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$raididdata[$row ['eqdkpraid_id']] = $row ['raid_id']; 
				}
				$db->sql_freeresult($result);
				
			    $number_raidattendees = 0;    
				$sql = 'select a.raid_id, a.member_name from ' . $eqdkp_table_prefix . 'raid_attendees a, eqdkp_raids b where a.raid_id = b.raid_id group by a.raid_id, a.member_name having count(*) = 1 ';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
			    {
			        $number_raidattendees++;
			        $query [] = array (
						'raid_id' 	        => $raididdata[$row ['raid_id']],  
			        	'member_id' 		=> get_member_id( $row ['member_name']),
			        	'member_name' 		=> $row ['member_name'], 
					);
			    }
			    $db->sql_multi_insert($bbdkp_table_prefix . 'raid_attendees', $query);
			    unset ($query);
			    $db->sql_freeresult($result);
			    
			    return array(
					'command'	=> array( 'UMIL_INSERT_RAIDATTENDEES'),
					'result'	=> 'SUCCESS'
				);
			
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_RAIDATTENDEES_FAIL'),
					'result'	=> 'FAIL'
				);
							
			}
		
		break;
		
		case 'uninstall':
		
			// quick uninstall added
			if($umil->table_exists($eqdkp_table_prefix . 'raid_attendees'))
			{
				$sql2 = 'delete FROM ' . $bbdkp_table_prefix . "raid_attendees ";
				$db->sql_query($sql2);
			    return 'UMIL_REMOVE_RAIDATTENDEES'; 
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_RAIDATTENDEES_FAIL'),
					'result'	=> 'FAIL'
				);
			
			}
			break;
					
	}
	return true;	
}


//	7) eqdkp_items	-> bbeqdkp_items
function importfase8($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix,$bbdkp_table_prefix, $dkpid, $raididdata;
    
    $text = ''; 
    switch ($action)
	{
		case 'install' :

			if($umil->table_exists($eqdkp_table_prefix . 'items'))
			{

				$number_items = 0;
				$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'items ';
				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
			    {
			        $number_items++; 
			        //item id is auto_increment 
			        $query [] = array (
						'item_dkpid' 	    => $dkpid ,  
						'item_name' 		=> $row ['item_name'], 
			        	'item_buyer'		=> $row ['item_buyer'],  
						'raid_id' 	 	    => $raididdata[$row ['raid_id']], 
						'item_value' 	 	=> $row ['item_value'], 
						'item_date' 	    => $row ['item_date'],
			     		'item_added_by' 	=> $row ['item_added_by'],  
			     		'item_updated_by' 	=> (isset($row ['item_updated_by']) ? $row ['item_updated_by']: ''),  
			     		'item_group_key' 	=> $row ['item_group_key'],  
					);
					
					$text .=  "<br/>" . $number_items . ') '. $db->sql_escape($row ['item_name']) . ' bought by : ' . $db->sql_escape($row ['item_buyer'])  . ' for : ' . $row ['item_value']  . ' inserted. ' ;
					
			    }
			    $db->sql_multi_insert($bbdkp_table_prefix . 'items', $query);
			    unset ($query);
			    $db->sql_freeresult($result);
			    
			    return array(
					'command'	=> array( 'UMIL_INSERT_ITEMS', $text),
					'result'	=> 'SUCCESS'
				);
				
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_ITEMS_FAIL'),
					'result'	=> 'FAIL'
				);
			
			}
		break;
		
		case 'uninstall' :
			
			if($umil->table_exists($eqdkp_table_prefix . 'items'))
			{
				$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'items ';
				$eqresult = $db->sql_query($sql);
				
				while ($row = $db->sql_fetchrow($eqresult))
			    {
					$sql2 = 'delete FROM ' . $bbdkp_table_prefix . "items where item_name = '" . $db->sql_escape($row ['item_name']) . "' and item_dkpid =" . $dkpid ;
					$db->sql_query($sql2);
			    }
			    $db->sql_freeresult($eqresult);
			    
			    // delete logs
			    $sql3 = 'delete FROM ' . $bbdkp_table_prefix . 'logs '; 
			    $db->sql_query($sql3);
			    
			    return 'UMIL_REMOVE_ITEMS'; 
			}
			else
			{
				return array(
					'command'	=> array( 'UMIL_ITEMS_FAIL'),
					'result'	=> 'FAIL'
				);
			
			}	

		break;
			
	}
	return true;	
}



// cleanup 
function importfase9($action, $version)
{
	global $umil, $game, $bbdkp_table_prefix;
	
	switch ($action)
	{
		case 'install' :
		
		if ($umil->table_column_exists($bbdkp_table_prefix . 'raids', 'eqdkpraid_id'))
		{
			$umil->table_column_remove($bbdkp_table_prefix . 'raids', 'eqdkpraid_id');	
		}
			
		switch ($game)
		{
			case 'wow':
			
			
				return array(
					'command' => 'UMIL_INSERT_WOWDATA', 
					'result' => 'SUCCESS');
				break;
			
			case 'vanguard':
				return array(
					'command' => 'UMIL_INSERT_VANGUARDDATA', 
					'result' => 'SUCCESS');			
				break; 
				
			case 'eq':
				return array(
					'command' => 'UMIL_INSERT_EQDATA', 
					'result' => 'SUCCESS');			
				break; 
				
			case 'eq2':									
				return array(
					'command' => 'UMIL_INSERT_EQ2DATA', 
					'result' => 'SUCCESS');
				break; 
		}
		break;
			
	}
}


/****************************
 *  
 * global function for rendering pulldown menu
 * 
 */
function guild_options($selected_value, $key)
{
	global $db, $user;
    /* guild pulldown menu rendering */
	$sql = 'SELECT id, name FROM '. GUILD_TABLE . ' where id > 0 order by id'; 
	$result = $db->sql_query($sql);
					
	$resultguild = $db->sql_query($sql);
	$glist= ''; 
	
	while ( $row = $db->sql_fetchrow($resultguild) )
	{
		$glist .= '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
	}
	 $db->sql_freeresult($resultguild);

	return $glist;
}


/****************************
 *  
 * global function for rendering pulldown menu
 * 
 */
function gameoptions($selected_value, $key)
{
	global $user;
    /* game importer pulldown menu rendering */
    $gametypes = array(
    	'eq'     		=> "EverQuest",
    	'eq2'     		=> "EverQuest II",
    	'vanguard'		=> "Vanguard - Saga of Heroes",
    	'wow'     		=> "World of Warcraft", 
    );
    $default = 'wow'; 
	$pass_char_options = '';
	foreach ($gametypes as $key => $game)
	{
		$selected = ($selected_value == $default) ? ' selected="selected"' : '';
		$pass_char_options .= '<option value="' . $key . '"' . $selected . '>' . $game . '</option>';
	}
	return $pass_char_options;
}

/****************************
 *  
 * global function for rendering pulldown menu
 * 
 */
function dkpidoptions($selected_value, $key)
{
	global $db, $user;
    /* dkp pools pulldown menu rendering */
	$sql = 'SELECT dkpsys_id, dkpsys_name , dkpsys_default  
			FROM ' . DKPSYS_TABLE .' 
			ORDER BY dkpsys_name';
	$resultdkpsys = $db->sql_query($sql);
	$dkplist= ''; 
	
	while ( $row = $db->sql_fetchrow($resultdkpsys) )
	{
		$dkplist .= '<option value="' . $row['dkpsys_id'] . '" >' . $row['dkpsys_name'] . '</option>';
	}
	 $db->sql_freeresult($resultdkpsys);

	return $dkplist;
}


/**************************************
 *  
 * global function for clearing cache
 * 
 */
function bbdkp_caches($action, $version)
{
    global $umil;
    
    $umil->cache_purge();
    $umil->cache_purge('imageset');
    $umil->cache_purge('template');
    $umil->cache_purge('theme');
    $umil->cache_purge('auth');
    
    return 'UMIL_CACHECLEARED';
}

function get_member_id($member_name)
{
	global $db, $bbdkp_table_prefix;

    $sql = 'SELECT member_id FROM ' . $bbdkp_table_prefix . "memberlist where member_name = '"  . $db->sql_escape($member_name) . "'" ;
    $result = $db->sql_query($sql);
    $member_id = (int) $db->sql_fetchfield('member_id', false, $result);
    $db->sql_freeresult($result);
    return $member_id; 
}


?>