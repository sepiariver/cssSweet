<?php
if (!$object->xpdo) return false;
/** @var modX $modx */
$modx = &$object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UPGRADE:
        if (array_key_exists('csssweet_attempt_restore_plugin_process_property', $options) && $options['csssweet_attempt_restore_plugin_process_property']) {
            $plugins = $modx->cacheManager->get('csssweet-setup-plugin_properties');
            if (is_array($pluginEvents)) {
                foreach ($pluginEvents as $pe) {
                    $plugin = $modx->getObject('modPlugin', $pe);
                    if ($plugin instanceof modPlugin) {
                        $plugin->set('property_preprocess', 1);
                        $plugin->save();
                    }
                }
            }
        }
        break;
    default:
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
        break;
}
return true;