<?php
/**
 * cssSweet setup options resolver borrowed from GitPackageManagement 
 *
 * @package cssSweet
 * @subpackage build
 */

$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modx->log(modX::LOG_LEVEL_ERROR,'Confirmation not selected. Installation aborted.');//test
        $success= false;//test
        die();//test
        if (isset($options['confirm_upgrade_breaking_changes']) && !empty($options['confirm_upgrade_breaking_changes'])) {
            $success= true;
        } else {
            $modx->log(modX::LOG_LEVEL_WARN,'Confirmation not selected. Installation aborted.');
            $success= false;  
        }
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}
return $success;