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
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\Forms\TextField;
use SilverWare\Contact\Model\ContactItem;
use SilverWare\Forms\FieldSection;
use SilverWare\Forms\PageDropdownField;
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
     * Define constants.
     */
    const MODE_PAGE = 'page';
    const MODE_URL  = 'url';
    
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
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A contact item to show a link';
    
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
        'LinkTo' => 'Varchar(8)',
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
        'LinkTo' => 'page',
        'FontIcon' => 'external-link',
        'OpenLinkInNewTab' => 0
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
                SelectionGroup::create(
                    'LinkTo',
                    [
                        SelectionGroup_Item::create(
                            self::MODE_PAGE,
                            PageDropdownField::create(
                                'LinkPageID',
                                ''
                            ),
                            $this->fieldLabel('Page')
                        ),
                        SelectionGroup_Item::create(
                            self::MODE_URL,
                            TextField::create(
                                'LinkURL',
                                ''
                            ),
                            $this->fieldLabel('URL')
                        )
                    ]
                )->setTitle($this->fieldLabel('LinkTo'))
            ]
        );
        
        // Create Options Fields:
        
        $fields->addFieldToTab(
            'Root.Options',
            FieldSection::create(
                'LinkItemOptions',
                $this->i18n_singular_name(),
                [
                    CheckboxField::create(
                        'OpenLinkInNewTab',
                        $this->fieldLabel('OpenLinkInNewTab')
                    )
                ]
            )
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
        
        $labels['URL'] = _t(__CLASS__ . '.URL', 'URL');
        $labels['Name'] = _t(__CLASS__ . '.NAME', 'Name');
        $labels['Page'] = _t(__CLASS__ . '.PAGE', 'Page');
        $labels['LinkTo'] = _t(__CLASS__ . '.LINKTO', 'Link to');
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
     * Answers the page link for the template.
     *
     * @return string
     */
    public function getPageLink()
    {
        if ($this->isURL() && $this->LinkURL) {
            return $this->dbObject('LinkURL')->URL();
        }
        
        if ($this->isPage() && $this->LinkPageID) {
            return $this->LinkPage()->Link();
        }
    }
    
    /**
     * Answers true if the link is to a page.
     *
     * @return boolean
     */
    public function isPage()
    {
        return ($this->LinkTo == self::MODE_PAGE);
    }
    
    /**
     * Answers true if the link is to a URL.
     *
     * @return boolean
     */
    public function isURL()
    {
        return ($this->LinkTo == self::MODE_URL);
    }
    
    /**
     * Answers true if the item is disabled within the template.
     *
     * @return boolean
     */
    public function isDisabled()
    {
        if (!$this->PageLink) {
            return true;
        }
        
        return parent::isDisabled();
    }
}
