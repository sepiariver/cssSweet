<?php
if (!$object->xpdo) return false;
/** @var modX $modx */
$modx = &$object->xpdo;

switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_UPGRADE:
        if (array_key_exists('csssweet_attempt_restore_plugin_events', $options) && $options['csssweet_attempt_restore_plugin_events']) {
            $pluginEvents = $modx->cacheManager->get('csssweet-setup-plugin_events');
            if (is_array($pluginEvents)) {
                foreach ($pluginEvents as $pe) {
                    $event = $modx->getObject('modPluginEvent', [
                        'pluginid' => $pe['pluginid'],
                        'event' => $pe['event']
                    ]);
                    if ($event instanceof modPluginEvent) {
                        $event->fromArray($pe);
                        $event->save();
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