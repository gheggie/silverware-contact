<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */

namespace SilverWare\Contact\Model;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\RequiredFields;
use SilverWare\FontIcons\Extensions\FontIconExtension;
use SilverWare\Forms\FieldSection;
use SilverWare\Model\Component;

/**
 * An extension of the component class for a contact item.
 *
 * @package SilverWare\Contact\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-contact
 */
class ContactItem extends Component
{
    /**
     * Human-readable singular name.
     *
     * @var string
     * @config
     */
    private static $singular_name = 'Item';
    
    /**
     * Human-readable plural name.
     *
     * @var string
     * @config
     */
    private static $plural_name = 'Items';
    
    /**
     * Description of this object.
     *
     * @var string
     * @config
     */
    private static $description = 'A component which represents a contact item';
    
    /**
     * Defines the table name to use for this object.
     *
     * @var string
     * @config
     */
    private static $table_name = 'SilverWare_ContactItem';
    
    /**
     * Icon file for this object.
     *
     * @var string
     * @config
     */
    private static $icon = 'silverware/contact: admin/client/dist/images/icons/ContactItem.png';
    
    /**
     * Defines an ancestor class to hide from the admin interface.
     *
     * @var string
     * @config
     */
    private static $hide_ancestor = Component::class;
    
    /**
     * Defines the allowed children for this object.
     *
     * @var array|string
     * @config
     */
    private static $allowed_children = 'none';
    
    /**
     * Maps field names to field types for this object.
     *
     * @var array
     * @config
     */
    private static $db = [
        'HideTitle' => 'Boolean'
    ];
    
    /**
     * Defines the default values for the fields of this object.
     *
     * @var array
     * @config
     */
    private static $defaults = [
        'HideTitle' => 0
    ];
    
    /**
     * Defines the extension classes to apply to this object.
     *
     * @var array
     * @config
     */
    private static $extensions = [
        FontIconExtension::class
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
        
        // Create Options Fields:
        
        $fields->addFieldsToTab(
            'Root.Options',
            [
                FieldSection::create(
                    'TitleOptions',
                    $this->fieldLabel('TitleOptions'),
                    [
                        CheckboxField::create(
                            'HideTitle',
                            $this->fieldLabel('HideTitle')
                        )
                    ]
                )
            ]
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
        return RequiredFields::create([
            'Title'
        ]);
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
        
        $labels['HideTitle'] = _t(__CLASS__ . '.HIDETITLE', 'Hide title');
        $labels['TitleOptions'] = _t(__CLASS__ . '.TITLE', 'Title');
        
        // Answer Field Labels:
        
        return $labels;
    }
    
    /**
     * Answers the heading tag for the receiver.
     *
     * @return string
     */
    public function getHeadingTag()
    {
        return $this->getParent()->HeadingTag;
    }
    
    /**
     * Answers true if the font icon is to be shown.
     *
     * @return boolean
     */
    public function getShowIcon()
    {
        if ($parent = $this->getParent()) {
            return (boolean) $parent->ShowIcons;
        }
    }
    
    /**
     * Answers true if the title is to be shown in the template.
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return !$this->HideTitle;
    }
    
    /**
     * Answers true to enable fixed width mode.
     *
     * @return boolean
     */
    public function getFontIconFixedWidth()
    {
        return true;
    }
    
    /**
     * Answers null to avoid problems with '$Content' double-ups in the template.
     *
     * @return null
     */
    public function getContent()
    {
        return null;
    }
    
    /**
     * Renders the object for the HTML template.
     *
     * @param string $layout Page layout passed from template.
     * @param string $title Page title passed from template.
     *
     * @return DBHTMLText|string
     */
    public function renderSelf($layout = null, $title = null)
    {
        return $this->customise([
            'Content' => $this->renderContent()
        ])->renderWith(self::class);
    }
    
    /**
     * Renders the content for the HTML template.
     *
     * @return DBHTMLText|string
     */
    public function renderContent()
    {
        if ($this->getTemplate() != self::class) {
            return $this->renderWith($this->getTemplate());
        }
    }
}
