<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Forms\GridField;

use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverWare\Contact\Model\ContactMessage;

/**
 * An extension of the record viewer grid field config class for contact messages.
 *
 * @package SilverWare\Contact\Forms\GridField
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactMessageConfig extends GridFieldConfig_RecordViewer
{
    /**
     * Constructs the object upon instantiation.
     *
     * @param integer $itemsPerPage
     */
    public function __construct($itemsPerPage = null)
    {
        // Construct Parent:
        
        parent::__construct($itemsPerPage);
        
        // Add Delete Action:
        
        $this->addComponent(new GridFieldDeleteAction());
        
        // Apply Formatting:
        
        $formatting = [];
        
        foreach (ContactMessage::singleton()->summaryFields() as $k => $v) {
            
            // Highlight Unread Messages:
            
            $formatting[$k] = function ($value, $item) {
                return sprintf('<span class="%s">%s</span>', ($item->isRead() ? 'read' : 'unread'), $value);
            };
            
        }
        
        $this->getComponentByType(GridFieldDataColumns::class)->setFieldFormatting($formatting);
        
        // Apply Extensions:
        
        $this->extend('updateConfig');
    }
}
