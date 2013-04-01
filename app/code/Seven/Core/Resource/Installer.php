<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Core_Resource_Installer {

    /**
     * Set version of module into Database
     *
     * @param string $module_name module name
     * @param string $version module version
     * @return Core_Resource_Installer
     */
    
    public function changeVersionDb($module_name, $version) {
        $setup = Seven::getResource("core/setup");
        $adapt = Seven::getDatabaseAdapter();
        $table_name = "core_modules";
        $table = $adapt->getTable($table_name);
        $module_field = $adapt->select($table_name, array("name" => $module_name))->load();
        // TODO: Optimize: replace insert/update to Insert on duplicate key
        Seven::log(E_NOTICE, "Change {$module_name} version to {$version}");
        if (empty($module_field)) {      
            $adapt->insert($table_name, array("name" => $module_name, "version" => $version));  
        } else {
            $adapt->update($table_name, array("version" => $version), array("name" => $module_name));              
        }
        return $this;
    }

    /**
     *
     * Update module to version, which set in XML 
     * 
     * @param string $module_name module name 
     * @return Core_Resource_Installer 
     */
    
    public function update($module_name) {
        $verDB = $this->getVersionDb($module_name);
        //echo "<br>" . $module_name . " update() " . $verDB;
        $verXML = $this->getVersionXml($module_name);
        $module_path = BP . DS . "app" .DS. "code" .DS. 
                       Seven::getConfig("modules/" . $module_name ."/pool") .DS. 
                       $module_name .DS. "install";
        if (!is_dir($module_path)) 
            return $this; 
        $vc = version_compare($verXML, $verDB);
        if ($verDB == "0.0.0") {
        // DataBase hasn't any info about installed version
            if(($inst_version = $this->install($module_name, $verXML)) === NULL) {
                Seven::log(E_ERROR, "Skip '{$module_name}' installation");
                return $this;
            } 

            if ($inst_version != $verXML)
                    $this->upgradeTo ($module_name, $inst_version, $verXML);
        }
        else {
            if ($vc == 1)  // Db version lower
                $this->upgradeTo($module_name, $verDB, $verXML);
            //if ($vc == -1)  TODO: action, when XML version lower than Db
       }
       return $this;
    }    

    /**
     * Upgrade module from concrete version to concrete version
     *
     * @param string $module_name module name
     * @param string $from_version upgrade from module version
     * @param string $to_version upgrade from module version
     * @return Core_Resource_Installer
     */
    
    public function upgradeTo($module_name, $from_version, $to_version) {
        $versions = $this->getUpgradeVersions($module_name);
        $temp_curr = $from_version;
        do {
            $froms = array();
            foreach ($versions as $var) 
                if ($var[0] == $temp_curr)
                    $froms[] = $var; 
            // fill $froms with ver-s of updates, which has from_version equal with our from_version
            if (!empty($froms)) {
                foreach ($froms as $key => $value)
                    if (version_compare($value[1], $to_version) == 1 )
                        unset($froms[$key]); // delete higher version (than $to_version)
                while (count($froms) > 1) {
                // find max to_version
                    $froms = array_values($froms);
                    if ((version_compare($froms[0][1], $froms[1][1]) == 1))
                        unset($froms[1]);
                    if ((version_compare($froms[0][1], $froms[1][1]) == -1))
                        unset($froms[0]);
                }
            }
            else
                Seven::log(E_WARNING, "Can't upgrade from this version " . $temp_curr);
            $froms = array_values($froms);
            $file = BP . DS . "app" .DS. "code" .DS. 
                    Seven::getConfig("modules/" . $module_name ."/pool") .DS. 
                    $module_name .DS. "install" .DS. 
                    "upgrade-" . $froms[0][0] . "-" . $froms[0][1] . ".php";
            try {
                Seven::getResource("core/setup")->run($file);
                Seven::log(E_NOTICE, "Upgraded {$module_name} from {$temp_curr} to version {$froms[0][1]}");
                $this->changeVersionDb($module_name, $froms[0][1]);
            } catch(Exception $e) {
                Seven::log(E_WARNING, "Can't upgrade from this version " . $temp_curr);
                break;
            }
            $temp_curr = $froms[0][1];
        } while ($temp_curr != $to_version);
        return $this;
    }    
    
    /**
     * Make install 
     *
     * @param string $module_name module name
     * @param string $to_version version to install
     * @return string 
     */
    
    public function install($module_name, $to_version) {
        //echo "<br>" . $module_name . " try install " . $to_version;
        $versions = $this->getInstallVersions($module_name);
        $ver_to_install = "";
        foreach ($versions as $value) {
            if (version_compare($value, $to_version) == 0 )
                    $ver_to_install = $value;           
        }
        if (empty($ver_to_install)) {    
             foreach ($versions as $key => $value)
                    if (version_compare($value, $to_version) == 1 )
                        unset($versions[$key]); // delete higher version (than $to_version)
             while (count($versions) > 1) {
                 // find max to_version
                 $versions = array_values($versions);
                 if ((version_compare($versions[0], $versions[1]) == 1))
                     unset($versions[1]);
                 if ((version_compare($versions[0], $versions[1]) == -1))
                     unset($versions[0]);
                }
            $versions = array_values($versions);
            $ver_to_install = $versions[0];
        }
        $file = BP . DS . "app" .DS. "code" .DS.
                Seven::getConfig("modules/" . $module_name ."/pool") .DS.
                $module_name .DS. "install" . DS. "install-" . $ver_to_install . ".php";
        try {
            Seven::getResource("core/setup")->run($file);
            Seven::log(E_NOTICE, "Installed {$module_name} version {$ver_to_install}");
            $this->changeVersionDb($module_name, $ver_to_install);
        } catch(Exception $e) {
            Seven::log(E_WARNING, "Can't install " . $module_name . " version " . $to_version);
            return null;
        }
        return $ver_to_install;
    }
    
    /**
     * Return all available install files versions
     *
     * @param string $module_name module name
     * @return array
     */    
    
    public function getInstallVersions($module_name) {
        $module_path = BP . DS . "app" .DS. "code" .DS. 
                       Seven::getConfig("modules/" . $module_name ."/pool") .DS. 
                       $module_name .DS. "install";
        $files = glob($module_path . DS . "*.php");
        $versions = array();
        foreach ($files as $file) 
             if (preg_match('/^\D+-(\d\.\d\.\d)\.php$/U', $file, $matches))
                 $versions[] = $matches[1];
        return $versions;
     }
     
    /**
     * Return all available upgrade files versions
     *
     * @param string $module_name module name
     * @return array
     */       
     
     public function getUpgradeVersions($module_name) {
        $module_path = BP . DS . "app" .DS. "code" .DS. 
                       Seven::getConfig("modules/" . $module_name ."/pool") .DS. 
                       $module_name .DS. "install";
        $files = glob($module_path . DS . "*.php");
        $matches = array();
        $result = array();
        foreach ($files as $file)
            if (preg_match('/^.+(\d\.\d\.\d)-(\d\.\d\.\d)\.php$/U', $file, $matches)) {           
                $result[] = array($matches[1], $matches[2]);
                $matches = array();
            }
        return $result;
     }     
     
    /**
     * Return version of module into XML
     *
     * @param string $module_name module name
     * @return string 
     */     
     
    public function getVersionXml($module_name) {
        if ((Seven::getConfig("modules/" . $module_name . "/version")) === NULL )
                throw new Exception("Version for module " . $module_name . " not specified.");
        return Seven::getConfig("modules/" . $module_name . "/version");        
    }

    /**
     * Return version of module into Database
     *
     * @param string $module_name module name
     * @return string
     */    
    
    public function getVersionDb ($module_name) {
        $adapt = Seven::getDatabaseAdapter();
        $tables_list = $adapt->getTablesList();
        if (in_array("core_modules", $tables_list))
            $select_res = $adapt->select("core_modules", array("name" => $module_name))->load();
        if (empty($select_res))
            return "0.0.0";
        return $select_res[0]["version"];
    }
    
    /**
     * Make update for all modules
     *
     * @return Core_Resource_Installer
     */    
    
    public function updateAll() {

        $modules = Seven::getConfig("modules");
        $this->update("Core");
        foreach (array_keys($modules) as $module_name) {
            if ($module_name != "Core")
                $this->update($module_name);
        }
        
        return $this;
    }
}

