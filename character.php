<?php
error_reporting(0);
/*
+--------------------------------------------------------+
|                   Made by HoltHelper                   |
|                                                        |
| Please give proper credits when using this code since  |
| It took me over a couple of months to finish this code |
|                                                        |
+--------------------------------------------------------+
*/

require_once('./coordinates.php');
extract($_GET);
$Image = new Controller($debug);

if(!empty($name) && $name = stripslashes(htmlentities($name))) {
    $cache = "./characters/".$name.".png";
    $minutes = 15; // Minutes until next hash update
    
    if(file_exists($cache) && (time() - ($minutes * 60) < filemtime($cache)) && $debug != true) {
        $Image->useImage($name)->display();
    } else {
        $mysqli = @mysqli_connect("localhost", "root", "", "") or die("Connection Error: ".mysqli_connect_error());
        
        if($character = mysqli_fetch_row(mysqli_query($mysqli, "SELECT `id`, `gender`, `job`, `skincolor`, `hair`, `face`, `hash` FROM `characters` WHERE `name` = '".mysqli_real_escape_string($mysqli, $name)."' LIMIT 1"))) {
            $inventory = mysqli_query($mysqli, "SELECT `itemid`, `position` FROM `inventoryitems` WHERE `characterid` = '".$character[0]."' AND `inventorytype` = '-1' ORDER BY `position` DESC");
            $variables = array("debug" => (bool)$debug, "gender" => (int)$character[1], "job" => (int)$character[2], "skin" => (int)$character[3], "hair" => (int)$character[4], "face" => (int)$character[5]);
            while(list($id, $position) = mysqli_fetch_row($inventory)) {
                switch($position) {
                    case -1: case -101:$variables['cap']               = (int)$id;break;
                    case -2: case -102:$variables['accessory']['face'] = (int)$id;break;
                    case -3: case -103:$variables['accessory']['eyes'] = (int)$id;break;
                    case -4: case -104:$variables['accessory']['ears'] = (int)$id;break;
                    case -5: case -105:$variables['coat']              = (int)$id;break;
                    case -6: case -106:$variables['pants']             = (int)$id;break;
                    case -7: case -107:$variables['shoes']             = (int)$id;break;
                    case -8: case -108:$variables['glove']             = (int)$id;break;
                    case -9: case -109:$variables['cape']              = (int)$id;break;
                    case -10:case -110:$variables['shield']            = (int)$id;break;
                    case -11: $variables['weapon']['base']             = (int)$id;break;
                    case -111:$variables['weapon']['cash']             = (int)$id;break;
                }
            }
            $hash = $Image->hash($variables);
            
            if($hash == $character[6] && file_exists($cache)) {
                $Image->useImage($name)->display();
                touch($cache);
            } else {
                mysqli_query($mysqli, "UPDATE `characters` SET `hash` = '".$hash."' WHERE `id` = '".$character[0]."'");
                $Image->setConstants($variables)
                ->lv2('weapon', 'characterEnd')
                ->lv2('cape', '0')
                ->lv1('cap', 'backHair')
                ->lv2('cape', 'backWing')
                ->lv1('cap', 'backHairOverCape')
                ->lv1('cap', 'capBelowBody')
                ->lv1('cap', 'capBelowHead')
                ->lv2('weapon', 'weaponOverGloveBelowMailArm')
                ->lv2('weapon', 'weaponBelowBody')
                ->lv1('cap', 'capeBelowBody')
                ->lv1('cap', 'backCap')
                ->hair('hairBelowBody')
                ->lv2('cape', 'capeBelowBody')
                ->lv2('shoes', 'capAccessoryBelowBody')
                ->lv1('cap', 'capAccessoryBelowBody')
                ->lv1('weapon', 'capAccessoryBelowBody') // Cap
                ->lv2('shield', 'shield')
                ->lv2('shield', 'shieldBelowBody')
                ->skin('body')
                ->lv1('cap', 'body')
                ->lv2('pants', 'pantsBelowShoes')
                ->lv2('coat', 'pantsBelowShoes')
                ->lv2('glove', 'gloveOverBody')
                ->lv2('glove', 'gloveWristOverBody')
                ->lv2('shield', 'gloveOverBody') // Weapon
                ->lv2('coat', 'mailChestBelowPants')
                ->lv2('shoes', 'shoes')
                ->lv2('pants', 'pants')
                ->lv2('coat', 'pants')
                ->lv2('coat', 'mailArmOverHair')
                ->lv2('shoes', 'shoesOverPants')
                ->lv2('coat', 'pantsOverShoesBelowMailChest')
                ->lv2('shoes', 'shoesTop')
                ->lv2('coat', 'backMailChest')
                ->lv2('pants', 'pantsOverShoesBelowMailChest')
                ->lv2('coat', 'mailChest')
                ->lv2('coat', 'mailChestOverPants')
                ->lv2('pants', 'pantsOverMailChest')
                ->lv2('coat', 'mailChestOverHighest')
                ->lv2('shoes', 'pantsOverMailChest')
                ->lv2('shoes', 'mailChestTop')
                ->lv2('shoes', 'weaponOverBody')
                ->lv2('coat', 'capeBelowBody')
                ->lv2('coat', 'mailChestTop')
                ->lv2('weapon', 'weaponOverArmBelowHead')
                ->lv2('shield', 'weaponOverArmBelowHead') // Weapon
                ->lv2('weapon', 'weapon')
                ->lv2('weapon', 'armBelowHeadOverMailChest')
                ->lv2('weapon', 'weaponOverBody')
                ->skin('arm')
                ->lv2('glove', 'gloveBelowMailArm')
                ->lv2('glove', 'glove')
                ->lv2('glove', 'gloveWrist')
                ->lv2('coat', 'mailArm')
                ->lv2('coat', 'capeBelowBody')
                ->lv2('weapon', 'emotionOverBody')
                ->skin('head')
                ->lv2('cape', 'cape')
                ->accessory('face', 'accessoryFaceBelowFace')
                ->accessory('eyes', 'accessoryEyeBelowFace')
                ->lv1('face', 'face')
                ->accessory('face', 'accessoryFace')
                ->accessory('face', 'accessoryFaceOverFaceBelowCap')
                ->lv2('coat', 'accessoryFaceOverFaceBelowCap')
                ->accessory('face', 'weaponBelowArm')
                ->accessory('face', 'capeOverHead')
                ->lv1('cap', 'capBelowAccessory')
                ->lv1('cap', 'capAccessoryBelowAccFace')
                ->accessory('eyes', 'accessoryEye')
                ->accessory('ears', 'accessoryEar')
                ->lv1('cap', 'accessoryEar')
                ->hair('hair')
                ->lv1('cap', 'cap')
                ->lv1('weapon', 'cap') // Cap
                ->skin('ear')
                ->accessory('eyes', 'accessoryEyeOverCap')
                ->lv1('cap', 'accessoryEyeOverCap')
                ->hair('hairOverHead')
                ->lv2('weapon', 'weaponOverArm')
                ->lv2('weapon', 'weaponBelowArm')
                ->lv2('weapon', 'weaponOverHand')
                ->skin('hand')
                ->lv2('glove', 'gloveOverHair')
                ->lv2('glove', 'gloveWristOverHair')
                ->lv2('weapon', 'weaponOverGlove')
                ->lv2('weapon', 'weaponWristOverGlove')
                ->lv2('cape', 'capeOverHead')
                ->accessory('eyes', 'accessoryOverHair')
                ->accessory('eyes', 'hairOverHead')
                ->accessory('eyes', 'accessoryEarOverHair')
                ->lv1('cap', 'capOverHair')
                ->lv1('cap', '0')
                ->accessory('ears', 'capOverHair')
                ->lv2('cape', 'capeOverWepon')
                ->lv2('cape', 'capOverHair')
                ->createImage($name)
                ->display();
            }
            if($debug) {
                $Image->setConstants($variables)->debug();
            }
        } else {
            $Image->useImage("faek")->display();
        }
    }
} else {
    $Image->useImage("faek")->display();
}
?>