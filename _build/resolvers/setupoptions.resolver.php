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
        if ($options['confirm_upgrade_breaking_changes']) {
            $success= true;
        } else {
            $success= false;
        }
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}
return $success;