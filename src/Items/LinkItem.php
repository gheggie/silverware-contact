<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Items;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TreeDropdownField;
use SilverWare\Contact\Model\ContactItem;
use Page;

/**
 * An extension of the contact item class for a link item.
 *
 * @package SilverWare\Contact\Items
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class LinkItem extends ContactItem
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Link Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Link Items';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = ContactItem::class;
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'Name' => 'Varchar(255)',
        'LinkURL' => 'Varchar(2048)',
        'OpenLinkInNewTab' => 'Boolean'
    ];
    
    /**
     * Defines the has-one associations for this object.
     *
     * @var array
     * @config
     */
    private static $has_one = [
        'LinkPage' => Page::class
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'FontIcon' => 'external-link'
    ];
    
    /**
     * Answers a list of field objects for the CMS interface.
     *
     * @return FieldList
     */
    public function getCMSFields()
    {
        // Obtain Field Objects (from parent):
        
        $fields = parent::getCMSFields();
        
        // Create Main Fields:
        
        $fields->addFieldsToTab(
            'Root.Main',
            [
                TextField::create(
                    'Name',
                    $this->fieldLabel('Name')
                ),
                TreeDropdownField::create(
                    'LinkPageID',
                    $this->fieldLabel('LinkPageID'),
                    Page::class
                ),
                TextField::create(
                    'LinkURL',
                    $this->fieldLabel('LinkURL')
                )
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            CompositeField::create([
                CheckboxField::create(
                    'OpenLinkInNewTab',
                    $this->fieldLabel('OpenLinkInNewTab')
                )
            ])->setName('LinkItemOptions')->setTitle($this->i18n_singular_name())
        );
        
        // Answer Field Objects:
        
        return $fields;
    }
    
    /**
     * Answers a validator for the CMS interface.
     *
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return parent::getCMSValidator()->addRequiredField('Name');
    }
    
    /**
     * Answers the labels for the fields of the receiver.
     *
     * @param boolean $includerelations Include labels for relations.
     *
     * @return array
     */
    public function fieldLabels($includerelations = true)
    {
        // Obtain Field Labels (from parent):
        
        $labels = parent::fieldLabels($includerelations);
        
        // Define Field Labels:
        
        $labels['Name'] = _t(__CLASS__ . '.NAME', 'Name');
        $labels['LinkURL'] = _t(__CLASS__ . '.LINKURL', 'Link URL');
        $labels['LinkPageID'] = _t(__CLASS__ . '.LINKPAGE', 'Link page');
        $labels['OpenLinkInNewTab'] = _t(__CLASS__ . '.OPENLINKINNEWTAB', 'Open link in new tab');
        
        // Define Relation Labels:
        
        if ($includerelations) {
            $labels['LinkPage'] = _t(__CLASS__ . '.has_one_LinkPage', 'Link Page');
        }
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Populates the default values for the fields of the receiver.
     *
     * @return void
     */
    public function populateDefaults()
    {
        // Populate Defaults (from parent):
        
        parent::populateDefaults();
        
        // Populate Defaults:
        
        $this->Title = _t(__CLASS__ . '.DEFAULTTITLE', 'Link');
        
        $this->OpenLinkInNewTab = 0;
    }
    
    /**
     * Answers the value of the item for the CMS interface.
     *
     * @return string
     */
    public function getValue()
    {
        $parts = [$this->Name];
        
        if ($link = $this->Link) {
            $parts[] = sprintf('(%s)', rtrim($link, '/'));
        }
        
        return implode(' ', $parts);
    }
    
    /**
     * Answers the link for the template.
     *
     * @return string
     */
    public function getLink()
    {
        if ($this->LinkURL) {
            return $this->dbObject('LinkURL')->URL();
        }
        
        if ($this->LinkPageID) {
            return $this->LinkPage()->Link();
        }
    }
    
    /**
     * Answers true if the item is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->Link) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
