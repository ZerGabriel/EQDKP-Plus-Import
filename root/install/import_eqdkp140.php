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

if (!file_exists($phpbb_root_path . 'install/import_eqdkp140.' . $phpEx))
{
    trigger_error('Warning! Install directory has wrong name. 
    it must be \'install\'. Please rename it and launch again.', E_USER_WARNING);
}

$mod_name = 'EQDKP 1.4.0 Importer to bbDKP 1.2.3 ';

$version_config_name = 'eqdkp140mporter';

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

// your eqdkp table prefix
	    'eqdkp_table_prefix'  => array('lang' => 'UMIL_EQTP',  'type'  => 'text:40:255', 'explain' => true, 'default' => 'eqdkp_'), 
 
);

$eqdkp_table_prefix= request_var('eqdkp_table_prefix', 'eqdkp_');
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
   
$game_id = request_var('gameoptions', '');
   
switch ($game_id)
{
case 'wow':
		// array for converting eqdkp140 classid to bbdkp123 classid (=blizz)
	    $bbdkpclass = array( 
	    	0 => 0, //unknown
	        1 => 11, //Druid 
	        2 => 3, //hunter
	        3 => 8, //Mage,
	        4 => 2, //Paladin
	        5 => 5, //Priest
	        6 => 4, //Rogue
	        7 => 7, //Shaman
	        8 => 9, //Warlock
	        9 => 1, //Warrior
	        10 => 6, //Deathknight
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
	
	case 'eq':
			
			// array for converting eqdkp140 classid to bbdkp classid
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
	      
	     // array for converting eqdkp140 raceid to bbdkp raceid
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
}
	
	
$versions = array(
    
    '1.0.2'    => array(
        
        'custom' => array( 
            'importfase0', 
            'importfase1', 
            'importfase2',
            'importfase3',
            'importfase4',
            'importfase5',
            'importfase6',
            'importfase7',
            'bbdkp_caches'
       ),

       
    ),

);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

function importfase0($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx, $user, $guild_id; 
    $text = 'purge data'; 
    if ($action == 'install')
	{
			$sql = 'DELETE FROM  ' . MEMBER_RANKS_TABLE . ' where rank_id != 90 and rank_id != 99 and guild_id = ' . $guild_id;
			$db->sql_query($sql);
			
			$sql = 'DELETE FROM ' . MEMBER_LIST_TABLE . ' where member_id > 1 and member_guild_id  = ' . $guild_id  ;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . MEMBER_DKP_TABLE;
			$db->sql_query($sql);

			$sql = 'TRUNCATE ' . ADJUSTMENTS_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . EVENTS_TABLE;
			$db->sql_query($sql);

			$sql = 'TRUNCATE ' . RAIDS_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . RAID_DETAIL_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . RAID_ITEMS_TABLE;
			$db->sql_query($sql);
			
			
		   return array(
				'command'	=> array( 'UMIL_INITIALISE', $text),
				'result'	=> 'SUCCESS'
			);
	}
	if ($action == 'uninstall')
	{
			$sql = 'DELETE FROM  ' . MEMBER_RANKS_TABLE . ' where rank_id != 90 and rank_id != 99 and guild_id = ' . $guild_id;
			$db->sql_query($sql);
			
			$sql = 'DELETE FROM ' . MEMBER_LIST_TABLE . ' where member_id > 1 and member_guild_id  = ' . $guild_id  ;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . MEMBER_DKP_TABLE;
			$db->sql_query($sql);
						
			$sql = 'TRUNCATE ' . ADJUSTMENTS_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . EVENTS_TABLE;
			$db->sql_query($sql);

			$sql = 'TRUNCATE ' . RAIDS_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . RAID_DETAIL_TABLE;
			$db->sql_query($sql);
			
			$sql = 'TRUNCATE ' . RAID_ITEMS_TABLE;
			$db->sql_query($sql);
	}
	
}


//eqdkp_member_ranks	-> phpbb_bbdkp_member_ranks
function importfase1($action, $version)
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx, $user; 
    global $eqdkp_table_prefix, $guild_id, $dkpid, $acp_dkp_mm1; 
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
				$sql2 = 'SELECT count(*) as rcheck FROM ' . $table_prefix . 'bbdkp_member_ranks where rank_id = ' .
				 $row ['rank_id'] . ' and guild_id = ' . $guild_id;
				$resultbb = $db->sql_query($sql2);
				if( (int) $db->sql_fetchfield('rcheck', false, $resultbb) == 0)
				{
					$acp_dkp_mm1->insertnewrank($row ['rank_id'], $row ['rank_name'] , $row ['rank_hide'], 
					$row ['rank_prefix'],  $row ['rank_suffix'],  $guild_id) ; 
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


}



// eqdkp_members -> phpbb_bbdkp_memberlist
function importfase2($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx, $user; 
    global $eqdkp_table_prefix, $guild_id, $dkpid, $acp_dkp_mm1, $bbdkprace, $bbdkpclass, $memberiddata, $game_id;
    
     $text = ''; 
     
    // insert eqdkp members in bbdkp
	if ($action == 'install')
	{
	
		if($umil->table_exists($eqdkp_table_prefix . 'members'))
		{
			// treat members ordered by member_id
			$sql = 'SELECT member_id, member_name, member_status, member_level, member_race_id, member_firstraid, 
			 member_class_id, member_rank_id FROM ' . $eqdkp_table_prefix . 'members order by member_id ';
			
			$result = $db->sql_query($sql);
			
			$boardtime = array(); 
        	$boardtime = getdate(time() + $user->timezone + $user->dst - date('Z'));
        	$nowdate = $boardtime[0];
			$member_id = 0;
		    $number_members = 0; 
		    while ($row = $db->sql_fetchrow($result))
		    {
		    	
		    	if((int) $row ['member_firstraid'] > 0)
		    	{
					$joindate = intval($row ['member_firstraid'])- 3600*24*10;
		    	}
		    	else 
		    	{
		    		$joindate = $nowdate;
		    	}
				
				$member_id = $acp_dkp_mm1->insertnewmember(
					$row['member_name'], 
					$row['member_status'], 
					$row['member_level'], 
					$bbdkprace[$row['member_race_id']],
					$bbdkpclass[$row['member_class_id']],  
					$row['member_rank_id'], 
				    "Member inserted " . date("F j, Y, g:i a") . ' by EQDKP+ importer. ', 
					$joindate, 
					0, 
					$guild_id, 
					0, 
					0, 
					' ',
					$game_id,
					0
					);
				$text .= '<br />' . $row ['member_name'] . ' inserted.';
				$number_members++; 			
				
				// fill global array of eqdkp and bbdkp memberids
				$memberiddata[$row ['member_id']] = $member_id; 
				
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
	
}

// 3 eqdkp_members	-> phpbb_bbdkp_memberdkp 
function importfase3($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix, $guild_id, $dkpid, $memberiddata;
     
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
			    	$member_id = $memberiddata[$row['member_id']];
			        $number_dkpmembers++; 
					$query [] = array (
			   				'member_id' 		    => $member_id , 
			   				'member_dkpid' 		    => $dkpid , 
			   				'member_raid_value'		=> $row['member_earned'] ,
			   				'member_earned'		    => $row['member_earned'] ,  
			   				'member_spent' 	        => $row['member_spent'] , 
			   				'member_adjustment' 	=> $row['member_adjustment'] , 
			   				'member_status' 	    => $row['member_status'],
			   				'member_firstraid' 	    => $row['member_firstraid'],
			  				'member_lastraid' 	    => $row['member_lastraid'] ,
			   				'member_raidcount' 		=> $row['member_raidcount'] , 
			  				);
				  				
				  	$text .= '<br />' . $member_id . ' : ' . $row ['member_earned'] . ' - ' . $row ['member_spent'] . ' + ' . $row ['member_adjustment'] . 
				  	' = ' . $row ['member_earned'] - $row ['member_spent'] + $row ['member_adjustment'] . ' transferred.   ';
			    
			    }
				$db->sql_multi_insert($table_prefix . 'bbdkp_memberdkp ', $query);
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


// 4) eqdkp_adjustments    -> phpbb_bbdkp_adjustments
function importfase4($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix, $guild_id, $dkpid;
    
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
						'member_id' 	            => get_member_id( $row ['member_name']),
						'adjustment_dkpid' 		    => $dkpid , 
						'adjustment_value'		    => $row ['adjustment_value'] ,  
						'adjustment_date' 	        => $row ['adjustment_date'] , 
						'adjustment_reason' 	    => $row['adjustment_reason'],
						'adjustment_added_by' 	    => $row['adjustment_added_by'],
						'adjustment_updated_by' 	=> (isset($row ['adjustment_updated_by']) ? $row ['adjustment_updated_by'] : ' '),  
						'adjustment_group_key' 		=> $row['adjustment_group_key'] , 
					);
			
			        $text .=  "<br/>" . $number_adjustments . ') '. $row ['member_name'] . ' : ' . $row ['adjustment_value'] . ' inserted' ;
			    }
			    $db->sql_multi_insert($table_prefix . 'bbdkp_adjustments', $query);
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

// 5) eqdkp_events		  -> phpbb_bbdkp_events
function importfase5($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix, $dkpid; 
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
			    $db->sql_multi_insert($table_prefix . 'bbdkp_events', $query);
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
			
	}
	return true;
}

// 6) eqdkp_raids -> phpbb_bbdkp_raids
function importfase6($action, $version )
{
    global $db, $table_prefix, $umil, $phpbb_root_path, $phpEx; 
    global $eqdkp_table_prefix, $dkpid, $game_id;
	switch ($action)
	{
		case 'install' :
			if($umil->table_exists($eqdkp_table_prefix . 'raids'))
			{
				$raididdata = array(); 
				$raidvalue = array(); 
				$text = '';
				if (!$umil->table_column_exists($table_prefix . 'bbdkp_raids', 'eqdkpraid_id'))
				{
					// add a new key in raids table to store the old eqdkp unique key. 
					$umil->table_column_add($table_prefix . 'bbdkp_raids', 'eqdkpraid_id', array('UINT', 0));
				}
				
				if (!$umil->table_column_exists($table_prefix . 'bbdkp_raids', 'raid_value'))
				{
					// add temp column to hold raid value 
					$umil->table_column_add($table_prefix . 'bbdkp_raids', 'raid_value',array('DECIMAL:11', 0));
				}
				
			    $number_raids = 0;
				$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'raids ';
				$result = $db->sql_query($sql);
			    // insert the raid
			    
				while ($row = $db->sql_fetchrow($result))
			    {
			    	// get the event id from the raid_name derived from the event_name
			    	// note the event_name is NOT unique in eqdkp+ so we will have to select distinct to get the id...
			    	$sql = 'SELECT DISTINCT event_id FROM ' . $table_prefix . "bbdkp_events WHERE event_name = '" . 
			    		$db->sql_escape( $row ['raid_name'])  . "' and event_dkpid = " . $dkpid;
		    	    $result1 = $db->sql_query($sql);
				    $event_id = intval($db->sql_fetchfield('event_id', false, $result1));
				    $db->sql_freeresult($result1);
				    
				    if($event_id == 0)
				    {
				    	// no valid event_id found, get first event...
				    	$sql = 'SELECT DISTINCT event_id FROM ' . $table_prefix . "bbdkp_events WHERE event_dkpid = " . $dkpid;
				    	$result1 = $db->sql_query($sql);
				    	$event_id = intval($db->sql_fetchfield('event_id', false, $result1));
				    	$db->sql_freeresult($result1);
				    }

			        $number_raids++;
		        	
			        //bbdkp raid_id is set to autoincrease
			        $query [] = array (
						'event_id' 			=> $event_id, 
			        	'eqdkpraid_id'		=> $row ['raid_id'],
			        	'raid_note'		    => $row ['raid_note'],  
						'raid_start' 	 	=> $row ['raid_date'],
			        	'raid_value' 	 	=> $row ['raid_value'],
			        	'raid_end' 	 		=> $row ['raid_date'] + 3600, 
			     		'raid_added_by' 	=> (isset($row ['raid_added_by']) ? $row ['raid_added_by'] : '')  ,
			     		'raid_updated_by' 	=> (isset($row ['raid_updated_by']) ? $row ['raid_updated_by'] : '')  
					);
			        $text .=  "<br/>" . $number_raids . ') Raid inserted for ' . $row ['raid_value'] . ' points ' ;
			        
			   }
			   $db->sql_multi_insert($table_prefix . 'bbdkp_raids', $query);
			   
			   unset ($query);	
			   $db->sql_freeresult($result);
			   
			   //make link array between eqdkp and bbdkp raid_id 
			   $sql = 'SELECT raid_id, eqdkpraid_id, raid_value FROM ' . $table_prefix . 'bbdkp_raids'; 
			   $result = $db->sql_query($sql);
			   while ($row = $db->sql_fetchrow($result))
			   {
					$raididdata[$row ['eqdkpraid_id']] = $row ['raid_id']; 
					$raidvalue[$row ['eqdkpraid_id']] = $row ['raid_value'];
			   }
			   $db->sql_freeresult($result);
			   
			   // Raid detail
			   if($umil->table_exists($eqdkp_table_prefix . 'raid_attendees'))
			   {
				    $number_raidattendees = 0;    
					$sql = 'select a.raid_id, a.member_name from ' . $eqdkp_table_prefix . 'raid_attendees a, eqdkp_raids b 
						where a.raid_id = b.raid_id group by a.raid_id, a.member_name having count(*) = 1 ';
					$result = $db->sql_query($sql);
					while ($row = $db->sql_fetchrow($result))
				    {
				        $number_raidattendees++;
				        $query [] = array (
							'raid_id' 	        => $raididdata[$row ['raid_id']],  
				        	'raid_value' 		=> $raidvalue[$row ['raid_id']],
				        	'member_id' 		=> get_member_id( $row ['member_name']),
						);
				    }
				    $db->sql_multi_insert($table_prefix . 'bbdkp_raid_detail', $query);
				    unset ($query);
				    $db->sql_freeresult($result);
				}
				
				// Raid items
				if($umil->table_exists($eqdkp_table_prefix . 'items'))
				{
					$number_items = 0;
					$sql = 'SELECT * FROM ' . $eqdkp_table_prefix . 'items ';
					$result = $db->sql_query($sql);
					// build bbdkp insert query
					while ($row = $db->sql_fetchrow($result))
				    {
				        $number_items++; 
				        //item id is auto_increment 
				        $query [] = array (
							'raid_id' 	 	    => $raididdata[$row ['raid_id']], 
				        	'member_id'			=> get_member_id( $row ['item_buyer']),
							'item_name' 		=> $row ['item_name'], 
							'item_value' 	 	=> $row ['item_value'], 
							'item_date' 	    => $row ['item_date'],
				     		'item_added_by' 	=> $row ['item_added_by'],  
				     		'item_updated_by' 	=> (isset($row ['item_updated_by']) ? $row ['item_updated_by']: ''),  
				     		'item_group_key' 	=> $row ['item_group_key'],  
						);
						
						$text .=  "<br/>" . $number_items . ') '. $db->sql_escape($row ['item_name']) . ' bought by : 
						' . $db->sql_escape($row ['item_buyer'])  . ' for : ' . $row ['item_value']  . ' inserted. ' ;
						
				    }
				    $db->sql_multi_insert($table_prefix . 'bbdkp_raid_items', $query);
				    unset ($query);
				    $db->sql_freeresult($result);
					
				}
				
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

	}
	return true;	
}


// cleanup 
function importfase7($action, $version)
{
	global $umil, $game_id, $table_prefix;
	
	switch ($action)
	{
		case 'install' :
		
		// remove temp columns
		if ($umil->table_column_exists($table_prefix . 'bbdkp_raids', 'eqdkpraid_id'))
		{
			$umil->table_column_remove($table_prefix . 'bbdkp_raids', 'eqdkpraid_id');	
		}
		
		if ($umil->table_column_exists($table_prefix . 'bbdkp_raids', 'raid_value'))
		{
			$umil->table_column_remove($table_prefix . 'bbdkp_raids', 'raid_value');	
		}
			
		switch ($game_id)
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
	global $db;
    /* guild pulldown menu rendering */
	$sql = 'SELECT id, name FROM '. GUILD_TABLE . ' where id > 0 order by id'; 
	$result = $db->sql_query($sql);
	$glist= ''; 
	while ( $row = $db->sql_fetchrow($result) )
	{
		$glist .= '<option value="' . $row['id'] . '" >' . $row['name'] . '</option>';
	}
	 $db->sql_freeresult($result);

	return $glist;
}


/****************************
 *  
 * global function for rendering pulldown menu
 * 
 */
function gameoptions($selected_value, $key)
{
    /* game importer pulldown menu rendering */
    $gametypes = array(
    	'eq'     		=> "EverQuest",
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
	global $db;
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


/**
 * finds memberid
 *
 * @param string $member_name
 * @return int
 */
function get_member_id($member_name)
{
	global $db, $table_prefix;

    $sql = 'SELECT member_id FROM ' . $table_prefix . "bbdkp_memberlist where member_name = '"  . $db->sql_escape($member_name) . "'" ;
    $result = $db->sql_query($sql);
    $member_id = (int) $db->sql_fetchfield('member_id', false, $result);
    $db->sql_freeresult($result);
    return $member_id; 
}


?>